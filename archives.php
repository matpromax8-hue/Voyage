<?php require_once "./traitement.php";
requireAuth();
$controler = new Controler();
$voyages = $controler->getDataByStatus('archive')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des voyages | Archives</title>
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
        .nav-link {
            color: #3d7bfd;
            text-decoration: none;
            font-size: 0.95rem;
        }
        .nav-link:hover {
            text-decoration: underline;
        }
        .logout-link {
            color: #d32f2f;
            text-decoration: none;
            font-size: 0.95rem;
        }
        .logout-link:hover {
            text-decoration: underline;
        }
        .empty-msg {
            text-align: center;
            color: #8899aa;
            padding: 40px 0;
            font-size: 1.1rem;
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
        .action-btn.unarchive {
            background: #3d7bfd;
        }
        .action-btn.unarchive:hover {
            background: #2e63d7;
        }
        .action-btn.delete {
            background: #d32f2f;
        }
        .action-btn.delete:hover {
            background: #b71c1c;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-bar">
            <div>
                <h2>Voyages archivés</h2>
                <a class="nav-link" href="acceuil.php">&larr; Retour à l'accueil</a>
            </div>
            <a class="logout-link" href="logout.php">Déconnexion</a>
        </div>

        <?php if (empty($voyages)): ?>
            <div class="empty-msg">Aucun voyage archivé pour le moment.</div>
        <?php else: ?>
        <table>
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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($voyages as $voyage): ?>
                    <tr>
                        <td><?= htmlspecialchars($voyage['nom']) ?></td>
                        <td><?= htmlspecialchars($voyage['prenom']) ?></td>
                        <td><?= htmlspecialchars($voyage['mission']) ?></td>
                        <td><?= htmlspecialchars($voyage['objectif']) ?></td>
                        <td><?= htmlspecialchars($voyage['pays_organisateur']) ?></td>
                        <td><?= htmlspecialchars($voyage['date_depart']) ?></td>
                        <td><?= htmlspecialchars($voyage['date_arrivee']) ?></td>
                        <td><?= htmlspecialchars($voyage['responsable_financier']) ?></td>
                        <td class="actions">
                            <form method="POST" action="archiver-voyage.php" style="display:inline">
                                <?= Controler::csrfHiddenField() ?>
                                <input type="hidden" name="id" value="<?= (int)$voyage['id'] ?>">
                                <input type="hidden" name="action" value="unarchive">
                                <input type="hidden" name="redirect" value="archives.php">
                                <button type="submit" class="action-btn unarchive">Désarchiver</button>
                            </form>
                            <form method="POST" action="supprimer-voyage.php" style="display:inline" onsubmit="return confirm('Supprimer définitivement ce voyage ?');">
                                <?= Controler::csrfHiddenField() ?>
                                <input type="hidden" name="id" value="<?= (int)$voyage['id'] ?>">
                                <input type="hidden" name="redirect" value="archives.php">
                                <button type="submit" class="action-btn delete">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</body>
</html>
