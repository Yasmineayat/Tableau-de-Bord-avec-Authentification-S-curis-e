<?php
require_once 'db.php'; // Inclut le fichier 'db.php' pour accéder à la connexion à la base de données

class Utilisateur {
    private $conn; // Propriété pour stocker la connexion à la base de données
    private $table_name = "utilisateurs"; // Nom de la table dans la base de données qui contient les utilisateurs

    // Définition des propriétés pour stocker les informations d'un utilisateur
    public $id; 
    public $nom; 
    public $prenom; 
    public $email; 
    public $password; 
    public $role; 

    public function __construct($db) {
        $this->conn = $db; // Assigne la connexion à la base de données à la propriété $conn
    }

    // Fonction pour enregistrer un utilisateur
    public function register() {
        // Vérifier si l'email existe déjà dans la base de données
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email"; // Requête pour vérifier si l'email existe
        $stmt = $this->conn->prepare($query); // Prépare la requête
        $stmt->bindParam(':email', $this->email); // Lie la variable email à la requête
        $stmt->execute(); // Exécute la requête

        if ($stmt->rowCount() > 0) { // Si le nombre de lignes retournées est supérieur à 0, l'email existe déjà
            return "Email already exists"; // Retourne un message indiquant que l'email existe déjà
        }

        // Si l'email est unique, procéder à l'insertion de l'utilisateur dans la base de données
        $query = "INSERT INTO " . $this->table_name . " (nom, prenom, email, password, role) VALUES (:nom, :prenom, :email, :password, :role)";
        $stmt = $this->conn->prepare($query); // Prépare la requête d'insertion

        // Sécurisation du mot de passe en utilisant password_hash avec l'algorithme bcrypt
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);

        // Lier les variables aux paramètres de la requête
        $stmt->bindParam(':nom', $this->nom); 
        $stmt->bindParam(':prenom', $this->prenom); 
        $stmt->bindParam(':email', $this->email); 
        $stmt->bindParam(':password', $this->password); 
        $stmt->bindParam(':role', $this->role); 

        if ($stmt->execute()) { // Si la requête s'exécute avec succès, l'utilisateur est créé
            return true; // Retourne vrai pour indiquer que l'enregistrement a réussi
        }
        return false; // Retourne faux si l'exécution de la requête échoue
    }

    // Fonction pour connecter un utilisateur (login)
    public function login() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email"; // Requête pour récupérer un utilisateur par email
        $stmt = $this->conn->prepare($query); // Prépare la requête
        $stmt->bindParam(':email', $this->email); // Lie l'email à la requête
        $stmt->execute(); // Exécute la requête

        $row = $stmt->fetch(PDO::FETCH_ASSOC); // Récupère les données de l'utilisateur en tant que tableau associatif

        if ($row && password_verify($this->password, $row['password'])) { // Vérifie si l'email existe et si le mot de passe est valide
            // Si l'authentification réussit, assigne les valeurs de l'utilisateur aux propriétés de la classe
            $this->id = $row['id']; 
            $this->nom = $row['nom']; 
            $this->prenom = $row['prenom']; 
            $this->role = $row['role']; 
            return true; // Retourne vrai si la connexion est réussie
        }
        return false; // Retourne faux si l'authentification échoue
    }

    // Fonction pour récupérer un utilisateur par son email
    public function getUser($email) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email"; // Requête pour récupérer un utilisateur par email
        $stmt = $this->conn->prepare($query); // Prépare la requête
        $stmt->bindParam(':email', $email); // Lie l'email à la requête
        $stmt->execute(); // Exécute la requête

        $row = $stmt->fetch(PDO::FETCH_ASSOC); // Récupère l'utilisateur sous forme de tableau associatif
        if ($row) {
            return $row; // Si l'utilisateur existe, retourne les données de l'utilisateur
        }
        return null; // Retourne null si l'utilisateur n'existe pas
    }

    // Fonction pour récupérer tous les utilisateurs
    public function getUsers() {
        $query = "SELECT * FROM " . $this->table_name; // Requête pour récupérer tous les utilisateurs
        $stmt = $this->conn->prepare($query); // Prépare la requête
        $stmt->execute(); // Exécute la requête

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retourne un tableau de tous les utilisateurs sous forme de tableaux associatifs
    }
}
?>
