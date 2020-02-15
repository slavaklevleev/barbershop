<?php
include('server.php');

require 'db.php';

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['email']);
    header("location: signin.php");
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
        header("location: /accessIsDenied.php");
    }
    $userAuthorized = true;
} else {
    header("location: /signin.php");
    $userAuthorized = false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Онлайн запись</title>
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
        <a class='dropbtn' href='/signin.php' class='right-side'>Войти</a>
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
                echo "<a href='/signin.php'>Войти</a>";
            }
            ?>
            <a href="index.php?logout='1'">Выход</a>
        </div>
    </div>
</header>

<body>
    <div class="wrapper">
        <div class="form">
            <h2>Онлайн запись</h2>
            <div><?php include('errors.php'); ?></div>
            <form method="post" action="entry.php/#popup1">
                <div class="input-group">
                    <div class="select">
                        <select name="barber" id="slct">
                            <?php
                            if ($barber == "0") {
                                $selected = "selected";
                            } else {
                                $selected = "";
                            }
                            echo "<option $selected value='0'>Выберете мастера</option>";

                            $query_barber = "SELECT `id`, `name`, `last_name` FROM `users` WHERE `user-type` = 3";
                            $results_barber = mysqli_query($db, $query_barber);

                            while ($result_barber = mysqli_fetch_array($results_barber)) {
                                if ($barber == $result_barber['id']) {
                                    $selected = "selected";
                                } else {
                                    $selected = "";
                                }
                                echo "<option $selected value='{$result_barber['id']}' >{$result_barber['name']} {$result_barber['last_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="input-group">
                    <div class="select">
                        <select name="service" id="slct">
                            <?php
                            echo $service; //НЕ РАБОТАЕТ ПОСЛЕ ОТПРАВКИ
                            if ($service == "0") {
                                $selected = "selected";
                            } else {
                                $selected = "";
                            }
                            echo "<option $selected value='0'>Выберете услугу</option>";

                            $query_service = "SELECT * FROM `services` WHERE 1";
                            $results_service = mysqli_query($db, $query_service);

                            while ($result_service = mysqli_fetch_array($results_service)) {
                                if ($service == $result_service['id']) {
                                    $selected = "selected";
                                } else {
                                    $selected = "";
                                }
                                echo "<option $selected value='{$result_service['id']}' >{$result_service['name']} - {$result_service['price']}₽</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="input-group">
                    <input type="date" name="entry_date" placeholder="Дата dd.mm.yy" value="<?php echo $entry_date; ?>">
                </div>
                <div class="input-group">
                    <input type="time" name="entry_time" placeholder="Время hh:tt" value="<?php echo $entry_time; ?>">
                </div>
                <div class="input-group">
                    <input type="text" name="comment" placeholder="Комментарий" value="<?php echo $comment; ?>">
                </div>
                <p>Вам доступно <?php echo $_SESSION['bonuses'] ?> бонусов</p>
                <div class="input-group" id="chkbox">
                    <?php 
                    if ($debiting_bonuses) {
                        $checked = "checked";
                    } else {
                        $checked = "";
                    }
                    ?>
                    <input id="checkbox" type="checkbox" name="checkbox" value="1" <?php echo $checked?>>
                    <p>Использовать бонусы?</p>
                </div>
                <div class="input-group">
                    <button type="submit" name="entry_user">Подтвердить</button>
                </div>
            </form>

            <div class='overlay' id='popup1'>
                <div class='popup'>
                    <h2>Подтвердите запись</h2>
                    <a class='close' href='#'>&times;</a>
                    <div class='popupContent'>
                        <table>
                            <tr>
                                <th>Мастер</th>
                                <td> <?php echo $barber_data['f_name'] ?> </td>
                            </tr>
                            <tr>
                                <th>Клиент</th>
                                <td> <?php echo $client_data['f_name'] ?> </td>
                            </tr>
                            <tr>
                                <th>Услуга</th>
                                <td><?php echo $service_data['name'] ?></td>
                            </tr>
                            <tr>
                                <th>Стоимость</th>
                                <td><?php echo "$cost" ?>₽</td>
                            </tr>
                            <tr>
                                <th>День</th>
                                <td><?php echo date("d.m.Y", strtotime($entry_date)) ?></td>
                            </tr>
                            <tr>
                                <th>Время</th>
                                <td> <?php echo "$entry_time" ?> </td>
                            </tr>
                            <tr>
                                <th>Комментарий</th>
                                <td> <?php echo "$comment" ?> </td>
                            </tr>
                        </table>
                        <form method="POST" action="entry.php/#popup1">
                            <input type="hidden" name="barber" value="<?php echo "$barber_data[id]" ?>">
                            <input type="hidden" name="client" value="<?php echo "$client_data[id]" ?>">
                            <input type="hidden" name="service" value="<?php echo "$service_data[id]" ?>">
                            <input type="hidden" name="price" value="<?php echo "$service_data[price]" ?>">
                            <input type="hidden" name="cost" value="<?php echo "$cost" ?>">
                            <input type="hidden" name="bonuses" value="<?php echo "$bonuses" ?>">
                            <input type="hidden" name="date" value="<?php echo "$entry_date" ?>">
                            <input type="hidden" name="time" value="<?php echo "$entry_time" ?>">
                            <input type="hidden" name="comment" value="<?php echo "$comment" ?>">
                            <input type="hidden" name="debiting_bonuses" value="<?php echo "$debiting_bonuses" ?>">
                            <button type="submit" name="entry_confirm">Все верно!</button>
                        </form>
                    </div>
                </div>
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