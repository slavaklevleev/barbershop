<?php
session_start();
require '../../db.php';

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['email']);
    header("location: login.php");
}

if (isset($_SESSION['email'])) {
    switch ($_SESSION['user-type']) {
        case '2':
            $lkLink = "/lk/client/lk.php";
            break;
        case '3':
            $lkLink = "/lk/barber/lk.php";
            break;
        case '4':
            $lkLink = "/lk/admin/lk.php";
            break;
        case '5':
            $lkLink = "/lk/superadmin/lk.php";
            break;
        default:
            break;
    }
    if (!($_SESSION['user-type'] == 4 || $_SESSION['user-type'] == 5)) {
        header("location: /accessIsDenied.php");
    }
    $userAuthorized = true;
} else {
    header('location: /login.php');
    $userAuthorized = false;
}

if (isset($_POST['publish'])) {
    $title = mysqli_real_escape_string($db, $_POST['Header']);
    $text = mysqli_real_escape_string($db, $_POST['text']);

    $query = "INSERT INTO `news`(`header`, `text`) VALUES ('$title', '$text')";
    mysqli_query($db, $query) or die("Ошибка при авторизации" . mysqli_error($db));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Услуги</title>
    <link href="/css/style.css" rel="stylesheet">
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
    <?php
    if ($userAuthorized) {
        echo "<div class='dropdown'>
        <a class='dropbtn' href='#' class='right-side'> {$_SESSION['name']}</a>
        <div class='dropdown-content'>
            <a href='$lkLink'>Личный кабинет</a>
            <a href='?logout='1''>Выход</a>
        </div>
    </div>";
    } else {
        echo "<div class='dropdown'>
        <a class='dropbtn' href='/login.php' class='right-side'>Войти</a>
    </div>";
    }
    ?>

    <div class="dropdown" id="small">
        <a class="dropbtn" id="logo" href="#">Парикмахерская</a>
        <div class="dropdown-content" id="small">
            <a href="http://barbershop.com">Главная страница</a>
            <a href="http://barbershop.com/#services">Услуги</a>
            <a href="http://barbershop.com/#news">Новости</a>
            <a href="http://barbershop.com/#about">О нас</a>
            <?php
            if ($userAuthorized) {
                echo "<a href='$lkLink'>{$_SESSION['name']}</a>";
            } else {
                echo "<a href='/login.php'>Войти</a>";
            }
            ?>
            <a href="?logout='1'">Выход</a>
        </div>
    </div>
</header>

<body>
    <div class="wrapper">
        <div class="form">
            <h2>Создать новость</h2>

            <form method="post" action="add_article.php">
                <div class="input-group">
                    <input type="text" name="Header" placeholder="Заголовок">
                </div>
                <div class="input-group">
                    <textarea rows="10" cols="45" name="text" placeholder="Текст статьи"></textarea>
                </div>
                <div class="input-group">
                    <button type="submit" name="publish">Опубликовать</button>
                </div>
            </form>

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
