<?php
include_once("db_connect.php");

// JSON 데이터 받기
$input = file_get_contents("php://input");
$data = json_decode($input, true);

$comment_id = $data['comment_id'] ?? null;
$user_id = $data['user_id'] ?? null;
$content = trim($data['content'] ?? '');
$rating = $data['rating'] ?? null;

// 필수 값 확인
if (!$comment_id || !$user_id || $content === '') {
    http_response_code(400);
    echo "필수 값 누락";
    exit;
}

// 사용자 본인 댓글인지 확인
$check_query = "SELECT user_id FROM comments WHERE id = ? AND is_deleted = 0";
$stmt = mysqli_prepare($connect, $check_query);
mysqli_stmt_bind_param($stmt, "i", $comment_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if (!$row || $row['user_id'] !== $user_id) {
    http_response_code(403);
    echo "본인만 수정할 수 있습니다.";
    exit;
}
mysqli_stmt_close($stmt);

// 수정 쿼리 실행
$update_query = "UPDATE comments 
                 SET content = ?, rating = ?, updated_at = NOW() 
                 WHERE id = ? AND user_id = ? AND is_deleted = 0";
$stmt = mysqli_prepare($connect, $update_query);
mysqli_stmt_bind_param($stmt, "siis", $content, $rating, $comment_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo "success";
} else {
    $err = mysqli_stmt_error($stmt);
    http_response_code(500);
    echo "DB 수정 실패: $err";
}

mysqli_stmt_close($stmt);
mysqli_close($connect);
?>
