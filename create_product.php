<?php
// Foutmeldingen inschakelen
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to database
require_once 'db_connect.php'; // $conn is a PDO instance

// Controleer of het formulier is ingediend
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verkrijg formuliergegevens
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image_url = $_POST['image_url'];

    // SQL-query om het product in te voegen
    $query = "INSERT INTO products (name, description, price, image_url) 
              VALUES (:name, :description, :price, :image_url)";
    $stmt = $conn->prepare($query);
    $result = $stmt->execute([
        ':name' => $name, 
        ':description' => $description, 
        ':price' => $price, 
        ':image_url' => $image_url
    ]);

    if ($result) {
        echo "Product added successfully!";
        // Redirect naar de productlijst of hoofdpagina na succes
        header("Location: products.html");
        exit(); // Zorg ervoor dat je exit() aanroept na de header redirect
    } else {
        $errorInfo = $stmt->errorInfo();
        echo "Error: " . $errorInfo[2];
    }
}

$conn = null;
?>