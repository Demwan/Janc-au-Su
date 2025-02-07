<?php
// Add a simple function to load environment variables from a .env file.
function loadEnv($file) {
    if (!file_exists($file)) {
        die('.env file not found. Please create one at ' . $file);
    }
    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode("=", $line, 2);
        putenv(sprintf("%s=%s", trim($key), trim($value, " \t\n\r\0\x0B\"")));
    }
}

loadEnv(__DIR__ . '/.env');

$uri = getenv('DB_URI');
if (!$uri) {
    die('DB_URI not set in .env file.');
}

$fields = parse_url($uri);

// Build the DSN string instead of $conn
$dsn = "mysql:";
$dsn .= "host=" . $fields["host"];
$dsn .= ";port=" . $fields["port"];
$dsn .= ";dbname=defaultdb";
$dsn .= ";sslmode=verify-ca;sslrootcert=ca.pem";

try {
    $conn = new PDO($dsn, $fields["user"], $fields["pass"]);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

?>