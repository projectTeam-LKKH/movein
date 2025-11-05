

<?php
$connect = mysqli_connect("localhost", "greenproject", "project2!", "greenproject");
if (!$connect) {
    die("DB 연결 실패: " . mysqli_connect_error());
}
mysqli_set_charset($connect, "utf8mb4");

$query = "SELECT * FROM movies ORDER BY release_date ASC";
$result = mysqli_query($connect, $query);
$movies = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>영화 & 콘텐츠</h1>
    </header>
    <main class="movie-list">
        <?php foreach($movies as $movie): ?>
            <?php 
                $poster_file = "../img/poster/pt" . str_pad($movie['id'], 3, "0", STR_PAD_LEFT) . ".webp";
                $poster = file_exists($poster_file) ? $poster_file : ""; // 이미지 없으면 빈 문자열
                
                $stillcut_file = "../img/stillcut/st" . str_pad($movie['id'], 3, "0", STR_PAD_LEFT) . ".webp";
                $stillcut = file_exists($stillcut_file) ? $stillcut_file : "";
                $genres = json_decode($movie['genre'], true);
                $streamings = json_decode($movie['streaming'], true);
            ?>
            <div class="movie-card" onclick="openModal(<?= $movie['id'] ?>)">
                <?php if($poster): ?>
                    <img src="<?= $poster ?>" alt="<?= htmlspecialchars($movie['title']) ?> 포스터">
                <?php else: ?>
                    <div class="no-image">이미지 없음</div>
                <?php endif; ?>

                <div class="movie-info">
                    <h2><?= htmlspecialchars($movie['title']) ?></h2>
                    <p class="genre"><?= $genres ? implode(", ", $genres) : "" ?></p>
                    <p class="summary"><?= nl2br(htmlspecialchars($movie['summary'])) ?></p>
                </div>
            </div>

            <!-- 모달 -->
            <div class="modal" id="modal-<?= $movie['id'] ?>">
                <div class="modal-content">
                    <span class="close" onclick="closeModal(<?= $movie['id'] ?>)">&times;</span>
                    <?php if($stillcut): ?>
                        <img src="<?= $stillcut ?>" alt="<?= htmlspecialchars($movie['title']) ?> 스틸컷">
                    <?php else: ?>
                        <div class="no-image modal-no-image">이미지 없음</div>
                    <?php endif; ?>

                    <h2><?= htmlspecialchars($movie['title']) ?></h2>
                    <p><strong>감독:</strong> <?= htmlspecialchars($movie['director']) ?></p>
                    <p><strong>제작사:</strong> <?= htmlspecialchars($movie['producer']) ?></p>
                    <p><strong>배급사:</strong> <?= htmlspecialchars($movie['distributor']) ?></p>
                    <p><strong>개봉일:</strong> <?= $movie['release_date'] ?></p>
                    <p><strong>상영시간:</strong> <?= $movie['running_time'] ?>분</p>
                    <p><strong>등급:</strong> <?= htmlspecialchars($movie['rating']) ?></p>
                    <p><strong>종류:</strong> <?= htmlspecialchars($movie['type']) ?></p>
                    <p><strong>스트리밍:</strong> <?= $streamings ? implode(", ", $streamings) : "" ?></p>
                    <p><strong>메모:</strong> <?= nl2br(htmlspecialchars($movie['memo'])) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </main>

    <script src="script.js"></script>
</body>
</html>
