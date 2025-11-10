<?php
session_start();
include_once 'login/db_connect.php'; // DB 연결

// 로그인 정보 확인
$nickname = $_SESSION['nickname'] ?? null;
$userid = $_SESSION['userid'] ?? null;

// 로그인 안 했으면 리다이렉트
if (!$userid) {
    header("Location: login.php");
    exit;
}

// GET으로 전달된 영화 ID 받기
$movie_id = $_GET['id'] ?? null;
if (!$movie_id) {
    echo "잘못된 접근입니다.";
    exit;
}

// SQL 준비 및 실행 (SQL Injection 방지)
$stmt = $connect->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();

// 영화 정보 확인
if ($result->num_rows === 0) {
    echo "해당 영화 정보를 찾을 수 없습니다.";
    exit;
}

$movie = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($movie['title']) ?> - 상세</title>
    <link rel="stylesheet" href="css/reset.css" />
    <link rel="stylesheet" href="css/root.css" />
    <link rel="stylesheet" href="css/a_sub.css" />
</head>
<body>
        <?php 
            $poster_path = sprintf("img/poster/pt%03d.webp", $movie['id']);
            $stillcut_path = sprintf("img/stillcut/st%03d.webp", $movie['id']);
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/movein/" . $poster_path)) {
                $img_tag = '<img src="' . htmlspecialchars($poster_path) . '" alt="poster">';
            } else {
                $img_tag = '<div style="width:200px; height:250px; background:#eee; color:#555; display:flex; align-items:center; justify-content:center; text-align:center;">이미지 없음</div>';
            }
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/movein/" . $stillcut_path)) {
                $st_tag = '<img src="' . htmlspecialchars($stillcut_path) . '" alt="stillcut">';
            } else {
                $st_tag = '<div style="width:100%; height:500px; background:#333; color:#eee; display:flex; align-items:center; justify-content:center; text-align:center;">이미지 없음</div>';
            }
        ?>
		<!--메인 컨텐츠-->
		<div id="main_content">
			<!--메인1 (a_여진)-->
			<div class="posterBox">
				<!-- 배경 이미지 스틸컷 -->
				<div class="a_background_still"> <?= $st_tag ?> </div>

				<div class="a_top_nav_container">
					<!-- 상단 아이콘 모음 -->
					<div class="a_top_nav">
						<!-- 왼쪽 아이콘 -->
						<div class="a_top_nav_left">
							<button type="button" class="a_icon_button" onclick="history.back();">
								<img src="img/prev_f5f5f5.png" alt="뒤로가기" />
							</button>
						</div>

						<!-- 오른쪽 아이콘 -->
						<div class="a_top_nav_right">
							<!-- 알림 아이콘 -->
							<button type="button" class="a_icon_button">
								<img src="img/alarm_f5f5f5.png" alt="알림" />
							</button>
							<!-- 더보기 아이콘 -->
							<button type="button" class="a_icon_button">
								<img
									src="img/hamburger_f5f5f5.png"
									alt="더보기"
								/>
							</button>
						</div>
					</div>
				</div>

				<!-- 메인 포스터, 제목 등 세부 정보 데이터 -->
				<div class="a_movie_meta_wrap">
					<!-- 세부 데이터 그룹 틀 ( flex 적용 )-->
					<div class="a_info_group">
                        
						<!--  메인 포스터 영역 -->
						<div class="a_movie_poster">
                            <?= $img_tag ?>
						</div>

						<!-- 영화 제목 + 세부 정보 + 별점 -->
						<div class="a_meta_data">
							<!-- 세부 텍스트 그룹 -->
							<div class="a_movie_info_text">
								<!-- 영화 제목-->
								<h1>
									<span class="a_movie_title_value"><?= htmlspecialchars($movie['title']) ?></span>
								</h1>
								<!-- 세부 정보 -->
                                <?php
                                // genre 처리: JSON -> 배열
                                $genres = json_decode($movie['genre'], true); // true: associative array로 변환
                                if (json_last_error() !== JSON_ERROR_NONE) {
                                    $genres = []; // JSON 파싱 실패 시 빈 배열
                                }

                                // 최대 3개까지만 제한
                                $genres_to_display = array_slice($genres, 0, 3);

                                // 문자열로 합치기
                                $genre_str = htmlspecialchars(implode(", ", $genres_to_display));
                                ?>
								<p>
									<span class="a_details_value">
                                    <?= htmlspecialchars($movie['rating']) ?>
                                     · 
                                    <?= htmlspecialchars($movie['release_date']) ?>
                                     · 
                                    <?= htmlspecialchars($movie['type']) ?>
                                     · 
                                     <?= $genre_str ?>
                                     · 
                                    <?= htmlspecialchars($movie['running_time']) ?>
                                    분
									</span>
								</p>
							</div>

							<!-- 평가 + 별점 -->
							<div class="a_ratings">
								<!-- 평점 -->
								<p class="a_rating_percent">
									<img
										src="img/like_icon_49E99C.png"
										class="a_icon_like"
										alt="좋아요 아이콘"
									/>
									<span class="rating_value">80.2%</span>
									<img
										src="img/i_6f6c76.png"
										class="a_icon_info"
										alt="정보 아이콘"
									/>
								</p>
								<!-- 별점 -->
								<p class="a_star_rating">
									<img
										src="img/star_49E99C.png"
										class="a_icon_star"
										alt="별점 아이콘"
									/>
									<span class="star_value">4.8</span>
								</p>
							</div>
						</div>

						<!-- 영화 줄거리 -->
						<div class="a_movie_forms">
							<p>
								<!-- 줄거리 텍스트 -->
								<span class="a_plot_value">
                                <?= htmlspecialchars($movie['summary']) ?>
								</span>
							</p>
						</div>

						<!-- 스트리밍 가능 플랫폼 -->
						<div class="a_movie_ott_platforms">
							<div class="a_movie_ott_platforms_wrap">
								<!-- 왼쪽 스트리밍 아이콘 -->
								<div class="a_movie_streaming_icon">
									<img
										src="img/streaming_f5f5f5.png"
										alt="스트리밍_아이콘"
									/>
								</div>

								<!-- 오른쪽 플랫폼 아이콘-->
								<ul class="a_streaming_icon">
									<li>
										<a href="#" target="_blank">
											<img
												src="img/disney.png"
												alt="플랫폼_1"
											/>
										</a>
									</li>
									<li>
										<a href="#" target="_blank">
											<img
												src="img/TVING.png"
												alt="플랫폼_2"
											/>
										</a>
									</li>
									<li>
										<a href="#" target="_blank">
											<img
												src="img/watcha.png"
												alt="플랫폼_3"
											/>
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!--poster box 끝-->

			<div class="tabBox">
				<!--메인2 (b_혜정님영역이니까건들지말기)-->
			</div>

			<!--메인3 (a_여진)-->
			<div class="a_gameBox">
				<!-- 밸런스 게임 영역 -->
				<section class="a_balance_game_section">
					<!-- 로그인 시 -->
					<div class="a_balance_game_title_login">
						<!-- 유저 닉네임 -->
						<h2>
							<span class="a_user_nickname">닉네임</span> 님을
							위한
						</h2>
						<h2>오늘의 밸런스 게임이에요</h2>
					</div>

					<!-- 비로그인 시 -->
					<div class="a_balance_game_title_non_login">
						<h2>다양한 게임에 참여해보세요</h2>
					</div>

					<!-- 밸런스 게임 선택 영역 -->
					<div class="a_balance_game_container">
						<!-- 왼쪽 영역 -->
						<div class="a_balance_card left">
							<img
								src="img/like_icon_6F6C76.png"
								class="a_like_icon"
								alt="좋아요 아이콘"
							/>
							<p>
								<span class="a_balance_text_value_left"
									>얼음 성에서 열리는<br />
									마법 같은 파티</span
								>
							</p>
						</div>
						<!-- VS 효과 -->
						<div class="a_vs_divider">VS</div>

						<!-- 오른쪽 영역 -->
						<div class="a_balance_card right">
							<img
								src="img/like_icon_6F6C76.png"
								class="a_like_icon"
								alt="좋아요 아이콘"
							/>
							<p>
								<span class="a_balance_text_value_right"
									>산타와 함께<br />전 세계 선물 배달</span
								>
							</p>
						</div>
					</div>
				</section>

				<!-- 자.만.추 -->
				<section class="a_recommend_section">
					<h3 class="a_recommend_title">
						취향.자.만.추 여기에서 하세요
					</h3>

					<!-- 자만추 영역 -->
					<div class="a_recommend_cards_container">
						<!-- 티켓 뽑기 -->
						<div class="a_recommend_ticket">
							<div class="a_card_icon_wrap">
								<img src="img/game1.png" alt="티켓 아이콘" />
							</div>
							<p>
								<span class="a_card_text_value"
									>오늘의 작품<br />티켓 뽑기</span
								>
							</p>
						</div>

						<!-- 밸런스 게임 -->
						<div class="a_recommend_balance">
							<div class="a_card_icon_wrap">
								<img src="img/game.png" alt="그래프 아이콘" />
							</div>
							<p>
								<span class="a_card_text_value"
									>밸런스<br />게임</span
								>
							</p>
						</div>

						<div class="a_recommend_cardgem_finder">
							<div class="a_card_icon_wrap">
								<img
									src="img/game3.png"
									alt="보석찾기 아이콘"
								/>
							</div>
							<p>
								<span class="a_card_text_value"
									>잔혹한 평점 속, 나만의 보석 찾기</span
								>
							</p>
						</div>
					</div>
				</section>
			</div>
			<!-- 메인3 (a_여진 종료) -->
		</div>
</body>
</html>
