<?php
$connect = mysqli_connect("localhost", "greenproject", "project2!", "greenproject");
// https://greenproject.dothome.co.kr/
if (!$connect) {
    die("DB 연결 실패: " . mysqli_connect_error());
}
?>
