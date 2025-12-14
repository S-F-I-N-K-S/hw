<?php
$title = 'Создание задачи';
require VIEWS_PATH . '/layouts/header.php';
?>

<h2 class="mb-4">Создание новой задачи</h2>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Данные задачи</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo baseUrl('tasks/store'); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    <div class="mb-3">
                        <label for="title" class="form-label">Название задачи *</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo escape($_SESSION['old']['title'] ?? ''); ?>" 
                               required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Описание *</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="5" required><?php echo escape($_SESSION['old']['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="assigned_to" class="form-label">Назначить работнику *</label>
                            <select class="form-select" id="assigned_to" name="assigned_to" required>
                                <option value="">Выберите работника</option>
                                <?php foreach ($workers as $worker): ?>
                                <option value="<?php echo $worker['id']; ?>"
                                    <?php echo (($_SESSION['old']['assigned_to'] ?? '') == $worker['id']) ? 'selected' : ''; ?>>
                                    <?php echo escape($worker['name']); ?> (<?php echo escape($worker['email']); ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="deadline" class="form-label">Срок выполнения</label>
                            <input type="date" class="form-control" id="deadline" name="deadline"
                                   value="<?php echo escape($_SESSION['old']['deadline'] ?? ''); ?>"
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo baseUrl('tasks'); ?>" class="btn btn-secondary me-md-2">Отмена</a>
                        <button type="submit" class="btn btn-primary">Создать задачу</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require VIEWS_PATH . '/layouts/footer.php';