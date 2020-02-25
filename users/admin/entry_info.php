<?php
session_start();
require '../../db.php';

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

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['email']);
    header("location: /login.php");
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
            <div class="entryInfoPage">
                <?php
                if (isset($_GET['id'])) {
                    $id = $_GET['id'];
                    $query_entry = "SELECT 
                        CONCAT(`clientData`.`name`, ' ', `clientData`.`last_name`) AS clientFN, 
                        `clientData`.tel AS clientTEL,
                        `services`.`name`, 
                        CONCAT(`barberData`.`name`, ' ', `barberData`.`last_name`) AS barberFN, 
                        `barberData`.tel AS barberTEL,
                        `entries`.`id`,
                        `entries`.`date`, 
                        `entries`.`time`, 
                        `entries`.`cost`, 
                        `entries`.`comment` 
                        FROM `entries` 
                        LEFT JOIN `users` AS `clientData` ON `entries`.`client` = `clientData`.`id` 
                        JOIN `users` AS `barberData` ON `entries`.`barber` = `barberData`.`id` 
                        JOIN `services` ON `entries`.`service` = `services`.`id` 
                        WHERE `entries`.`id` = {$_GET['id']}";

                    $results_entry = mysqli_query($db, $query_entry);
                    $result_entry = mysqli_fetch_array($results_entry);
                    echo "<h2> {$result_entry['name']} </h2>";
                }
                ?>
                <table>
                    <tr>
                        <th>ID услуги</th>
                        <td><?php echo $result_entry['id'] ?></td>
                    </tr>
                    <tr>
                        <th>Имя клиента</th>
                        <td><?php echo $result_entry['clientFN'] ?></td>
                    </tr>
                    <tr>
                        <th>Телефон клиента</th>
                        <td><?php echo $result_entry['clientTEL'] ?></td>
                    </tr>
                    <tr>
                        <th>Имя мастера</th>
                        <td><?php echo $result_entry['barberFN'] ?></td>
                    </tr>
                    <tr>
                        <th>Телефон мастера</th>
                        <td><?php echo $result_entry['barberTEL'] ?></td>
                    </tr>
                    <tr>
                        <th>Дата и время</th>
                        <td><?php echo date_format(date_create($result_entry['date']), "d.m.Y") . " " . date("H:i", strtotime($result_entry['time'])) ?></td>
                    </tr>
                    <tr>
                        <th>Стоимость</th>
                        <td><?php echo $result_entry['cost'] ?>₽</td>
                    </tr>
                    <tr>
                        <th>Комментарий</th>
                        <td><?php echo $result_entry['comment'] ?></td>
                    </tr>
                </table>
                <?php
                $query_visit = "SELECT `visit` FROM `entries` WHERE `id` = $id";
                $results_visit = mysqli_query($db, $query_visit) or die("Ошибка при начислении бонусов " . mysqli_error($db));
                list($visit) = mysqli_fetch_array($results_visit);
                if ($visit == 0) {
                    echo "<table class='visit'><tr>";
                    echo "<th colspan='2'>ПОСЕЩЕНИЕ</th>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td><a class='link' href='/users/admin/schedule.php?visit=2&&id={$_GET['id']}'>ДА</a></td>";
                    echo "<td><a class='link' href='/users/admin/schedule.php?visit=1&&id={$_GET['id']}'>НЕТ</a></td>";
                    echo "</tr></table>";
                    echo "<div><a href='#popup1'><button>Удалить запись</button></a></div>";
                }
                ?>

                <div><a href='schedule.php'><button>Вернуться к результатам</button></a></div>

                <div  class="overlay" id="popup1">
                    <div class="popup">
                        <h2>Подтвердите удаление</h2>
                        <a class="close" href="#">&times;</a>
                        <div class="popupContent">
                            <p>Вы действительно хотите удалить запись "<?php echo $result_entry['name']?>"?</p>
                            <div><a href="schedule.php?del_id=<?php echo $id ?>"><button>Да</button></a></div>
                        </div>
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