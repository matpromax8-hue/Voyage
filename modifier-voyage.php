<?php
    require_once "./traitement.php";
    requireAuth();

    $errors = [];
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id <= 0) {
        header('Location: liste-voyages.php?error=invalid_id');
        exit;
    }

    $controller = new Controler();
    $voyage = $controller->getOneData($id)->fetch(PDO::FETCH_ASSOC);

    if (!$voyage) {
        header('Location: liste-voyages.php?error=not_found');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!Controler::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $errors[] = "Token de sécurité invalide. Veuillez réessayer.";
        } else {
            $errors = $controller->validateUpdate($_POST);

            if (empty($errors)) {
                try {
                    $data = [
                        'nom' => $_POST['nom'],
                        'prenom' => $_POST['prenom'],
                        'mission' => $_POST['mission'],
                        'objectif' => $_POST['objectif'],
                        'pays_organisateur' => $_POST['pays_organisateur'],
                        'date_depart' => Controler::formatDate($_POST['date_depart']),
                        'date_arrivee' => Controler::formatDate($_POST['date_arrivee']),
                        'responsable_financier' => $_POST['responsable_financier']
                    ];
                    $controller->updateData($id, $data);
                    $controller->logHistorique($id, 'modification', 'Modifié par ' . getLoggedInUserEmail());
                    header("Location: liste-voyages.php");
                    exit();
                } catch (Exception $e) {
                    error_log("Update voyage error: " . $e->getMessage());
                    $errors[] = "Une erreur est survenue lors de la modification.";
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un voyage</title>
    <link rel="stylesheet" href="ajouter-voyage.css">
</head>
<body>
    <div class="container">
        <div class="heading">Modifier un voyage</div>

        <?php if (!empty($errors)): ?>
            <div class="error-messages" style="color:#d32f2f;background:#fce4ec;padding:10px;border-radius:8px;margin-bottom:15px;">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form class="form" action="" method="POST">
            <?php echo Controler::csrfHiddenField(); ?>

            <div class="input-field">
                <input required autocomplete="off" type="text" name="nom" id="nom" value="<?php echo htmlspecialchars($voyage['nom'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"/>
                <label for="nom">Nom</label>
            </div>

            <div class="input-field">
                <input required autocomplete="off" type="text" name="prenom" id="prenom" value="<?php echo htmlspecialchars($voyage['prenom'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"/>
                <label for="prenom">Prénom</label>
            </div>

            <div class="input-field">
                <input required autocomplete="off" type="text" name="mission" id="mission" value="<?php echo htmlspecialchars($voyage['mission'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"/>
                <label for="mission">Mission</label>
            </div>

            <div class="input-field">
                <input required autocomplete="off" type="text" name="objectif" id="objectif" value="<?php echo htmlspecialchars($voyage['objectif'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"/>
                <label for="objectif">Objectif</label>
            </div>

            <div class="input-field">
                <input required autocomplete="off" type="text" name="pays_organisateur" id="pays_organisateur" value="<?php echo htmlspecialchars($voyage['pays_organisateur'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"/>
                <label for="pays_organisateur">Pays Organisateur</label>
            </div>

            <div class="input-field">
                <input required autocomplete="off" type="date" name="date_depart" id="date_depart" value="<?php echo htmlspecialchars($voyage['date_depart'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"/>
                <label for="date_depart">Date de départ</label>
            </div>

            <div class="input-field">
                <input required autocomplete="off" type="date" name="date_arrivee" id="date_arrivee" value="<?php echo htmlspecialchars($voyage['date_arrivee'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"/>
                <label for="date_arrivee">Date d'arrivée</label>
            </div>

            <div class="input-field">
                <input required autocomplete="off" type="text" name="responsable_financier" id="responsable_financier" value="<?php echo htmlspecialchars($voyage['responsable_financier'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"/>
                <label for="responsable_financier">Responsable financier</label>
            </div>

            <div class="btn-container">
                <button class="btn" type="submit">Enregistrer</button>
            </div>
        </form>
    </div>
</body>
</html>
