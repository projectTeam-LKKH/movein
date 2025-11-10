<?php
session_start();
include_once 'login/db_connect.php'; // DB 연결 파일 (login.php랑 동일한 파일)

$nickname = $_SESSION['nickname'] ?? null;
$userid = $_SESSION['userid'] ?? null;

$favorite_genres = [];
$first_favorite = '';
$favorite_movies = [];

// ✅ 로그인한 경우에만 DB에서 선호 장르 불러오기
if ($userid) {
    $sql = "SELECT favorite_genres FROM User WHERE userid = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('s', $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // favorite_genres가 JSON으로 저장되어 있다고 가정
        $favorite_genres = json_decode($row['favorite_genres'], true) ?? [];
        $first_favorite = $favorite_genres[0];
      }
}

// 취향에 따른 추천 리스트 불러오기
if ($first_favorite) {
  $sql = "
      SELECT id, title
      FROM movies
      WHERE JSON_CONTAINS(genre, JSON_QUOTE(?))
      ORDER BY release_date DESC
      LIMIT 8
  ";
  $stmt = $connect->prepare($sql);
  $stmt->bind_param('s', $first_favorite);
  $stmt->execute();
  $favorite_movies = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
// 플랫폼 선택 (GET 파라미터)
$platform = $_GET['platform'] ?? 'All'; // 기본값은 All
$platform = trim($platform); // 혹시 공백 제거

// 요즘 대세 영화 TOP10 (애니 제외, 플랫폼 필터 적용)
$sql = "
SELECT m.id, m.title, m.release_date, m.streaming, 
       ROUND(IFNULL(AVG(c.rating),0)) AS avg_rating
FROM movies m
LEFT JOIN comments c ON m.id = c.movie_id
WHERE NOT JSON_CONTAINS(m.genre, JSON_QUOTE('애니'))
  AND m.release_date < CURDATE() 
  AND m.type = '영화'
";

// All이 아닌 경우 JSON_CONTAINS로 필터
if($platform !== 'All'){
    // SQL 인젝션 방지를 위해 mysqli_real_escape_string 사용
    $platform_esc = $connect->real_escape_string($platform);
    $sql .= " AND JSON_CONTAINS(streaming, '\"$platform_esc\"')";
    // $sql .= " AND streaming LIKE '%\"$platform_esc\"%'";
}
$sql .= " GROUP BY m.id
          ORDER BY m.release_date DESC
          LIMIT 10";

$result = $connect->query($sql);
$hot_movies = $result->fetch_all(MYSQLI_ASSOC);

// 플랫폼 선택 (GET 파라미터)
$platform2 = $_GET['platform2'] ?? 'All'; // 기본값은 All
$platform2 = trim($platform2); // 혹시 공백 제거

// 요즘 대세 영화외 TOP10
$sql = "
SELECT m.id, m.title, m.release_date, m.streaming,
       ROUND(IFNULL(AVG(c.rating),0)) AS avg_rating
FROM movies m
LEFT JOIN comments c ON m.id = c.movie_id
WHERE m.release_date < CURDATE()
  AND m.type != '영화'
";

// All이 아닌 경우 JSON_CONTAINS로 필터
if($platform2 !== 'All'){
    // SQL 인젝션 방지를 위해 mysqli_real_escape_string 사용
    $platform_esc = $connect->real_escape_string($platform2);
    $sql .= " AND JSON_CONTAINS(streaming, '\"$platform_esc\"')";
    // $sql .= " AND streaming LIKE '%\"$platform_esc\"%'";
}

$sql .= " GROUP BY m.id
          ORDER BY m.release_date DESC
          LIMIT 10";

$result = $connect->query($sql);
$hot_dramas = $result->fetch_all(MYSQLI_ASSOC);

?>


<!DOCTYPE html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MoveIn</title>

    <!-- CSS -->
    <link rel="stylesheet" href="css/reset.css" />
    <link rel="stylesheet" href="css/root.css" />
    <link rel="stylesheet" href="css/main.css" />
    <link rel="stylesheet" href="css/import.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  </head>

  <body>
    
    <div id="container">
      <!-- 헤더 -->
      <header id="header">
        <a href="index.php">
          <img src="img/logo.png" alt="MOVEINlogo" />
        </a>

        <div class="header-icon">
          <img src="img/alarm_f5f5f5.png" alt="alarm" />
          <img class="hambtn" src="img/hamburger_f5f5f5.png" alt="hamburger" />
        </div>

        <nav class="hamburger">
          <ul class="ham-title-box">
            <li class="ham-title">
              <span>종류별</span>
              <ul class="ham-sub-box">
                <li class="ham-sub active">영화</li>
                <li class="ham-sub">드라마</li>
                <li class="ham-sub">예능</li>
                <li class="ham-sub">다큐멘터리</li>
                <li class="ham-sub">애니메이션</li>
                <li class="ham-sub">스포츠</li>
              </ul>
            </li>

            <li class="ham-title">
              <span>OTT별</span>
              <ul class="ham-sub-box">
                <li class="ham-sub">넷플릭스</li>
                <li class="ham-sub">쿠팡플레이</li>
                <li class="ham-sub">티빙</li>
                <li class="ham-sub">웨이브</li>
                <li class="ham-sub">디즈니+</li>
                <li class="ham-sub">왓챠</li>
                <li class="ham-sub">기타</li>
              </ul>
            </li>

            <li class="ham-title">
              <span>장르별</span>
              <ul class="ham-sub-box">
                <li class="ham-sub">드라마</li>
                <li class="ham-sub">로맨스</li>
                <li class="ham-sub">액션</li>
                <li class="ham-sub">스릴러</li>
                <li class="ham-sub">코미디</li>
                <li class="ham-sub">미스터리</li>
                <li class="ham-sub">판타지</li>
              </ul>
            </li>

            <li class="ham-title">
              <span>랭킹순</span>
              <ul class="ham-sub-box">
                <li class="ham-sub">조회수 랭킹순</li>
                <li class="ham-sub">이용자 랭킹순</li>
              </ul>
            </li>

            <li class="ham-title">
              <span>평점순</span>
              <ul class="ham-sub-box">
                <li class="ham-sub">전체 평점순</li>
                <li class="ham-sub">종류별 평점순</li>
                <li class="ham-sub">OTT별 평점순</li>
                <li class="ham-sub">장르별 평점순</li>
              </ul>
            </li>

            <li class="ham-title">
              <span>탐색</span>
              <ul class="ham-sub-box">
                <li class="ham-sub">오늘의 작품 티켓 뽑기</li>
                <li class="ham-sub">밸런스 게임</li>
                <li class="ham-sub">나만의 보석 찾기</li>
              </ul>
            </li>

            <li class="ham-title">
              <span>커뮤니티</span>
              <ul class="ham-sub-box">
                <li class="ham-sub">감상 피드</li>
              </ul>
            </li>

            <li class="ham-title">
              <span>마이페이지</span>
              <ul class="ham-sub-box">
                <li class="ham-sub">나의 수집함</li>
                <li class="ham-sub">내가 남긴 여정</li>
                <li class="ham-sub">선호 장르 관리</li>
              </ul>
            </li>
            <li class="ham-title">
              <span>로그인 설정</span>
              <ul class="ham-sub-box">
                <?php if ($nickname): ?>
                  <!-- ✅ 로그인된 상태 -->
                  <li class="ham-sub"><a href="login/reset_password.php">비밀번호 변경</a></li>
                  <li class="ham-sub"><a href="login/logout.php">로그아웃</a></li>
                <?php else: ?>
                  <!-- ❌ 로그인 안된 상태 -->
                  <li class="ham-sub"><a href="login/login.php">로그인</a></li>
                  <li class="ham-sub"><a href="login/register.php">회원가입</a></li>
                <?php endif; ?>
              </ul>
            </li>
          </ul>
        </nav>
      </header>
      <div class="modal-bg"></div>

      <!-- 검색창 -->
      <form class="search-f">
        <label for="search" class="search skip">검색어 입력</label>
        <div class="search-box">
          <button>
            <img src="img/search_3B393C.png" alt="search_btn" />
          </button>
          <input
            class="search-in"
            type="text"
            id="search"
            placeholder="당신의 마음을 뒤흔들 작품을 검색해보세요"
          />
        </div>
      </form>
      <!-- 검색창 끝-->

      <!-- 닉네임 활성화 칸 -->
      <div class="user-txt-box">
        <?php if ($nickname): ?>
          <h2 class="user-txt"><span class="user-nick"><?php echo htmlspecialchars($nickname); ?></span>님,
          <br>취향을 탐험할 준비 되셨나요?</h2>
        <?php else: ?>
          <h2 class="user-txt">나만의 취향 탐험,<br> 로그인으로 시작하세요</h2>
        <?php endif; ?>
      </div>
      
      <main>
        <div class="container">
          <!-- 카테고리 원 박스 구현 부탁드립니다. navi-wrap을 빠져나오면 안됩니다. 
          그리고 원이 모서리 부분에 잘리지 않게 해주세요-->
          <section id="navi-wrap">
            <div class="bubble-panel">
              <div id="genre-bubble-container" style="width:100%; aspect-ratio: 1.06 /1;"></div>
          </div>
          </section>
        </div>

    <!-- 데이터에 해당되는 장르를 보여주는 구간 -->
     <section class="favorite">
      <div class="favorite-txt-box">
        <p class="favorite-txt">
          <?php if ($first_favorite): ?>
            <span class="input"><?= htmlspecialchars($first_favorite) ?></span>
            <span>장르를 좋아하신다면, 이건 어때요?</span>
          <?php endif; ?>
        </p>
      </div>
    
      <?php if (!empty($favorite_movies)): ?>
        <div class="favorite-list">
          <ul class="favorite-list-box">
            <?php foreach ($favorite_movies as $movie): ?>
              <?php
                  $poster_path = sprintf("img/poster/pt%03d.webp", $movie['id']);
                  // 실제 서버 경로로 파일 존재 여부 확인
                  if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/movein/" . $poster_path)) {
                    $img_tag = '<img src="' . htmlspecialchars($poster_path) . '" alt="poster" style="max-width:65px; max-height:65px;">';
                  } else {
                      $img_tag = '<div style="width:65px; height:65px; background:#eee; color:#555; display:flex; align-items:center; justify-content:center; text-align:center;">이미지 없음</div>';
                  }
              ?>
              <li class="favorite-thing">
                  <a href="movie_detail.php?id=<?= htmlspecialchars($movie['id']) ?>">
                      <?= $img_tag ?>
                  </a>
                  <button class="likeBtn">
                      <img src="img/heart_6f6c76.png" alt="heart button">
                  </button>
              </li>
            <?php endforeach; ?>
            <!-- "더보기" 버튼 -->
            <li class="favorite-thing">
                <a href="javascript:void(0);" class="blankbtn" onclick="showComingSoon()">
                    <img src="img/next_icon_6F6C76.png" alt="moreBtn">
                </a>
            </li>
          </ul>
        </div>
      <?php endif; ?>
    </section>


     
     <!-- 요즘 대세 영화는? -->
     <section class="hot-container">
      <div class="hot-title">
        <div class="hot-txt-box">
          <h3 class="hot-txt">요즘 대세 영화는?</h3>
          <img src="img/next_icon_6F6C76.png" alt="다음 버튼">
        </div>
        
        <div class="hot-wrap">
          <ul class="hot-nav-box">
            <li class="all-btn <?= ($platform === 'All') ? 'active' : '' ?>" data-platform="All">
              <a href="?platform=All"><p>All</p><span class="point"></span></a>
            </li>
            <li class="all-btn <?= ($platform === 'Netflix') ? 'active' : '' ?>" data-platform="Netflix">
              <a href="?platform=Netflix"><img src="img/netflix.png" alt="Netflix"><span class="point"></span></a>
            </li>
            <li class="all-btn <?= ($platform === 'Watcha') ? 'active' : '' ?>" data-platform="Watcha">
              <a href="?platform=Watcha"><img src="img/watcha.png" alt="Watcha"><span class="point"></span></a>
            </li>
            <li class="all-btn <?= ($platform === 'Wavve') ? 'active' : '' ?>" data-platform="Wavve">
              <a href="?platform=Wavve"><img src="img/wavve.png" alt="Wavve"><span class="point"></span></a>
            </li>
            <li class="all-btn <?= ($platform === 'TVING') ? 'active' : '' ?>" data-platform="TVING">
              <a href="?platform=TVING"><img src="img/TVING.png" alt="TVING"><span class="point"></span></a>
            </li>
            <li class="all-btn <?= ($platform === 'Disney+') ? 'active' : '' ?>" data-platform="Disney+">
              <a href="?platform=Disney%2B"><img src="img/disney.png" alt="Disney+"><span class="point"></span></a>
            </li>
            <li class="all-btn <?= ($platform === 'Coupang') ? 'active' : '' ?>" data-platform="Coupang">
              <a href="?platform=Coupang"><img src="img/coupang.png" alt="Coupang"><span class="point"></span></a>
            </li>
            <li class="all-btn <?= ($platform === 'Other') ? 'active' : '' ?>" data-platform="Other">
              <a href="?platform=Other"><p>Other</p><span class="point"></span></a>
            </li>
          </ul>
        </div>

        <div class="poster-wrap">
          <div class="poster-box">
            <ul class="all-posters">
              <?php foreach ($hot_movies as $movie): ?>
                <?php 
                  $streaming = json_decode($movie['streaming'], true) ?? [];
                  $platform_classes = implode(' ', array_map('strtolower', $streaming));
                  $poster_path = sprintf("img/poster/pt%03d.webp", $movie['id']);
                  if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/movein/" . $poster_path)) {
                      $img_tag = '<img src="' . htmlspecialchars($poster_path) . '" alt="poster">';
                  } else {
                      $img_tag = '<div style="width:200px; height:250px; background:#eee; color:#555; display:flex; align-items:center; justify-content:center; text-align:center;">이미지 없음</div>';
                  }
                  $avg_rating = (int)$movie['avg_rating'];
                ?>
                <li class="all-poster <?= $platform_classes ?>">
                <a href="movie_detail.php?id=<?= htmlspecialchars($movie['id']) ?>">
                    <?= $img_tag ?>
                    <p class="poster-title"><?= htmlspecialchars($movie['title']) ?></p>
                </a>


                  <div class="detail-box">
                    <div class="date-box">
                      <p>개봉일</p>
                      <span class="date-detail"><?= htmlspecialchars($movie['release_date']) ?></span>
                    </div>
                    <div class="score-contain">
                      <p class="score">별점</p>
                      <ul class="score-box">
                          <?php 
                          for ($i = 1; $i <= 5; $i++) {
                              if ($i <= $avg_rating) {
                                  echo '<li class="score"><img src="img/star_49E99C.png" alt="star"></li>';
                              } else {
                                  echo '<li class="score"><img src="img/star_6f6c76.png" alt="star"></li>';
                              }
                          }
                          ?>
                      </ul>
                    </div>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    </section>

     <!-- 요즘 대세 드라마는? -->
    <section class="hot-container">
      <div class="hot-title">
        <div class="hot-txt-box">
          <h3 class="hot-txt">요즘 대세 드라마&예능은?</h3>
          <img src="img/next_icon_6F6C76.png" alt="다음 버튼">
        </div>

        <div class="hot-wrap">
          <ul class="hot-nav-box">
            <li class="all-btn <?= ($platform2 === 'All') ? 'active' : '' ?>" data-platform="All">
              <a href="?platform2=All"><p>All</p><span class="point"></span></a>
            </li>
            <li class="all-btn <?= ($platform2 === 'Netflix') ? 'active' : '' ?>" data-platform="Netflix">
              <a href="?platform2=Netflix"><img src="img/netflix.png" alt="Netflix"><span class="point"></span></a>
            </li>
            <li class="all-btn <?= ($platform2 === 'Watcha') ? 'active' : '' ?>" data-platform="Watcha">
              <a href="?platform2=Watcha"><img src="img/watcha.png" alt="Watcha"><span class="point"></span></a>
            </li>
            <li class="all-btn <?= ($platform2 === 'Wavve') ? 'active' : '' ?>" data-platform="Wavve">
              <a href="?platform2=Wavve"><img src="img/wavve.png" alt="Wavve"><span class="point"></span></a>
            </li>
            <li class="all-btn <?= ($platform2 === 'TVING') ? 'active' : '' ?>" data-platform="TVING">
              <a href="?platform2=TVING"><img src="img/TVING.png" alt="TVING"><span class="point"></span></a>
            </li>
            <li class="all-btn <?= ($platform2 === 'Disney+') ? 'active' : '' ?>" data-platform="Disney+">
              <a href="?platform2=Disney%2B"><img src="img/disney.png" alt="Disney+"><span class="point"></span></a>
            </li>
            <li class="all-btn <?= ($platform2 === 'Coupang') ? 'active' : '' ?>" data-platform="Coupang">
              <a href="?platform2=Coupang"><img src="img/coupang.png" alt="Coupang"><span class="point"></span></a>
            </li>
            <li class="all-btn <?= ($platform2 === 'Other') ? 'active' : '' ?>" data-platform="Other">
              <a href="?platform2=Other"><p>Other</p><span class="point"></span></a>
            </li>
          </ul>
        </div>

        <!-- 포스터 영역 -->
        <div class="poster-wrap">
          <div class="poster-box">
            <ul class="all-posters">
              <?php foreach ($hot_dramas as $drama): ?>
                <?php 
                  $streaming = json_decode($drama['streaming'], true) ?? [];
                  $platform_classes = implode(' ', array_map('strtolower', $streaming));
                  $poster_path = sprintf("img/poster/pt%03d.webp", $drama['id']);
                  if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/movein/" . $poster_path)) {
                      $img_tag = '<img src="' . htmlspecialchars($poster_path) . '" alt="poster">';
                  } else {
                      $img_tag = '<div style="width:200px; height:250px; background:#eee; color:#555; display:flex; align-items:center; justify-content:center; text-align:center;">이미지 없음</div>';
                  }
                  $avg_rating = (int)$drama['avg_rating'];
                ?>
                <li class="all-poster <?= $platform_classes ?>">
                  <a href="movie_detail.php?id=<?= htmlspecialchars($drama['id']) ?>">
                      <?= $img_tag ?>
                      <p class="poster-title"><?= htmlspecialchars($drama['title']) ?></p>
                  </a>

                  <div class="detail-box">
                    <div class="date-box">
                      <p>방영 시작일</p>
                      <span class="date-detail"><?= htmlspecialchars($drama['release_date']) ?></span>
                    </div>
                    <div class="score-contain">
                      <p class="score">별점</p>
                      <ul class="score-box">
                          <?php 
                          for ($i = 1; $i <= 5; $i++) {
                              if ($i <= $avg_rating) {
                                  echo '<li class="score"><img src="img/star_49E99C.png" alt="star"></li>';
                              } else {
                                  echo '<li class="score"><img src="img/star_6f6c76.png" alt="star"></li>';
                              }
                          }
                          ?>
                      </ul>
                    </div>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    </section>

    
     <!-- 무브오너들의 감상평 -->
      <section>
        <div class="review-txt-box">
          <div class="hot-txt-box">
            <h3 class="hot-txt">무브오너들의 감상평</h3>
            <img src="img/next_icon_6F6C76.png" alt="다음 버튼">
          </div>

          <div class="review-container">
            <!-- 데이터 첨부 필요 -->
             <div class="review-wrap">
              <ul class="review-card-box">
                <li class="review-card">
                  <!-- 리뷰피드로 연결되는 a묶음 (구현 미정) -->
                  <a href="" class="review">
                    <!-- 작품 데이터가 들어가야 합니다 -->
                    <div class="left-box">
                      <img src="img/poster/pt003.webp" alt="poster">
                    </div>
                    <div class="right-box">
                      <p class="work-name">작품 제목이 들어가는 곳</p>
                      <p class="user-review">유저들의 리뷰가 들어가야 하는 곳으로 두줄까지 출력되며 두 줄 이상은
                        줄바꿈으로 처리됩니다 유저들의 리뷰가 들어가야 하는 곳으로 두줄까지 출력되며 두 줄 이상은
                        줄바꿈으로 처리됩니다 css 참고
                      </p>
                      <div class="user-box">
                        <img src="img/user_6F6C76.png" alt="user icon">
                        <p class="user-nick">한글 아이디</p><span>님의 감상</span>
                      </div>

                    </div>
                  </a>
                </li>
                <li class="review-card">
                  <!-- 리뷰피드로 연결되는 a묶음 (구현 미정) -->
                  <a href="" class="review">
                    <!-- 작품 데이터가 들어가야 합니다 -->
                    <div class="left-box">
                      <img src="img/poster/pt003.webp" alt="poster">
                    </div>
                    <div class="right-box">
                      <p class="work-name">작품 제목이 들어가는 곳</p>
                      <p class="user-review">유저들의 리뷰가 들어가야 하는 곳으로 두줄까지 출력되며 두 줄 이상은
                        줄바꿈으로 처리됩니다
                      </p>
                      <div class="user-box">
                        <img src="img/user_6F6C76.png" alt="user icon">
                        <p class="user-nick">한글 아이디</p><span>님의 감상</span>
                      </div>

                    </div>
                  </a>

                </li>
                <li class="review-card">
                  <!-- 리뷰피드로 연결되는 a묶음 (구현 미정) -->
                  <a href="" class="review">
                    <!-- 작품 데이터가 들어가야 합니다 -->
                    <div class="left-box">
                      <img src="img/poster/pt003.webp" alt="poster">
                    </div>
                    <div class="right-box">
                      <p class="work-name">작품 제목이 들어가는 곳</p>
                      <p class="user-review">유저들의 리뷰가 들어가야 하는 곳으로 두줄까지 출력되며 두 줄 이상은
                        줄바꿈으로 처리됩니다
                      </p>
                      <div class="user-box">
                        <img src="img/user_6F6C76.png" alt="user icon">
                        <p class="user-nick">한글 아이디</p><span>님의 감상</span>
                      </div>

                    </div>
                  </a>

                </li>
                <li class="review-card">
                  <!-- 리뷰피드로 연결되는 a묶음 (구현 미정) -->
                  <a href="" class="review">
                    <!-- 작품 데이터가 들어가야 합니다 -->
                    <div class="left-box">
                      <img src="img/poster/pt003.webp" alt="poster">
                    </div>
                    <div class="right-box">
                      <p class="work-name">작품 제목이 들어가는 곳</p>
                      <p class="user-review">유저들의 리뷰가 들어가야 하는 곳으로 두줄까지 출력되며 두 줄 이상은
                        줄바꿈으로 처리됩니다
                      </p>
                      <div class="user-box">
                        <img src="img/user_6F6C76.png" alt="user icon">
                        <p class="user-nick">한글 아이디</p><span>님의 감상</span>
                      </div>

                    </div>
                  </a>

                </li>
                <li class="review-card">
                  <!-- 리뷰피드로 연결되는 a묶음 (구현 미정) -->
                  <a href="" class="review">
                    <!-- 작품 데이터가 들어가야 합니다 -->
                    <div class="left-box">
                      <img src="img/poster/pt003.webp" alt="poster">
                    </div>
                    <div class="right-box">
                      <p class="work-name">작품 제목이 들어가는 곳</p>
                      <p class="user-review">유저들의 리뷰가 들어가야 하는 곳으로 두줄까지 출력되며 두 줄 이상은
                        줄바꿈으로 처리됩니다
                      </p>
                      <div class="user-box">
                        <img src="img/user_6F6C76.png" alt="user icon">
                        <p class="user-nick">한글 아이디</p><span>님의 감상</span>
                      </div>

                    </div>
                  </a>

                </li>
              </ul>
            </div>

            <!-- 감상평 남기기 버튼. 로그인이 되어 있지 않을 때에는 로그인 창으로 넘어간다 -->
            <div class="btn-form-container">
              <div class="review-input">
                <div class="review-input-box">
                  <div class="formBtn-box">
                    <p class="btn-text">감상평 남기기</p>
                    <button type="submit" class="formBtn"><img src="img/pen_6f6c76.png" alt="review icon"> </button>
                  </div>
                </div>
              </div>
              
              <!-- 버튼을 누르면 폼이 열린다 이부분 데이터 연결 후에 css 다시 만져야 할듯 -->
              <form class="review-form" action="review_insert.php" method="post">
                <div class="input-box form">
                  <!-- [1] 작품명 자동검색 입력 -->
                  <div class="title-search-box">
                    <label for="work-name" class="skip">작품 이름</label>
                    <input
                      type="text"
                      id="work-name"
                      name="work_name"
                      placeholder="작품 이름을 입력하세요"
                      autocomplete="off"
                      required
                    />
                    <button><img src="img/search_6F6C76.png" alt="search btn"></button>
                  </div>
                  <!-- 자동완성 목록 들어가는 곳(나중에 css 잡기)-->
                    <ul id="search-result" class="search-result"></ul>
                  <!-- [2] 감상평 입력 -->
                  <div class="review-text-box">
                    <label for="review-text" class="skip">감상평</label>
                    <textarea
                      id="review-text"
                      name="review"
                      placeholder="감상평을 작성하세요"
                      required
                    ></textarea>
                  
                     <!-- [3] 제출 버튼 -->
                  <button type="submit" class="submit"><img src="img/pen_6f6c76.png" alt="submit btn"></button>
                  </div>
                 
                </div>
                <div class="prev-btn-box">
                  <img class="prev-btn" src="img/prev_6f6c76.png" alt="prev btn">
                </div>
              </form>
            </div>

          </div>
        </div>
      </section>
     </main>

     <div id="footer"></div>
    <div id="bottom-nav"></div>

    <!-- JS -->
    <!-- JS -->
     <script src="js/import.js"></script>
    <script src="js/main.js"></script>

    
  <script defer src="https://cdnjs.cloudflare.com/ajax/libs/matter-js/0.19.0/matter.min.js"></script>
  <script defer src="js/genre-bubbles.js"></script>

  <script>

function showComingSoon() {
    // 팝업 div 생성
    const popup = document.createElement('div');
    popup.textContent = "개발중인 화면입니다.";
    popup.style.position = "fixed";
    popup.style.top = "50%";
    popup.style.left = "50%";
    popup.style.transform = "translate(-50%, -50%)";
    popup.style.background = "#333";
    popup.style.color = "#fff";
    popup.style.padding = "20px 40px";
    popup.style.borderRadius = "8px";
    popup.style.boxShadow = "0 0 10px rgba(0,0,0,0.5)";
    popup.style.zIndex = "9999";
    popup.style.fontSize = "16px";
    popup.style.textAlign = "center";
    
    document.body.appendChild(popup);

    // 1초 후 자동 제거
    setTimeout(() => {
        popup.remove();
    }, 1000);
}

  // main-bubbles-init.js
  // 2025-11-10 정리본 : 중복 제거 및 리사이즈/스크롤 복원 최적화

  // [A] 페이지 진입 시 버블 초기화
  window.addEventListener("DOMContentLoaded", () => {
    const app = window.genreBubbleApp?.init("genre-bubble-container");
    if (!app) return;

    // PHP → JS
    const favoriteGenres = <?php echo json_encode($favorite_genres); ?>;
    const isLoggedIn = <?php echo $nickname ? 'true' : 'false'; ?>;

    // 모든 버블에 공통 적용할 그라데이션 옵션
    const GRAD_OPT = { gradient: { inner: "#504399", outer: "#8670FF" } };

    const allGenres = [
      { name: "애니", color: "#8670FF" },
      { name: "드라마", color: "#8670FF" },
      { name: "액션", color: "#8670FF" },
      { name: "SF", color: "#8670FF" },
      { name: "코미디", color: "#8670FF" },
      { name: "판타지", color: "#8670FF" },
      { name: "스릴러", color: "#8670FF" },
      { name: "로맨스", color: "#8670FF" },
    ];

    if (!isLoggedIn) {
      // ✅ 비로그인도 전부 그라데이션
      allGenres.forEach((g) => app.createGenreBubble(g.name, g.color, 40, GRAD_OPT));
    } else {
      const base = 40, max = 90, step = 5;

      allGenres.forEach((g) => {
        const idx = favoriteGenres.indexOf(g.name);
        if (idx !== -1) {
          const size = Math.max(base, max - idx * step);

          // ✅ 로그인도 전부 그라데이션 (+ 1순위만 볼드 유지)
          const opts = (idx === 0)
            ? { ...GRAD_OPT, fontWeight: 700 } // 1순위 강조(굵기만)
            : GRAD_OPT;

            const color = (idx === 0) ? "#49e99c" : g.color; // 1순위 색상 변경

            app.createGenreBubble(g.name, color, size, opts);
        } else {
          app.createGenreBubble(g.name, g.color, base, GRAD_OPT);
        }
      });
    }
  });

  
  let resizeTimeout;

  window.addEventListener('resize', () => {
    // 이전 타이머 취소
    if (resizeTimeout) clearTimeout(resizeTimeout);

    // 마지막 resize 후 500ms 지나면 실행
    resizeTimeout = setTimeout(() => {
      console.log('리사이즈 멈춤, 새로고침 실행');
      location.reload();
    }, 500); // 0.5초 동안 멈추면 새로고침
  });

  window.addEventListener("beforeunload", () => {
  sessionStorage.setItem("scrollY", window.scrollY);
});

window.addEventListener("load", () => {
  const savedY = sessionStorage.getItem("scrollY");
  if (savedY !== null) {
    window.scrollTo(0, parseInt(savedY));
  }
});

document.addEventListener("click", (e) => {
  const target = e.target.closest("button, input[type='submit']");
  if (target) {
    sessionStorage.setItem("scrollY", window.scrollY);
  }
});

window.addEventListener("pagehide", () => {
  sessionStorage.setItem("scrollY", window.scrollY);
});

// [2] 페이지 로드 시 스크롤 복원
window.addEventListener("DOMContentLoaded", () => {
  const savedY = sessionStorage.getItem("scrollY");
  if (savedY !== null) {
    // DOM 렌더링 후 잠시 기다렸다가 복원 (모바일 안정화용)
    setTimeout(() => {
      window.scrollTo(0, parseInt(savedY));
    }, 50);
  }
});

// [3] 버튼 클릭 시 수동 저장
document.addEventListener("click", (e) => {
  const target = e.target.closest("button, input[type='submit']");
  if (target) {
    sessionStorage.setItem("scrollY", window.scrollY);
  }
});
</script>
  </body>
</html>
