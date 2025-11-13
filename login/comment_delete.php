<?php
include_once("db_connect.php");

$input = file_get_contents("php://input");
$data = json_decode($input, true);

$comment_id = $data['comment_id'] ?? null;
$user_id = $data['user_id'] ?? null;

if (!$comment_id || !$user_id) {
    http_response_code(400);
    echo "필수 값 누락";
    exit;
}

// 본인 댓글인지 확인
$check_query = "SELECT user_id FROM comments WHERE id = ? AND is_deleted = 0";
$stmt = mysqli_prepare($connect, $check_query);
mysqli_stmt_bind_param($stmt, "i", $comment_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if (!$row || $row['user_id'] !== $user_id) {
    http_response_code(403);
    echo "본인만 삭제할 수 있습니다.";
    exit;
}
mysqli_stmt_close($stmt);

// 삭제(소프트 딜리트)
$delete_query = "UPDATE comments SET is_deleted = 1, updated_at = NOW() WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($connect, $delete_query);
mysqli_stmt_bind_param($stmt, "is", $comment_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo "success";
} else {
    $err = mysqli_stmt_error($stmt);
    http_response_code(500);
    echo "DB 삭제 실패: $err";
}

mysqli_stmt_close($stmt);
mysqli_close($connect);
?>
