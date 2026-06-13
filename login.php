<?php
require_once 'database.php';
require_once 'auth_middleware.php';

if (isLoggedIn()) {
    header('Location: acceuil.php');
    exit;
}

$error = '';
$expired = isset($_GET['expired']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("SELECT id, email, mot_de_passe FROM utilisateurs WHERE email = :email LIMIT 1");
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['mot_de_passe'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['last_activity'] = time();
                header('Location: acceuil.php');
                exit;
            }
            $error = 'Email ou mot de passe incorrect.';
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
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
    <title>Connexion - Gestion de Voyages</title>
    <link rel="stylesheet" href="login.css">
    <link rel="shortcut icon" href="anavev.jpeg" type="image/x-icon">
</head>
<body>
    <div class="container">
        <img src="anavev.jpeg" alt="Logo" class="logo" height="80px" width="180px">
        <div class="heading">Connexion</div>
        <?php if ($expired): ?>
            <div class="error-message">Session expirée. Veuillez vous reconnecter.</div>
        <?php elseif ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <form class="form" method="POST" action="">
            <div class="input-field">
                <input required autocomplete="email" type="email" name="email" id="email" />
                <label for="email">Email</label>
            </div>
            <div class="input-field">
                <input required autocomplete="current-password" type="password" name="password" id="password" />
                <label for="password">Mot de passe</label>
            </div>
            <div class="btn-container">
                <button class="btn" type="submit">Se connecter</button>
            </div>
        </form>
        <div class="auth-link">
            <a href="register.php">Créer un compte</a>
        </div>
    </div>
</body>
</html>
