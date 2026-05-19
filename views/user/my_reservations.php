<?php
session_start();

require_once __DIR__ . "/../../helpers/auth.php";
require_once __DIR__ . "/../../controllers/ReservationController.php";

requireLogin();

$controller   = new ReservationController();
$reservations = $controller->myReservations();

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

$statusColors = [
    'pending'   => 'warning',
    'confirmed' => 'success',
    'cancelled' => 'danger',
];
$statusIcons = [
    'pending'   => 'bi-clock',
    'confirmed' => 'bi-check-circle-fill',
    'cancelled' => 'bi-x-circle-fill',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Reservations — ScootRent</title>
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
        .page-title {
            font-family: var(--font-display);
            font-size: clamp(1.8rem, 4vw, 2.4rem);
            font-weight: 800;
            background: linear-gradient(135deg, #fff 30%, var(--accent-blue) 70%, var(--accent-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .res-table-wrap {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            overflow: hidden;
            animation: fadeUp .4s ease both;
        }
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            background: rgba(255,255,255,0.04);
        }
        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            animation: fadeUp .4s ease both;
        }
    </style>
</head>
<body>

<div class="container" style="max-width:1100px; padding-top:2rem; padding-bottom:4rem;">

    <!-- Topbar -->
    <div class="topbar">
        <span class="auth-logo" style="font-size:1.5rem;">🛴 ScootRent</span>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <a href="../dashboard.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-grid me-1"></i>Dashboard
            </a>
            <a href="../trottinettes.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-search me-1"></i>Browse Scooters
            </a>
            <a href="../../controllers/AuthController.php?action=logout" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </a>
        </div>
    </div>

    <!-- Header -->
    <div class="mb-4" style="animation:fadeUp .35s ease both;">
        <h1 class="page-title mb-1">📋 My Reservations</h1>
        <p style="color:var(--text-muted);">View all your scooter bookings in one place.</p>
    </div>

    <!-- Success Alert -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success mb-4">
            <i class="bi bi-check-circle me-2"></i>Reservation created successfully!
        </div>
    <?php endif; ?>

    <!-- Empty State -->
    <?php if (empty($reservations)): ?>
        <div class="empty-state">
            <div style="font-size:5rem; margin-bottom:1rem;">📭</div>
            <h3 class="text-white mb-2">No Reservations Yet</h3>
            <p style="color:var(--text-muted);" class="mb-4">You haven't booked any scooter yet.</p>
            <a href="../trottinettes.php" class="btn btn-primary">
                <i class="bi bi-search me-2"></i>Browse Scooters
            </a>
        </div>

    <?php else: ?>

    <!-- Table -->
    <div class="res-table-wrap">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Scooter</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Total Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($reservations as $r): ?>
                <tr>
                    <td style="color:var(--text-muted); font-size:.82rem;">#<?= (int)$r['id'] ?></td>
                    <td>
                        <span style="font-family:var(--font-display); font-weight:600; color:#aaccff;">
                            <?= e($r['trottinette_name']) ?>
                        </span>
                    </td>
                    <td style="color:var(--text-muted); font-size:.88rem;">
                        <i class="bi bi-calendar me-1"></i>
                        <?= e(date('d/m/Y H:i', strtotime($r['start_date']))) ?>
                    </td>
                    <td style="color:var(--text-muted); font-size:.88rem;">
                        <i class="bi bi-calendar-check me-1"></i>
                        <?= e(date('d/m/Y H:i', strtotime($r['end_date']))) ?>
                    </td>
                    <td>
                        <span style="font-family:var(--font-display); font-weight:700; color:var(--accent-cyan);">
                            <?= number_format((float)$r['total_price'], 2) ?> DT
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-<?= $statusColors[$r['status']] ?? 'secondary' ?>">
                            <i class="bi <?= $statusIcons[$r['status']] ?? 'bi-question-circle' ?> me-1"></i>
                            <?= e(ucfirst($r['status'])) ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php endif; 
    ?>

</div>
  <script>
   fetch("http://localhost/scoot-rent/api/trottinettes.php")
      .then(res => res.json())
      .then(data => {
        console.log(data);
      });
  </script>
</body>
</html>