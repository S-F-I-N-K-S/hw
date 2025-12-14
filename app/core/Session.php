<?php
class Session {
    
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    public static function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    public static function remove($key) {
        unset($_SESSION[$key]);
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public static function getUserRole() {
        return $_SESSION['user_role'] ?? null;
    }
    
    public static function isAdmin() {
        return self::getUserRole() === ROLE_ADMIN;
    }
    
    public static function isWorker() {
        return self::getUserRole() === ROLE_WORKER;
    }
    
    public static function destroy() {
        session_destroy();
    }
}