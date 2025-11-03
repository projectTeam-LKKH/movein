<?php
include 'db_connect.php';

$userid = $_POST['userid'];
$password = $_POST['password'];

$sql = "SELECT * FROM User WHERE userid='$userid'";
$result = mysqli_query($connect, $sql);

if(mysqli_num_rows($result) == 1){
    $row = mysqli_fetch_assoc($result);
    if(password_verify($password, $row['password'])){
        // 로그인 성공
        mysqli_query($connect, "UPDATE User SET lastdate=NOW() WHERE userid='$userid'");
        echo "로그인 성공!";
    } else {
        echo "비밀번호가 틀렸습니다.";
    }
} else {
    echo "아이디가 존재하지 않습니다.";
}
?>
