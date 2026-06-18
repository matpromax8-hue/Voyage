<?php
    require_once 'database.php';

    class Model {
        private const ALLOWED_TABLES = ['missions', 'historique', 'utilisateurs'];
        private const ALLOWED_COLUMNS = [
            'missions' => ['id', 'nom', 'prenom', 'mission', 'objectif', 'pays_organisateur', 'date_depart', 'date_arrivee', 'responsable_financier', 'statut', 'created_at'],
            'historique' => ['id', 'mission_id', 'action', 'details', 'date_action'],
            'utilisateurs' => ['id', 'email', 'mot_de_passe', 'created_at'],
        ];
        private string $table_name;
        private \PDO $conn;

        public function __construct(){
            $database = new Database();
            $this->conn = $database->getConnection();
        }

        private function validateTable(string $table_name): void {
            if (!in_array($table_name, self::ALLOWED_TABLES, true)) {
                throw new \InvalidArgumentException("Table non autorisée : $table_name");
            }
        }

        private function validateColumns(string $table_name, array $columns): void {
            $allowed = self::ALLOWED_COLUMNS[$table_name] ?? [];
            foreach ($columns as $col) {
                if (!in_array($col, $allowed, true)) {
                    throw new \InvalidArgumentException("Colonne non autorisée : $col");
                }
            }
        }

        private function quoteColumn(string $column): string {
            return "`$column`";
        }

        public function lireTout(string $table_name) {
            $this->validateTable($table_name);
            $query = "SELECT * FROM `$table_name`";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        public function lireParStatut(string $table_name, string $statut) {
            $this->validateTable($table_name);
            $query = "SELECT * FROM `$table_name` WHERE statut = :statut";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":statut", $statut);
            $stmt->execute();
            return $stmt;
        }

        public function lireUn(int $id, string $table_name){
            $this->validateTable($table_name);
            $query = "SELECT * FROM `$table_name` WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt;
        }

        public function insererDansTable(array $data, string $table_name){
            $this->validateTable($table_name);
            $columns = array_map(fn($column) => ltrim($column, ':'), array_keys($data));
            $this->validateColumns($table_name, $columns);
            $quotedCols = implode(", ", array_map(fn($col) => $this->quoteColumn($col), $columns));
            $placeholders = implode(", ", array_map(fn($column) => ":$column", $columns));

            $query = "INSERT INTO `$table_name` ($quotedCols) VALUES ($placeholders)";
            $stmt = $this->conn->prepare($query);

            foreach ($columns as $column) {
                $value = $data[$column] ?? $data[":$column"] ?? null;
                $stmt->bindValue(":$column", $value);
            }

            if (!$stmt->execute()) {
                throw new \RuntimeException("Échec de l'insertion dans la table $table_name");
            }
            return (int)$this->conn->lastInsertId();
        }

        public function modifierUn(int $id, array $data, string $table_name){
            $this->validateTable($table_name);
            $columns = array_map(fn($column) => ltrim($column, ':'), array_keys($data));
            $this->validateColumns($table_name, $columns);
            $set_clauses = [];
            foreach ($columns as $column) {
                $set_clauses[] = $this->quoteColumn($column) . " = :$column";
            }
            $set_sql = implode(", ", $set_clauses);

            $query = "UPDATE `$table_name` SET $set_sql WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            foreach ($columns as $column) {
                $value = $data[$column] ?? $data[":$column"] ?? null;
                $stmt->bindValue(":$column", $value);
            }
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                throw new \RuntimeException("Échec de la modification dans la table $table_name");
            }
            return $stmt;
        }

        public function supprimerUn(int $id, string $table_name){
            $this->validateTable($table_name);
            $query = "DELETE FROM `$table_name` WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            if (!$stmt->execute()) {
                throw new \RuntimeException("Échec de la suppression dans la table $table_name");
            }
            return $stmt;
        }
    }
