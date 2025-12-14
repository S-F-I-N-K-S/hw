<?php

require_once APP_PATH . '/core/Database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function register($data) {
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        $params = [
            $data['name'],
            $data['email'],
            hashPassword($data['password']),
            $data['role'] ?? ROLE_WORKER
        ];
        
        return $this->db->insert($sql, $params);
    }
    
    public function login($email, $password) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $user = $this->db->fetch($sql, [$email]);
        
        if ($user && verifyPassword($password, $user['password'])) {
            Session::set('user_id', $user['id']);
            Session::set('user_name', $user['name']);
            Session::set('user_email', $user['email']);
            Session::set('user_role', $user['role']);
            return true;
        }
        
        return false;
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    public function getAllWorkers() {
        $sql = "SELECT id, name, email FROM users WHERE role = ? ORDER BY name";
        return $this->db->fetchAll($sql, [ROLE_WORKER]);
    }
    
    public function getAll() {
        $sql = "SELECT id, name, email, role, created_at FROM users ORDER BY role, name";
        return $this->db->fetchAll($sql);
    }
    
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'] > 0;
    }
    
    public static function getCurrent() {
        $user = null;
        if (Session::isLoggedIn()) {
            $db = Database::getInstance();
            $sql = "SELECT * FROM users WHERE id = ?";
            $user = $db->fetch($sql, [Session::getUserId()]);
        }
        return $user;
    }
    public static function validatePasswordStrength($password) {
        $errors = [];
        
        if (strlen($password) < 6) {
            $errors[] = 'Пароль должен содержать минимум 6 символов';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Пароль должен содержать хотя бы одну заглавную букву';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Пароль должен содержать хотя бы одну цифру';
        }
        
        return $errors;
    }
}