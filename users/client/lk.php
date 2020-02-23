<?php
include('../../server.php');
require '../../db.php';

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['email']);
    header("location: /login.php");
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
    if (!($_SESSION['user-type'] == 2 || $_SESSION['user-type'] == 5)) {
        header("location: /accessIsDenied.php");
    }
    $userAuthorized = true;
} else {
    $userAuthorized = false;
    header('location: /login.php');
}

if (isset($_GET['del_id_confirmed'])) {
    $query = mysqli_query($db, "DELETE FROM `entries` WHERE `id` = {$_GET['del_id_confirmed']}");
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
            <h2>Личный кабинет</h2>
            <div class="user_info">
                <table>
                    <tr>
                        <th>ID:</th>
                        <td><?php echo $_SESSION['id']; ?></td>
                    </tr>
                    <tr>
                        <th>Фамилия Имя:</th>
                        <td><?php echo $_SESSION['last_name']; ?> <?php echo $_SESSION['name']; ?></td>
                    </tr>
                    <tr>
                        <th>Почта</th>
                        <td><?php echo $_SESSION['email']; ?></td>
                    </tr>
                    <tr>
                        <th>Телефон</th>
                        <td><?php echo $_SESSION['tel']; ?></td>
                    </tr>
                    <tr>
                        <th>Баланс бонусов: </th>
                        <td><?php echo $_SESSION['bonuses']; ?></td>
                    </tr>
                    <tr>
                        <th>Уровень привелегий: </th>
                        <td><?php echo $_SESSION['level']; ?></td>
                    </tr>
                </table>

                <a href='?changeUserData=1#popup1'><button>Изменить</button></a>
            </div>

            <div class="entryUserLK">
                <h2>Ваши записи</h2>
                <?php
                $querry = "SELECT `entries`.`id`, `services`.`name`, CONCAT( `barberData`.`name`, ' ', `barberData`.`last_name` ) AS barberFN, `entries`.`date`, `entries`.`cost` FROM `entries` JOIN `users` AS barberData ON `entries`.`barber` = `barberData`.`id` JOIN `services` ON `entries`.`service` = `services`.`id` WHERE `entries`.`client` = {$_SESSION['id']} && `entries`.`date` >= CURRENT_DATE() ORDER BY `entries`.`date` DESC ";
                $results = mysqli_query($db, $querry);
                if (mysqli_fetch_array($results) == NULL) {
                    echo "<p>Записи на услуги отсутствуют</p>";
                }
                while ($result = mysqli_fetch_array($results)) {
                    echo "<div class='serviceInfo'>";
                    echo "<img src='/img/hairstyle1.png' alt=''>";
                    echo "<div class='data'>";
                    echo "<h3 class='item1'>{$result['name']}</h3>";
                    echo "<div class='item2' id='title'><p>Дата посещения:</p></div>";
                    echo "<div class='item3' id='data'><p>" . date_format(date_create($result['date']), "d.m.Y") . "</p></div>";
                    echo "<div class='item4' id='title'><p>Стоимость:</p></div>";
                    echo "<div class='item5' id='data'><p>{$result['cost']} ₽</p></div>";
                    echo "<div class='item6' id='title'><p>Мастер:</p></div>";
                    echo "<div class='item7' id='data'><p>{$result['barberFN']}</p></div>";
                    echo "</div>";

                    echo "<a href='?del_name=$result[name]&&del_id=$result[id]#popup1'><button class='land_btn'>Отменить запись</button></a>";
                    echo "</div>";
                }
                ?>
            </div>

            <div class="entryUserLK">
                <h2>История услуг</h2>
                <?php
                $querry = "SELECT `entries`.`id`, `services`.`name`, CONCAT( `barberData`.`name`, ' ', `barberData`.`last_name` ) AS barberFN, `entries`.`date`, `entries`.`cost`, ! ISNULL(`feedback`.`entry`) AS feedback FROM `entries` JOIN `users` AS barberData ON `entries`.`barber` = `barberData`.`id` LEFT JOIN `feedback` ON `entries`.`id` = `feedback`.`entry` JOIN `services` ON `entries`.`service` = `services`.`id` WHERE `entries`.`client` = {$_SESSION['id']} && `entries`.`date` < CURRENT_DATE() ORDER BY `entries`.`date` DESC";
                $results = mysqli_query($db, $querry);

                while ($result = mysqli_fetch_array($results)) {
                    echo "<div class='serviceInfo'>";
                    echo "<img src='/img/hairstyle1.png' alt=''>";
                    echo "<div class='data'>";
                    echo "<h3 class='item1'>{$result['name']}</h3>";
                    echo "<div class='item2' id='title'><p>Дата посещения:</p></div>";
                    echo "<div class='item3' id='data'><p>" . date_format(date_create($result['date']), "d.m.Y") . "</p></div>";
                    echo "<div class='item4' id='title'><p>Стоимость:</p></div>";
                    echo "<div class='item5' id='data'><p>{$result['cost']} ₽</p></div>";
                    echo "<div class='item6' id='title'><p>Мастер:</p></div>";
                    echo "<div class='item7' id='data'><p>{$result['barberFN']}</p></div>";
                    echo "</div>";
                    if ($result['feedback'] == 0) {
                        $disabled = "";
                        $feedback_page = "/feedback.php";
                    } else {
                        $disabled = "disabled";
                        $feedback_page = "#";
                    }
                    echo "<a href='$feedback_page'><button $disabled class='land_btn'>Оставить отзыв</button></a>";
                    echo "</div>";
                }
                ?>
            </div>

            <div class="overlay" id="popup1">
                <div class="popup">

                    <a class="close" href="#">&times;</a>
                    <div class="popupContent">
                        <?php
                        if (isset($_GET['del_id'])) {
                            echo "<h2>Подтвердите удаление</h2>";
                            echo "<p>Вы действительно хотите удалить запись \"$_GET[del_name]\"?</p>";
                            echo "<div><a href='lk.php?del_id_confirmed=$_GET[del_id]'><button>Да</button></a></div>";
                        } elseif (isset($_GET['changeUserData'])) { ?>
                            <h2>Изменение профиля</h2>
                            <?php include('../../errors.php'); ?>
                            <form method="post" action="lk.php">
                                <div class="row">
                                    <div class="label">Имя:</div>
                                    <div class="input">
                                        <div class="input-group">
                                            <input type="text" name="name" placeholder="Имя" value="<?php echo $_SESSION['name']; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="label">Фамилия:</div>
                                    <div class="input">
                                        <div class="input-group">
                                            <input type="text" name="last_name" placeholder="Фамилия" value="<?php echo $_SESSION['last_name']; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="label">Телефон:</div>
                                    <div class="input">
                                        <div class="input-group">
                                            <input type="text" name="tel" id="tel" placeholder="Телефон" value="<?php echo $_SESSION['tel']; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="label">Email:</div>
                                    <div class="input">
                                        <div class="input-group">
                                            <input type="email" name="email" placeholder="E-mail" value="<?php echo $_SESSION['email']; ?>">
                                        </div>
                                    </div>
                                </div>




                                <div class="input-group">
                                    <button type="submit" name="changeUserDataConfirmed">Сохранить</button>
                                </div>
                            </form>
                        <?php
                        }
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/imask"></script>
    <script>
        var element = document.getElementById('tel');
        var maskOptions = {
            mask: '+7 (000) 000-00-00',
            lazy: false
        }
        var mask = new IMask(element, maskOptions);
    </script>
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