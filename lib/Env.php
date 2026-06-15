<?php

class Env
{
    /**
     * Load environment variables from a .env file.
     * 
     * @param string $path Path to the .env file
     */
    public static function load($path)
    {
        if (!file_exists($path)) {
            return false;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            // Skip comments and empty lines
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                // Remove surrounding quotes if present
                if (preg_match('/^"([^"]*)"$/', $value, $matches) || preg_match("/^'([^']*)'$/", $value, $matches)) {
                    $value = $matches[1];
                }

                if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                    putenv(sprintf('%s=%s', $name, $value));
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }
        return true;
    }

    /**
     * Get an environment variable with an optional default.
     */
    public static function get($key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            return isset($_ENV[$key]) ? $_ENV[$key] : $default;
        }
        return $value;
    }
}
