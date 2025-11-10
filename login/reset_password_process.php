<?php
include 'db_connect.php';

$userid = $_POST['userid'];
$email = $_POST['email'];
$new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

$sql = "SELECT * FROM User WHERE userid='$userid' AND email='$email'";
$result = mysqli_query($connect, $sql);

if(mysqli_num_rows($result) == 1){
    mysqli_query($connect, "UPDATE User SET password='$new_password' WHERE userid='$userid'");
    echo "<script>
        alert('비밀번호가 재설정되었습니다.');
        window.location.href = 'login.php';
    </script>";
} else {
    echo "<script>
        alert('일치하는 정보가 없습니다.');
        history.back(); // 이전 페이지(아이디 찾기 페이지)로 돌아감
    </script>";
}
?>


