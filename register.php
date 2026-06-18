<?php
require_once 'database.php';
require_once 'auth_middleware.php';

if (isLoggedIn()) {
    header('Location: acceuil.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($email === '' || $password === '' || $confirm_password === '') {
        $error = 'Veuillez remplir tous les champs.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse email invalide.';
    } elseif (strlen($password) < 8) {
        $error = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif ($password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        try {
            $db = new Database();
            $conn = $db->getConnection();

            $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = :email LIMIT 1");
            $stmt->bindValue(':email', $email);
            $stmt->execute();

            if ($stmt->fetch()) {
                $error = 'Cet email est déjà utilisé.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $insert = $conn->prepare("INSERT INTO utilisateurs (email, mot_de_passe) VALUES (:email, :mot_de_passe)");
                $insert->bindValue(':email', $email);
                $insert->bindValue(':mot_de_passe', $hash);
                $insert->execute();
                $success = 'Compte créé avec succès. <a href="login.php">Connectez-vous</a>';
            }
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            $error = 'Une erreur est survenue. Veuillez réessayer plus tard.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte - Gestion de Voyages</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <div class="heading">Créer un compte</div>
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (!$success): ?>
        <form class="form" method="POST" action="">
            <div class="input-field">
                <input required autocomplete="email" type="email" name="email" id="email" />
                <label for="email">Email</label>
            </div>
            <div class="input-field">
                <input required autocomplete="new-password" type="password" name="password" id="password" />
                <label for="password">Mot de passe</label>
            </div>
            <div class="input-field">
                <input required autocomplete="new-password" type="password" name="confirm_password" id="confirm_password" />
                <label for="confirm_password">Confirmer le mot de passe</label>
            </div>
            <div class="btn-container">
                <button class="btn" type="submit">Créer le compte</button>
            </div>
        </form>
        <?php endif; ?>
        <div class="auth-link">
            <a href="login.php">Déjà un compte ? Connectez-vous</a>
        </div>
    </div>
</body>
</html>
