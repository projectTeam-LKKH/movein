<?php
include 'db_connect.php';

$userid = $_POST['userid'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // 해시 처리
$username = $_POST['username'];
$email = $_POST['email'];
$favorite_genres = $_POST['favorite_genres'];
$preferred_ott = $_POST['preferred_ott'];
$preferred_regions = $_POST['preferred_regions'];

// 중복 체크
$check_sql = "SELECT * FROM User WHERE userid='$userid' OR username='$username' OR email='$email'";
$result = mysqli_query($connect, $check_sql);
if(mysqli_num_rows($result) > 0){
    die("이미 존재하는 아이디, 닉네임, 이메일이 있습니다.");
}

// 저장
$sql = "INSERT INTO User (userid, password, username, email, favorite_genres, preferred_ott, preferred_regions) 
        VALUES ('$userid', '$password', '$username', '$email', '$favorite_genres', '$preferred_ott', '$preferred_regions')";

if(mysqli_query($connect, $sql)){
    echo "회원가입 완료!";
} else {
    echo "오류: " . mysqli_error($connect);
}
?>
