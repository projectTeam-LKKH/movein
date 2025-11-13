<?php
session_start();
include_once 'login/db_connect.php'; // DB 연결

// 로그인 정보 확인
$nickname = $_SESSION['nickname'] ?? null;
$userid = $_SESSION['userid'] ?? null;

// 로그인 안 했으면 리다이렉트
// if (!$userid) {
//     header("Location: login/login.php");
//     exit;
// }

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

// 좋아요,싫어요 여부 확인
$like_status = null;

if (isset($userid)) {
    // SQL 준비
    $stmt = mysqli_prepare($connect, "SELECT status FROM Likes WHERE user_id = ? AND movie_id = ?");
    mysqli_stmt_bind_param($stmt, "si", $userid, $movie_id);
    mysqli_stmt_execute($stmt);
    
    // 결과 가져오기
    mysqli_stmt_bind_result($stmt, $status);
    if (mysqli_stmt_fetch($stmt)) {
        $like_status = $status; // 'like' 또는 'hate'
    }
    mysqli_stmt_close($stmt);
}



// 로그인한 사용자의 댓글 1개 (본인 리뷰)
$user_review = null;
if ($userid) {
    $user_query = "SELECT c.*, u.username
					FROM comments AS c
					JOIN User AS u ON c.user_id = u.userid
					WHERE c.movie_id = ? AND c.user_id = ? AND c.is_deleted = 0
					ORDER BY c.created_at DESC
					LIMIT 1";
    $stmt = mysqli_prepare($connect, $user_query);
    mysqli_stmt_bind_param($stmt, "is", $movie_id, $userid);
    mysqli_stmt_execute($stmt);
    $user_result = mysqli_stmt_get_result($stmt);
    $user_review = mysqli_fetch_assoc($user_result);
}

// 전체 댓글 목록 (본인 댓글 제외)
$query = "SELECT c.*, u.username 
			FROM comments AS c
			JOIN User AS u ON c.user_id = u.userid
			WHERE c.movie_id = ? AND c.is_deleted = 0
			ORDER BY c.created_at DESC";
$stmt = mysqli_prepare($connect, $query);
mysqli_stmt_bind_param($stmt, "i", $movie_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);


// 별점 평균 가져오기
$query_rating = "
	SELECT ROUND(IFNULL(AVG(c.rating), 0), 1) AS avg_rating
	FROM comments c
	WHERE c.movie_id = ? AND c.is_deleted = 0
";
$stmt = mysqli_prepare($connect, $query_rating);
mysqli_stmt_bind_param($stmt, "i", $movie_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$avg_rating = $row['avg_rating'] ?? 0;

// 좋아요 비율
$query_likes = "
	SELECT 
		SUM(CASE WHEN status = 'like' THEN 1 ELSE 0 END) AS like_count,
		COUNT(*) AS total_count
	FROM Likes
	WHERE movie_id = ?
";
$stmt2 = mysqli_prepare($connect, $query_likes);
mysqli_stmt_bind_param($stmt2, "i", $movie_id);
mysqli_stmt_execute($stmt2);
$result2 = mysqli_stmt_get_result($stmt2);
$row2 = mysqli_fetch_assoc($result2);

$like_count = $row2['like_count'] ?? 0;
$total_count = $row2['total_count'] ?? 0;
$like_percent = $total_count > 0 ? round(($like_count / $total_count) * 100, 1) : 0;
?>


<!DOCTYPE html>
<html lang="ko">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<title><?= htmlspecialchars($movie['title']) ?> - 상세</title>
		<link rel="stylesheet" href="css/reset.css" />
		<link rel="stylesheet" href="css/root.css" />
		<link rel="stylesheet" href="css/a_sub.css" />
		<link rel="stylesheet" href="css/b_sub.css" />
		<link rel="stylesheet" href="css/import.css" />
		<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
		<script src="js/import.js"></script>
		<script src="js/babel.min.js"></script>
	</head>
<body class="sub-body">
        <?php 
            $poster_path = sprintf("img/poster/pt%03d.webp", $movie['id']);
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/movein/" . $poster_path)) {
                $img_tag = '<img src="' . htmlspecialchars($poster_path) . '" alt="poster" >';
            } else {
                $img_tag = '<img src="img/picture_6f6c76.png" alt="noImage" >';
                // $img_tag = '<div style="width:200px; height:250px; background:#eee; color:#555; display:flex; align-items:center; justify-content:center; text-align:center;">이미지 없음</div>';
            }
            $st_tags = []; // 결과 태그를 담을 배열
			for ($i = 1; $i <= 5; $i++) {
				$stillcut_path = sprintf("img/stillcut/st%03d_%d.webp", $movie['id'], $i);
				$full_path = $_SERVER['DOCUMENT_ROOT'] . "/movein/" . $stillcut_path;
				if (file_exists($full_path)) {
					if ($i === 1) {
						$st_tags[$i] = '<img src="' . htmlspecialchars($stillcut_path) . '" alt="영화 배경 스틸컷" class="a_background_still">';
					} else {
						$st_tags[$i] = '<img src="' . htmlspecialchars($stillcut_path) . '" alt="스틸컷 위치별">';
					}
					
				} else {
					$st_tags[$i] = '<img src="img/picture_6f6c76.png" alt="noImage" >';
					// $st_tags[$i] = '<div style="width:100%; height:500px; background:#333; color:#eee; display:flex; align-items:center; justify-content:center; text-align:center;">이미지 없음</div>';
				}
			}
			// genre 처리: JSON -> 배열
			$genres = json_decode($movie['genre'], true); // true: associative array로 변환
			if (json_last_error() !== JSON_ERROR_NONE) {
				$genres = []; // JSON 파싱 실패 시 빈 배열
			}

			// 최대 3개까지만 제한
			$genres_to_display = array_slice($genres, 0, 3);

			// 문자열로 합치기
			$genre_str = htmlspecialchars(implode(", ", $genres_to_display));

			
			// genre 처리: JSON -> 배열
			$streams = json_decode($movie['streaming'], true); // true: associative array로 변환
			if (json_last_error() !== JSON_ERROR_NONE) {
				$streams = []; // JSON 파싱 실패 시 빈 배열
			}
			// 최대 3개까지만 제한
			$streams_to_display = array_slice($streams, 0, 3);
			// 문자열로 합치기
			$stream_str = htmlspecialchars(implode(", ", $streams_to_display));
		?>
		<!--헤더-->
		<div id="header-slot"></div>

		<!--메인 컨텐츠-->
		<div id="main_content">
			<!--메인1 (a_여진)-->
			<div class="posterBox">
				<!-- 배경 이미지 스틸컷 -->
				<?= $st_tags[1] ?>

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
								<p>
									<span class="a_details_value">
                                    <?= htmlspecialchars($movie['rating']) ?>
                                     · 
									 <?= htmlspecialchars(substr($movie['release_date'], 0, 4)) ?>
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
									<span class="rating_value"><?php echo $like_percent; ?>%</span>
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
									<span class="star_value"><?php echo $avg_rating; ?></span>
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

			<!--메인2 (b_혜정)-->
			<div class="tabBox">
				<div class="movie_detail">
				<!-- 탭 메뉴 -->
				<ul class="tab_menu">
   					<li class="b_active"><button type="button" data-tab="tab_info">콘텐츠 정보</button></li>
					<li><button type="button" data-tab="tab_review">감상평 99+</button></li>
					<li><button type="button" data-tab="tab_media">영상/이미지</button></li>
				</ul>

				<!-- 탭1 콘텐츠 정보 영역 -->
				<section class="tab_content b_active" id="tab_info">
					<h3>이 작품에 대한 간단한 피드백을 부탁해요</h3>

					<div class="like_box">
					<button 
						type="button" 
						class="like_btn <?= ($like_status === 'like') ? 'active' : '' ?>" 
						data-status="like" 
						data-movie-id="<?= $movie_id ?>">
						<img src="img/like_icon_6F6C76.png" alt="like_icon" /> 추천해요
					</button>

					<button 
						type="button" 
						class="hate_btn <?= ($like_status === 'hate') ? 'active' : '' ?>" 
						data-status="hate" 
						data-movie-id="<?= $movie_id ?>">
						<img src="img/hate_icon_6F6C76.png" alt="hate_icon" /> 별로예요
					</button>
					</div>


					<ul class="movie_meta">
					<li><strong>개봉 연도</strong><span><?= htmlspecialchars(substr($movie['release_date'], 0, 4)) ?></span></li>
					<li><strong>종류</strong><span><?= htmlspecialchars($movie['type']) ?></span></li>
					<li><strong>장르</strong><span><?= $genre_str ?></span></li>
					<li><strong>감독</strong><span><?= htmlspecialchars($movie['director']) ?></span></li>
					<li><strong>제작</strong><span><?= htmlspecialchars($movie['producer']) ?></span></li>
					<li><strong>상영 시간</strong><span><?= htmlspecialchars($movie['running_time']) ?>분</span></li>
					<li><strong>스트리밍</strong><span><?= $stream_str ?></span></li>
					<li><strong>연령 등급</strong><span><?= htmlspecialchars($movie['rating']) ?></span></li>
					</ul>
				</section>

				<!-- 탭2 감상평 영역 -->
				<section class="tab_content" id="tab_review">
					<!-- 로그인된 상태 -->
                <?php if ($nickname): ?>
					<div class="review_login">
					<p>
						<strong
						><span class="nickname"><?php echo htmlspecialchars($nickname); ?></span>
						님, 이 작품을 보신 적이 있으세요? <img src="img/i_6f6c76.png" alt="i_icon" />
						</strong>
					</p>

					<!-- i아이콘 클릭 시 안내창 (기본은 숨김 상태) -->
					<div class="iicon_popup" id="iicon_popup">
						<p>
						무브인 사용자 리뷰를 작성하기 위한 공간입니다.<br />
						부적절하거나 불법적인 리뷰 및 내용은 업로드할 수<br />
						없습니다. 규정을 위반할 경우 즉시 삭제되며 서비스<br />
						이용이 제한될 수 있습니다. 지속적인 위반 시, 별도의<br />
						통보 없이 탈퇴 처리될 수 있습니다.
						</p>
					</div>

					<!-- 별점 -->
					<div class="star_rating">
						<img src="img/star_6f6c76.png" alt="1점" data-value="1" class="star" />
						<img src="img/star_6f6c76.png" alt="2점" data-value="2" class="star" />
						<img src="img/star_6f6c76.png" alt="3점" data-value="3" class="star" />
						<img src="img/star_6f6c76.png" alt="4점" data-value="4" class="star" />
						<img src="img/star_6f6c76.png" alt="5점" data-value="5" class="star" />
					</div>

					<!-- 텍스트 입력 -->
					<textarea
						class="review_input"
						placeholder="지금 바로 리뷰를 작성해 또다른 무브오너들의 취향 형성에 기여해주세요!"
					></textarea>
					

					<!-- 등록 버튼 -->
					<button type="button" class="register_btn">
						등록하기 <img src="img/pen_6f6c76.png" alt="pen_icon" />
					</button>
					</div>
				<?php else: ?>
					<!-- 로그인하지 않은 상태 (비로그인 시 노출) -->
					<div class="review_unlogin">
					<p>
						<strong
						>이 작품을 보신 적이 있으세요?
						<img src="img/i_6f6c76.png" alt="i_icon" id="i_icon"/></strong>
					</p>

					<!-- i아이콘 클릭 시 안내창 (기본은 숨김 상태) -->
					<div class="iicon_popup" id="iicon_popup">
						<p>
						무브인 사용자 리뷰를 작성하기 위한 공간입니다.<br />
						부적절하거나 불법적인 리뷰 및 내용은 업로드할 수<br />
						없습니다. 규정을 위반할 경우 즉시 삭제되며 서비스<br />
						이용이 제한될 수 있습니다. 지속적인 위반 시, 별도의<br />
						통보 없이 탈퇴 처리될 수 있습니다.
						</p>
					</div>

					<!-- 별점 -->
					<div class="star_rating">
						<img src="img/star_6f6c76.png" alt="1점" data-value="1" class="star" />
						<img src="img/star_6f6c76.png" alt="2점" data-value="2" class="star" />
						<img src="img/star_6f6c76.png" alt="3점" data-value="3" class="star" />
						<img src="img/star_6f6c76.png" alt="4점" data-value="4" class="star" />
						<img src="img/star_6f6c76.png" alt="5점" data-value="5" class="star" />
					</div>

					<!-- 텍스트 입력 -->
					<a href="login/login.php"><textarea
						class="review_input"
						placeholder="지금 바로 로그인 하고 리뷰를 작성해 또다른 무브오너들의 취향 형성에 기여해주세요!"
						disabled
					></textarea></a>

					<!-- 등록 버튼 -->
					<a href="login/login.php"><button type="button" class="register_btn" disabled>
						등록하기 <img src="img/pen_6f6c76.png" alt="pen_icon" />
					</button></a>
					</div>
				<?php endif; ?>

<!-- 📝 전체 리뷰 리스트 -->	
					<div class="review_list">
						<div class="review_header">
							<h3>전체 리뷰</h3>
							<button type="button" class="sort_btn">최신순 <img src="img/next_icon_6F6C76.png" alt="down_icon"></button>
						</div>
												
						<ul id="review_ul"> <!-- 원랜 id가 없었음? -->
							<?php if (mysqli_num_rows($result) > 0): ?>
							<?php while ($row = mysqli_fetch_assoc($result)): ?>
								<li class="review_item">
								<div class="review_info">
									<img src="img/user_6F6C76.png" alt="user_icon" />
									<span class="list_nickname"><?= htmlspecialchars($row['username']) ?></span>
									<?php if (!is_null($row['rating'])): ?>
									<span class="score">
										<img src="img/star_49E99C.png" alt="star_icon" />
										<?= htmlspecialchars($row['rating']) ?>.0
									</span>
									<?php endif; ?>
									<span class="date">
										<?php
										$created_at = strtotime($row['created_at']);
										$today = time();
										$diff_days = floor(($today - $created_at) / (60 * 60 * 24));

										if ($diff_days == 0) {
											echo "오늘";
										} elseif ($diff_days == 1) {
											echo "어제";
										} else {
											echo $diff_days . "일 전";
										}
										?>
									</span>
								</div>

								<p class="review_text"><?= nl2br(htmlspecialchars($row['content'])) ?></p>

								<button type="button" class="bookmark_btn">
									+<?= $row['likes'] ?>
									<img src="img/bookmark_01_6f6c76.png" alt="bookmark_icon" />
								</button>
								</li>
							<?php endwhile; ?>
							<?php else: ?>
							<li class="no_review">아직 등록된 리뷰가 없습니다.</li>
							<?php endif; ?>
						</ul>
						<button type="button" class="more_btn">
							더보기 <img src="img/next_icon_6F6C76.png" alt="next_icon" />
						</button>
					</div>
					
            		<!-- 로그인 후 별점 남겼을 시 상태 -->
					<?php if ($user_review): ?>
						
					<div class="review_edit">
						<h3><strong><?= htmlspecialchars($nickname) ?></strong>님이 등록한 리뷰</h3>

						<div class="star_rating">
						<?php for ($i = 1; $i <= 5; $i++): ?>
							<img src="img/<?= ($i <= $user_review['rating']) ? 'star_49E99C.png' : 'star_6f6c76.png' ?>"
								alt="<?= $i ?>점" data-value="<?= $i ?>" class="star" />
						<?php endfor; ?>
						</div>

						<li class="review_item">
							<div class="review_info">
								<span class="list_nickname"><?= htmlspecialchars($user_review['username']) ?></span>
								<span class="score">
								<img src="img/star_49E99C.png" alt="star_icon" />
								<?= htmlspecialchars($user_review['rating']) ?>.0
								</span>
								<span class="date">
									<?php
									$created_at = strtotime($user_review['created_at']);
									$today = time();
									$diff_days = floor(($today - $created_at) / (60 * 60 * 24));

									if ($diff_days == 0) {
										echo "오늘";
									} elseif ($diff_days == 1) {
										echo "어제";
									} else {
										echo $diff_days . "일 전";
									}
									?>
								</span>
							</div>
							<p class="review_text"><?= nl2br(htmlspecialchars($user_review['content'])) ?></p>

							<button type="button" class="bookmark_btn">
								+<?= $user_review['likes'] ?>
								<img src="img/bookmark_01_6f6c76.png" alt="bookmark_icon" />
							</button>
						</li>

						<button type="button" class="edit_btn">
						수정하기 <img src="img/pen_6f6c76.png" alt="pen_icon" />
						</button>
					</div>
					<?php endif; ?>
				</section>

				<!-- 탭3 영상/이미지 영역 -->
				<section class="tab_content" id="tab_media">
					<!-- 영상 섹션 -->
					<div class="media_section">
						<h3 class="media_title">영상</h3>

						<div class="video_box">
							<iframe width="90%" height="315"
							src="https://www.youtube.com/embed/i50tT8n9fp8?si=MBFIC67575e8x7h7"
							title="YouTube video player" frameborder="0"
							allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
							referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
						</iframe>
						</div>
					</div>

					<!-- 스틸컷 섹션 -->
					<div class="media_section stillcuts">
						<h3 class="media_title">이미지</h3>

						<!-- 큰 이미지 -->
						<div class="image_item large">
							<?= $st_tags[2] ?>
						</div>

						<!-- 하단 3분할 이미지 -->
						<div class="image_layout">
							<div class="image_left">
								<?= $st_tags[3] ?>
							</div>

							<div class="image_right">
								<div class="image_small top">
									<?= $st_tags[4] ?>
								</div>
								<div class="image_small bottom">
									<?= $st_tags[5] ?>
								</div>
							</div>
						</div>
					</div>
				</section>
				</div>
			</div>

			<!--메인3 (a_여진)-->
			<div class="a_gameBox">
				<!-- 밸런스 게임 영역 -->
				<section class="a_balance_game_section">
					<?php if ($userid): ?>
						<!-- 로그인 시 -->
						<div class="a_balance_game_title_login">
							<!-- 유저 닉네임 -->
							<h2>
							<span class="a_user_nickname">
								<?php echo htmlspecialchars($nickname); ?></span>님을 위한
							</h2>
							<h2>오늘의 밸런스 게임이에요</h2>
						</div>
					<?php else: ?>
						<!-- 비로그인 시 -->
						<div class="a_balance_game_title_non_login">
							<h2>다양한 게임에 참여해보세요</h2>
						</div>
					<?php endif; ?>

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
								<span class="a_balance_text_value_left"></span>
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
									></span
								>
							</p>
						</div>
					</div>

					<!-- 선택 결과 -->
					<div class="a_balance_result_area" style="display:none;">
					<div class="a_balance_result_message_wrap">
						<p class="a_balance_result_message">
						<span id="a_result_message_text"></span>
						</p>
						<p class="a_balance_result_title">
						그건 바로 <span id="a_result_movie_title_value"></span>
						</p>
					</div>

					<div class="a_result_content">
						<div class="a_result_poster_wrap">
						<img src="" alt="결과 영화 포스터" class="a_result_movie_poster" />
						</div>

						<div class="a_result_meta">
						<div class="a_result_tags_container" id="a_result_tags_container"></div>
						<a href="#" class="a_result_view_details_btn" id="a_result_view_details_btn">정보 보러 가기</a>
						<img src="img/next_icon_6F6C76.png" alt="화살표 버튼">
						</div>
					</div>

					<button class="a_balance_restart_btn">다시 하기</button>
					 <!-- 밸런스게임 선택 결과 끝 -->
				</section>

				<!-- 자.만.추 -->
				<section class="a_recommend_section">
					<h3 class="a_recommend_title">
						취향.자.만.추 여기에서 하세요
					</h3>

					<!-- 자.만.추 영역 -->
					<div class="a_recommend_cards_container">
						<!-- 티켓 뽑기 -->
						<div class="a_recommend_ticket">
							<div class="a_card_icon_wrap">
								<img src="img/game1.svg" alt="티켓 아이콘" />
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
								<img src="img/game2.svg" alt="그래프 아이콘" />
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
		<div id="bottom-nav"></div>
		<div id="footer"></div>

		<script>
			// 탭 전환
			document.addEventListener("DOMContentLoaded", function() {
			const tabItems = document.querySelectorAll(".tab_menu li");
			const tabButtons = document.querySelectorAll(".tab_menu button");
			const tabContents = document.querySelectorAll(".tab_content");

			// 기본 탭 설정 (첫 번째 탭 활성화)
			tabItems[0].classList.add("b_active");
			tabContents[0].classList.add("b_active");

			tabButtons.forEach((button, index) => {
				button.addEventListener("click", () => {
				const targetId = button.getAttribute("data-tab");
				const targetContent = document.getElementById(targetId);

				// 모든 탭 / 콘텐츠 비활성화
				tabItems.forEach(li => li.classList.remove("b_active"));
				tabContents.forEach(content => content.classList.remove("b_active"));

				// 클릭된 탭 및 해당 콘텐츠 활성화
				tabItems[index].classList.add("b_active");
				targetContent.classList.add("b_active");
				});
			});
			});


			//  i아이콘 클릭
			document.addEventListener("DOMContentLoaded", function() {
			const infoIcon = document.getElementById("i_icon");
			const popup = document.getElementById("iicon_popup");

			if (infoIcon && popup) {
				infoIcon.addEventListener("click", function (event) {
				event.stopPropagation(); // 다른 이벤트에 영향 주지 않도록
				popup.classList.add("active");
				});

				popup.addEventListener("click", function () {
				popup.classList.remove("active");
				});
			}
			});


			// 좋아요 싫어요 여부 서버 저장
			document.addEventListener("DOMContentLoaded", function() {
			const likeButtons = document.querySelectorAll(".like_box button");

			likeButtons.forEach(btn => {
				btn.addEventListener("click", function() {
				const movieId = this.dataset.movieId;
				const status = this.dataset.status;

				// ✅ 로그인 여부 확인 (PHP 변수로 전달)
				const isLoggedIn = <?= isset($userid) ? 'true' : 'false' ?>;

				if (!isLoggedIn) {
					alert("로그인 후 이용해주세요 😊");
					window.location.href = "login/login.php";
					return;
				}

				// ✅ AJAX 요청
				fetch("login/like_process.php", {
					method: "POST",
					headers: { "Content-Type": "application/x-www-form-urlencoded" },
					body: `movie_id=${movieId}&status=${status}`,
					credentials: "include" // ✅ 세션 유지 (쿠키 전송)
				})
				.then(res => res.json())
				.then(data => {
					if (data.success) {
						document.querySelectorAll(".like_box button").forEach(btn => btn.classList.remove("active"));
						if (data.status !== "none") {
							document.querySelector(`.like_box button[data-status="${data.status}"]`).classList.add("active");
						}
					} else {
					alert(data.message || "오류가 발생했습니다.");
					}
				})
				.catch(err => console.error(err));
				});
			});
			});

			// 별점,댓글 저장
			document.addEventListener("DOMContentLoaded", () => {
			const stars = document.querySelectorAll(".star_rating .star");
			const textarea = document.querySelector(".review_input");
			const registerBtn = document.querySelector(".register_btn");

			let selectedRating = null;

			// ⭐ 별 클릭 시 별점 설정
			stars.forEach((star, index) => {
				star.addEventListener("click", () => {
				selectedRating = star.dataset.value;
				stars.forEach(s => s.src = "img/star_6f6c76.png");
				for (let i = 0; i <= index; i++) {
					stars[i].src = "img/star_49E99C.png"; // 선택된 별까지 활성화 이미지로 변경
				}
				});
			});

			// 💬 등록 버튼 클릭 시 DB에 전송
			registerBtn.addEventListener("click", async () => {
				const content = textarea.value.trim();

				if (!content) {
				alert("댓글 내용을 입력해주세요!");
				return;
				}

				// PHP에 전달할 데이터
				const data = {
					movie_id: Number(<?= $movie_id ?>),
					user_id: "<?= $userid ?>",
					content: content,
					rating: Number(selectedRating)
				};

				try {
				const response = await fetch("login/comment_insert.php", {
					method: "POST",
					headers: { "Content-Type": "application/json" },
					body: JSON.stringify(data)
				});

				const result = await response.text();

				if (response.ok && result.trim() === "success") {
					alert("댓글이 등록되었습니다!");
					location.reload();
				} else {
					alert("댓글 등록 실패: " + result);
				}
				} catch (error) {
				console.error(error);
				alert("서버 요청 중 오류가 발생했습니다.");
				}
			});
			});

			// 영화 밸런스 질문
			const balanceQuestions = [
				{ left: "시간을 돌릴 수<br> 있는 능력", right: "미래를 미리<br> 볼 수있는 능력" }
				// { left: "하루 동안 투명 인간이 되기", right: "하루 동안 다른 사람의 마음을 읽기" },
				// { left: "고요한 숲속 오두막에서의 일주일", right: "활기찬 도시의 펜트하우스에서의 일주일" },
				// { left: "아무도 없는 섬에서 혼자 살아남기", right: "낯선 행성에서 외계인과 함께 생존하기" },
				// { left: "모든 언어를 완벽히 구사할 수 있는 능력", right: "어떤 악기든 단번에 마스터하는 능력" },
				// { left: "꿈속에서 원하는 세상을 자유롭게 여행하기", right: "현실에서 원하는 대로 날씨를 바꾸기" },
				// { left: "세상에서 단 하나뿐인 미식 요리사가 되기", right: "세계적인 음악 프로듀서로 성공하기" },
				// { left: "시간이 멈춘 세상에서 혼자 움직이기", right: "모든 사람이 느끼는 감정을 색으로 볼 수 있기" },
				// { left: "전생의 기억을 모두 가진 채 다시 태어나기", right: "완전히 새로운 기억으로 새로운 삶 살기" },
				// { left: "과거로 돌아가 한 가지 실수를 고치기", right: "미래에서 한 가지 성공을 미리 얻기" },
				// { left: "드래곤을 타고 하늘을 나는 모험", right: "심해 도시를 탐험하는 잠수 여행" },
				// { left: "친구들과 하루 종일 게임 파티", right: "조용히 책 읽으며 혼자만의 시간 보내기" },
				// { left: "겨울 왕국 같은 눈 덮인 마을에서 살기", right: "열대 해변에서 여유롭게 살기" },
				// { left: "로봇이 모든 일을 대신해주는 미래 도시", right: "자연과 함께 살아가는 전원 마을" },
				// { left: "세상에서 단 한 번 열리는 가면 무도회 초대", right: "비밀 요원으로서의 하루 체험" },
				// { left: "거대한 미로 속에서 탈출 미션 수행", right: "무인 우주선에서 혼자 생존 미션 수행" },
				// { left: "AI와 사랑에 빠지는 미래", right: "가상 현실 속에서 이상형과 만나는 사랑" },
				// { left: "시간 여행을 하는 탐정", right: "꿈속 범죄를 해결하는 수사관" },
				// { left: "하루 동안 영화 주인공으로 살기", right: "하루 동안 게임 속 캐릭터로 살기" },
				// { left: "내 인생을 다룬 영화를 직접 연출하기", right: "내 인생을 소설로 써서 베스트셀러 만들기" },
				// { left: "자신의 과거를 기억하는 로봇이 되기", right: "감정을 느끼는 인공지능이 되기" },
				// { left: "모든 이들이 나를 기억하는 세상", right: "아무도 나를 모르는 완전한 자유의 세상" },
				// { left: "평행세계의 또 다른 나를 만나는 여행", right: "미래 세대와 직접 대화할 수 있는 기술" },
				// { left: "단 한 번의 완벽한 공연으로 전설이 되기", right: "평생 무대 뒤에서 최고의 조력자로 남기" },
				// { left: "거대한 마법 학교에서 수업 듣기", right: "최첨단 사이버 학교에서 가상 수업 듣기" }

			];
			
			// 예시 결과 데이터 매칭 (원하시면 DB나 JSON에서 가져올 수도 있음)
			const resultData = {
			"시간을 돌릴 수 있는 능력": {
				message: "시간의 주인이 된 당신에게 어울리는 작품은...",
				title: "슈타인즈 게이트",
				poster: "img/poster/pt134.webp",
				tags: ["#타임루프", "#SF", "#로맨스"],
				link: "movie_detail.php?id=134"
			},
			"미래를 미리 볼 수있는 능력": {
				message: "미래를 꿰뚫는 당신에게 추천하는 작품은...",
				title: "닥터 후",
				poster: "img/poster/pt408.webp",
				tags: ["#SF", "#공상과학", "#시간여행"],
				link: "movie_detail.php?id=408"
			},
			// ...다른 선택지도 추가 가능
			};

			const leftCard = document.querySelector(".a_balance_card.left");
			const rightCard = document.querySelector(".a_balance_card.right");
			const leftText = document.querySelector(".a_balance_text_value_left");
			const rightText = document.querySelector(".a_balance_text_value_right");
			const resultArea = document.querySelector(".a_balance_result_area");

			// 결과 영역 내부 요소
			const resultMessage = document.getElementById("a_result_message_text");
			const resultTitle = document.getElementById("a_result_movie_title_value");
			const resultPoster = document.querySelector(".a_result_movie_poster");
			const resultTags = document.getElementById("a_result_tags_container");
			const resultLink = document.getElementById("a_result_view_details_btn");
			const restartBtn = document.querySelector(".a_balance_restart_btn");

			function loadQuestion() {
			const index = Math.floor(Math.random() * balanceQuestions.length);
			const question = balanceQuestions[index];

			if (Math.random() > 0.5) {
				leftText.innerHTML = question.left;
				rightText.innerHTML = question.right;
			} else {
				leftText.innerHTML = question.right;
				rightText.innerHTML = question.left;
			}

			document.querySelector(".a_balance_game_container").style.display = "flex";
			resultArea.style.display = "none";

			leftCard.style.pointerEvents = "auto";
			rightCard.style.pointerEvents = "auto";
			}

			// 결과 표시 함수
			function showResult(optionText) {
			// <br> 제거 (HTML 태그 무시)
			const cleanOption = optionText.replace(/<br\s*\/?>/gi, "").trim();

			const data = resultData[cleanOption];
			// const data = resultData[optionText];

			if (data) {
				resultMessage.textContent = data.message;
				resultTitle.textContent = data.title;
				resultPoster.src = data.poster;
				resultTags.innerHTML = data.tags.map(tag => `<span class="a_result_tag">${tag}</span>`).join(" ");
				resultLink.href = data.link;
			} else {
				resultMessage.textContent = `"${cleanOption}"를 선택한 당신의 취향을 분석 중입니다...`;
				resultTitle.textContent = "Coming Soon";
				resultPoster.src = "img/default_poster.webp";
				resultTags.innerHTML = `<span class="a_result_tag">#밸런스게임</span>`;
				resultLink.href = "#";
			}

			document.querySelector(".a_balance_game_container").style.display = "none";
			resultArea.style.display = "block";
			}

			// 이벤트
			leftCard.addEventListener("click", () => {
			showResult(leftText.textContent);
			});
			rightCard.addEventListener("click", () => {
			showResult(rightText.textContent);
			});

			// 다시 하기
			restartBtn.addEventListener("click", () => {
			loadQuestion();
			});

			// 시작 시 로드
			loadQuestion();
		</script>
</body>
</html>
