<?php

require_once APP_PATH . '/core/Database.php';

class Task {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $sql = "INSERT INTO tasks (title, description, assigned_to, created_by, status, deadline) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['title'],
            $data['description'],
            $data['assigned_to'],
            $data['created_by'],
            $data['status'] ?? STATUS_PENDING,
            $data['deadline'] ?? null
        ];
        
        return $this->db->insert($sql, $params);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE tasks SET 
                title = ?, 
                description = ?, 
                assigned_to = ?, 
                status = ?, 
                deadline = ?,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";
        
        $params = [
            $data['title'],
            $data['description'],
            $data['assigned_to'],
            $data['status'],
            $data['deadline'] ?? null,
            $id
        ];
        
        $this->db->query($sql, $params);
        return true;
    }
    
    public function updateStatus($id, $status) {
        $sql = "UPDATE tasks SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $this->db->query($sql, [$status, $id]);
        return true;
    }
    
    public function delete($id) {
        $sql = "DELETE FROM tasks WHERE id = ?";
        $this->db->query($sql, [$id]);
        return true;
    }
    
    public function getById($id) {
        $sql = "SELECT t.*, 
                creator.name as creator_name,
                assignee.name as assignee_name
                FROM tasks t
                LEFT JOIN users creator ON t.created_by = creator.id
                LEFT JOIN users assignee ON t.assigned_to = assignee.id
                WHERE t.id = ?";
        
        return $this->db->fetch($sql, [$id]);
    }
    
    public function getAll() {
        $sql = "SELECT t.*, 
                creator.name as creator_name,
                assignee.name as assignee_name
                FROM tasks t
                LEFT JOIN users creator ON t.created_by = creator.id
                LEFT JOIN users assignee ON t.assigned_to = assignee.id
                ORDER BY t.created_at DESC";
        
        return $this->db->fetchAll($sql);
    }
    
    public function getByWorker($workerId) {
        $sql = "SELECT t.*, 
                creator.name as creator_name,
                assignee.name as assignee_name
                FROM tasks t
                LEFT JOIN users creator ON t.created_by = creator.id
                LEFT JOIN users assignee ON t.assigned_to = assignee.id
                WHERE t.assigned_to = ?
                ORDER BY 
                    CASE t.status
                        WHEN 'pending' THEN 1
                        WHEN 'in_progress' THEN 2
                        WHEN 'completed' THEN 3
                    END,
                    t.deadline ASC";
        
        return $this->db->fetchAll($sql, [$workerId]);
    }
    
    public function getByCreator($creatorId) {
        $sql = "SELECT t.*, assignee.name as assignee_name
                FROM tasks t
                LEFT JOIN users assignee ON t.assigned_to = assignee.id
                WHERE t.created_by = ?
                ORDER BY t.created_at DESC";
        
        return $this->db->fetchAll($sql, [$creatorId]);
    }
    
    public function canEdit($taskId, $userId) {
        $task = $this->getById($taskId);
        
        if (!$task) {
            return false;
        }
        
        if (Session::isAdmin()) {
            return true;
        }
        
        if (Session::isWorker()) {
            return $task['assigned_to'] == $userId;
        }
        
        return false;
    }
    
    public function canDelete($taskId, $userId) {
        return Session::isAdmin();
    }
}