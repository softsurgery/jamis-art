<?php
/**
 * Check if user is logged in
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin()
{
    return isLoggedIn() && isset($_SESSION['user_role']) && strtolower($_SESSION['user_role']) === 'admin';
}

/**
 * Require admin access - redirect to login/home if not authorized
 */
function requireAdmin()
{
    if (!isAdmin()) {
        header('Location: ../../index.php');
        exit;
    }
}

/**
 * Get current user role
 */
function getUserRole()
{
    return $_SESSION['user_role'] ?? null;
}

/**
 * Get current user ID
 */
function getUserId()
{
    return $_SESSION['user_id'] ?? null;
}
?>