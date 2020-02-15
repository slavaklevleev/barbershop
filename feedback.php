<?php
include('server.php');

require 'db.php';

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['email']);
    header("location: login.php");
}

//ПРОВЕРКА ДОСТУПА
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
    if (!($_SESSION['user-type'] == 2 || $_SESSION['user-type'] == 3 || $_SESSION['user-type'] == 4 || $_SESSION['user-type'] == 5)) {
        header("location: accessIsDenied.php");
    }
    $userAuthorized = true;
} else {
    header("location: /login.php");
    $userAuthorized = false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Обратная связь</title>
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
            <a href='index.php?logout='1''>Выход</a>
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
            <a href="index.php?logout='1'">Выход</a>
        </div>
    </div>
</header>

<body>
    <div class="wrapper" id="feedback">
        <div class="form">
            <h2>Обратная связь</h2>

            <form method="post" action="feedback.php">
                <div class="input-group">
                    <div class="select">
                        <select name="entry" id="slct" onchange="showInfo(this.value)" >
                            <option selected disabled>Выберете услугу</option>
                            <?php
                            $query_entries = "SELECT `entries`.`id`, `services`.`name` FROM `entries` LEFT JOIN `feedback` ON `entries`.`id` = `feedback`.`entry` JOIN `services` ON `entries`.`service` = `services`.`id` WHERE `feedback`.`entry` IS NULL && `entries`.`date` < CURRENT_DATE() && `entries`.`client` = '$_SESSION[id]'";
                            $results_entries = mysqli_query($db, $query_entries);

                            while ($result_entries = mysqli_fetch_array($results_entries)) {
                                echo "<option $selected value='{$result_entries['id']}' >{$result_entries['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div id="entryInfo"></div>

                <div class="input-group">
                    <p>Ваша оценка</p>

                    <div class="rating">
                        <input type="radio" name="rating-star" class="rating__control" id="rc1" value="1">
                        <input type="radio" name="rating-star" class="rating__control" id="rc2" value="2">
                        <input type="radio" name="rating-star" class="rating__control" id="rc3" value="3">
                        <input type="radio" name="rating-star" class="rating__control" id="rc4" value="4">
                        <input type="radio" name="rating-star" class="rating__control" id="rc5" value="5">

                        <label for="rc1" class="rating__item">
                            <span>★</span>
                            <span class="rating__label">1</span>
                        </label>
                        <label for="rc2" class="rating__item">
                            <span>★</span>
                            <span class="rating__label">2</span>
                        </label>
                        <label for="rc3" class="rating__item">
                            <span>★</span>
                            <span class="rating__label">3</span>
                        </label>
                        <label for="rc4" class="rating__item">
                            <span>★</span>
                            <span class="rating__label">4</span>
                        </label>
                        <label for="rc5" class="rating__item">
                            <span>★</span>
                            <span class="rating__label">5</span>
                        </label>
                    </div>
                </div>

                <div class="input-group">
                    <textarea rows="10" cols="30" name="review" placeholder="Комментарий"></textarea>
                </div>

                <div class="input-group">
                    <button type="submit" name="feedback">Отправить</button>
                </div>
            </form>
        </div>
    </div>
</body>

<script>
    function showInfo(val) {
        var ajax = new XMLHttpRequest();
        var method = "GET";
        var url = "data.php?q=" + val;
        var asynchronous = true;

        ajax.open(method, url, asynchronous);
        ajax.send();

        ajax.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("entryInfo").innerHTML = this.responseText;
            }
        }
    }
</script>

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