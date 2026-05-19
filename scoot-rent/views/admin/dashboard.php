<?php
session_start();

require_once __DIR__ . "/../../helpers/auth.php";
require_once __DIR__ . "/../../config/Database.php";

requireAdmin();

$db = (new Database())->getConnection();

$users        = $db->query("SELECT COUNT(*) as total FROM users")->fetch()['total'];
$trottinettes = $db->query("SELECT COUNT(*) as total FROM trottinettes")->fetch()['total'];
$reservations = $db->query("SELECT COUNT(*) as total FROM reservations")->fetch()['total'];
$revenue      = $db->query("SELECT COALESCE(SUM(total_price), 0) as total FROM reservations WHERE status = 'confirmed'")->fetch()['total'];
$pending      = $db->query("SELECT COUNT(*) as total FROM reservations WHERE status = 'pending'")->fetch()['total'];

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard — ScootRent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../public/css/style.css">
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
        .admin-badge {
            background: linear-gradient(135deg, var(--accent-violet), #7c3aed);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.2em 0.6em;
            border-radius: 6px;
            vertical-align: middle;
            margin-left: 0.4rem;
        }
        .page-title {
            font-family: var(--font-display);
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            font-weight: 800;
            background: linear-gradient(135deg, #fff 30%, var(--accent-violet) 70%, var(--accent-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .stat-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 22px;
            padding: 1.75rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            height: 100%;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            animation: fadeUp .4s ease both;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.3);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            flex-shrink: 0;
        }
        .stat-icon.blue   { background: rgba(56,189,248,0.15); }
        .stat-icon.cyan   { background: rgba(6,214,160,0.15); }
        .stat-icon.violet { background: rgba(167,139,250,0.15); }
        .stat-icon.orange { background: rgba(251,146,60,0.15); }
        .stat-value {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }
        .stat-label {
            color: var(--text-muted);
            font-size: 0.8rem;
            margin-top: 0.2rem;
        }
        .action-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            padding: 2.5rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            height: 100%;
            transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
            animation: fadeUp .4s ease .15s both;
        }
        .action-card:hover { transform: translateY(-5px); }
        .action-card.card-scooter:hover {
            box-shadow: 0 20px 50px rgba(56,189,248,0.12);
            border-color: rgba(56,189,248,0.2);
        }
        .action-card.card-reservations:hover {
            box-shadow: 0 20px 50px rgba(167,139,250,0.12);
            border-color: rgba(167,139,250,0.2);
        }
        .action-icon {
            width: 90px; height: 90px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 3rem;
            margin-bottom: 1.5rem;
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

<div class="container" style="max-width:1300px; padding-top:2rem; padding-bottom:4rem;">

    <!-- Topbar -->
    <div class="topbar">
        <div>
            <span class="auth-logo" style="font-size:1.5rem;">🛴 ScootRent</span>
            <span class="admin-badge">Admin</span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="user-pill">
                <i class="bi bi-person-circle"></i>
                <?= e($_SESSION['user']['name']) ?>
            </div>
            <a href="../../controllers/AuthController.php?action=logout" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </a>
        </div>
    </div>

    <!-- Header -->
    <div class="mb-4" style="animation:fadeUp .35s ease both;">
        <h1 class="page-title mb-1">📊 Admin Dashboard</h1>
        <p style="color:var(--text-muted);">Welcome back, <?= e($_SESSION['user']['name']) ?></p>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">

        <div class="col-lg-3 col-md-6" style="animation-delay:0s;">
            <div class="stat-card">
                <div class="stat-icon blue">👤</div>
                <div>
                    <div class="stat-value"><?= $users ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6" style="animation-delay:.06s;">
            <div class="stat-card">
                <div class="stat-icon cyan">🛴</div>
                <div>
                    <div class="stat-value"><?= $trottinettes ?></div>
                    <div class="stat-label">Scooters</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6" style="animation-delay:.12s;">
            <div class="stat-card">
                <div class="stat-icon violet">📋</div>
                <div>
                    <div class="stat-value"><?= $reservations ?></div>
                    <div class="stat-label">Reservations</div>
                    <?php if ($pending > 0): ?>
                        <span class="badge bg-warning mt-1"><?= $pending ?> Pending</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6" style="animation-delay:.18s;">
            <div class="stat-card">
                <div class="stat-icon orange">💰</div>
                <div>
                    <div class="stat-value" style="font-size:1.5rem;"><?= number_format((float)$revenue, 2) ?> DT</div>
                    <div class="stat-label">Confirmed Revenue</div>
                </div>
            </div>
        </div>

    </div>

    <!-- Action Cards -->
    <div class="row g-4">

        <div class="col-md-6">
            <div class="action-card card-scooter">
                <div class="action-icon" style="background:rgba(56,189,248,0.12);">🛴</div>
                <h3 class="text-white mb-3" style="font-family:var(--font-display);">Manage Scooters</h3>
                <p style="color:var(--text-muted); margin-bottom:2rem; font-size:.9rem; flex:1;">
                    Add, edit, and manage all scooters on the platform.
                </p>
                <a href="trottinettes.php" class="btn btn-primary w-100 py-2">
                    <i class="bi bi-gear me-2"></i>Manage Scooters
                </a>
            </div>
        </div>

        <div class="col-md-6">
            <div class="action-card card-reservations">
                <div class="action-icon" style="background:rgba(167,139,250,0.12);">📋</div>
                <h3 class="text-white mb-3" style="font-family:var(--font-display);">Manage Reservations</h3>
                <p style="color:var(--text-muted); margin-bottom:2rem; font-size:.9rem; flex:1;">
                    View, confirm, and manage all customer reservations.
                </p>
                <a href="reservations.php" class="btn btn-primary w-100 py-2" style="background:linear-gradient(135deg,#7c3aed,var(--accent-violet));">
                    <i class="bi bi-calendar-check me-2"></i>Manage Reservations
                </a>
            </div>
        </div>

    </div>

</div>

</body>
</html>