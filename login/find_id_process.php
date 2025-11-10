<?php
include 'db_connect.php';

$username = $_POST['username'];
$email = $_POST['email'];

// SQL 인젝션 방지 (기본적인 보안 조치)
$username = mysqli_real_escape_string($connect, $username);
$email = mysqli_real_escape_string($connect, $email);

$sql = "SELECT userid FROM User WHERE username='$username' AND email='$email'";
$result = mysqli_query($connect, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $userid = $row['userid'];
    echo "<script>
        alert('찾으신 아이디: {$userid}');
        window.location.href = 'login.php'; // 아이디 확인 후 로그인 페이지로 이동
    </script>";
} else {
    echo "<script>
        alert('일치하는 정보가 없습니다.');
        history.back(); // 이전 페이지(아이디 찾기 페이지)로 돌아감
    </script>";
}
?>
