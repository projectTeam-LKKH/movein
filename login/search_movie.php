<?php
include_once 'db_connect.php';

$q = $_GET['q'] ?? '';
$q = trim($q);

if ($q === '') {
  echo json_encode([]);
  exit;
}

$stmt = $connect->prepare("SELECT id, title FROM movies WHERE title LIKE CONCAT('%', ?, '%') ORDER BY title LIMIT 10");
$stmt->bind_param("s", $q);
$stmt->execute();
$result = $stmt->get_result();

$movies = $result->fetch_all(MYSQLI_ASSOC);
echo json_encode($movies, JSON_UNESCAPED_UNICODE);
?>
