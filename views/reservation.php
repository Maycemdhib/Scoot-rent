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
<?php
session_start(); // FIX: was missing — caused $_SESSION to be unavailable

require_once __DIR__ . "/../../helpers/auth.php";
require_once __DIR__ . "/../../controllers/ReservationController.php";

requireAdmin();

// ── Language ──
if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr','en'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang;
    setcookie('lang', $lang, time() + 60*60*24*30, '/');
} elseif (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} elseif (isset($_COOKIE['lang'])) {
    $lang = $_COOKIE['lang'];
    $_SESSION['lang'] = $lang;
} else {
    $lang = 'fr';
    $_SESSION['lang'] = $lang;
}

$controller   = new ReservationController();
$reservations = $controller->allReservations();

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

$statusColors = [
    'pending'   => 'warning',
    'confirmed' => 'success',
    'cancelled' => 'danger',
];
$statusLabels = [
    'pending'   => $lang === 'fr' ? 'En attente' : 'Pending',
    'confirmed' => $lang === 'fr' ? 'Confirmée'  : 'Confirmed',
    'cancelled' => $lang === 'fr' ? 'Annulée'    : 'Cancelled',
];

$t = [
    'title'       => $lang === 'fr' ? 'Gestion des Réservations' : 'Reservations Management',
    'dashboard'   => $lang === 'fr' ? 'Tableau de bord' : 'Dashboard',
    'scooters'    => $lang === 'fr' ? 'Trottinettes' : 'Scooters',
    'updated_ok'  => $lang === 'fr' ? '✅ Statut mis à jour.' : '✅ Status updated successfully.',
    'no_resa'     => $lang === 'fr' ? 'Aucune réservation trouvée.' : 'No reservations found.',
    'save'        => $lang === 'fr' ? 'Enregistrer' : 'Save',
    'unknown'     => $lang === 'fr' ? 'Inconnu' : 'Unknown',
    'logout'      => $lang === 'fr' ? 'Déconnexion' : 'Logout',
];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $t['title'] ?> — ScootRent Admin</title>
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
            font-size: clamp(1.8rem, 4vw, 2.4rem);
            font-weight: 800;
            background: linear-gradient(135deg, #fff 30%, var(--accent-violet) 70%, var(--accent-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .table-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            overflow: hidden;
            animation: fadeUp .4s ease both;
        }
        .form-select {
            background-color: rgba(54, 16, 239, 0.06) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #070101 !important;
        }
        .form-select option { background: var(--bg-mid); color: #fff; }
    </style>
</head>
<body>

<div class="container" style="max-width:1400px; padding-top:2rem; padding-bottom:4rem;">

    <!-- Topbar -->
    <div class="topbar">
        <div>
            <span class="auth-logo" style="font-size:1.5rem;">🛴 ScootRent</span>
            <span class="admin-badge">Admin</span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Lang -->
            <div class="lang-switcher d-flex gap-1">
                <a href="?lang=fr" class="btn btn-sm <?= $lang==='fr'?'active':'' ?>">FR</a>
                <a href="?lang=en" class="btn btn-sm <?= $lang==='en'?'active':'' ?>">EN</a>
            </div>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-speedometer2 me-1"></i><?= $t['dashboard'] ?>
            </a>
            <a href="trottinettes.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-scooter me-1"></i><?= $t['scooters'] ?>
            </a>
            <a href="../../controllers/AuthController.php?action=logout" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i><?= $t['logout'] ?>
            </a>
        </div>
    </div>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-4" style="animation:fadeUp .35s ease both;">
        <div>
            <h1 class="page-title mb-1">📋 <?= $t['title'] ?></h1>
            <p style="color:var(--text-muted); font-size:.9rem; margin:0;">
                <?= count($reservations) ?> <?= $lang==='fr'?'réservation(s) au total':'reservation(s) total' ?>
            </p>
        </div>
    </div>

    <!-- Alert -->
    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success mb-4"><i class="bi bi-check-circle me-2"></i><?= $t['updated_ok'] ?></div>
    <?php endif; ?>

    <?php if (empty($reservations)): ?>
        <div class="text-center py-5">
            <div style="font-size:5rem;">📭</div>
            <h3 class="text-white mt-3 mb-2"><?= $lang==='fr'?'Aucune réservation':'No Reservations' ?></h3>
            <p style="color:var(--text-muted);"><?= $t['no_resa'] ?></p>
        </div>

    <?php else: ?>

    <div class="table-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?= $lang==='fr'?'Utilisateur':'User' ?></th>
                        <th><?= $lang==='fr'?'Trottinette':'Scooter' ?></th>
                        <th><?= $lang==='fr'?'Début':'Start' ?></th>
                        <th><?= $lang==='fr'?'Fin':'End' ?></th>
                        <th><?= $lang==='fr'?'Prix':'Price' ?></th>
                        <th><?= $lang==='fr'?'Statut':'Status' ?></th>
                        <th><?= $lang==='fr'?'Modifier':'Update' ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($reservations as $r): ?>
                <tr>
                    <td style="color:var(--text-muted); font-size:.82rem;">#<?= (int)$r['id'] ?></td>

                    <td>
                        <span style="font-family:var(--font-display); font-weight:600;">
                            <?= e($r['user_name'] ?? $t['unknown']) ?>
                        </span>
                    </td>

                    <td><?= e($r['trottinette_name'] ?? $t['unknown']) ?></td>

                    <td style="color:var(--text-muted); font-size:.88rem;">
                        <i class="bi bi-calendar me-1"></i>
                        <?= isset($r['start_date']) ? date('d/m/Y H:i', strtotime($r['start_date'])) : '-' ?>
                    </td>

                    <td style="color:var(--text-muted); font-size:.88rem;">
                        <i class="bi bi-calendar-check me-1"></i>
                        <?= isset($r['end_date']) ? date('d/m/Y H:i', strtotime($r['end_date'])) : '-' ?>
                    </td>

                    <td>
                        <span style="font-family:var(--font-display); font-weight:700; color:var(--accent-cyan);">
                            <?= number_format((float)($r['total_price'] ?? 0), 2) ?> DT
                        </span>
                    </td>

                    <td>
                        <span class="badge bg-<?= $statusColors[$r['status']] ?? 'secondary' ?>">
                            <span class="status-dot <?= e($r['status']) ?>"></span>
                            <?= $statusLabels[$r['status']] ?? e(ucfirst($r['status'] ?? '')) ?>
                        </span>
                    </td>

                    <td>
                        <form method="POST"
                              action="../../controllers/ReservationController.php?action=updateStatus"
                              class="d-flex gap-2 align-items-center">
                            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                            <select name="status" class="form-select form-select-sm" style="min-width:130px;">
                                <option value="pending"   <?= ($r['status'] ?? '')  === 'pending'   ? 'selected' : '' ?>><?= $statusLabels['pending'] ?></option>
                                <option value="confirmed" <?= ($r['status'] ?? '') === 'confirmed'  ? 'selected' : '' ?>><?= $statusLabels['confirmed'] ?></option>
                                <option value="cancelled" <?= ($r['status'] ?? '') === 'cancelled'  ? 'selected' : '' ?>><?= $statusLabels['cancelled'] ?></option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-check2 me-1"></i><?= $t['save'] ?>
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

</body>
</html>
</body>
</html>