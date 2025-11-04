<?php
include 'db_connect.php';

// 입력값 받기 및 기본값 처리
$userid = trim($_POST['userid'] ?? '');
$password_raw = $_POST['password'] ?? '';
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$favorite_genres = $_POST['favorite_genres'] ?? '[]';
$preferred_ott = $_POST['preferred_ott'] ?? '[]';
$preferred_regions = $_POST['preferred_regions'] ?? '[]';

// 비밀번호 해시
$password = password_hash($password_raw, PASSWORD_DEFAULT);

// ✅ 빈 문자열이거나 JSON이 아니면 강제로 빈 배열로 대체
if (empty($favorite_genres)) $favorite_genres = '[]';
if (empty($preferred_ott)) $preferred_ott = '[]';
if (empty($preferred_regions)) $preferred_regions = '[]';

// JSON 유효성 확인 (PHP 7.3+)
function is_valid_json($str) {
    json_decode($str);
    return (json_last_error() === JSON_ERROR_NONE);
}

if (!is_valid_json($favorite_genres)) $favorite_genres = '[]';
if (!is_valid_json($preferred_ott)) $preferred_ott = '[]';
if (!is_valid_json($preferred_regions)) $preferred_regions = '[]';

// ✅ 필수 입력값 검증
if (!$userid || !$password_raw || !$username || !$email) {
    die("필수 입력값이 누락되었습니다.");
}


// ✅ 중복 체크 (Prepared Statement)
$check_sql = "SELECT 1 FROM User WHERE userid = ? OR username = ? OR email = ?";
$stmt = mysqli_prepare($connect, $check_sql);
mysqli_stmt_bind_param($stmt, "sss", $userid, $username, $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    die("이미 존재하는 아이디, 닉네임, 또는 이메일이 있습니다.");
}
mysqli_stmt_close($stmt);

// ✅ 데이터 저장 (Prepared Statement)
$sql = "INSERT INTO User (userid, password, username, email, favorite_genres, preferred_ott, preferred_regions)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($connect, $sql);

if (!$stmt) {
    die("쿼리 준비 중 오류 발생: " . mysqli_error($connect));
}

mysqli_stmt_bind_param($stmt, "sssssss",
    $userid,
    $password,
    $username,
    $email,
    $favorite_genres,
    $preferred_ott,
    $preferred_regions
);

// 실행
// if (mysqli_stmt_execute($stmt)) {
//     // JS 리다이렉트 대신, step4에서 이미 완료화면을 띄우므로 메시지 생략 가능
//     echo "<script>
//         window.location.href = 'login.php';
//     </script>";
// } else {
//     echo "회원가입 중 오류가 발생했습니다: " . mysqli_error($connect);
// }
if (mysqli_stmt_execute($stmt)) {
    echo "success";
} else {
    echo "error";
}

mysqli_stmt_close($stmt);
mysqli_close($connect);
?>
