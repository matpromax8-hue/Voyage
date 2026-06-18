<?php
require_once "./traitement.php";
requireAuth();

$errors = [];
$formData = [
    'nom' => '',
    'prenom' => '',
    'mission' => '',
    'objectif' => '',
    'pays_organisateur' => '',
    'date_depart' => '',
    'date_arrivee' => '',
    'responsable_financier' => '',
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!Controler::validateCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = "Token de sécurité invalide. Veuillez réessayer.";
    } else {
        $formData = array_merge($formData, array_intersect_key($_POST, $formData));

        $controller = new Controler();
        $errors = $controller->validateCreate($_POST);

        if (empty($errors)) {
            try {
                $data = [
                    "nom" => $_POST["nom"],
                    "prenom" => $_POST["prenom"],
                    "mission" => $_POST["mission"],
                    "objectif" => $_POST["objectif"],
                    "pays_organisateur" => $_POST["pays_organisateur"],
                    "date_depart" => Controler::formatDate($_POST["date_depart"]),
                    "date_arrivee" => Controler::formatDate($_POST["date_arrivee"]),
                    "responsable_financier" => $_POST["responsable_financier"],
                    "statut" => "actif"
                ];

                $newId = $controller->insertData($data);
                $controller->logHistorique($newId, 'creation', 'Voyage créé par ' . getLoggedInUserEmail());
                header("Location: liste-voyages.php");
                exit;
            } catch (Exception $e) {
                error_log("Insert voyage error: " . $e->getMessage());
                $errors[] = "Une erreur est survenue lors de la création du voyage.";
            }
        }
    }
}

function escape(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un voyage</title>
    <link rel="stylesheet" href="ajouter-voyage.css">
</head>
<body>
    <div class="container">
        <div class="heading">Créer un voyage</div>

        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo escape($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form class="form" action="" method="POST">
            <?php echo Controler::csrfHiddenField(); ?>

            <div class="input-field">
                <input required autocomplete="off" type="text" name="nom" id="nom" value="<?php echo escape($formData['nom']); ?>" />
                <label for="nom">Nom</label>
            </div>

            <div class="input-field">
                <input required autocomplete="off" type="text" name="prenom" id="prenom" value="<?php echo escape($formData['prenom']); ?>" />
                <label for="prenom">Prénom</label>
            </div>

            <div class="input-field">
                <input required autocomplete="off" type="text" name="mission" id="mission" value="<?php echo escape($formData['mission']); ?>" />
                <label for="mission">Mission</label>
            </div>

            <div class="input-field">
                <input required autocomplete="off" type="text" name="objectif" id="objectif" value="<?php echo escape($formData['objectif']); ?>" />
                <label for="objectif">Objectif</label>
            </div>

            <div class="input-field">
                <input required autocomplete="off" type="text" name="pays_organisateur" id="pays_organisateur" value="<?php echo escape($formData['pays_organisateur']); ?>" />
                <label for="pays_organisateur">Pays Organisateur</label>
            </div>

            <div class="input-field">
                <input required autocomplete="off" type="date" name="date_depart" id="date_depart" value="<?php echo escape($formData['date_depart']); ?>" />
                <label for="date_depart">Date de départ</label>
            </div>

            <div class="input-field">
                <input required autocomplete="off" type="date" name="date_arrivee" id="date_arrivee" value="<?php echo escape($formData['date_arrivee']); ?>" />
                <label for="date_arrivee">Date d'arrivée</label>
            </div>

            <div class="input-field">
                <input required autocomplete="off" type="text" name="responsable_financier" id="responsable_financier" value="<?php echo escape($formData['responsable_financier']); ?>" />
                <label for="responsable_financier">Responsable financier</label>
            </div>

            <div class="btn-container">
                <button class="btn" type="submit">Soumettre</button>
            </div>
        </form>
    </div>
</body>
</html>
