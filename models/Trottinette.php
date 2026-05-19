<?php

class Trottinette {

    private $conn;
    private $table = "trottinettes";

    // Properties
    public $id;
    public $name;
    public $brand;
    public $autonomy;
    public $price_per_hour;
    public $available;
    public $image;
    public $description;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    //  CREATE
    public function create() {

        $query = "INSERT INTO " . $this->table . "
        (name, brand, autonomy, price_per_hour, image, description)
        VALUES (:name, :brand, :autonomy, :price_per_hour, :image, :description)";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->brand = htmlspecialchars(strip_tags($this->brand));
        $this->description = htmlspecialchars(strip_tags($this->description));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":brand", $this->brand);
        $stmt->bindParam(":autonomy", $this->autonomy);
        $stmt->bindParam(":price_per_hour", $this->price_per_hour);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":description", $this->description);

        return $stmt->execute();
    }

    //  READ ALL
    public function readAll() {

        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //  READ ONE
    public function readOne() {

        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE
    public function update() {

        $query = "UPDATE " . $this->table . "
        SET name = :name,
            brand = :brand,
            autonomy = :autonomy,
            price_per_hour = :price_per_hour,
            description = :description,
            image = :image
        WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":brand", $this->brand);
        $stmt->bindParam(":autonomy", $this->autonomy);
        $stmt->bindParam(":price_per_hour", $this->price_per_hour);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":image", $this->image);

        return $stmt->execute();
    }

    //  DELETE
    public function delete() {

        $query = "DELETE FROM " . $this->table . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }
}