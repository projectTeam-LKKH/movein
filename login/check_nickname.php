<?php
include 'db_connect.php';

if(isset($_GET['username'])){
    $username = $_GET['username'];
    $sql = "SELECT * FROM User WHERE username='$username'";
    $result = mysqli_query($connect, $sql);

    if(mysqli_num_rows($result) > 0){
        echo "이미 사용 중인 닉네임입니다.";
    } else {
        echo "사용 가능한 닉네임입니다.";
    }
} else {
    echo "닉네임을 입력해주세요.";
}
?>
