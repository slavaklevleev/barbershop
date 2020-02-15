<?php
session_start();

// formating phone number
// форматирование телефонного номера
function phone_number_format($number)
{
    $number = preg_replace("/[^\d]/", "", $number);
    $length = strlen($number);

    if ($length == 10) {
        $number = preg_replace("/^1?(\d{3})(\d{3})(\d{2})(\d{2})$/", "+7 ($1) $2-$3-$4", $number);
    }

    return $number;
}

// initializing variables 
// инициализация переменных
$name = "";
$last_name = "";
$email = "";
$tel = "";

$barber = "";
$service = "";
$entry_date = "";
$entry_time = "";
$comment = "";

$debiting_bonuses = false;
$errors = array();

// connect to the database 
// подключение к БД
require 'db.php';

// REGISTER USER 
// РЕГИСТРАЦИЯ ПОЛЬЗОВАТЕЛЯ)
if (isset($_POST['reg_user'])) {
    // receive all input values from the form 
    // получение переменных из формы
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $last_name = mysqli_real_escape_string($db, $_POST['last_name']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $tel = mysqli_real_escape_string($db, $_POST['tel']);
    $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
    $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

    // form validation
    // проверка ввода данных
    if (empty($name)) {
        array_push($errors, "Введите имя");
    }
    if (empty($last_name)) {
        array_push($errors, "Введтите фамилию");
    }
    if (empty($email)) {
        array_push($errors, "Введите E-mail");
    }
    if (empty($tel)) {
        array_push($errors, "Введите телефон");
    } elseif (strlen($tel) != 10) {
        array_push($errors, "Номер телефона должен содеражть 10 цифр");
    }
    if (empty($password_1)) {
        array_push($errors, "Введите пароль");
    }
    if ($password_1 != $password_2) {
        array_push($errors, "Пароли не совпадают");
    }

    // check the database to make sure a user does not already exist with the same email
    // проверка существования пользователя с таким же email в БД
    $user_check_query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($db, $user_check_query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        if ($user['email'] === $email) {
            array_push($errors, "Такой E-mail уже зарегистрирован");
        }
    }

    // register user if there are no errors in the form
    // регистрация пользователя если нет ошибок
    if (count($errors) == 0) {
        $password = md5($password_1);
        $tel = phone_number_format($tel);

        $query = "INSERT INTO `users` (`user-type`, `name`, `last_name`, `email`, `tel`, `password`, `bonuses`, `level`) VALUES('1','$name', '$last_name', '$email', '$tel', '$password', '100', '1')";
        mysqli_query($db, $query) or die("Ошибка при авторизации" . mysqli_error($db));

        $query_id = "SELECT `id` FROM users WHERE email='$email'";
        $results_id = mysqli_query($db, $query_id);
        list($id) = mysqli_fetch_array($results_id);

        $_SESSION['id'] = $id;
        $_SESSION['email'] = $email;
        $_SESSION['success'] = "Вы авторизованы!";
        $_SESSION['name'] = $name;
        $_SESSION['tel'] = $tel;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['bonuses'] = 100;
        $_SESSION['level'] = 1;
        $_SESSION['user-type'] = 2;

        header('location: /index.php');
    }
}

// LOGIN USER
// Авторизация пользователя
if (isset($_POST['login_user'])) {
    // receive all input values from the form 
    // получение переменных из формы
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    // form validation
    // проверка ввода данных
    if (empty($email)) {
        array_push($errors, "Введите E-mail");
    }
    if (empty($password)) {
        array_push($errors, "Введите пароль");
    }

    // user authorization if there are no errors in the form
    // авторизация пользователя если нет ошибок
    if (count($errors) == 0) {
        $password = md5($password);
        $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
        $results = mysqli_query($db, $query);

        if (mysqli_num_rows($results) == 1) {
            $_SESSION['email'] = $email;
            $_SESSION['success'] = "Вы авторизованы!";

            $result = mysqli_fetch_array($results);
            $_SESSION['id'] = $result['id'];
            $_SESSION['name'] = $result['name'];
            $_SESSION['last_name'] = $result['last_name'];
            $_SESSION['tel'] = $result['tel'];
            $_SESSION['bonuses'] = $result['bonuses'];
            $_SESSION['level'] = $result['level'];
            $_SESSION['user-type'] = $result['user-type'];

            header('location: /index.php');
        } else {
            array_push($errors, "Неверный E-mail или пароль");
        }
    }
}

//Онлайн-запись от пользователя
if (isset($_POST['entry_user'])) {
    // receive all input values from the form 
    // получение переменных из формы
    $barber = mysqli_real_escape_string($db, $_POST['barber']);
    $service = mysqli_real_escape_string($db, $_POST['service']);
    $entry_date = mysqli_real_escape_string($db, $_POST['entry_date']);
    $entry_time = mysqli_real_escape_string($db, $_POST['entry_time']);

    // form validation
    // проверка ввода данных
    if ($barber == "0") {
        array_push($errors, "Выберете мастера");
    }
    if ($service == "0") {
        array_push($errors, "Выберете услугу");
    }
    if (empty($entry_date)) {
        array_push($errors, "Введите дату");
    } elseif (strtotime($entry_date) < strtotime("now")) {
        array_push($errors, "Введите будущую дату");
    }
    if (empty($entry_time)) {
        array_push($errors, "Введите время");
    } elseif (strtotime($entry_time) < strtotime("9:00:00") or strtotime($entry_time) > strtotime("20:00:00")) {
        array_push($errors, "Парикмахерская не работает в данное время");
    }

    if (count($errors) == 0) {
        $barber_data = mysqli_fetch_array(mysqli_query($db, "SELECT `id`, CONCAT(`name`, ' ', `last_name`) AS f_name FROM `users` WHERE `id` = $barber"));
        $client_data = mysqli_fetch_array(mysqli_query($db, "SELECT `id`, CONCAT(`name`, ' ', `last_name`) AS f_name FROM `users` WHERE `id` = {$_SESSION['id']}"));
        $service_data = mysqli_fetch_array(mysqli_query($db, "SELECT `id`, `name`, `price` FROM `services` WHERE `id` = $service"));

        if (isset($_POST['checkbox'])) {
            $min_amonut = $service_data['price'] * 0.25;
            if ($service_data['price'] - $_SESSION['bonuses'] < $min_amonut) {
                $cost = $min_amonut;
                $bonuses = $_SESSION['bonuses'] - ($service['price'] - $min_amonut);
            } else {
                $cost = $service_data['price'] - $_SESSION['bonuses'];
                $bonuses = 0;
            }
            $debiting_bonuses = true;
        } else {
            $cost = $service_data['price'];
            $bonuses = $_SESSION['bonuses'];
            $debiting_bonuses = false;
        } 
    }
}

//Подтверждение записи
if (isset($_POST['entry_confirm'])) {
    $client = mysqli_real_escape_string($db, $_POST['client']);
    $service = mysqli_real_escape_string($db, $_POST['service']);
    $barber = mysqli_real_escape_string($db, $_POST['barber']);
    $date = date("Y-m-d", strtotime(mysqli_real_escape_string($db, $_POST['date']))); 
    $time = mysqli_real_escape_string($db, $_POST['time']);
    $price = mysqli_real_escape_string($db, $_POST['price']);
    $cost = mysqli_real_escape_string($db, $_POST['cost']);
    $bonuses = mysqli_real_escape_string($db, $_POST['bonuses']);
    $comment = mysqli_real_escape_string($db, $_POST['comment']);
    $query = "INSERT INTO `entries` (`client`, `service`, `barber`, `date`, `time`, `cost`, `comment`, `visit`, `accrual`) 
        VALUES ('$client',  '$service', '$barber', '$date', '$time', '$cost', '$comment', '0', '0')";
    mysqli_query($db, $query) or die("Ошибка при записи " . mysqli_error($db));

    if ($_POST['debiting_bonuses']) {
        $min_amonut = $service_data['price'] * 0.25;
        if ($service_data['price'] - $_SESSION['bonuses'] < $min_amonut) {
            $cost = $min_amonut;
        } else {
            $cost = $service_data['price'] - $_SESSION['bonuses'];
        }
        $_SESSION['bonuses'] = $bonuses;
        $query_bonuses = "UPDATE `users` SET `bonuses`='{$_SESSION['bonuses']}' WHERE `id`= '{$_POST['client']}'";
        mysqli_query($db, $query_bonuses) or die("Ошибка при изменении баланса бонусов" . mysqli_error($db));
    }

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

    header('location: ' . $lkLink);
}
