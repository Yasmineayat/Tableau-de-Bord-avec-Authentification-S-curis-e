<?php
require_once 'db.php';  // Inclut le fichier db.php pour établir la connexion à la base de données

// Définition de la classe Tache
class Tache {
    private $conn;  // Propriété pour stocker la connexion à la base de données
    private $table_name = "taches";  // Le nom de la table associée à cette classe

    // Propriétés qui correspondent aux colonnes de la table 'taches'
    public $id;
    public $nom;
    public $description;
    public $date_time;
    public $id_utilisateur;

    // Constructeur de la classe, qui prend une connexion à la base de données comme paramètre
    public function __construct($db) {
        $this->conn = $db;  // Affecte la connexion à la base de données à la propriété $conn
    }

    // Méthode pour créer une nouvelle tâche dans la base de données
    public function create()
    {
        $query = "INSERT INTO taches (nom, description, date_time, id_utilisateur) VALUES (:nom, :description, :date_time, :id_utilisateur)";
        $stmt = $this->conn->prepare($query);
    
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':date_time', $this->date_time);
        $stmt->bindParam(':id_utilisateur', $this->id_utilisateur);
        
        var_dump($this->date_time); // This will output the value of date_time

        return $stmt->execute();
    }
    

    // Méthode pour lire toutes les tâches de la base de données
    public function read() {
        // Déclare la requête SQL pour sélectionner toutes les colonnes de la table 'taches'
        $query = "SELECT * FROM " . $this->table_name;
        // Prépare la requête SQL pour l'exécution
        $stmt = $this->conn->prepare($query);
        // Exécute la requête
        $stmt->execute();
        // Retourne le résultat de la requête (un objet PDOStatement)
        return $stmt;
    }

    // Méthode pour mettre à jour une tâche existante dans la base de données
    public function update() {
        // Déclare la requête SQL pour mettre à jour une tâche en fonction de son ID
        $query = "UPDATE " . $this->table_name . " SET nom = :nom, description = :description, id_utilisateur = :id_utilisateur WHERE id = :id";
        // Prépare la requête SQL pour l'exécution
        $stmt = $this->conn->prepare($query);

        // Lie les paramètres de la requête aux propriétés de l'objet
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':id_utilisateur', $this->id_utilisateur);

        // Exécute la requête et retourne true si la mise à jour a réussi, sinon false
        if ($stmt->execute()) {
            return true;  // La mise à jour a réussi
        }
        return false;  // La mise à jour a échoué
    }

    // Méthode pour supprimer une tâche de la base de données
    public function delete() {
        // Déclare la requête SQL pour supprimer une tâche en fonction de son ID
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        // Prépare la requête SQL pour l'exécution
        $stmt = $this->conn->prepare($query);

        // Lie le paramètre de la requête à l'ID de la tâche
        $stmt->bindParam(':id', $this->id);

        // Exécute la requête et retourne true si la suppression a réussi, sinon false
        if ($stmt->execute()) {
            return true;  // La suppression a réussi
        }
        return false;  // La suppression a échoué
    }
}
?>
