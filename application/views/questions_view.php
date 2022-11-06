<div class="intro">
    <h1 class="semester"><?php echo $data[0]; ?></h1>
    <div class="intro__text"><?php echo $data[2]; ?></div>
</div>
<div class="quiz">
    
    <form action="" method="post">
        <?php echo $data[1]; ?>
        <div class="button-block">
        <input type="submit" value="Завершить" name="quiz-submit" class="send-btn">
        </div>
    </form>
</div>