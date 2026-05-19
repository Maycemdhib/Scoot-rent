<?php

class Reservation {

    private $conn;
    private $table = "reservations";

    // Properties
    public $id;
    public $user_id;
    public $trottinette_id;
    public $start_date;
    public $end_date;
    public $total_price;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    //  CREATE RESERVATION
    public function create() {

        $query = "INSERT INTO " . $this->table . "
        (user_id, trottinette_id, start_date, end_date, total_price, status)
        VALUES (:user_id, :trottinette_id, :start_date, :end_date, :total_price, :status)";

        $stmt = $this->conn->prepare($query);

        $this->status = "pending";

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":trottinette_id", $this->trottinette_id);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":total_price", $this->total_price);
        $stmt->bindParam(":status", $this->status);

        return $stmt->execute();
    }
     //  CHECK OVERLAP
    public function hasOverlap() {

        $query = "
        SELECT COUNT(*) as total
        FROM reservations
        WHERE trottinette_id = :trottinette_id
        AND status != 'cancelled'
        AND (
            start_date < :end_date
            AND end_date > :start_date
        )
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":trottinette_id", $this->trottinette_id);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'] > 0;
    }

    // GET USER RESERVATIONS
    public function getByUser() {

        $query = "SELECT r.*, t.name AS trottinette_name, t.image
                  FROM " . $this->table . " r
                  JOIN trottinettes t ON r.trottinette_id = t.id
                  WHERE r.user_id = :user_id
                  ORDER BY r.id DESC";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //  GET ALL (ADMIN)
    public function getAll() {

        $query = "SELECT r.*, u.name AS user_name, t.name AS trottinette_name
                  FROM " . $this->table . " r
                  JOIN users u ON r.user_id = u.id
                  JOIN trottinettes t ON r.trottinette_id = t.id
                  ORDER BY r.id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //  UPDATE STATUS (admin)
    public function updateStatus() {

        $query = "UPDATE " . $this->table . "
                  SET status = :status
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }
     // BOOKED PERIODS
    public function getBookedPeriodsByTrottinette($trottinette_id) {

        $query = "
        SELECT start_date, end_date
        FROM reservations
        WHERE trottinette_id = :id
        AND status != 'cancelled'
        ORDER BY start_date ASC
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $trottinette_id);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getReservationsByTrottinette($trottinette_id) {

    $query = "
        SELECT *
        FROM reservations
        WHERE trottinette_id = ?
        ORDER BY start_date ASC
    ";

    $stmt = $this->conn->prepare($query);

    $stmt->execute([$trottinette_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}