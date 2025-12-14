<?php

$title = 'Регистрация';
require VIEWS_PATH . '/layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Регистрация нового пользователя</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo baseUrl('register'); ?>" id="registerForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Имя *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo escape($_SESSION['old']['name'] ?? ''); ?>" 
                               required minlength="2" maxlength="100">
                        <div class="form-text">Ваше имя будет отображаться в системе</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo escape($_SESSION['old']['email'] ?? ''); ?>" 
                               required>
                        <div class="form-text">На этот email будут приходить уведомления</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль *</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               required minlength="6">
                        <div class="form-text">Пароль должен содержать минимум 6 символов</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Подтверждение пароля *</label>
                        <input type="password" class="form-control" id="confirm_password" 
                               name="confirm_password" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                Я согласен с <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">правилами использования</a>
                            </label>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                    </div>
                    <div class="mt-3 text-center">
                        <p>Уже есть аккаунт? <a href="<?php echo baseUrl('login'); ?>">Войти</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Правила использования</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Зарегистрировавшись в системе Task Manager, вы соглашаетесь:</p>
                <ol>
                    <li>Использовать систему только по назначению</li>
                    <li>Не передавать свои учетные данные третьим лицам</li>
                    <li>Соблюдать конфиденциальность рабочих задач</li>
                    <li>Не создавать задачи с оскорбительным содержанием</li>
                </ol>
                <p>Администратор оставляет за собой право блокировать пользователей, нарушающих эти правила.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    form.addEventListener('submit', function(e) {
        if (password.value !== confirmPassword.value) {
            e.preventDefault();
            confirmPassword.classList.add('is-invalid');
            
            let errorDiv = confirmPassword.nextElementSibling;
            if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                confirmPassword.parentNode.insertBefore(errorDiv, confirmPassword.nextSibling);
            }
            errorDiv.textContent = 'Пароли не совпадают';
            
            confirmPassword.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            confirmPassword.classList.remove('is-invalid');
        }
        
        if (password.value.length < 6) {
            e.preventDefault();
            password.classList.add('is-invalid');
            
            let errorDiv = password.nextElementSibling;
            if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                password.parentNode.insertBefore(errorDiv, password.nextSibling);
            }
            errorDiv.textContent = 'Пароль должен содержать минимум 6 символов';
        }
    });
    
    [password, confirmPassword].forEach(function(field) {
        field.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });
});
</script>

<?php
require VIEWS_PATH . '/layouts/footer.php';