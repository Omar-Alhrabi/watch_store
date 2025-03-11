<?php
    require_once "config/database.php"; 

    class Contact {
        private $conn;
        private $table = "contact_messages";

        public function __construct() {
            $database = new Database();
            $this->conn = $database->getConnection();
        }

        public function getAllContacts() {
            $query = "SELECT * FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getContactById($id) {
            $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
?>
