<?php
$connect = mysqli_connect("localhost", "greenproject", "project2!", "greenproject");
if (!$connect) {
    die("DB 연결 실패: " . mysqli_connect_error());
}
mysqli_set_charset($connect, "utf8mb4"); // 한글, 이모지 지원
?>
