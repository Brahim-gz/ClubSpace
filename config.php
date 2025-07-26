<?php
// Configuration de la base de données
$host = 'localhost';
$dbname = 'clubspace';
$username = 'root';
$password = '';

try {
    // Création de la connexion PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Configuration des options PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
    // En cas d'erreur, afficher un message et arrêter l'exécution
    die("Erreur de connexion : " . $e->getMessage());
}
?> 