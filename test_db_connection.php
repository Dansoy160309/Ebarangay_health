<?php
$host = '127.0.0.1';
$port = 3307;
$user = 'root';
$pass = '';

echo "Testing connection to $host:$port...\n";
try {
    $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass);
    echo "Success! Connected to MySQL via 127.0.0.1:$port.\n";
} catch (PDOException $e) {
    echo "Error (127.0.0.1): " . $e->getMessage() . "\n";
}

echo "\nTesting connection to localhost:$port...\n";
try {
    $pdo = new PDO("mysql:host=localhost;port=$port", $user, $pass);
    echo "Success! Connected to MySQL via localhost:$port.\n";
} catch (PDOException $e) {
    echo "Error (localhost): " . $e->getMessage() . "\n";
}
