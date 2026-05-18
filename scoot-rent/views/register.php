<?php
session_start();

if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr','en'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang;
    setcookie('lang', $lang, time() + 60*60*24*30, '/');
} elseif (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} elseif (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], ['fr','en'])) {
    $lang = $_COOKIE['lang'];
    $_SESSION['lang'] = $lang;
} else {
    $lang = 'fr';
    $_SESSION['lang'] = $lang;
}

$translations = require __DIR__ . "/../lang/$lang.php";

if (isset($_SESSION['user'])) {
    header("Location: dashboard.php"); exit;
}

$errors = [
    'missing_fields' => '❌ ' . ($lang==='fr' ? 'Tous les champs sont requis.' : 'All fields are required.'),
    'invalid_email'  => '❌ ' . ($lang==='fr' ? 'Adresse email invalide.' : 'Please enter a valid email address.'),
    'weak_password'  => '❌ ' . ($lang==='fr' ? 'Le mot de passe doit contenir au moins 6 caractères.' : 'Password must be at least 6 characters.'),
    'email_taken'    => '❌ ' . ($lang==='fr' ? 'Cet email est déjà utilisé.' : 'This email is already registered.'),
];
$error = (isset($_GET['error']) && isset($errors[$_GET['error']])) ? $errors[$_GET['error']] : '';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $translations['register'] ?> — ScootRent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../public/css/style.css">
    <style>
        .auth-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
        }
        .auth-panel-left {
            background: linear-gradient(145deg, #0a1628 0%, #060b18 50%, #0d1526 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }
        .auth-panel-left::before {
            content: '';
            position: absolute;
            top: -40%;
            left: -20%;
            width: 80%;
            height: 80%;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(6,214,160,0.1), transparent 70%);
            pointer-events: none;
        }
        .auth-panel-left::after {
            content: '';
            position: absolute;
            bottom: -30%;
            right: -10%;
            width: 60%;
            height: 60%;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(56,189,248,0.08), transparent 70%);
            pointer-events: none;
        }
        .auth-brand-panel { position: relative; z-index: 1; text-align: center; }
        .brand-icon-large {
            font-size: 6rem;
            display: block;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 0 30px rgba(6,214,160,0.4));
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        .brand-name-large {
            font-family: var(--font-display);
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: block;
            margin-bottom: 1rem;
        }
        .brand-tagline {
            color: var(--text-muted);
            font-size: 1rem;
            max-width: 280px;
            margin: 0 auto 2.5rem;
        }
        .steps-list { list-style: none; padding: 0; margin: 0; text-align: left; }
        .steps-list li {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.85rem 1.25rem;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 14px;
            margin-bottom: 0.65rem;
            color: var(--text-muted);
            font-size: 0.875rem;
        }
        .step-num {
            width: 28px; height: 28px;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-cyan));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-family: var(--font-display);
            font-weight: 800;
            font-size: 0.75rem;
            color: #fff;
            flex-shrink: 0;
        }
        .auth-panel-right {
            background: var(--bg-deep);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
        }
        .auth-form-wrap { width: 100%; max-width: 400px; }
        .input-group-text {
            background: rgba(255,255,255,0.05) !important;
            border: 1px solid rgba(255,255,255,0.09) !important;
            border-right: none !important;
            color: #64748b !important;
        }
        .form-control { border-left: none !important; }
        @media (max-width: 768px) {
            .auth-grid { grid-template-columns: 1fr; }
            .auth-panel-left { display: none; }
        }
    </style>
</head>
<body style="background:var(--bg-deep);">

<div class="auth-grid">

    <!-- Left Panel -->
    <div class="auth-panel-left">
        <div class="auth-brand-panel">
            <span class="brand-icon-large">🛴</span>
            <span class="brand-name-large">ScootRent</span>
            <p class="brand-tagline"><?= $lang === 'fr' ? 'Rejoignez des milliers d\'utilisateurs satisfaits.' : 'Join thousands of happy riders.' ?></p>
            <ul class="steps-list">
                <li><div class="step-num">1</div><?= $lang === 'fr' ? 'Créez votre compte gratuitement' : 'Create your free account' ?></li>
                <li><div class="step-num">2</div><?= $lang === 'fr' ? 'Choisissez votre trottinette' : 'Pick your scooter' ?></li>
                <li><div class="step-num">3</div><?= $lang === 'fr' ? 'Réservez en un clic' : 'Book in one click' ?></li>
                <li><div class="step-num">4</div><?= $lang === 'fr' ? 'Profitez de votre trajet !' : 'Enjoy your ride!' ?></li>
            </ul>
        </div>
    </div>

    <!-- Right Panel — Form -->
    <div class="auth-panel-right">
        <div class="auth-form-wrap">

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="?lang=fr" class="btn btn-sm <?= $lang==='fr' ? 'active' : '' ?> lang-switcher">FR</a>
                <a href="?lang=en" class="btn btn-sm <?= $lang==='en' ? 'active' : '' ?> lang-switcher">EN</a>
            </div>

            <h2 class="text-white fw-bold mb-1" style="font-family:var(--font-display);"><?= $translations['create_account'] ?></h2>
            <p style="color:var(--text-muted); margin-bottom:2rem; font-size:.95rem;"><?= $lang === 'fr' ? 'Remplissez le formulaire ci-dessous.' : 'Fill in the form below to get started.' ?></p>

            <?php if ($error): ?><div class="alert alert-danger mb-4"><?= $error ?></div><?php endif; ?>

            <form action="../controllers/AuthController.php?action=register" method="POST">

                <div class="mb-4">
                    <label class="form-label"><?= $translations['full_name'] ?></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="name" class="form-control"
                               placeholder="<?= $translations['full_name_placeholder'] ?>" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label"><?= $translations['email'] ?></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control"
                               placeholder="vous@exemple.com" required>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label">
                        <?= $translations['password'] ?>
                        <span style="color:var(--text-muted);font-size:.75rem;text-transform:none;letter-spacing:0;font-weight:400;">
                            (<?= $translations['min_chars'] ?>)
                        </span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control"
                               placeholder="••••••••" required minlength="6">
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 py-3" style="font-size:1rem;">
                    <i class="bi bi-person-plus me-2"></i><?= $translations['create_account_btn'] ?>
                </button>

            </form>

            <p class="text-center mt-4" style="color:var(--text-muted); font-size:.9rem;">
                <?= $translations['already_account'] ?>
                <a href="login.php?lang=<?= $lang ?>" style="color:var(--accent-blue);"><?= $translations['login_here'] ?></a>
            </p>

        </div>
    </div>

</div>

</body>
</html>