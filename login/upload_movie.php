<?php
include_once("db_connect.php");

// 1. 클라이언트에서 JSON 데이터 받기
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// 필수 값 추출
$title = $data['title'] ?? null;
$genre = $data['genre'] ?? null; // 배열 형태
$director = $data['director'] ?? null;
$producer = $data['producer'] ?? null;
$distributor = $data['distributor'] ?? null;
$release_date = $data['release_date'] ?? null;
$running_time = $data['running_time'] ?? null;
$streaming = $data['streaming'] ?? null; // 배열 형태
$rating = $data['rating'] ?? null;
$type = $data['type'] ?? null;
$summary = $data['summary'] ?? null;
$memo = $data['memo'] ?? null;

// 필수 값 확인
if (!$title) {
    http_response_code(400);
    echo "필수 값 누락: title";
    exit;
}

if ($release_date) {
    // 공백 제거
    $release_date = trim($release_date);

    // 4자리 연도만 들어온 경우
    if (preg_match('/^\d{4}$/', $release_date)) {
        $release_date .= '-01-01';
    }

    // YYYY-MM-DD 형식 체크
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $release_date)) {
        $release_date = null; // 올바른 형식이 아니면 NULL 처리
    }
}

// 2. JSON 배열을 문자열로 변환
$genre_json = $genre ? json_encode($genre, JSON_UNESCAPED_UNICODE) : null;
$streaming_json = $streaming ? json_encode($streaming, JSON_UNESCAPED_UNICODE) : null;

// 3. DB INSERT
$stmt = $connect->prepare("
    INSERT INTO movies
    (title, genre, director, producer, distributor, release_date, running_time, streaming, rating, type, summary, memo)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "ssssssisisss",
    $title,
    $genre_json,
    $director,
    $producer,
    $distributor,
    $release_date,
    $running_time,
    $streaming_json,
    $rating,
    $type,
    $summary,
    $memo
);

if ($stmt->execute()) {
    // 4. INSERT 후 ID 리턴
    $movie_id = $connect->insert_id;

    // 5. 다시 SELECT해서 제목 확인
    $stmt2 = $connect->prepare("SELECT id, title FROM movies WHERE id = ?");
    $stmt2->bind_param("i", $movie_id);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $movie = $result->fetch_assoc();

    if ($movie) {
        // 6. 버튼 생성
        echo '<a href="movie_detail.php?id=' . htmlspecialchars($movie['id']) . '">'
            . htmlspecialchars($movie['title']) .
            '</a>';
    } else {
        echo "업로드 후 영화 조회 실패";
    }

    $stmt2->close();
} else {
    http_response_code(500);
    echo "DB 저장 실패: " . $stmt->error;
}

$stmt->close();
$connect->close();
?>
