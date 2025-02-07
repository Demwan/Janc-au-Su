<?php
// DB connection (similar to index.php)
$uri = "";
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