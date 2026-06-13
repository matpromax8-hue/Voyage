<?php
    require_once "./traitement.php";
    requireAuth();
    $historique = new Controler();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="historique.css">
    <title>Historique des Voyages</title>
</head>
<body>
    <div class="container">
        <h2>Historique des Voyages</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom du voyageur</th>
                    <th>Destination</th>
                    <th>Date de Départ</th>
                    <th>Date de Retour</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($historique->getAllData()->fetchAll() as $voyage) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($voyage['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($voyage['nom'] . " " . $voyage['prenom']) . "</td>";
                        echo "<td>" . htmlspecialchars($voyage['mission']) . "</td>";
                        echo "<td>" . htmlspecialchars($voyage['date_depart']) . "</td>";
                        echo "<td>" . htmlspecialchars($voyage['date_arrivee']) . "</td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
