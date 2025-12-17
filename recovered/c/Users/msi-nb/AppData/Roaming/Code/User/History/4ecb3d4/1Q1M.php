<?php
// Simple require helper to protect admin pages
function require_admin()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['is_admin'])) {
        header('Location: login.php');
        exit;
    }
}
