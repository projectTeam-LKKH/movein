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
        echo "<script>
                alert('로그인 성공!');
                window.location.href = '../index.html';
            </script>";
    } else {
        echo "<script>
            alert('비밀번호가 틀렸습니다.');
            history.back(); // 이전 페이지(아이디 찾기 페이지)로 돌아감
        </script>";
    }
} else {
    echo "<script>
        alert('아이디가 존재하지 않습니다.');
        history.back(); // 이전 페이지(아이디 찾기 페이지)로 돌아감
    </script>";
}
?>
