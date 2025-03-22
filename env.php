<?php

function loadEnv($filePath)
{
    if (!file_exists($filePath)) {
        throw new Exception(".env file not found.");
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Ignore comments

        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'"); // Remove spaces and quotes

        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

// Load .env file
loadEnv(__DIR__ . '/.env');
