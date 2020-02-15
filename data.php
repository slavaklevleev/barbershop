<?php
session_start();

$db = mysqli_connect('localhost', 'admin', 'qwer1234', 'barbershop');

if (!$db) {
    echo "Ошибка при подключении к базе данных" . mysqli_connect_error();
    exit;
}

$query_entries = "SELECT `barber`, `date`, `cost` FROM `entries` WHERE `id` = ?";

$stmt = $db->prepare($query_entries);
$stmt->bind_param("s", $_GET['q']);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($barber, $date, $cost);
$stmt->fetch();
$stmt->close();

$query_barber = "SELECT * FROM `users` WHERE `id` = '$barber'";
$results_barber = mysqli_query($db, $query_barber);
$result_barber = mysqli_fetch_array($results_barber);

echo "Мастер: $result_barber[name]<br>Дата посещения: ".date("d.m.Y", strtotime($date))."<br>Стоимость: $cost ₽";

?>