<?php
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['email']);
    header("location: login.php");
}

//ПРОВЕРКА ДОСТУПА
if (isset($_SESSION['email'])) {
    switch ($_SESSION['user-type']) {
        case '2':
            $lkLink = "/users/client/lk.php";
            break;
        case '3':
            $lkLink = "/users/barber/lk.php";
            break;
        case '4':
            $lkLink = "/users/admin/lk.php";
            break;
        case '5':
            $lkLink = "/users/superadmin/lk.php";
            break;
        default:
            break;
    }
    if (!($_SESSION['user-type'] == 4 || $_SESSION['user-type'] == 5)) {
        header("location: /accessIsDenied.php");
    }
    $userAuthorized = true;
} else {
    $userAuthorized = false;
    header('location: /login.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Личный кабинет</title>
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

    <a class="right-side" href="?logout='1'">Выход</a>

    <div class="dropdown" id="small">
        <a class="dropbtn" id="logo" href="#">Парикмахерская</a>
        <div class="dropdown-content" id="small">
            <a href="http://barbershop.com">Главная страница</a>
            <a href="http://barbershop.com/#services">Услуги</a>
            <a href="http://barbershop.com/#news">Новости</a>
            <a href="http://barbershop.com/#about">О нас</a>
            <a href="#"><?php echo $_SESSION['name']; ?></a>
            <a href="?logout='1'">Выход</a>
        </div>
    </div>
</header>

<body>
    <div class="wrapper">
        <div class="content">
            <h2>Личный кабинет администратора</h2>
            <p>
                Фамилия имя: <?php echo $_SESSION['last_name']; ?> <?php echo $_SESSION['name']; ?>
            </p>
            <p>
                Сегодня: <?php echo date("d.m.Y") ?>
            </p>

            <div class="menuAdmin">
                <div><a href="schedule.php"><button class="land_btn">Расписание</button></a></div>
                <div><a href="services.php"><button class="land_btn">Услуги салона</button></a></div>
                <div><a href="add_article.php"><button class="land_btn">Создать новость</button></a></div>
                <div><a href="clients.php"><button class="land_btn">Данные клиентов</button></a></div>
                <div><a href="workers.php"><button class="land_btn">Данные сотрудников</button></a></div>
                <div><a href="/lk/admin/entry.php"><button class="land_btn">Запись клиента</button></a></div>
                <div><a href="feedback.php"><button class="land_btn">Отзывы</button></a></div>
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