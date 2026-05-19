<?php
session_start();

require_once __DIR__ . "/../../helpers/auth.php";
require_once __DIR__ . "/../../controllers/ReservationController.php";

requireAdmin();

$controller = new ReservationController();
$reservations = $controller->allReservations() ?? [];

$statusColors = [
    'pending'   => 'warning',
    'confirmed' => 'success',
    'cancelled' => 'danger',
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Manage Reservations — ScootRent Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="../../public/css/style.css">

    <style>
        .reservation-card {
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.08);
            backdrop-filter: blur(14px);
            border-radius: 24px;
            overflow: hidden;
        }

        .reservation-table {
            color: white;
            margin-bottom: 0;
        }

        .reservation-table thead {
            background: rgba(255,255,255,.08);
        }

        .reservation-table td,
        .reservation-table th {
            padding: 1rem;
            border-color: rgba(255,255,255,.06);
            vertical-align: middle;
        }

        .reservation-table tbody tr:hover {
            background: rgba(255,255,255,.03);
        }

        .form-select {
            background-color: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.1);
            color: white;
        }

        .form-select option {
            color: black;
        }
    </style>
</head>

<body>

<div class="auth-wrapper">

    <div class="auth-box" style="max-width:1400px;">

        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">

            <span class="auth-logo">🛴 ScootRent Admin</span>

            <div class="d-flex gap-2 flex-wrap">

                <a href="dashboard.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-grid me-1"></i> Dashboard
                </a>

                <a href="trottinettes.php" class="btn btn-outline-info btn-sm">
                    <i class="bi bi-scooter me-1"></i> Scooters
                </a>

                <a href="../../controllers/AuthController.php?action=logout"
                   class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </a>

            </div>
        </div>

        <!-- Heading -->
        <div class="text-center mb-5">
            <h1 class="text-white fw-bold">📋 Manage Reservations</h1>
            <p style="color:var(--text-muted);">
                View and manage all customer reservations.
            </p>
        </div>

        <!-- Alerts -->
        <?php if (!empty($_GET['updated'])): ?>
            <div class="alert alert-success mb-4">
                ✅ Reservation status updated successfully.
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET['error'])): ?>
            <div class="alert alert-danger mb-4">
                ❌ <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <!-- Empty -->
        <?php if (empty($reservations)): ?>

            <div class="auth-card text-center">
                <div style="font-size:5rem;">📭</div>
                <h3 class="text-white mt-3">No Reservations Yet</h3>
                <p style="color:var(--text-muted);">
                    No customer reservations found.
                </p>
            </div>

        <?php else: ?>

        <!-- Table -->
        <div class="reservation-card">
            <div class="table-responsive">

                <table class="table reservation-table align-middle">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Scooter</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Update</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($reservations as $r): ?>

                        <?php
                            $start = isset($r['start_date']) ? date('d/m/Y H:i', strtotime($r['start_date'])) : '-';
                            $end   = isset($r['end_date']) ? date('d/m/Y H:i', strtotime($r['end_date'])) : '-';
                        ?>

                        <tr>

                            <td><?= (int)($r['id'] ?? 0) ?></td>

                            <td><?= htmlspecialchars($r['user_name'] ?? '') ?></td>

                            <td><?= htmlspecialchars($r['trottinette_name'] ?? '') ?></td>

                            <td><?= $start ?></td>
                            <td><?= $end ?></td>

                            <td>
                                <span class="fw-bold text-success">
                                    <?= number_format((float)($r['total_price'] ?? 0), 2) ?> DT
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-<?= $statusColors[$r['status']] ?? 'secondary' ?>">
                                    <?= htmlspecialchars(ucfirst($r['status'] ?? 'unknown')) ?>
                                </span>
                            </td>

                            <td>

                                <form action="../../controllers/ReservationController.php?action=updateStatus"
                                      method="POST"
                                      class="d-flex gap-2">

                                    <input type="hidden"
                                           name="id"
                                           value="<?= (int)($r['id'] ?? 0) ?>">

                                    <select name="status" class="form-select form-select-sm" style="width:140px">

                                        <option value="pending"   <?= ($r['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="confirmed" <?= ($r['status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                        <option value="cancelled" <?= ($r['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>

                                    </select>

                                    <button class="btn btn-sm btn-primary">
                                        <i class="bi bi-save me-1"></i> Save
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>
        </div>

        <?php endif; ?>

    </div>

</div>

</body>
</html>