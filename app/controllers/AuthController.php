<?php

require_once APP_PATH . '/models/User.php';

class AuthController {
    
    public function showLogin() {
        if (Session::isLoggedIn()) {
            redirect('/tasks');
        }
        require VIEWS_PATH . '/auth/login.php';
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login');
        }
        
        verifyCsrfToken();
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $userModel = new User();
        
        if ($userModel->login($email, $password)) {
            redirect('/tasks');
        } else {
            $_SESSION['error'] = 'Неверный email или пароль';
            redirect('/login');
        }
    }
    
    public function showRegister() {
        if (Session::isLoggedIn()) {
            redirect('/tasks');
        }
        require VIEWS_PATH . '/auth/register.php';
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/register');
        }
        
        verifyCsrfToken();
        
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        $errors = [];
        
        if (empty($name)) {
            $errors[] = 'Имя обязательно';
        }
        
        if (empty($email) || !isValidEmail($email)) {
            $errors[] = 'Некорректный email';
        }
        
        if (strlen($password) < 6) {
            $errors[] = 'Пароль должен содержать минимум 6 символов';
        }
        
        if ($password !== $confirm_password) {
            $errors[] = 'Пароли не совпадают';
        }
        
        $userModel = new User();
        if ($userModel->emailExists($email)) {
            $errors[] = 'Пользователь с таким email уже существует';
        }
        
        if (empty($errors)) {
            $userId = $userModel->register([
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]);
            
            if ($userId) {
                $userModel->login($email, $password);
                redirect('/tasks');
            } else {
                $errors[] = 'Ошибка при регистрации';
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = [
                'name' => $name,
                'email' => $email
            ];
            redirect('/register');
        }
    }
    
    public function logout() {
        Session::destroy();
        redirect('/login');
    }
}