<?php
/**
 * Application Configuration & Environment Loader
 * CWebS - Multi-Tenant Public School CMS
 */

// Prevent multiple inclusions
if (defined('CWEBS_CONFIG_LOADED')) {
    return;
}
define('CWEBS_CONFIG_LOADED', true);

/**
 * Lightweight .env file parser
 */
function load_env_file($path) {
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        // Skip comments and invalid lines
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Strip surrounding quotes
            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv("{$name}={$value}");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

// Load .env from project root
load_env_file(dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env');

/**
 * Retrieve an environment variable with a fallback default
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function env($key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }

    if ($value === false || $value === null) {
        return $default;
    }

    // Convert string booleans
    $lower = strtolower((string)$value);
    if ($lower === 'true' || $lower === '(true)') {
        return true;
    }
    if ($lower === 'false' || $lower === '(false)') {
        return false;
    }
    if ($lower === 'null' || $lower === '(null)') {
        return null;
    }

    return $value;
}

// Database Configuration Constants
define('DB_HOST', env('DB_HOST', '127.0.0.1'));
define('DB_PORT', (int)env('DB_PORT', 3306));
define('DB_NAME', env('DB_NAME', 'accounts'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));

// Application Settings
define('APP_NAME', env('APP_NAME', 'CWebS'));
define('APP_ENV', env('APP_ENV', 'development'));
define('APP_DEBUG', (bool)env('APP_DEBUG', true));
define('APP_URL', env('APP_URL', 'http://localhost:8000'));

// Configure PHP error reporting according to environment
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}
