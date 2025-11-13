<?php
session_start();
include_once 'db_connect.php'; // DB 연결

// 검색어 받기
$search = $_GET['q'] ?? '';
$search = trim($search);

if ($search === '') {
    // 검색어가 없으면 메인 페이지로 이동
    header("Location: ../index.php");
    exit;
}

// SQL injection 방지
$search_esc = $connect->real_escape_string($search);

// 가장 유사한 영화 Top 1 검색
$sql = "
    SELECT id, title
    FROM movies
    WHERE TRIM(title) LIKE '%$search_esc%'
    ORDER BY CHAR_LENGTH(title), title
    LIMIT 1
";

$result = $connect->query($sql);

if ($result && $result->num_rows > 0) {
    $movie = $result->fetch_assoc();
    // movie_detail.php로 리디렉션
    header("Location: ../movie_detail.php?id=" . $movie['id']);
    exit;
} else {
    // 검색 결과 없으면 알림 후 메인으로
    echo "<script>alert('검색 결과가 없습니다.'); window.location.href='../index.php';</script>";
    exit;
}
?>
