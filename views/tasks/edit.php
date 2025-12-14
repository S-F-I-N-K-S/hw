<?php
$title = 'Редактирование задачи';
require VIEWS_PATH . '/layouts/header.php';

$isAdmin = Session::isAdmin();
$canEditAll = $isAdmin; 
$canEditStatus = !$isAdmin; 
?>

<h2 class="mb-4">Редактирование задачи #<?php echo $task['id']; ?></h2>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Редактирование задачи</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo baseUrl('tasks/update/' . $task['id']); ?>" id="editForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    <?php if ($canEditAll): ?>
                    <div class="mb-3">
                        <label for="title" class="form-label">Название задачи *</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo escape($task['title']); ?>" 
                               required maxlength="255" <?php echo $canEditAll ? '' : 'readonly'; ?>>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Описание *</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="5" required <?php echo $canEditAll ? '' : 'readonly'; ?>><?php echo escape($task['description']); ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="assigned_to" class="form-label">Назначить работнику *</label>
                            <select class="form-select" id="assigned_to" name="assigned_to" required <?php echo $canEditAll ? '' : 'disabled'; ?>>
                                <option value="">Выберите работника</option>
                                <?php foreach ($workers as $worker): ?>
                                <option value="<?php echo $worker['id']; ?>"
                                    <?php echo ($task['assigned_to'] == $worker['id']) ? 'selected' : ''; ?>>
                                    <?php echo escape($worker['name']); ?> (<?php echo escape($worker['email']); ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!$canEditAll): ?>
                            <input type="hidden" name="assigned_to" value="<?php echo $task['assigned_to']; ?>">
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="deadline" class="form-label">Срок выполнения</label>
                            <input type="date" class="form-control" id="deadline" name="deadline"
                                   value="<?php echo $task['deadline'] ? date('Y-m-d', strtotime($task['deadline'])) : ''; ?>"
                                   min="<?php echo date('Y-m-d'); ?>" <?php echo $canEditAll ? '' : 'readonly'; ?>>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="mb-3">
                        <label class="form-label">Название задачи</label>
                        <div class="form-control bg-light"><?php echo escape($task['title']); ?></div>
                        <input type="hidden" name="title" value="<?php echo escape($task['title']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <div class="form-control bg-light" style="min-height: 100px;"><?php echo nl2br(escape($task['description'])); ?></div>
                        <input type="hidden" name="description" value="<?php echo escape($task['description']); ?>">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Назначена</label>
                            <div class="form-control bg-light"><?php echo escape($task['assignee_name']); ?></div>
                            <input type="hidden" name="assigned_to" value="<?php echo $task['assigned_to']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Срок выполнения</label>
                            <div class="form-control bg-light">
                                <?php echo $task['deadline'] ? date('d.m.Y', strtotime($task['deadline'])) : 'Не указан'; ?>
                            </div>
                            <input type="hidden" name="deadline" value="<?php echo $task['deadline']; ?>">
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="status" class="form-label">Статус *</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" <?php echo ($task['status'] == 'pending') ? 'selected' : ''; ?>>Ожидает</option>
                            <option value="in_progress" <?php echo ($task['status'] == 'in_progress') ? 'selected' : ''; ?>>В работе</option>
                            <option value="completed" <?php echo ($task['status'] == 'completed') ? 'selected' : ''; ?>>Завершена</option>
                        </select>
                    </div>
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Информация о задаче</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Создана:</strong><br>
                                    <?php echo escape($task['creator_name']); ?><br>
                                    <small class="text-muted"><?php echo date('d.m.Y H:i', strtotime($task['created_at'])); ?></small>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Назначена:</strong><br>
                                    <?php echo escape($task['assignee_name']); ?><br>
                                    <small class="text-muted">ID: <?php echo $task['assigned_to']; ?></small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                        <a href="<?php echo baseUrl('tasks'); ?>" class="btn btn-secondary">Назад к списку</a>
                        <div>
                            <?php if ($isAdmin): ?>
                            <a href="<?php echo baseUrl('tasks/delete/' . $task['id']); ?>" 
                               class="btn btn-danger me-2" 
                               onclick="return confirm('Вы уверены, что хотите удалить эту задачу?')">
                                Удалить
                            </a>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary">
                                <?php echo $canEditAll ? 'Сохранить изменения' : 'Обновить статус'; ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php if ($isAdmin): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">История изменений</h6>
            </div>
            <div class="card-body">
                <p class="text-muted mb-0">Функция истории изменений будет добавлена в следующей версии.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editForm');
    const statusSelect = document.getElementById('status');
    
    function updateStatusBadge() {
        const selectedValue = statusSelect.value;
        const badge = document.querySelector('.status-badge');
        
        if (!badge) return;
        
        badge.className = 'badge status-badge';
        switch(selectedValue) {
            case 'pending':
                badge.classList.add('bg-secondary');
                badge.textContent = 'Ожидает';
                break;
            case 'in_progress':
                badge.classList.add('bg-warning');
                badge.textContent = 'В работе';
                break;
            case 'completed':
                badge.classList.add('bg-success');
                badge.textContent = 'Завершена';
                break;
        }
    }
    
    const statusContainer = document.createElement('div');
    statusContainer.className = 'mb-3';
    statusContainer.innerHTML = '<label class="form-label">Предпросмотр статуса</label><br>';
    
    const badge = document.createElement('span');
    badge.className = 'badge status-badge';
    statusContainer.appendChild(badge);
    
    statusSelect.parentNode.insertBefore(statusContainer, statusSelect.nextSibling);
    
    updateStatusBadge();
    statusSelect.addEventListener('change', updateStatusBadge);
    
    form.addEventListener('submit', function(e) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(function(field) {
            if (!field.value.trim() && !field.disabled) {
                isValid = false;
                field.classList.add('is-invalid');
                
                let errorDiv = field.nextElementSibling;
                if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    field.parentNode.insertBefore(errorDiv, field.nextSibling);
                }
                errorDiv.textContent = 'Это поле обязательно для заполнения';
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        const deadlineInput = document.getElementById('deadline');
        if (deadlineInput && deadlineInput.value) {
            const deadlineDate = new Date(deadlineInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (deadlineDate < today) {
                isValid = false;
                deadlineInput.classList.add('is-invalid');
                
                let errorDiv = deadlineInput.nextElementSibling;
                if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    deadlineInput.parentNode.insertBefore(errorDiv, deadlineInput.nextSibling);
                }
                errorDiv.textContent = 'Дата выполнения не может быть в прошлом';
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Пожалуйста, исправьте ошибки в форме');
        }
    });
    
    form.addEventListener('input', function(e) {
        if (e.target.hasAttribute('required')) {
            e.target.classList.remove('is-invalid');
        }
    });
});
</script>

<?php
require VIEWS_PATH . '/layouts/footer.php';