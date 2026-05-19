<?php
session_start();

require_once __DIR__ . "/../../helpers/auth.php";
require_once __DIR__ . "/../../controllers/TrottinetteController.php";

requireAdmin();

$controller   = new TrottinetteController();
$trottinettes = $controller->readAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Manage Scooters — ScootRent Admin</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../public/css/style.css">

    <style>

        .table-card {
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.08);
            backdrop-filter: blur(14px);
            border-radius: 24px;
            overflow: hidden;
        }

        .custom-table {
            color: white;
            margin-bottom: 0;
        }

        .custom-table thead {
            background: rgba(255,255,255,.08);
        }

        .custom-table td,
        .custom-table th {
            padding: 1rem;
            border-color: rgba(255,255,255,.06);
            vertical-align: middle;
        }

        .custom-table tbody tr:hover {
            background: rgba(255,255,255,.03);
        }

        .thumb {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 12px;
        }

        .modal-content {
            background: #111827;
            color: white;
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 24px;
        }

        .modal-header,
        .modal-footer {
            border-color: rgba(255,255,255,.08);
        }

        .form-control,
        .form-select {
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.08);
            color: white;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255,255,255,.08);
            color: white;
            border-color: #3b82f6;
            box-shadow: none;
        }

        .form-control::placeholder {
            color: #9ca3af;
        }

    </style>

</head>

<body>

<div class="auth-wrapper">

    <div class="auth-box" style="max-width:1400px;">

        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">

            <!-- Logo -->
            <div>

                <span class="auth-logo">
                    🛴 ScootRent Admin
                </span>

            </div>

            <!-- Actions -->
            <div class="d-flex align-items-center gap-2 flex-wrap">

                <!-- Dashboard -->
                <a href="dashboard.php"
                   class="btn btn-outline-light btn-sm">

                    <i class="bi bi-grid me-1"></i>

                    Dashboard

                </a>

                <!-- Reservations -->
                <a href="reservations.php"
                   class="btn btn-outline-info btn-sm">

                    <i class="bi bi-calendar-check me-1"></i>

                    Reservations

                </a>

                <!-- Logout -->
                <a href="../../controllers/AuthController.php?action=logout"
                   class="btn btn-outline-danger btn-sm">

                    <i class="bi bi-box-arrow-right me-1"></i>

                    Logout

                </a>

            </div>

        </div>

        <!-- Heading -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

            <div>

                <h1 class="text-white fw-bold mb-1">
                    🛴 Manage Scooters
                </h1>

                <p style="color:var(--text-muted);">
                    Add, edit, and manage all scooters.
                </p>

            </div>

            <!-- Add Button -->
            <button class="btn btn-success px-4 py-2"
                    data-bs-toggle="modal"
                    data-bs-target="#addModal">

                <i class="bi bi-plus-circle me-2"></i>

                Add Scooter

            </button>

        </div>

        <!-- Alerts -->
        <?php if (isset($_GET['success'])): ?>

            <div class="alert alert-success mb-4">
                ✅ Scooter added successfully.
            </div>

        <?php endif; ?>

        <?php if (isset($_GET['updated'])): ?>

            <div class="alert alert-info mb-4">
                ✏️ Scooter updated successfully.
            </div>

        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>

            <div class="alert alert-warning mb-4">
                🗑️ Scooter deleted successfully.
            </div>

        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>

            <div class="alert alert-danger mb-4">
                ❌ <?= e($_GET['error']) ?>
            </div>

        <?php endif; ?>

        <!-- Empty -->
        <?php if (empty($trottinettes)): ?>

            <div class="auth-card text-center">

                <div style="font-size:5rem;">🛴</div>

                <h3 class="text-white mt-3">
                    No Scooters Yet
                </h3>

                <p style="color:var(--text-muted);">
                    Start by adding your first scooter.
                </p>

            </div>

        <?php else: ?>

        <!-- Table -->
        <div class="table-card">

            <div class="table-responsive">

                <table class="table custom-table align-middle">

                    <thead>

                    <tr>

                        <th>Image</th>
                        <th>Name</th>
                        <th>Brand</th>
                        <th>Autonomy</th>
                        <th>Price / Hour</th>
                        <th>Actions</th>

                    </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($trottinettes as $t): ?>

                    <tr>

                        <!-- Image -->
                        <td>

                            <img src="../../public/uploads/<?= e($t['image']) ?>"
                                 class="thumb"
                                 alt="<?= e($t['name']) ?>"
                                 onerror="this.src='https://placehold.co/80x60?text=?'">

                        </td>

                        <!-- Name -->
                        <td class="fw-semibold">

                            <?= e($t['name']) ?>

                        </td>

                        <!-- Brand -->
                        <td>

                            <?= e($t['brand']) ?>

                        </td>

                        <!-- Autonomy -->
                        <td>

                            <?= e($t['autonomy']) ?> km

                        </td>

                        <!-- Price -->
                        <td>

                            <span class="fw-bold text-success">

                                <?= number_format((float)$t['price_per_hour'], 2) ?> DT

                            </span>

                        </td>

                        <!-- Actions -->
                        <td class="d-flex gap-2 flex-wrap">

                            <!-- Edit -->
                            <button class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"

                                    data-id="<?= (int)$t['id'] ?>"
                                    data-name="<?= e($t['name']) ?>"
                                    data-brand="<?= e($t['brand']) ?>"
                                    data-autonomy="<?= (int)$t['autonomy'] ?>"
                                    data-price="<?= e($t['price_per_hour']) ?>"
                                    data-desc="<?= e($t['description']) ?>">

                                <i class="bi bi-pencil-square me-1"></i>

                                Edit

                            </button>

                            <!-- Delete -->
                            <a href="../../controllers/TrottinetteController.php?action=delete&id=<?= (int)$t['id'] ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Delete this scooter?')">

                                <i class="bi bi-trash me-1"></i>

                                Delete

                            </a>

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

<!-- ADD MODAL -->
<div class="modal fade" id="addModal" tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Add New Scooter
                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>

            </div>

            <form action="../../controllers/TrottinetteController.php?action=create"
                  method="POST"
                  enctype="multipart/form-data">

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">
                            Name
                        </label>

                        <input type="text"
                               name="name"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Brand
                        </label>

                        <input type="text"
                               name="brand"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Autonomy (km)
                        </label>

                        <input type="number"
                               name="autonomy"
                               class="form-control"
                               min="0"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Price per hour (DT)
                        </label>

                        <input type="number"
                               name="price_per_hour"
                               class="form-control"
                               step="0.01"
                               min="0"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Description
                        </label>

                        <textarea name="description"
                                  class="form-control"
                                  rows="3"></textarea>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Image
                        </label>

                        <input type="file"
                               name="image"
                               class="form-control"
                               accept="image/*">

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button type="submit"
                            class="btn btn-success">

                        Add Scooter

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Edit Scooter
                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>

            </div>

            <form action="../../controllers/TrottinetteController.php?action=update"
                  method="POST"
                  enctype="multipart/form-data">

                <div class="modal-body">

                    <input type="hidden"
                           name="id"
                           id="editId">

                    <div class="mb-3">

                        <label class="form-label">
                            Name
                        </label>

                        <input type="text"
                               name="name"
                               id="editName"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Brand
                        </label>

                        <input type="text"
                               name="brand"
                               id="editBrand"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Autonomy (km)
                        </label>

                        <input type="number"
                               name="autonomy"
                               id="editAutonomy"
                               class="form-control"
                               min="0"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Price per hour (DT)
                        </label>

                        <input type="number"
                               name="price_per_hour"
                               id="editPrice"
                               class="form-control"
                               step="0.01"
                               min="0"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Description
                        </label>

                        <textarea name="description"
                                  id="editDesc"
                                  class="form-control"
                                  rows="3"></textarea>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            New Image
                        </label>

                        <input type="file"
                               name="image"
                               class="form-control"
                               accept="image/*">

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button type="submit"
                            class="btn btn-primary">

                        Save Changes

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>

// Populate edit modal
document.getElementById('editModal').addEventListener('show.bs.modal', function(e) {

    const btn = e.relatedTarget;

    document.getElementById('editId').value =
        btn.dataset.id;

    document.getElementById('editName').value =
        btn.dataset.name;

    document.getElementById('editBrand').value =
        btn.dataset.brand;

    document.getElementById('editAutonomy').value =
        btn.dataset.autonomy;

    document.getElementById('editPrice').value =
        btn.dataset.price;

    document.getElementById('editDesc').value =
        btn.dataset.desc;

});

</script>

</body>
</html>