<?php
$title = 'Вход в систему';
require VIEWS_PATH . '/layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Вход в систему</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo baseUrl('login'); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo escape($_SESSION['old']['email'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Войти</button>
                    </div>
                    
                    <div class="mt-3 text-center">
                        <p>Нет аккаунта? <a href="<?php echo baseUrl('register'); ?>">Зарегистрироваться</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require VIEWS_PATH . '/layouts/footer.php';