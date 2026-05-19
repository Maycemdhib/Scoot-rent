<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../helpers/auth.php";
require_once __DIR__ . "/../controllers/TrottinetteController.php";
require_once __DIR__ . "/../models/Reservation.php";
require_once __DIR__ . "/../config/Database.php";

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'fr');
$_SESSION['lang'] = $lang;

requireLogin();

$controller   = new TrottinetteController();
$trottinettes = $controller->readAll();

$database = new Database();
$db = $database->getConnection();

$reservationModel = new Reservation($db);
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        <?= $lang === 'fr' ? 'Trottinettes' : 'Scooters' ?> — ScootRent
    </title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../public/css/style.css">

    <style>

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 18px;
            margin-bottom: 2.5rem;
        }

        .scooter-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: .3s ease;
        }

        .scooter-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 50px rgba(0,0,0,.25);
        }

        .scooter-img-wrap {
            height: 220px;
            overflow: hidden;
        }

        .scooter-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .scooter-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .scooter-brand {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #38bdf8;
            margin-bottom: .3rem;
            font-weight: 700;
        }

        .scooter-name {
            font-size: 1.4rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
        }

        .scooter-specs {
            display: flex;
            gap: .6rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .spec-chip {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 50px;
            padding: .35rem .9rem;
            font-size: .8rem;
            color: #cbd5e1;
        }

        .price-tag {
            font-size: 1.7rem;
            font-weight: 800;
            color: #22c55e;
            margin-bottom: 1rem;
        }

        .price-tag span {
            font-size: .9rem;
            color: #94a3b8;
            font-weight: 400;
        }

        .booking-form {
            margin-top: auto;
            background: rgba(255,255,255,.03);
            border: 1px solid rgba(255,255,255,.07);
            border-radius: 18px;
            padding: 1rem;
        }

        .booking-form label {
            color: #cbd5e1;
            font-size: .8rem;
            margin-bottom: .3rem;
        }

        .page-title {
            font-size: 2.3rem;
            font-weight: 800;
            color: white;
        }

        .booked-box {
            background: rgba(239,68,68,.12);
            border: 1px solid rgba(239,68,68,.3);
            color: #fecaca;
            border-radius: 14px;
            padding: .8rem;
            margin-bottom: 1rem;
            font-size: .85rem;
        }

        .available-box {
            background: rgba(34,197,94,.12);
            border: 1px solid rgba(34,197,94,.3);
            color: #bbf7d0;
            border-radius: 14px;
            padding: .8rem;
            margin-bottom: 1rem;
            font-size: .85rem;
        }

    </style>

</head>

<body>

<div class="container" style="max-width:1250px; padding-top:2rem; padding-bottom:4rem;">

    <!-- TOPBAR -->
    <div class="topbar">

        <span class="auth-logo fs-4">
            🛴 ScootRent
        </span>

        <div class="d-flex gap-2 flex-wrap">

            <a href="dashboard.php"
               class="btn btn-outline-light btn-sm">

                <i class="bi bi-grid me-1"></i>

                <?= $lang === 'fr' ? 'Dashboard' : 'Dashboard' ?>

            </a>

            <a href="user/my_reservations.php"
               class="btn btn-outline-info btn-sm">

                <i class="bi bi-calendar-check me-1"></i>

                <?= $lang === 'fr'
                    ? 'Mes réservations'
                    : 'My Reservations' ?>

            </a>

            <a href="../controllers/AuthController.php?action=logout"
               class="btn btn-outline-danger btn-sm">

                <i class="bi bi-box-arrow-right me-1"></i>

                <?= $lang === 'fr'
                    ? 'Déconnexion'
                    : 'Logout' ?>

            </a>

        </div>

    </div>

    <!-- TITLE -->
    <div class="mb-5">

        <h1 class="page-title">
            <?= $lang === 'fr'
                ? '🛴 Trottinettes disponibles'
                : '🛴 Available Scooters' ?>
        </h1>

        <p style="color:#94a3b8;">
            <?= $lang === 'fr'
                ? 'Choisissez votre trottinette et réservez facilement.'
                : 'Choose your scooter and reserve easily.' ?>
        </p>

    </div>

    <!-- EMPTY -->
    <?php if (empty($trottinettes)): ?>

        <div class="alert alert-warning text-center">
            <?= $lang === 'fr'
                ? 'Aucune trottinette disponible.'
                : 'No scooters available.' ?>
        </div>

    <?php else: ?>

    <div class="row g-4">

        <?php foreach ($trottinettes as $t): ?>

            <?php
                $bookings = $reservationModel->getReservationsByTrottinette($t['id']);
                $isBooked = false;

                foreach ($bookings as $b) {

                    if ($b['status'] !== 'cancelled') {

                        $now = date('Y-m-d H:i:s');

                        if ($now >= $b['start_date'] && $now <= $b['end_date']) {
                            $isBooked = true;
                            break;
                        }
                    }
                }
            ?>

            <div class="col-lg-4 col-md-6">

                <div class="scooter-card">

                    <!-- IMAGE -->
                    <div class="scooter-img-wrap">

                        <img src="../public/uploads/<?= e($t['image']) ?>"
                             alt="<?= e($t['name']) ?>"
                             onerror="this.src='https://placehold.co/600x400?text=No+Image'">

                    </div>

                    <!-- BODY -->
                    <div class="scooter-body">

                        <div class="scooter-brand">
                            <?= e($t['brand']) ?>
                        </div>

                        <div class="scooter-name">
                            <?= e($t['name']) ?>
                        </div>

                        <!-- SPECS -->
                        <div class="scooter-specs">

                            <div class="spec-chip">
                                🔋 <?= e($t['autonomy']) ?> km
                            </div>

                            <div class="spec-chip">
                                💳 <?= e($t['price_per_hour']) ?> DT/h
                            </div>

                        </div>

                        <!-- STATUS -->
                        <?php if ($isBooked): ?>

                            <div class="booked-box">
                                ❌
                                <?= $lang === 'fr'
                                    ? 'Cette trottinette est actuellement réservée.'
                                    : 'This scooter is currently booked.' ?>
                            </div>

                        <?php else: ?>

                            <div class="available-box">
                                ✅
                                <?= $lang === 'fr'
                                    ? 'Disponible maintenant'
                                    : 'Available now' ?>
                            </div>

                        <?php endif; ?>

                        <!-- BOOKING FORM -->
                        <form action="../controllers/ReservationController.php?action=create"
                              method="POST"
                              class="booking-form">

                            <input type="hidden"
                                   name="trottinette_id"
                                   value="<?= (int)$t['id'] ?>">

                            <div class="mb-3">

                                <label>
                                    <?= $lang === 'fr' ? 'Début' : 'Start' ?>
                                </label>

                                <input type="datetime-local"
                                       name="start_date"
                                       class="form-control"
                                       required
                                       <?= $isBooked ? 'disabled' : '' ?>>

                            </div>

                            <div class="mb-3">

                                <label>
                                    <?= $lang === 'fr' ? 'Fin' : 'End' ?>
                                </label>

                                <input type="datetime-local"
                                       name="end_date"
                                       class="form-control"
                                       required
                                       <?= $isBooked ? 'disabled' : '' ?>>

                            </div>

                            <button type="submit"
                                    class="btn btn-success w-100"
                                    <?= $isBooked ? 'disabled' : '' ?>>

                                <i class="bi bi-calendar-plus me-2"></i>

                                <?= $lang === 'fr'
                                    ? 'Réserver'
                                    : 'Reserve' ?>

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

    <?php endif; ?>

</div>

</body>
</html>