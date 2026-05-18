<?php
session_start();

//Language resolution: GET > cookie > session > default ──
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
    'invalid_credentials' => '❌ ' . ($lang === 'fr' ? 'Email ou mot de passe invalide.' : 'Invalid email or password.'),
    'not_logged_in'       => '⚠️ ' . ($lang === 'fr' ? 'Veuillez vous connecter pour continuer.' : 'Please log in to continue.'),
];
$error      = (isset($_GET['error']) && isset($errors[$_GET['error']])) ? $errors[$_GET['error']] : '';
$success    = isset($_GET['success'])    ? '✅ ' . $translations['account_created']  : '';
$logged_out = isset($_GET['logged_out']) ? '👋 ' . $translations['logged_out_msg']   : '';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $translations['login'] ?> — ScootRent</title>
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
            background: radial-gradient(circle, rgba(56,189,248,0.12), transparent 70%);
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
            background: radial-gradient(circle, rgba(167,139,250,0.1), transparent 70%);
            pointer-events: none;
        }
        .auth-brand-panel {
            position: relative;
            z-index: 1;
            text-align: center;
        }
        .brand-icon-large {
            font-size: 6rem;
            display: block;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 0 30px rgba(56,189,248,0.4));
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
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-cyan));
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
            line-height: 1.6;
        }
        .feature-pills {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .feature-pill {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 50px;
            padding: 0.6rem 1.25rem;
            font-size: 0.85rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .feature-pill i { color: var(--accent-cyan); }
        .auth-panel-right {
            background: var(--bg-deep);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
        }
        .auth-form-wrap {
            width: 100%;
            max-width: 400px;
        }
        .input-group-text {
            background: rgba(255,255,255,0.05) !important;
            border: 1px solid rgba(255,255,255,0.09) !important;
            border-right: none !important;
            color: #64748b !important;
        }
        .form-control {
            border-left: none !important;
        }
        @media (max-width: 768px) {
            .auth-grid { grid-template-columns: 1fr; }
            .auth-panel-left { display: none; }
        }
    </style>
</head>
<body style="background:var(--bg-deep);">

<div class="auth-grid">

    <!-- Left Panel — Brand -->
    <div class="auth-panel-left">
        <div class="auth-brand-panel">
            <span class="brand-icon-large">🛴</span>
            <span class="brand-name-large">ScootRent</span>
            <p class="brand-tagline"><?= $lang === 'fr' ? 'Réservez votre trottinette en quelques clics.' : 'Rent your scooter in just a few clicks.' ?></p>
            <div class="feature-pills">
                <div class="feature-pill"><i class="bi bi-lightning-charge-fill"></i><?= $lang === 'fr' ? 'Réservation instantanée' : 'Instant booking' ?></div>
                <div class="feature-pill"><i class="bi bi-shield-check-fill"></i><?= $lang === 'fr' ? '100% sécurisé' : '100% secure' ?></div>
                <div class="feature-pill"><i class="bi bi-geo-alt-fill"></i><?= $lang === 'fr' ? 'Disponible partout' : 'Available everywhere' ?></div>
            </div>
        </div>
    </div>

    <!-- Right Panel — Form -->
    <div class="auth-panel-right">
        <div class="auth-form-wrap">

            <!-- Lang Switcher -->
            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="?lang=fr" class="btn btn-sm <?= $lang==='fr' ? 'active' : '' ?> lang-switcher">FR</a>
                <a href="?lang=en" class="btn btn-sm <?= $lang==='en' ? 'active' : '' ?> lang-switcher">EN</a>
            </div>

            <h2 class="text-white fw-bold mb-1" style="font-family:var(--font-display);"><?= $translations['login'] ?></h2>
            <p style="color:var(--text-muted); margin-bottom:2rem; font-size:.95rem;"><?= $translations['login_subtitle'] ?></p>

            <!-- Alerts -->
            <?php if ($error):     ?><div class="alert alert-danger  mb-4"><?= $error ?></div><?php endif; ?>
            <?php if ($success):   ?><div class="alert alert-success mb-4"><?= $success ?></div><?php endif; ?>
            <?php if ($logged_out):?><div class="alert alert-info    mb-4"><?= $logged_out ?></div><?php endif; ?>

            <form action="../controllers/AuthController.php?action=login" method="POST">

                <div class="mb-4">
                    <label class="form-label"><?= $translations['email'] ?></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control"
                               placeholder="vous@exemple.com" required autofocus>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label"><?= $translations['password'] ?></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control"
                               placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3" style="font-size:1rem;">
                    <i class="bi bi-box-arrow-in-right me-2"></i><?= $translations['login'] ?>
                </button>

            </form>

            <p class="text-center mt-4" style="color:var(--text-muted); font-size:.9rem;">
                <?= $translations['no_account'] ?>
                <a href="register.php?lang=<?= $lang ?>" style="color:var(--accent-cyan);"><?= $translations['register_here'] ?></a>
            </p>

        </div>
    </div>

</div>

</body>
</html>