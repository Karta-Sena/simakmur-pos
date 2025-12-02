<?php
// Load dan parse .env file

class EnvLoader {
    public static function load($path) {
        if (!file_exists($path)) {
            throw new Exception(".env file not found at: " . $path);
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                
                $key = trim($key);
                $value = trim($value);
                $value = self::removeQuotes($value);
                
                if (!array_key_exists($key, $_ENV)) {
                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }
        
        return true;
    }

    public static function get($key, $default = null) {
        if (array_key_exists($key, $_ENV)) {
            return self::parseValue($_ENV[$key]);
        }
        
        $value = getenv($key);
        if ($value !== false) {
            return self::parseValue($value);
        }
        
        return $default;
    }

    private static function removeQuotes($value) {
        if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
            (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
            return substr($value, 1, -1);
        }
        return $value;
    }

    private static function parseValue($value) {
        $lower = strtolower($value);
        
        if ($lower === 'true') return true;
        if ($lower === 'false') return false;
        if ($lower === 'null') return null;
        if ($lower === 'empty') return '';
        
        return $value;
    }

    public static function validateRequired(array $required) {
        $missing = [];
        
        foreach ($required as $key) {
            if (empty(self::get($key))) {
                $missing[] = $key;
            }
        }
        
        if (!empty($missing)) {
            throw new Exception(
                "Missing required environment variables: " . implode(', ', $missing)
            );
        }
    }
}

function env($key, $default = null) {
    return EnvLoader::get($key, $default);
}
?>
