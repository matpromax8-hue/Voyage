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

        public static function csrfHiddenField(): string {
            $token = self::generateCsrfToken();
            return '<input type="hidden" name="csrf_token" value="' . $token . '">';
        }

        public function validate(array $post){
            $errors = [];
            $nom = trim($post['nom'] ?? '');
            $prenom = trim($post['prenom'] ?? '');
            $mission = trim($post['mission'] ?? '');
            $objectif = trim($post['objectif'] ?? '');
            $pays_organisateur = trim($post['pays_organisateur'] ?? '');
            $responsable_financier = trim($post['responsable_financier'] ?? '');
            $date_depart = strtotime($post['date_depart'] ?? '');
            $date_arrivee = strtotime($post['date_arrivee'] ?? '');
            $today = strtotime(date("Y-m-d"));

            if (strlen($nom) < 3){
                $errors[] = "Le nom doit contenir au moins 3 caractères.";
            }
            if (strlen($nom) > 100){
                $errors[] = "Le nom ne doit pas dépasser 100 caractères.";
            }
            if (strlen($prenom) < 3){
                $errors[] = "Le prénom doit contenir au moins 3 caractères.";
            }
            if (strlen($prenom) > 100){
                $errors[] = "Le prénom ne doit pas dépasser 100 caractères.";
            }
            if (strlen($mission) < 3){
                $errors[] = "La mission doit contenir au moins 3 caractères.";
            }
            if (strlen($mission) > 500){
                $errors[] = "La mission ne doit pas dépasser 500 caractères.";
            }
            if (strlen($objectif) < 3){
                $errors[] = "L'objectif doit contenir au moins 3 caractères.";
            }
            if (strlen($objectif) > 500){
                $errors[] = "L'objectif ne doit pas dépasser 500 caractères.";
            }
            if (strlen($pays_organisateur) < 2){
                $errors[] = "Le pays organisateur doit contenir au moins 2 caractères.";
            }
            if (strlen($pays_organisateur) > 100){
                $errors[] = "Le pays organisateur ne doit pas dépasser 100 caractères.";
            }
            if (strlen($responsable_financier) < 3){
                $errors[] = "Le responsable financier doit contenir au moins 3 caractères.";
            }
            if (strlen($responsable_financier) > 100){
                $errors[] = "Le responsable financier ne doit pas dépasser 100 caractères.";
            }
            if ($date_depart === false) {
                $errors[] = "La date de départ est invalide.";
            }
            if ($date_arrivee === false) {
                $errors[] = "La date d'arrivée est invalide.";
            }
            if ($date_depart !== false && $date_arrivee !== false && $date_depart > $date_arrivee) {
                $errors[] = "La date de départ doit être antérieure à la date d'arrivée.";
            }
            if ($date_depart !== false && $date_depart < $today) {
                $errors[] = "La date de départ doit être supérieure ou égale à la date actuelle.";
            }
            if ($date_arrivee !== false && $date_arrivee < $today) {
                $errors[] = "La date d'arrivée doit être supérieure ou égale à la date actuelle.";
            }
            return $errors;
        }
    }
