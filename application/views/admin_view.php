<!-- Страница панели администратора -->

<div class="tab">
  <button class="tablinks" id="defaultOpen">Добавление/Удаление/Редактирование пользователя</button>
  <button class="tablinks">Добавление/Удаление/Редактирование опроса</button>
</div>


<div class="tabcontent">
    <div class="usersearch-block">
        <div <?php if ($data[2]) echo "class='error-message error-userchange active'"; else echo "class='error-message error-userchange'" ?>>
            <?php if ($data[2]){
                echo $data[2];
            }?>
        </div>
        <h2>Поиск пользователя:</h2>
        <div class="admin-search">
            <form method="post">
                <input type="text" name="admin-usersearch" class="admin-input">
                <input type="submit" name="admin-usersubmit" value="Найти" class="admin-querybtn">
            </form>
        </div>

        <div class="admin-query">
            <?php if ($data[0]){
                foreach ($data[0] as $user){
                    echo $user;
                }
            }?>
        </div>
    </div>
    
    <div class="useradd-block">
        <h2>Добавление пользователя:</h2>
        <form method="post">
            <div class="add-field first-field">
                <label for="username">Имя: </label><input type="text" name="admin-username" id="username" class="admin-input" required>
            </div>
            <div class="add-field sec-field">
                <label for="login">Логин:</label><input type="text" name="admin-userlogin" id="login" class="admin-input" required>
            </div>
            <div class="add-field">
                <label for="password">Пароль:</label><input type="text" name="admin-userpass" id="password" class="admin-input" required>
            </div>
            <div class="add-field fourth-field">
                <label for="group">Группа:</label><input type="text" name="admin-usergroup" id="group" class="admin-input">
            </div>
            <div class="add-field">
                <label for="ifadmin">Права администратора:</label><input type="checkbox" name="admin-useradmin" id="ifadmin">
            </div>
            <input type="submit" name="admin-useraddsubmit" value="Добавить" class="admin-querybtn">
            <div class="error-message error-top">
                <?php if ($data[1]){
                    echo $data[1];
                }?>
            </div>
        </form>
    </div>
    
</div>

<div class="tabcontent">
    <div class="surveys">
        <h2>Текущие опросы</h2>
        <table class="surveys__table">
            <tr>
                <th>#</th>
                <th>Период</th>
                <th>Дата начала</th>
                <th>Дата окончания</th>
                <th>Действия</th>
            </tr>
            <?php
                foreach($data[3] as $i => $survey){
                    echo "<tr>
                            <td>".($i+1)."</td>
                            <td>".$survey[0]."</td>
                            <td>".$survey[1]."</td>
                            <td>".$survey[2]."</td>
                            <td>".$survey[3]."</td>
                        </tr>";
                }
            ?>
        </table>
    </div>
    <div class="survey-constructor">
        <button class="survey-constructor__create-btn">Создать опрос</button>
        <form method="post">
            <div class="survey-constructor__constructor-block">
                <div class="survey-constructor__title-inputs">
                    <div class="title-inputs__block"><label for="" >Период (заголовок):</label><input type="text" name="period" id=""></div>
                    <div class="title-inputs__block"><label for="" >Дата начала:</label><input type="date" name="startdate" id=""></div>
                    <div class="title-inputs__block"><label for="" >Дата окончания:</label><input type="date" name="enddate" id=""></div>
                    <div class="title-inputs__block">
                        <label for="" >Описание:</label><textarea name="description" id="" cols="50" rows="5"></textarea>
                        <label for="" >Описание по умолчанию:</label><input type="checkbox" name="desc-default" id="">
                    </div>
                </div>
                <button type="button" class="survey-constructor__create-btn add-block">Добавить блок</button>
                <input type="submit" name="newsurvey-submit" class="send-btn hidden" value="Создать">
            </div>
        </form>
    </div>
</div>