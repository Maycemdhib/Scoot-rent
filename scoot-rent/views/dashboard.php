<?php
session_start();

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'fr');
$_SESSION['lang'] = $lang;
if (isset($_GET['lang'])) {
    setcookie('lang', $lang, time() + 60*60*24*30, '/');
}

$translations = require __DIR__ . "/../lang/$lang.php";

require_once __DIR__ . "/../helpers/auth.php";
requireLogin();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — ScootRent</title>
    <!-- FIX: was "https://://cdn..." — corrected URL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../public/css/style.css">
    <style>
        .page-header {
            padding: 2rem 0 3rem;
            text-align: center;
            position: relative;
        }
        .greeting {
            font-family: var(--font-display);
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 800;
            background: linear-gradient(135deg, #fff 30%, var(--accent-blue) 70%, var(--accent-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .dashboard-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            padding: 2.5rem 2rem;
            backdrop-filter: blur(20px);
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
        }
        .dashboard-card:hover {
            transform: translateY(-6px);
        }
        .dashboard-card.card-scooter:hover {
            box-shadow: 0 20px 50px rgba(56,189,248,0.15);
            border-color: rgba(56,189,248,0.25);
        }
        .dashboard-card.card-reservations:hover {
            box-shadow: 0 20px 50px rgba(6,214,160,0.15);
            border-color: rgba(6,214,160,0.25);
        }
        .card-icon-wrap {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin-bottom: 1.5rem;
        }
        .icon-blue { background: rgba(56,189,248,0.12); }
        .icon-cyan  { background: rgba(6,214,160,0.12); }
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
            margin-bottom: 2rem;
        }
        .user-pill {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 50px;
            padding: 0.45rem 1rem;
            color: var(--text-main);
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
</head>
<body>

<div class="container" style="max-width:1000px; padding-top:2rem; padding-bottom:4rem;">

    <!-- Topbar -->
    <div class="topbar">
        <span class="auth-logo" style="font-size:1.5rem;">🛴 ScootRent</span>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Language -->
            <div class="lang-switcher d-flex gap-1">
                <a href="?lang=fr" class="btn btn-sm <?= $lang==='fr' ? 'active' : '' ?>">FR</a>
                <a href="?lang=en" class="btn btn-sm <?= $lang==='en' ? 'active' : '' ?>">EN</a>
            </div>

            <!-- User -->
            <div class="user-pill">
                <i class="bi bi-person-circle"></i>
                <?= htmlspecialchars($_SESSION['user']['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </div>

            <!-- Logout -->
            <a href="../controllers/AuthController.php?action=logout"
               class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i>
                <?= $lang === 'fr' ? 'Déconnexion' : 'Logout' ?>
            </a>
        </div>
    </div>

    <!-- Header -->
    <div class="page-header">
        <p class="greeting">
            <?= $lang === 'fr' ? 'Bonjour,' : 'Welcome back,' ?>
            <?= htmlspecialchars($_SESSION['user']['name'] ?? '', ENT_QUOTES, 'UTF-8') ?> 👋
        </p>
        <p style="color:var(--text-muted); font-size:1.05rem; margin-top:0.5rem;">
            <?= $lang === 'fr' ? 'Que souhaitez-vous faire aujourd\'hui ?' : 'What would you like to do today?' ?>
        </p>
    </div>

    <!-- Cards -->
    <div class="row g-4">

        <!-- Scooters -->
        <div class="col-md-6" style="animation:fadeUp .4s ease both;">
            <div class="dashboard-card card-scooter">
                <div class="card-icon-wrap icon-blue">🛴</div>
                <h4 class="text-white mb-2" style="font-family:var(--font-display);">
                    <?= $lang === 'fr' ? 'Explorer les trottinettes' : 'Browse Scooters' ?>
                </h4>
                <p style="color:var(--text-muted); margin-bottom:2rem; font-size:.9rem; flex:1;">
                    <?= $lang === 'fr' ? 'Consultez les trottinettes disponibles et faites une réservation facilement.' : 'View available scooters and make your reservation easily.' ?>
                </p>
                <a href="trottinettes.php" class="btn btn-primary w-100 py-2">
                    <i class="bi bi-search me-2"></i>
                    <?= $lang === 'fr' ? 'Voir les trottinettes' : 'View Scooters' ?>
                </a>
            </div>
        </div>

        <!-- Reservations -->
        <div class="col-md-6" style="animation:fadeUp .4s ease .08s both;">
            <div class="dashboard-card card-reservations">
                <div class="card-icon-wrap icon-cyan">📋</div>
                <h4 class="text-white mb-2" style="font-family:var(--font-display);">
                    <?= $lang === 'fr' ? 'Mes Réservations' : 'My Reservations' ?>
                </h4>
                <p style="color:var(--text-muted); margin-bottom:2rem; font-size:.9rem; flex:1;">
                    <?= $lang === 'fr' ? 'Consultez vos réservations actuelles et passées à tout moment.' : 'Check your current and previous bookings anytime.' ?>
                </p>
                <a href="user/my_reservations.php" class="btn btn-success w-100 py-2">
                    <i class="bi bi-calendar-check me-2"></i>
                    <?= $lang === 'fr' ? 'Mes Réservations' : 'My Bookings' ?>
                </a>
            </div>
        </div>

    </div>

</div>

</body>
</html>