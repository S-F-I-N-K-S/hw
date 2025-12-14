<?php
$title = 'Задачи';
require VIEWS_PATH . '/layouts/header.php';
?>
<h2 class="mb-4"><?php echo Session::isAdmin() ? 'Все задачи' : 'Мои задачи'; ?></h2>
<?php if (Session::isAdmin()): ?>
<a href="<?php echo baseUrl('tasks/create'); ?>" class="btn btn-primary mb-3">
    <i class="bi bi-plus-circle"></i> Создать новую задачу
</a>
<?php endif; ?>
<?php if (empty($tasks)): ?>
<div class="alert alert-info">
    <?php echo Session::isAdmin() ? 'Задачи еще не созданы' : 'Вам не назначены задачи'; ?>
</div>
<?php else: ?>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Название</th>
                <th>Описание</th>
                <th>Назначена</th>
                <th>Статус</th>
                <th>Срок</th>
                <th>Дата создания</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?php echo $task['id']; ?></td>
                <td><?php echo escape($task['title']); ?></td>
                <td><?php echo nl2br(escape(substr($task['description'], 0, 100))); ?></td>
                <td>
                    <?php if (Session::isAdmin()): ?>
                        <?php echo escape($task['assignee_name']); ?>
                    <?php else: ?>
                        <?php echo escape($task['creator_name']); ?>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="badge bg-<?php 
                        switch ($task['status']) {
                            case STATUS_COMPLETED: echo 'success'; break;
                            case STATUS_IN_PROGRESS: echo 'warning'; break;
                            default: echo 'secondary';
                        }
                    ?>">
                        <?php 
                        $statuses = [
                            STATUS_PENDING => 'Ожидает',
                            STATUS_IN_PROGRESS => 'В работе',
                            STATUS_COMPLETED => 'Завершена'
                        ];
                        echo $statuses[$task['status']] ?? $task['status'];
                        ?>
                    </span>
                </td>
                <td>
                    <?php if ($task['deadline']): ?>
                        <?php echo date('d.m.Y', strtotime($task['deadline'])); ?>
                        <?php if (strtotime($task['deadline']) < time() && $task['status'] !== STATUS_COMPLETED): ?>
                            <span class="badge bg-danger">Просрочено</span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="text-muted">Не указан</span>
                    <?php endif; ?>
                </td>
                <td><?php echo date('d.m.Y H:i', strtotime($task['created_at'])); ?></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="<?php echo baseUrl('tasks/edit/' . $task['id']); ?>" 
                           class="btn btn-warning">
                            Редактировать
                        </a>
                        <?php if (Session::isAdmin()): ?>
                        <form method="POST" action="<?php echo baseUrl('tasks/delete/' . $task['id']); ?>" 
                              class="d-inline" onsubmit="return confirm('Удалить задачу?');">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                            <button type="submit" class="btn btn-danger">Удалить</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php
require VIEWS_PATH . '/layouts/footer.php';