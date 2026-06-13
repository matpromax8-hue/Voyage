<?php require_once "./traitement.php";
requireAuth();
$controler = new Controler();
$voyages = $controler->getAllData()->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des voyages | Liste des voyages</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f6fb;
            color: #2f3b4a;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            padding: 24px;
        }
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        h2 {
            margin: 0;
            color: #1f364b;
        }
        .logout-link {
            color: #d32f2f;
            text-decoration: none;
            font-size: 0.95rem;
        }
        .logout-link:hover {
            text-decoration: underline;
        }
        .table-responsive {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 860px;
        }
        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #e4e9f2;
        }
        thead th {
            background: #eef4fb;
            color: #1f2f45;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        tbody tr:hover {
            background: #f7fbff;
        }
        td:nth-child(9) {
            text-transform: capitalize;
        }
        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .action-btn {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.95rem;
            color: #ffffff;
            transition: background 0.2s ease;
            border: none;
            cursor: pointer;
        }
        .action-btn.edit {
            background: #3d7bfd;
        }
        .action-btn.edit:hover {
            background: #2e63d7;
        }
        .action-btn.archive {
            background: #ff8f3f;
        }
        .action-btn.archive:hover {
            background: #e37717;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-bar">
            <h2>Liste des voyages</h2>
            <a class="logout-link" href="logout.php">Déconnexion</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Mission</th>
                        <th>Objectif</th>
                        <th>Pays Organisateur</th>
                        <th>Date de départ</th>
                        <th>Date d'arrivée</th>
                        <th>Responsable financier</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach($voyages as $voyage){
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($voyage['nom']) . "</td>";
                            echo "<td>" . htmlspecialchars($voyage['prenom']) . "</td>";
                            echo "<td>" . htmlspecialchars($voyage['mission']) . "</td>";
                            echo "<td>" . htmlspecialchars($voyage['objectif']) . "</td>";
                            echo "<td>" . htmlspecialchars($voyage['pays_organisateur']) . "</td>";
                            echo "<td>" . htmlspecialchars($voyage['date_depart']) . "</td>";
                            echo "<td>" . htmlspecialchars($voyage['date_arrivee']) . "</td>";
                            echo "<td>" . htmlspecialchars($voyage['responsable_financier']) . "</td>";
                            echo "<td>" . htmlspecialchars($voyage['statut']) . "</td>";
                            $isArchived = strtolower(trim($voyage['statut'])) === 'archive';
                            $btnLabel = $isArchived ? 'Désarchiver' : 'Archiver';
                            $action = $isArchived ? 'unarchive' : 'archive';
                            echo "<td class='actions'>";
                            echo "<a class='action-btn edit' href='modifier-voyage.php?id=" . (int)$voyage['id'] . "'>Modifier</a> ";
                            echo "<form method='POST' action='archiver-voyage.php' style='display:inline'>";
                            echo Controler::csrfHiddenField();
                            echo "<input type='hidden' name='id' value='" . (int)$voyage['id'] . "'>";
                            echo "<input type='hidden' name='action' value='" . $action . "'>";
                            echo "<button type='submit' class='action-btn archive'>" . $btnLabel . "</button>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
