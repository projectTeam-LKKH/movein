<?php
include 'db_connect.php';

if(isset($_GET['userid'])){
    $userid = $_GET['userid'];
    $sql = "SELECT * FROM User WHERE userid='$userid'";
    $result = mysqli_query($connect, $sql);

    if(mysqli_num_rows($result) > 0){
        echo "이미 사용 중인 아이디입니다.";
    } else {
        echo "사용 가능한 아이디입니다.";
    }
} else {
    echo "아이디를 입력해주세요.";
}
?>
