<?php

class SmtpClient
{
    private $host;
    private $port;
    private $secure;
    private $username;
    private $password;
    private $connection;
    private $debug = false;

    public function __construct($host, $port = 587, $secure = 'tls', $username = '', $password = '')
    {
        $this->host = $host;
        $this->port = $port;
        $this->secure = strtolower($secure); // 'tls', 'ssl', or ''
        
        // Port 465 is always implicit SSL, so override if user accidentally left it as tls
        if ($this->port == 465 && $this->secure === 'tls') {
            $this->secure = 'ssl';
        }

        $this->username = $username;
        $this->password = $password;
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    private function log($message)
    {
        if ($this->debug) {
            error_log("SMTP: " . trim($message));
        }
    }

    private function readResponse($expectedCode)
    {
        $response = '';
        while ($line = fgets($this->connection, 515)) {
            $response .= $line;
            $this->log("S: $line");
            if (substr($line, 3, 1) == ' ') {
                break;
            }
        }
        $code = substr($response, 0, 3);
        if ($code != $expectedCode) {
            throw new Exception("SMTP Error: Expected $expectedCode, got $code. Response: $response");
        }
        return $response;
    }

    private function sendCommand($command, $expectedCode)
    {
        $this->log("C: $command");
        fwrite($this->connection, $command . "\r\n");
        return $this->readResponse($expectedCode);
    }

    public function send($toEmail, $subject, $htmlBody, $fromEmail, $fromName = '')
    {
        $hostPrefix = ($this->secure === 'ssl') ? 'ssl://' : '';
        $this->connection = fsockopen($hostPrefix . $this->host, $this->port, $errno, $errstr, 30);

        if (!$this->connection) {
            throw new Exception("Could not connect to SMTP host: $this->host:$this->port ($errno) $errstr");
        }

        $this->readResponse(220);
        $this->sendCommand("EHLO localhost", 250);

        if ($this->secure === 'tls') {
            $this->sendCommand("STARTTLS", 220);
            $cryptoMethod = STREAM_CRYPTO_METHOD_TLS_CLIENT;
            if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
                $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
                $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT;
            }
            if (!stream_socket_enable_crypto($this->connection, true, $cryptoMethod)) {
                throw new Exception("Failed to enable TLS encryption");
            }
            $this->sendCommand("EHLO localhost", 250);
        }

        if ($this->username && $this->password) {
            $this->sendCommand("AUTH LOGIN", 334);
            $this->sendCommand(base64_encode($this->username), 334);
            $this->sendCommand(base64_encode($this->password), 235);
        }

        $this->sendCommand("MAIL FROM:<$fromEmail>", 250);
        $this->sendCommand("RCPT TO:<$toEmail>", 250);
        $this->sendCommand("DATA", 354);

        $boundary = md5(time());
        $headers = [];
        if ($fromName) {
            $headers[] = "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <$fromEmail>";
        } else {
            $headers[] = "From: $fromEmail";
        }
        $headers[] = "To: $toEmail";
        $headers[] = "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=";
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: text/html; charset=UTF-8";

        $headerString = implode("\r\n", $headers) . "\r\n\r\n";

        $this->log("C: [Message Data]");
        fwrite($this->connection, $headerString . $htmlBody . "\r\n.\r\n");
        $this->readResponse(250);

        $this->sendCommand("QUIT", 221);
        fclose($this->connection);
    }
}
