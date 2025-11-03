// reset_password_process.php
<?php
include 'db_connect.php';

$userid = $_POST['userid'];
$email = $_POST['email'];
$new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

$sql = "SELECT * FROM User WHERE userid='$userid' AND email='$email'";
$result = mysqli_query($connect, $sql);

if(mysqli_num_rows($result) == 1){
    mysqli_query($connect, "UPDATE User SET password='$new_password' WHERE userid='$userid'");
    echo "비밀번호가 재설정되었습니다.";
} else {
    echo "아이디와 이메일이 일치하지 않습니다.";
}
?>
