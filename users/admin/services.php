<?php
include('../../server.php');

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
    $userAuthorized = false;
    header('location: /login.php');
}

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['email']);
    header("location: login.php");
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
        <div class="content">
            <div class="services">
                <h2>Услуги парикмахерской</h2>
                <table>
                    <tr>
                        <th>Название</th>
                        <th>Описание</th>
                        <th>Время</th>
                        <th>Стоимость</th>
                        <th colspan="2">Дейтсвия</th>
                    </tr>
                    <?php

                    if (isset($_GET['confirm_del_id'])) { //проверяем, есть ли переменная
                        //удаляем строку из таблицы
                        $query = mysqli_query($db, "DELETE FROM `services` WHERE `ID` = {$_GET['confirm_del_id']}");
                        if ($query) {
                            echo "<p class='admin' id='info'>Услуга удалена</p>";
                        } else {
                            echo "<p class='error'>Произошла ошибка: " . mysqli_error($db) . "</p>";
                        }
                    }
                    $query = "SELECT `id`, `name`, `price`, `description`, TIME_FORMAT(`time`, '%H:%i') `time` FROM `services` WHERE 1";
                    $results = mysqli_query($db, $query);

                    while ($result = mysqli_fetch_array($results)) {
                        echo "<tr><td>{$result['name']}</td><td>{$result['description']}</td><td>{$result['time']}</td><td>{$result['price']}₽</td><td class='symbol'><a href='?edit_id={$result['id']}#popup1'>✍︎</a> </td><td class='symbol'><a href='?del_id={$result['id']}#popup1'>⤫</a></td><tr>";
                    }
                    ?>
                </table>

                <a href="?add_service=1#popup1"><button class="land_btn" name="add_service">Добавить услугу</button></a>
            </div>

            <div class="overlay" id="popup1">
                <div class="popup">
                    <a class="close" href="#">&times;</a>
                    <div class="popupContent">

                        <?php
                        if (isset($_GET['del_id'])) {
                            list($service_name) =  mysqli_fetch_array(mysqli_query($db, "SELECT `name` FROM `services` WHERE `id` = '$_GET[del_id]'"));
                            echo "<h2>Подтвердите удаление</h2>";
                            echo "<p>Вы действительно хотите удалить услугу \"$service_name\"?</p>";
                            echo "<div><a href='?confirm_del_id=$_GET[del_id]'><button>Да</button></a></div>";
                        } elseif (isset($_GET['edit_id'])) {
                            $query = mysqli_query($db, "SELECT `name`, `price`, `description`, `time` FROM `services` WHERE `id`={$_GET['edit_id']}");
                            $result = mysqli_fetch_array($query);
                            $name = $result["name"];
                            $price = $result["price"];
                            $description = $result["description"];
                            $time = $result["time"];
                        ?>
                            <form method="post" action="services.php">
                                <h2>Изменить услугу</h2>
                                <?php include('../../errors.php'); ?>
                                <input type="hidden" name="id" value="<?php echo $_GET['edit_id']; ?>">
                                <div class="input-group">
                                    <input type="text" name="name" placeholder="Название услуги" value="<?php echo $name; ?>">
                                </div>
                                <div class="input-group">
                                    <input type="text" name="price" placeholder="Стоимость" value="<?php echo $price; ?>">
                                </div>
                                <div class="input-group">
                                    <textarea rows="10" cols="50" name="description" placeholder="Описание"><?php echo $description; ?></textarea>
                                </div>
                                <div class="input-group">
                                    <input type="time" name="time" placeholder="Время hh:mm" value="<?php echo $time; ?>">
                                </div>
                                <div class="input-group">
                                    <button type="submit" name="edit_service">Сохранить</button>
                                </div>
                            </form>
                        <?php
                        } else { ?>
                            <form method="post" action="services.php">
                                <h2>Добавить услугу</h2>
                                <?php include('../../errors.php'); ?>
                                <input type="hidden" name="id" value="<?php echo $_GET['edit_id']; ?>">
                                <div class="input-group">
                                    <input type="text" name="name" placeholder="Название услуги" value="<?php echo $name; ?>">
                                </div>
                                <div class="input-group">
                                    <input type="text" name="price" placeholder="Стоимость" value="<?php echo $price; ?>">
                                </div>
                                <div class="input-group">
                                    <textarea rows="10" cols="50" name="description" placeholder="Описание"><?php echo $description; ?></textarea>
                                </div>
                                <div class="input-group">
                                    <input type="time" name="time" placeholder="Время hh:mm" value="<?php echo $time; ?>">
                                </div>
                                <div class="input-group">
                                    <button type="submit" name="add_service">Сохранить</button>
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