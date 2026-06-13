<?php
require_once 'database.php';

$email = 'admin@voyage.com';
$plainPassword = 'Admin123!';

$hash = password_hash($plainPassword, PASSWORD_BCRYPT);

try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = :email LIMIT 1");
    $stmt->bindValue(':email', $email);
    $stmt->execute();

    if ($stmt->fetch()) {
        echo "L'utilisateur $email existe déjà.\n";
    } else {
        $insert = $conn->prepare("INSERT INTO utilisateurs (email, mot_de_passe) VALUES (:email, :mot_de_passe)");
        $insert->bindValue(':email', $email);
        $insert->bindValue(':mot_de_passe', $hash);
        $insert->execute();
        echo "Utilisateur $email créé avec le mot de passe : $plainPassword\n";
        echo "⚠️  Supprime ou protège ce fichier après utilisation !\n";
    }
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
