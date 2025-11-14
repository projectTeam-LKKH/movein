<?php
session_start();
include_once 'db_connect.php';
$action = $_POST['action'] ?? '';

$userid = $_SESSION['userid'] ?? null;
if (!$userid) {
  echo json_encode(['error' => 'Please Login']);
  exit;
}

// --- 여기서 세션 값 확인 ---
// var_dump($userid);

// DB에 실제 존재하는지 확인
$result = $connect->query("SELECT * FROM User WHERE userid = '{$userid}'");
// var_dump($result->num_rows); // 1이면 존재, 0이면 FK 문제 원인

// "check" 모드 → 기존 리뷰 존재 여부만 확인
if ($_POST['action'] === 'check') {
  $movie_id = intval($_POST['movie_id']);
  $stmt = $connect->prepare("SELECT id FROM comments WHERE movie_id=? AND user_id=? AND is_deleted=0 LIMIT 1");
  $stmt->bind_param("is", $movie_id, $userid);
  $stmt->execute();
  $exists = $stmt->get_result()->num_rows > 0;
  echo json_encode(['exists' => $exists]);
  exit;
}

// 실제 리뷰 작성/수정 처리
$movie_id = intval($_POST['movie_id']);
$content = trim($_POST['review']);
$rating = intval($_POST['rating'] ?? 1);


if ($movie_id && $content !== '') {
  // 기존 리뷰 확인
  $check = $connect->prepare("SELECT id FROM comments WHERE movie_id=? AND user_id=? LIMIT 1");
  $check->bind_param("is", $movie_id, $userid);
  $check->execute();
  $result = $check->get_result();

  if ($result->num_rows > 0) {
    // 덮어쓰기 (업데이트)
    $row = $result->fetch_assoc();
    $update = $connect->prepare("UPDATE comments SET content=?, rating=?, updated_at=NOW() WHERE id=?");
    $update->bind_param("sii", $content, $rating, $row['id']);
    $update->execute();
  } else {
    // 새 리뷰 삽입
    $insert = $connect->prepare("INSERT INTO comments (movie_id, user_id, parent_id, content, rating, likes, is_deleted, created_at, updated_at)
                                 VALUES (?, ?, NULL, ?, ?, 0, 0, NOW(), NOW())");
    $insert->bind_param("isss", $movie_id, $userid, $content, $rating);
    $insert->execute();
  }

  header("Location: ../index.php");
  exit;
}
?>
