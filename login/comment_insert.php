<?php

include_once("db_connect.php");

// JSON 데이터 받기
$input = file_get_contents("php://input");
$data = json_decode($input, true);
// var_dump($input, $data); // 여기서 브라우저에서 확인

$movie_id = $data['movie_id'] ?? null;
$user_id = $data['user_id'] ?? null;
$content = $data['content'] ?? null;
$rating = $data['rating'] ?? null;

// 필수 값 확인
if (!$movie_id || !$user_id || !$content) {
    http_response_code(400);
    echo "필수 값 누락";
    exit;
}

// DB 저장
$stmt = mysqli_prepare($connect, "
    INSERT INTO comments (movie_id, user_id, parent_id, content, rating, likes, is_deleted, created_at, updated_at)
    VALUES (?, ?, NULL, ?, ?, 0, 0, NOW(), NOW())
");

mysqli_stmt_bind_param($stmt, "issi", $movie_id, $user_id, $content, $rating);

if (mysqli_stmt_execute($stmt)) {
    echo "success";
} else {
    // DB 오류를 반드시 출력
    $err = mysqli_stmt_error($stmt);
    http_response_code(500);
    echo "DB 저장 실패: $err";
}

mysqli_stmt_close($stmt);
mysqli_close($connect);
?>
