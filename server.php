<?php
session_start();

// formating phone number
// форматирование телефонного номера
function phone_number_format($number) {
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
