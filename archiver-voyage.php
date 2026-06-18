<?php
    require_once "traitement.php";
    requireAuth();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: liste-voyages.php');
        exit;
    }

    if (!Controler::validateCsrfToken($_POST['csrf_token'] ?? null)) {
        header('Location: liste-voyages.php?error=csrf');
        exit;
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        header('Location: liste-voyages.php?error=invalid_id');
        exit;
    }

    $action = $_POST['action'] ?? 'archive';
    $newStatus = ($action === 'unarchive') ? 'actif' : 'archive';

    $ctrl = new Controler();
    try {
        $ctrl->updateData($id, ["statut" => $newStatus]);
        $actionLabel = ($newStatus === 'archive') ? 'archivage' : 'désarchivage';
        $ctrl->logHistorique($id, $actionLabel, 'Statut changé vers "' . $newStatus . '" par ' . getLoggedInUserEmail());
    } catch (Exception $e) {
        error_log("Archive error: " . $e->getMessage());
    }

    $allowedRedirects = ['liste-voyages.php', 'archives.php'];
    $redirect = $_POST['redirect'] ?? 'liste-voyages.php';
    if (!in_array($redirect, $allowedRedirects, true)) {
        $redirect = 'liste-voyages.php';
    }
    header("Location: $redirect");
    exit;
