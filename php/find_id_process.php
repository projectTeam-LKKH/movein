// find_id_process.php
<?php
include 'db_connect.php';

$username = $_POST['username'];
$email = $_POST['email'];

$sql = "SELECT userid FROM User WHERE username='$username' AND email='$email'";
$result = mysqli_query($connect, $sql);

if(mysqli_num_rows($result) > 0){
    $row = mysqli_fetch_assoc($result);
    echo "찾으신 아이디: " . $row['userid'];
} else {
    echo "일치하는 정보가 없습니다.";
}
?>
