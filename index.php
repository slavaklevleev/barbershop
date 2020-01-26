<?php
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['email']);
    header("location: /signin.php");
}

require 'db.php';

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
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Парикмахерская</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat+Alternates&display=swap" rel="stylesheet">
</head>

<header>
    <a href="http://barbershop.com" class="logo">Парикмахерская</a>
    <nav>
        <div class="menu">
            <ul>
                <li><a href="http://barbershop.com/#land">Услуги</a></li>
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
        <div class="start-pic"> <img src="/img/start-pic.jpg" alt=""></div>

        <div class="content">
            <div class="welcome-words">
                <h2>Парикмахерская</h2>
                <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
                <img src="/img/arrow-down.png" alt="">
            </div>

            <div class="services" id="land">
                <h2>Наши услуги</h2>
                <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
                <div class="grid">
                    <?php
                    $query = "SELECT `name`, `price` FROM `services` WHERE 1 LIMIT 10";
                    $query_results = mysqli_query($db, $query);

                    while ($query_result = mysqli_fetch_array($query_results)) {
                        echo "<div class='service'>";
                        echo "<div class='name'>{$query_result['name']}</div>";
                        echo "<div class='price'>{$query_result['price']}₽</div>";
                        echo "</div>";
                    }
                    ?>
                </div>
                <a href="entry.php"><button>Онлайн запись</button></a>
            </div>

            <div class="feedback" id="about">
                <div class="container">
                    <h2>Отзывы наших клиентов</h2>

                    <?php
                    $query = "SELECT `f`.`review`, `users`.`name`
                    FROM `feedback` AS `f`
                    JOIN `entries` ON `f`.`entry` = `entries`.`id`
                    JOIN `users` ON `entries`.`client` = `users`.`id`
                    JOIN (SELECT ((SELECT MIN(`id`) - 1 FROM `feedback`) + RAND() * (SELECT MAX(`id`) - MIN(`id`) 
                                   FROM `feedback`)) AS `id`) AS `rand`
                    WHERE `f`.`id` >= `rand`.`id` && `f`.`verified` = 1
                    ORDER BY `f`.`id`
                    LIMIT 3";
                    $query_results = mysqli_query($db, $query);

                    while ($query_result = mysqli_fetch_array($query_results)) {
                        echo "<blockquote>";
                        echo "<p> <span>{$query_result['review']}</span> </p>";
                        echo "— <cite>{$query_result['name']}</cite>";
                        echo "</blockquote>";
                    }
                    ?>
                    <a href="feedback.php"><button class="land_btn">Оставьте ваш отзыв!</button></a>
                </div>
            </div>

            <div class="news" id="news">
                <h2>Новости и публикации</h2>
                <div class="flexbox">
                    <div class="article">

                        <?php
                        $query = "SELECT `header` FROM `news` WHERE 1";
                        $results = mysqli_query($db, $query);
                        while ($result = mysqli_fetch_array($results)) {
                            echo "<a href=''><img src='/img/1-news.png'></a>";
                            echo "<p>{$result['header']}</p>";
                        }
                        ?>

                    </div>
                </div>
                <a href="news.php"><button class="land_btn">Все новости</button></a>
            </div>

            <div class="barbers">
                <div class="container">
                    <h2>Наши мастера</h2>
                    <?php
                    $query = "SELECT
                    `users`.`name`,
                    CEIL(AVG(`feedback`.`mark`)) as AVG
                FROM
                    `users`
                    JOIN `entries` ON `entries`.`barber` = `users`.`id`
                    JOIN `feedback`ON `feedback`.`entry` = `entries`.`id`
                WHERE
                    `users`.`user-type` = 3
                    GROUP BY `users`.`id`
                    ORDER BY RAND()
                    LIMIT 3";
                    $results = mysqli_query($db, $query);
                    ?>
                    <div class="flexbox">
                        <?php
                        $i=1;
                        while ($result = mysqli_fetch_array($results)) {
                            echo "<div class='barber' id='img$i'>";
                            echo "<a href=''><img src='/img/".$i."-barber.png' alt=''></a>";
                            echo "<a href='#'>{$result['name']}</a>";
                            echo "<p class = 'stars'>";
                            $count = 0;
                            while ($count < 5) {
                                if ($count < $result['AVG']) {
                                    echo "★ ";
                                } else {
                                    echo "☆ ";
                                }
                                $count = $count + 1;
                            }
                            echo "</p>";
                            echo "</div>";
                            $i = $i +1;
                        }
                        ?>
                    </div>
                    <a href="barbers.php"><button class="land_btn">Все мастера</button></a>
                </div>
            </div>

            <div class="examples">
                <h2>Наши работы</h2>

                <div class="grid">
                    <div class="example">
                        <img src="/img/hairstyle1.png" alt="">
                        <p>Lorem ipsum dolor</p>
                        <p class="icon" id="time">60 минут</p>
                        <p class="icon" id="cost">1500₽</p>
                    </div>
                    <div class="example">
                        <img src="/img/hairstyle2.png" alt="">
                        <p>Lorem ipsum dolor</p>
                        <p class="icon" id="time">60 минут</p>
                        <p class="icon" id="cost">1500₽</p>
                    </div>
                    <div class="example">
                        <img src="/img/hairstyle3.png" alt="">
                        <p>Lorem ipsum dolor</p>
                        <p class="icon" id="time">60 минут</p>
                        <p class="icon" id="cost">1500₽</p>
                    </div>
                    <div class="example">
                        <img src="/img/hairstyle4.png" alt="">
                        <p>Lorem ipsum dolor</p>
                        <p class="icon" id="time">60 минут</p>
                        <p class="icon" id="cost">1500₽</p>
                    </div>
                    <div class="example">
                        <img src="/img/hairstyle5.png" alt="">
                        <p>Lorem ipsum dolor</p>
                        <p class="icon" id="time">60 минут</p>
                        <p class="icon" id="cost">1500₽</p>
                    </div>
                    <div class="example">
                        <img src="/img/hairstyle6.png" alt="">
                        <p>Lorem ipsum dolor</p>
                        <p class="icon" id="time">60 минут</p>
                        <p class="icon" id="cost">1500₽</p>
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
            <a href="/entry.php">Онлайн запись</a>
        </div>
        <div>
            <a href="/feedback.php">Оставить отзыв</a>
        </div>
        <div>
            <a href="/news.php">Новости</a>
        </div>
    </div>
</footer>

</html>