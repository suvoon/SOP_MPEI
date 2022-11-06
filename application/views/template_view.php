<!-- Сообщаем браузеру как стоит обрабатывать эту страницу -->
<!DOCTYPE html>
<!-- Оболочка документа, указываем язык содержимого -->
<html lang="ru">
	<!-- Заголовок страницы, контейнер для других важных данных (не отображается) -->
	<head>
		<!-- Заголовок страницы в браузере -->
		<title>СОП МЭИ</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
		<link rel="stylesheet" href="/css/style.css" />
		<link rel="icon" type="image/png" href="/images/favicon-32x32.png">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300&family=Raleway&display=swap" rel="stylesheet">
		<script src="/js/script.js"></script>
		<!-- Кодировка страницы -->
		<meta charset="UTF-8">
	</head>
	<!-- Отображаемое тело страницы -->
	<body>
		<div class="wrapper">
		<nav class="menu-bar">
				<div class="menu-bar__container container">
					<ul class="menu-bar__list">
						<li class="logo"><a href="/main"><img src="/images/mpei.jpg" width="40" height="40" alt="logo"> <span>СОП МЭИ</span></a></li>
						<li><a href="/surveys">Опрос</a></li>
						<li><a href="">Результаты</a></li>
						<li><a href="">Форум</a></li>
						<li class="user">
							<?php include 'application/views/'.$login_data[0]; ?>
						</li>
					</ul>
				</div>
			</nav>
			<div class="content">
				<div class="container">
					<?php include 'application/views/'.$content_view; ?>
				</div>
			</div>
			<footer class="footer">
				<div class='container'>
					<div class="footer__row">
						<div class="footer__text">СОП. МЭИ, 2022.</div>
					</div>
				</div>
			</footer>
		</div>
	</body>
</html>