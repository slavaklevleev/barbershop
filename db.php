<?php 
$db = mysqli_connect('localhost', 'admin', 'qwer1234', 'barbershop');
if (!$db) {
    echo "Ошибка при подключении к базе данных" . mysqli_connect_error();
    exit;
}
?>