<?php
// Code de connexion à la base de données avec PDO
try {
    $dsn = 'mysql:host=localhost;dbname=nva+;charset=utf8mb4';
    $username = 'root';
    $password = '';

    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Gestion des erreurs
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Mode de fetch par défaut
        PDO::ATTR_EMULATE_PREPARES   => false                   // Désactiver l'émulation des requêtes préparées
    ]);

    // Message de confirmation uniquement
   // echo "<p style='color:green;'>✅ Connexion réussie à la base de données.</p>";

} catch (PDOException $e) {
    // Gestion d’erreur
    echo "<p style='color:red;'>❌ ERREUR : Connexion échouée. " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>
