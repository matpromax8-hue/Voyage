<?php require_once "auth_middleware.php"; requireAuth(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Voyages</title>
    <link rel="stylesheet" href="acceuil.css">
</head>
<body>
    <div class="header-bar">
        <span></span>
        <a class="logout-link" href="logout.php">Déconnexion</a>
    </div>
    <img src="anavev.jpeg" alt="Logo" class="logo" height="100px" width="180px">
    <h2>Bienvenue à la Gestion de Voyages</h2>
    <div class="button-container">
        <button class="btn">
            <span><a href="ajouter-voyage.php">Ajouter un Voyage</a></span>
        </button>

        <button class="btn">
            <span><a href="liste-voyages.php">Liste des Voyages</a></span>
        </button>

        <button class="btn">
            <span><a href="archives.php">Archiver un Voyage</a></span>
        </button>

        <button class="btn">
            <span><a href="historique.php">Voir l'historique</a></span>
        </button>
    </div>
</body>
</html>
