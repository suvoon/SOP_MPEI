<!-- Страница авторизации -->
<h1 class="top-margin">Вход</h1>
<form method="post">
    <div class="auth">
        <div class="auth__field">
            <h2>Логин:</h2>
            <input type="text" name="auth-login" required>
        </div>
        <div class="auth__field">
            <h2>Пароль:</h2>
            <input type="password" name="auth-pass" required>
        </div>
        <div class="auth__field centered">
            <h3>Запомнить меня</h3>
            <input type="checkbox" name="auth-remember">
        </div>
        <input type="submit" value="Войти" name="auth-submit" class="auth__btn">
        <div class="error-message">
            <?php if ($data) echo $data[0]; ?>
        </div>
    </div>
</form>

