<?php
    require_once "traitement.php";
    requireAuth();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: archives.php');
        exit;
    }

    if (!Controler::validateCsrfToken($_POST['csrf_token'] ?? null)) {
        header('Location: archives.php?error=csrf');
        exit;
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        header('Location: archives.php?error=invalid_id');
        exit;
    }

    $ctrl = new Controler();
    try {
        $ctrl->logHistorique($id, 'suppression', 'Supprimé par ' . getLoggedInUserEmail());
        $ctrl->deleteData($id);
    } catch (Exception $e) {
        error_log("Delete error: " . $e->getMessage());
    }

    $allowedRedirects = ['archives.php', 'liste-voyages.php'];
    $redirect = $_POST['redirect'] ?? 'archives.php';
    if (!in_array($redirect, $allowedRedirects, true)) {
        $redirect = 'archives.php';
    }
    header("Location: $redirect");
    exit;
