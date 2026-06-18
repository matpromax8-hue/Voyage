<?php
    require_once "./crudiction.php";
    require_once "auth_middleware.php";

    class Controler {
        private $model;

        public function __construct() {
            $this->model = new Model();
        }

        public function insertData(array $data){
            return $this->model->insererDansTable($data, "missions");
        }

        public function getAllData(){
            return $this->model->lireTout("missions");
        }

        public function getDataByStatus(string $statut){
            return $this->model->lireParStatut("missions", $statut);
        }

        public function getOneData(int $id){
            return $this->model->lireUn($id, "missions");
        }

        public function updateData(int $id, array $data){
            return $this->model->modifierUn($id, $data, "missions");
        }

        public function deleteData(int $id){
            return $this->model->supprimerUn($id, "missions");
        }

        public static function generateCsrfToken(): string {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            return $_SESSION['csrf_token'];
        }

        public static function validateCsrfToken(?string $token): bool {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (empty($_SESSION['csrf_token']) || empty($token)) {
                return false;
            }
            return hash_equals($_SESSION['csrf_token'], $token);
        }

        public static function formatDate(string $date): string {
            $timestamp = strtotime($date);
            if ($timestamp === false) {
                throw new \InvalidArgumentException("Format de date invalide : $date");
            }
            return date("Y-m-d", $timestamp);
        }

        public static function csrfHiddenField(): string {
            $token = self::generateCsrfToken();
            return '<input type="hidden" name="csrf_token" value="' . $token . '">';
        }

        private function validateCommon(array $post): array {
            $errors = [];
            $fields = [
                'nom' => [3, 100, 'Le nom doit contenir entre 3 et 100 caractères.'],
                'prenom' => [3, 100, 'Le prénom doit contenir entre 3 et 100 caractères.'],
                'mission' => [3, 500, 'La mission doit contenir entre 3 et 500 caractères.'],
                'objectif' => [3, 500, 'L\'objectif doit contenir entre 3 et 500 caractères.'],
                'responsable_financier' => [3, 100, 'Le responsable financier doit contenir entre 3 et 100 caractères.'],
            ];
            foreach ($fields as $field => [$min, $max, $msg]) {
                $val = trim($post[$field] ?? '');
                if (strlen($val) < $min || strlen($val) > $max) {
                    $errors[] = $msg;
                }
            }
            $pays = trim($post['pays_organisateur'] ?? '');
            if (strlen($pays) < 2 || strlen($pays) > 100) {
                $errors[] = 'Le pays organisateur doit contenir entre 2 et 100 caractères.';
            }
            $date_depart = strtotime($post['date_depart'] ?? '');
            $date_arrivee = strtotime($post['date_arrivee'] ?? '');
            if ($date_depart === false) {
                $errors[] = 'La date de départ est invalide.';
            }
            if ($date_arrivee === false) {
                $errors[] = "La date d'arrivée est invalide.";
            }
            if ($date_depart !== false && $date_arrivee !== false && $date_depart > $date_arrivee) {
                $errors[] = 'La date de départ doit être antérieure à la date d\'arrivée.';
            }
            return $errors;
        }

        public function validateCreate(array $post): array {
            $errors = $this->validateCommon($post);
            $today = strtotime(date("Y-m-d"));
            $date_depart = strtotime($post['date_depart'] ?? '');
            $date_arrivee = strtotime($post['date_arrivee'] ?? '');
            if ($date_depart !== false && $date_depart < $today) {
                $errors[] = 'La date de départ doit être supérieure ou égale à la date actuelle.';
            }
            if ($date_arrivee !== false && $date_arrivee < $today) {
                $errors[] = "La date d'arrivée doit être supérieure ou égale à la date actuelle.";
            }
            return $errors;
        }

        public function validateUpdate(array $post): array {
            return $this->validateCommon($post);
        }

        public function logHistorique(?int $mission_id, string $action, string $details = ''): void {
            $this->model->insererDansTable([
                'mission_id' => $mission_id,
                'action' => $action,
                'details' => $details,
            ], 'historique');
        }
    }
