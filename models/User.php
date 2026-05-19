<?php

class User {

    private $conn;
    private $table = "users";

    // Properties
    public $id;
    public $name;
    public $email;
    public $password;
    public $role;

    // Constructor (PDO connection)
    public function __construct($db) {
        $this->conn = $db;
    }

   //  REGISTER USER
public function register() {

    $query = "INSERT INTO " . $this->table . "
              (name, email, password, role)
              VALUES (:name, :email, :password, :role)";

    $stmt = $this->conn->prepare($query);
    
    //cleans the input before storing
    $this->name  = htmlspecialchars(strip_tags($this->name));
    $this->email = htmlspecialchars(strip_tags($this->email));



    $stmt->bindParam(":name",     $this->name);
    $stmt->bindParam(":email",    $this->email);
     $stmt->bindParam(":password", $this->password);
    $stmt->bindParam(":role",     $this->role);

    try {
        return $stmt->execute();
    } catch (PDOException $e) {
        // Duplicate email (error code 23000)
        if ($e->getCode() == 23000) {
            return false; // controller will redirect with error=email_taken
        }
        throw $e; // re-throw anything unexpected
    }
}

    //  LOGIN USER
    public function login() {

        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

       if ($user && $this->password === $user['password']) {

            unset($user['password']); // security

            return $user;
        }

        return false;
    }

    //  GET USER BY ID
    public function getUserById() {

        $query = "SELECT id, name, email, role 
                  FROM " . $this->table . " 
                  WHERE id = :id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}