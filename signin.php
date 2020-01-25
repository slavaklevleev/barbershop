<?php include('server.php') ?>

<!DOCTYPE html>
<html>

<head>
    <title>Авторизация</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat+Alternates&display=swap" rel="stylesheet">
</head>

<header>
    <a href="http://barbershop.com" class="logo">Парикмахерская</a>
    <nav>
        <div class="menu">
            <ul>
                <li><a href="http://barbershop.com/#services">Услуги</a></li>
                <li><a href="http://barbershop.com/#news">Новости</a></li>
                <li><a href="http://barbershop.com/#about">О нас</a></li>
            </ul>
        </div>
    </nav>

    <a class="right-side" href="/signup.php">Регистриция</a>

    <div class="dropdown" id="small">
        <a class="dropbtn" id="logo" href="#">Парикмахерская</a>
        <div class="dropdown-content" id="small">
            <a href="http://barbershop.com">Главная страница</a>
            <a href="http://barbershop.com/#services">Услуги</a>
            <a href="http://barbershop.com/#news">Новости</a>
            <a href="http://barbershop.com/#about">О нас</a>
            <a href="/registration.php">Регистриция</a>
        </div>
    </div>
</header>

<body>
    <div class="wrapper">
        <div class="form">
            <div>
                <h2>Авторизация</h2>
            </div>
            <div><?php include('errors.php'); ?></div>
            <div>
                <form method="post" action="signin.php">
                    <div class="input-group">
                        <input type="email" name="email" placeholder="E-mail">
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" placeholder="Пароль">
                    </div>
                    <div class="input-group">
                        <button type="submit" name="login_user">Войти</button>
                    </div>
                </form>
            </div>
            <div>
                <p>
                    Еще не зарегистрированы? <a class="link" href="signup.php">Регистриция</a><br>
                    <a class="link" href="signup.php">Забыли пароль?</a>
                </p>
            </div>
        </div>
    </div>
</body>

<footer>
    <div class="contacts">
        <div>
            <p id="phone">+7 (495) 875-63-01</p>
        </div>
        <div>
            <p id="email">support@barbershop.com</p>
        </div>
        <div>
            <p id="address">Москва, Пр-т Вернадского, д. 78</p>
        </div>
    </div>

    <div class="menu">
        <div>
            <a href="#">Онлайн запись</a>
        </div>
        <div>
            <a href="#">Оставить отзыв</a>
        </div>
        <div>
            <a href="#">Новости</a>
        </div>
    </div>
</footer>

</html>