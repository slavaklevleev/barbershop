<?php include('server.php') ?>

<!DOCTYPE html>
<html>

<head>
    <title>Регистрация</title>
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

    <a class="right-side" href="/signin.php">Войти</a>

    <div class="dropdown" id="small">
        <a class="dropbtn" id="logo" href="#">Парикмахерская</a>
        <div class="dropdown-content" id="small">
            <a href="http://barbershop.com">Главная страница</a>
            <a href="http://barbershop.com/#services">Услуги</a>
            <a href="http://barbershop.com/#news">Новости</a>
            <a href="http://barbershop.com/#about">О нас</a>
            <a href="/signin.php">Войти</a>
        </div>
    </div>
</header>

<body>
    <div class="wrapper">
        <div class="form">
            <h2>Регистрация</h2>
            <?php include('errors.php'); ?>
            <form method="post" action="singup.php">
                <div class="input-group">
                    <input type="text" name="name" placeholder="Имя" value="<?php echo $name; ?>">
                </div>
                <div class="input-group">
                    <input type="text" name="last_name" placeholder="Фамилия" value="<?php echo $last_name; ?>">
                </div>
                <div class="input-group">
                    <input type="tel" name="tel" placeholder="Телефон (без кода страны)" maxlength="10" value="<?php echo $tel; ?>">
                </div>
                <div class="input-group">
                    <input type="email" name="email" placeholder="E-mail" value="<?php echo $email; ?>">
                </div>
                <div class="input-group">
                    <input type="password" name="password_1" placeholder="Пароль">
                </div>
                <div class="input-group">
                    <input type="password" name="password_2" placeholder="Повторите пароль">
                </div>
                <div class="input-group">
                    <button type="submit" name="reg_user">Зарегистрировать</button>
                </div>
            </form>
            <p>Уже зарегистрированы? <a class="link" href="signin.php">Войти</a></p>
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