<?php
require_once APP_PATH . '/models/Task.php';
require_once APP_PATH . '/models/User.php';

class TaskController {
    
    public function __construct() {
        if (!Session::isLoggedIn()) {
            redirect('/login');
        }
    }
    
    public function index() {
        $taskModel = new Task();
        $userModel = new User();
        
        if (Session::isAdmin()) {
            $tasks = $taskModel->getAll();
        } else {
            $tasks = $taskModel->getByWorker(Session::getUserId());
        }
        
        $workers = $userModel->getAllWorkers();
        
        require VIEWS_PATH . '/tasks/index.php';
    }
    
    public function create() {
        if (!Session::isAdmin()) {
            $_SESSION['error'] = 'Доступ запрещен';
            redirect('/tasks');
        }
        
        $userModel = new User();
        $workers = $userModel->getAllWorkers();
        
        require VIEWS_PATH . '/tasks/create.php';
    }
    
    public function store() {
        if (!Session::isAdmin()) {
            $_SESSION['error'] = 'Доступ запрещен';
            redirect('/tasks');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/tasks/create');
        }
        
        verifyCsrfToken();
        
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $assigned_to = (int)($_POST['assigned_to'] ?? 0);
        $deadline = $_POST['deadline'] ?? null;
        
        $errors = [];
        
        if (empty($title)) {
            $errors[] = 'Название обязательно';
        }
        
        if (empty($description)) {
            $errors[] = 'Описание обязательно';
        }
        
        if ($assigned_to <= 0) {
            $errors[] = 'Выберите работника';
        }
        
        if ($deadline && strtotime($deadline) < strtotime('today')) {
            $errors[] = 'Дата выполнения не может быть в прошлом';
        }
        
        if (empty($errors)) {
            $taskModel = new Task();
            $taskId = $taskModel->create([
                'title' => $title,
                'description' => $description,
                'assigned_to' => $assigned_to,
                'created_by' => Session::getUserId(),
                'deadline' => $deadline ? date('Y-m-d', strtotime($deadline)) : null
            ]);
            
            if ($taskId) {
                $_SESSION['success'] = 'Задача успешно создана';
                redirect('/tasks');
            } else {
                $errors[] = 'Ошибка при создании задачи';
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            redirect('/tasks/create');
        }
    }
    
    public function edit($id) {
        $taskModel = new Task();
        $task = $taskModel->getById($id);
        
        if (!$task) {
            $_SESSION['error'] = 'Задача не найдена';
            redirect('/tasks');
        }
        
        if (!$taskModel->canEdit($id, Session::getUserId())) {
            $_SESSION['error'] = 'Доступ запрещен';
            redirect('/tasks');
        }
        
        $userModel = new User();
        $workers = $userModel->getAllWorkers();
        
        require VIEWS_PATH . '/tasks/edit.php';
    }
    
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/tasks');
        }
        
        verifyCsrfToken();
        
        $taskModel = new Task();
        $task = $taskModel->getById($id);
        
        if (!$task) {
            $_SESSION['error'] = 'Задача не найдена';
            redirect('/tasks');
        }
        
        if (!$taskModel->canEdit($id, Session::getUserId())) {
            $_SESSION['error'] = 'Доступ запрещен';
            redirect('/tasks');
        }
        
        if (Session::isAdmin()) {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $assigned_to = (int)($_POST['assigned_to'] ?? 0);
            $status = $_POST['status'] ?? STATUS_PENDING;
            $deadline = $_POST['deadline'] ?? null;
            
            $errors = [];
            
            if (empty($title)) {
                $errors[] = 'Название обязательно';
            }
            
            if (empty($description)) {
                $errors[] = 'Описание обязательно';
            }
            
            if ($assigned_to <= 0) {
                $errors[] = 'Выберите работника';
            }
            
            if (empty($errors)) {
                $taskModel->update($id, [
                    'title' => $title,
                    'description' => $description,
                    'assigned_to' => $assigned_to,
                    'status' => $status,
                    'deadline' => $deadline ? date('Y-m-d', strtotime($deadline)) : null
                ]);
                
                $_SESSION['success'] = 'Задача обновлена';
            }
        } 
        else {
            $status = $_POST['status'] ?? STATUS_PENDING;
            $taskModel->updateStatus($id, $status);
            $_SESSION['success'] = 'Статус задачи обновлен';
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            redirect("/tasks/edit/$id");
        } else {
            redirect('/tasks');
        }
    }
    
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/tasks');
        }
        
        verifyCsrfToken();
        
        $taskModel = new Task();
        $task = $taskModel->getById($id);
        
        if (!$task) {
            $_SESSION['error'] = 'Задача не найдена';
            redirect('/tasks');
        }
        
        if (!$taskModel->canDelete($id, Session::getUserId())) {
            $_SESSION['error'] = 'Доступ запрещен';
            redirect('/tasks');
        }
        
        if ($taskModel->delete($id)) {
            $_SESSION['success'] = 'Задача удалена';
        } else {
            $_SESSION['error'] = 'Ошибка при удалении задачи';
        }
        
        redirect('/tasks');
    }
}