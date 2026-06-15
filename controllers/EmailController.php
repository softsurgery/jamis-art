<?php

require_once __DIR__ . '/../lib/Env.php';
require_once __DIR__ . '/../lib/SmtpClient.php';

// Initialize env if not already done in the entry point
Env::load(__DIR__ . '/../.env');

class EmailController
{
    private $smtpClient;
    private $fromEmail;
    private $fromName;

    public function __construct()
    {
        $host = Env::get('SMTP_HOST', 'localhost');
        $port = Env::get('SMTP_PORT', 587);
        $secure = Env::get('SMTP_SECURE', 'tls'); // 'tls', 'ssl', or ''
        $username = Env::get('SMTP_USERNAME', '');
        $password = Env::get('SMTP_PASSWORD', '');

        $this->fromEmail = Env::get('SMTP_FROM_EMAIL', 'no-reply@jamis-art.com');
        $this->fromName = Env::get('SMTP_FROM_NAME', 'Jamis Art');

        $this->smtpClient = new SmtpClient($host, $port, $secure, $username, $password);
    }

    /**
     * Set debug mode on the SMTP client
     */
    public function setDebug($debug)
    {
        $this->smtpClient->setDebug($debug);
    }

    /**
     * Sends an email using a PHP template file
     * 
     * @param string $toEmail Recipient email address
     * @param string $subject Email subject
     * @param string $templatePath Path to the PHP template file relative to views/emails/ (e.g., 'welcome.php')
     * @param array $templateData Data to extract into the template scope
     * @return bool True on success, False on failure
     */
    public function sendTemplateEmail($toEmail, $subject, $templatePath, $templateData = [])
    {
        try {
            // Render the template
            $body = $this->renderTemplate($templatePath, $templateData);

            // Send the email
            $this->smtpClient->send($toEmail, $subject, $body, $this->fromEmail, $this->fromName);
            return true;
        } catch (Exception $e) {
            echo ("Failed to send email to $toEmail: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Sends a raw HTML email
     */
    public function sendHtmlEmail($toEmail, $subject, $htmlBody)
    {
        try {
            $this->smtpClient->send($toEmail, $subject, $htmlBody, $this->fromEmail, $this->fromName);
            return true;
        } catch (Exception $e) {
            error_log("Failed to send HTML email to $toEmail: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Renders a PHP file as a string
     */
    private function renderTemplate($templatePath, $data)
    {
        $fullPath = __DIR__ . '/../views/emails/' . ltrim($templatePath, '/');

        if (!file_exists($fullPath)) {
            throw new Exception("Email template not found: $fullPath");
        }

        // Extract variables to make them available in the template
        extract($data);

        // Start output buffering
        ob_start();
        include $fullPath;
        $content = ob_get_clean();

        return $content;
    }
}
