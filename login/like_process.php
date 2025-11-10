<?php
session_start();
require_once "db_connect.php"; // DB 연결 ($connect 사용)

header('Content-Type: application/json');

// 로그인 여부 확인
if (!isset($_SESSION['userid'])) {
    echo json_encode(["success" => false, "message" => "로그인이 필요합니다."]);
    exit;
}

$user_id = $_SESSION['userid']; // 이제 문자열 (예: "user123" 또는 "abcde-uuid-xxxx")
$movie_id = $_POST['movie_id'] ?? null;
$status = $_POST['status'] ?? null;

if (!$movie_id || !in_array($status, ['like', 'hate'])) {
    echo json_encode(["success" => false, "message" => "잘못된 요청입니다."]);
    exit;
}

try {
    // ✅ 1. 기존 상태 확인
    $stmt = mysqli_prepare($connect, "SELECT id, status FROM Likes WHERE user_id = ? AND movie_id = ?");
    mysqli_stmt_bind_param($stmt, "si", $user_id, $movie_id); // user_id → s (string), movie_id → i (int)
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $existing = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    // ✅ 2. 기존 상태가 존재하는 경우
    if ($existing) {
        if ($existing['status'] === $status) {
            // 이미 같은 상태면 → 취소 (삭제)
            $del = mysqli_prepare($connect, "DELETE FROM Likes WHERE id = ?");
            mysqli_stmt_bind_param($del, "i", $existing['id']);
            mysqli_stmt_execute($del);
            mysqli_stmt_close($del);

            echo json_encode([
                "success" => true,
                "status" => "none",
                "message" => "선택이 취소되었습니다."
            ]);
        } else {
            // 상태가 다르면 → 업데이트
            $upd = mysqli_prepare($connect, "UPDATE Likes SET status = ?, updated_at = NOW() WHERE id = ?");
            mysqli_stmt_bind_param($upd, "si", $status, $existing['id']);
            mysqli_stmt_execute($upd);
            mysqli_stmt_close($upd);

            echo json_encode([
                "success" => true,
                "status" => $status
            ]);
        }
    } else {
        // ✅ 3. 새로 추가
        $ins = mysqli_prepare($connect, "INSERT INTO Likes (user_id, movie_id, status) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($ins, "sis", $user_id, $movie_id, $status);
        mysqli_stmt_execute($ins);
        mysqli_stmt_close($ins);

        echo json_encode([
            "success" => true,
            "status" => $status
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "DB 처리 중 오류 발생: " . $e->getMessage()
    ]);
}
?>
