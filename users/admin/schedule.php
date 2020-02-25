<?php
session_start();
require '../../db.php';

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

if (isset($_POST['show_date'])) {
    $date = date("Y-m-d", strtotime($_POST['entry_date']));
} else {
    $date = date("Y-m-d");
}

if (isset($_GET['visit'])) {
    $query_visit = "UPDATE `entries` SET `visit`={$_GET['visit']} WHERE `id`={$_GET['id']}";
    mysqli_query($db, $query_visit) or die("Ошибка при изменении статуса посещения салона" . mysqli_error($db));

    if ($_GET['visit'] == 2) {
        $query_bonuses = "SELECT `entries`.`cost`, `entries`.`accrual`, `privilege_system`.`percent`, `users`.`bonuses`, `users`.`id` 
                        FROM `entries` JOIN users ON `entries`.`client` = `users`.`id` 
                        JOIN `privilege_system` ON `users`.`level` = `privilege_system`.`level` 
                        WHERE `entries`.`id` = {$_GET['id']}";
        $results_bonuses = mysqli_query($db, $query_bonuses) or die("Ошибка при начислении бонусов " . mysqli_error($db));
        $bonusesInfo = mysqli_fetch_array($results_bonuses);
        if ($bonusesInfo['accrual'] == 0) {
            $bonuses = $bonusesInfo['cost'] * ($bonusesInfo['percent'] / 100) + $bonusesInfo['bonuses'];
            mysqli_query($db, "UPDATE `users` SET `bonuses`=$bonuses WHERE `id` = {$bonusesInfo['id']}") or die("Ошибка при начислении бонусов " . mysqli_error($db));
            mysqli_query($db, "UPDATE `entries` SET `accrual`='1' WHERE `id` = {$_GET['id']}") or die("Ошибка при изменении статуса о начислении бонусов " . mysqli_error($db));
            //Обновление уровня привелегий
            list($num_entries) = mysqli_fetch_array(mysqli_query($db, "SELECT COUNT(*) AS `num_entries` FROM `entries` WHERE `client` = $bonusesInfo[id] AND `date` > DATE_SUB(CURRENT_DATE(), INTERVAL 180 DAY) AND `visit` = '2'"));
            if ($num_entries > 1 && $num_entries <= 5) {
                $level = 2;
            } elseif ($num_entries > 5 && $num_entries <= 10) {
                $level = 3;
            } elseif ($num_entries > 10 && $num_entries <= 25) {
                $level = 4;
            } elseif ($num_entries > 25) {
                $level = 5;
            }
            mysqli_query($db, "UPDATE `users` SET `level`='$level' WHERE `id` = '{$bonusesInfo['id']}'") or die("Ошибка при изменении уровня" . mysqli_error($db));
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Расписание работы</title>
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
        <div class="content">
            <div class="schedule">
                <h2>Расписание работы</h2>
                <div>
                    <div>
                        <form action="schedule.php" method="POST" class="show_date">
                            <h4>Поиск по дате</h4>
                            <div class="input">
                                <input type="date" name="entry_date" placeholder="Дата dd.mm.yy">
                                <button name="show_date">Найти</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php
                if (isset($_GET['del_id'])) {
                    $query = mysqli_query($db, "DELETE FROM `entries` WHERE `id` = {$_GET['del_id']}");
                    if ($query) {
                        echo "<p class='admin' id='info'>Запись удалена</p>";
                    } else {
                        echo "<p class='error'>Произошла ошибка: " . mysqli_error($db) . "</p>";
                    }
                }
                ?>
                Расписписание на <?php echo date("d.m.Y", strtotime($date)) ?> <br>
                <div id="table-scroll" class="table-scroll">
                    <?php
                    $results_barber = mysqli_query($db, "SELECT `id`, `name`, `last_name` FROM `users` WHERE `user-type` = 3 ORDER BY `users`.`id` ASC");
                    ?>
                    <table id="main-table" class="main-table">
                        <thead>
                            <tr>
                                <th scope="col">Мастер</th>
                                <?php
                                mysqli_data_seek($results_barber, 0);
                                while ($result_barber = mysqli_fetch_array($results_barber)) {
                                    echo "<th>{$result_barber['name']} {$result_barber['last_name']}</th>";
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>Кол-во<br>клиентов</th>
                                <?php
                                mysqli_data_seek($results_barber, 0);
                                while ($result_barber = mysqli_fetch_array($results_barber)) {
                                    $query_entry = "SELECT * FROM `entries` WHERE `barber` = {$result_barber['id']} && `date` = '$date'";
                                    $results_num = mysqli_query($db, $query_entry);
                                    echo "<td>" . mysqli_num_rows($results_num) . "</td>";
                                }
                                ?>
                            </tr>
                            <tr>
                                <th>Клиенты</th>
                                <?php
                                mysqli_data_seek($results_barber, 0);
                                while ($result_barber = mysqli_fetch_array($results_barber)) {
                                    $query_entry = "SELECT `services`.`name`, `entries`.`time`, `entries`.`id`, `entries`.`visit` FROM `services` JOIN `entries` ON `entries`.`service` = `services`.`id` WHERE `entries`.`barber` = {$result_barber['id']} && `entries`.`date` = '$date' ORDER BY `entries`.`time` ASC";
                                    $results_entry = mysqli_query($db, $query_entry);
                                    echo "<td class = entry_col>";
                                    while ($result_entry = mysqli_fetch_array($results_entry)) {
                                        $cellID = "";
                                        if ($result_entry['visit'] == 2) {
                                            $cellID = "id = 'visited'";
                                        } elseif ($result_entry['visit'] == 1) {
                                            $cellID = "id = 'notvisited'";
                                        }

                                        echo "<a href='entry_info.php?id={$result_entry['id']}'><div class='entry_cell'" . $cellID . "><p>{$result_entry['name']}</p><p>" . date("H:i", strtotime($result_entry['time'])) . "</p></div></a>";
                                    }
                                    echo "</td>";
                                }
                                ?>

                            </tr>
                        </tbody>
                    </table>

                    <?php
                    if (isset($_POST['show_date'])) {
                        unset($_POST['show_date']);
                        echo "<div><a href='schedule.php'><button>Вернуться к сегодняшнему дню</button></a></div>";
                    }
                    ?>
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