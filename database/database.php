<?php

$dns = 'mysql:host=localhost:3306;dbname=blog';
$user = 'root';
$pwd = '';

try {
    $pdo = new PDO($dns, $user, $pwd, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Besoin de voir les erreurs
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Tout ce qui est récupéré de la bdd je veux que tu en fasse un tableau associatif
    ]);
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}

return $pdo;