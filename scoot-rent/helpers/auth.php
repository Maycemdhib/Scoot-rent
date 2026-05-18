<?php
/**
 * Auth + language helpers
 * Safe to include multiple times — all functions are guarded with function_exists.
 */

// LANGUAGE SYSTEM 

if (!function_exists('loadLang')) {
    function loadLang(): array {
        if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en'])) {
            $lang = $_GET['lang'];
            $_SESSION['lang'] = $lang;
            setcookie('lang', $lang, time() + 60 * 60 * 24 * 30, '/');
        } elseif (isset($_SESSION['lang'])) {
            $lang = $_SESSION['lang'];
        } elseif (isset($_COOKIE['lang'])) {
            $lang = $_COOKIE['lang'];
            $_SESSION['lang'] = $lang;
        } else {
            $lang = 'fr';
            $_SESSION['lang'] = $lang;
            setcookie('lang', $lang, time() + 60 * 60 * 24 * 30, '/');
        }

        $base = dirname(__DIR__);
        return require $base . "/lang/$lang.php";
    }
}

if (!function_exists('currentLang')) {
    function currentLang(): string {
        return $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'fr';
    }
}

// AUTH GUARDS 

if (!function_exists('requireLogin')) {
    function requireLogin(): void {
        if (!isset($_SESSION['user'])) {
            header("Location: /scoot-rent-fixed/scoot-rent/views/login.php?error=not_logged_in");
            exit;
        }
    }
}

if (!function_exists('requireAdmin')) {
    function requireAdmin(): void {
        requireLogin();
        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            header("Location: /scoot-rent-fixed/scoot-rent/views/dashboard.php");
            exit;
        }
    }
}

// ESCAPE OUTPUT 
if (!function_exists('e')) {
    function e(?string $str): string {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}