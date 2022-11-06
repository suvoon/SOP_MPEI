<!-- Часть навигационной панели при зарегистрированном пользователе -->
<a href="">
    <?php echo $_SESSION['username'];?>
</a>
<div class="user__popup">
    <div class="user__info">
        <div class="user__image">
            <?php 
            if ($login_data[1]){
                echo '<img alt="Profile Picture" src="data:image/jpg;base64,' . $login_data[1] . '" />';
            }
            else echo '<img src="/images/no-image.jpg" />';
            ?>
        </div>
        <div class="user__name">
            <?php echo $_SESSION['username'];?>
        </div>
    </div>
    <div class="user__profile">
        <a href="/settings"><div class="user__link">
            Настройки профиля
        </div></a>
        <a href="/main/logout"><div class="user__link">
            Выход
        </div></a>
        <?php if ($login_data[2]) echo $login_data[2]; ?>	
    </div>
</div>