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

// 요즘 대세 영화 TOP10 (애니 제외)
$sql = "
SELECT id, title, release_date, streaming
FROM movies
WHERE NOT JSON_CONTAINS(genre, JSON_QUOTE('애니'))
AND release_date < '2025-11-07' AND type = '영화'
ORDER BY release_date DESC
LIMIT 10
";

$result = $connect->query($sql);
$hot_movies = $result->fetch_all(MYSQLI_ASSOC);

// 요즘 대세 영화외 TOP10
$sql = "
SELECT id, title, release_date, streaming
FROM movies
WHERE release_date < '2025-11-07'
AND type != '영화'
ORDER BY release_date DESC
LIMIT 10
";

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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

            <!-- <li class="ham-title">
              <span>로그인 설정</span>
              <ul class="ham-sub-box">
                <li class="ham-sub"><a href="login/login.php">로그인</a></li>
                <li class="ham-sub"><a href="login/register.php">회원가입</a></li>
                <li class="ham-sub"><a href="login/logout.php">로그아웃</a></li>
                <li class="ham-sub"><a href="login/reset_password.php">비밀번호 변경</li>
              </ul>
            </li> -->
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
              <div id="genre-bubble-container" style="width:100%; height:320px;"></div>
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
          <?php else: ?>
            <span>아직 수집된 취향이 없지만 이 장르는 어때요?</span>
          <?php endif; ?>
        </p>
      </div>
          
      <div class="favorite-list">
        <ul class="favorite-list-box">
          <?php if (!empty($favorite_movies)): ?>
            <?php foreach ($favorite_movies as $movie): ?>
              <?php
                $poster_path = sprintf("img/poster/pt%03d.webp", $movie['id']);
              ?>
              <li class="favorite-thing">
                <a href="movie_detail.php?id=<?= htmlspecialchars($movie['id']) ?>">
                  <img src="<?= htmlspecialchars($poster_path) ?>" alt="poster">
                </a>
                <button class="likeBtn">
                  <img src="img/heart_6f6c76.png" alt="heart button">
                </button>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li>추천할 영화가 없습니다.</li>
          <?php endif; ?>

          <!-- "더보기" 버튼 -->
          <li class="favorite-thing">
            <a href="genre_recommend.php?genre=<?= urlencode($first_favorite) ?>" class="blankbtn">
              <img src="img/next_icon_6F6C76.png" alt="moreBtn">
            </a>
          </li>
        </ul>
      </div>
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
            <li class="all-btn active" data-platform="All"><p>All</p><span class="point"></span></li>
            <li class="all-btn" data-platform="Netflix"><img src="img/netflix.png" alt="Netflix"><span class="point"></span></li>
            <li class="all-btn" data-platform="Watcha"><img src="img/watcha.png" alt="Watcha"><span class="point"></span></li>
            <li class="all-btn" data-platform="Wavve"><img src="img/wavve.png" alt="Wavve"><span class="point"></span></li>
            <li class="all-btn" data-platform="TVING"><img src="img/TVING.png" alt="TVING"><span class="point"></span></li>
            <li class="all-btn" data-platform="Disney+"><img src="img/disney.png" alt="Disney+"><span class="point"></span></li>
            <li class="all-btn" data-platform="Coupang"><img src="img/coupang.png" alt="Coupang"><span class="point"></span></li>
            <li class="all-btn" data-platform="Other"><p>Other</p><span class="point"></span></li>
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
                        <li class="score"><img src="img/star_49E99C.png" alt="star"></li>
                        <li class="score"><img src="img/star_49E99C.png" alt="star"></li>
                        <li class="score"><img src="img/star_49E99C.png" alt="star"></li>
                        <li class="score"><img src="img/star_49E99C.png" alt="star"></li>
                        <li class="score"><img src="img/star_49E99C.png" alt="star"></li>
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
            <li class="all-btn active" data-platform="All"><p>All</p><span class="point"></span></li>
            <li class="all-btn" data-platform="Netflix"><img src="img/netflix.png" alt="Netflix"><span class="point"></span></li>
            <li class="all-btn" data-platform="Watcha"><img src="img/watcha.png" alt="Watcha"><span class="point"></span></li>
            <li class="all-btn" data-platform="Wavve"><img src="img/wavve.png" alt="Wavve"><span class="point"></span></li>
            <li class="all-btn" data-platform="TVING"><img src="img/TVING.png" alt="TVING"><span class="point"></span></li>
            <li class="all-btn" data-platform="Disney+"><img src="img/disney.png" alt="Disney+"><span class="point"></span></li>
            <li class="all-btn" data-platform="Coupang"><img src="img/coupang.png" alt="Coupang"><span class="point"></span></li>
            <li class="all-btn" data-platform="Other"><p>Other</p><span class="point"></span></li>
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
                ?>
                <li class="all-poster <?= $platform_classes ?>">
                  <a href="drama_detail.php?id=<?= htmlspecialchars($drama['id']) ?>">
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
                        <?php for($i=0; $i<5; $i++): ?>
                          <li class="score"><img src="img/star_49E99C.png" alt="star"></li>
                        <?php endfor; ?>
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

    <!-- JS -->
  <script src="js/main.js"></script>

    
  <script defer src="https://cdnjs.cloudflare.com/ajax/libs/matter-js/0.19.0/matter.min.js"></script>
  <script defer src="js/genre-bubbles.js"></script>

  <script>
  window.addEventListener('DOMContentLoaded', () => {
    const bubbleApp = window.genreBubbleApp.init('genre-bubble-container');
    if (!bubbleApp) return;

    // PHP에서 JS로 데이터 전달
    const favoriteGenres = <?php echo json_encode($favorite_genres); ?>;
    const isLoggedIn = <?php echo $nickname ? 'true' : 'false'; ?>;

    // 전체 장르 목록
    const allGenres = [
      { name: "애니", color: "#FFE4B5" },   // 따뜻하고 부드러운 살구빛 — 활기찬 애니 느낌
      { name: "드라마", color: "#FFD6A5" }, // 감성적이면서 부드러운 오렌지톤
      { name: "액션", color: "#A0E7E5" },   // 에너지 넘치는 밝은 청록색
      { name: "SF", color: "#B5EAD7" },     // 미래적인 민트톤
      { name: "코미디", color: "#FFFACD" }, // 유쾌하고 밝은 레몬색
      { name: "판타지", color: "#C7CEEA" }, // 몽환적인 연보라색
      { name: "스릴러", color: "#FFB6B9" }, // 긴장감 있지만 너무 어둡지 않은 핑크빛 붉은색
      { name: "로맨스", color: "#FFD1DC" }  // 달콤한 파스텔 핑크
    ];


    if (!isLoggedIn) {
      // 👤 비로그인: 모두 동일한 크기
      allGenres.forEach(g => {
        bubbleApp.createGenreBubble(g.name, g.color, 30);
      });
    } else {
      // 👤 로그인 상태: 선호 장르 순으로 크기 조정
      const baseSize = 20;
      const maxSize = 40;
      const step = 5;

      allGenres.forEach(g => {
        const idx = favoriteGenres.indexOf(g.name);
        if (idx !== -1) {
          // 좋아하는 순서대로 크기 차등 적용
          const size = maxSize - idx * step;
          bubbleApp.createGenreBubble(g.name, g.color, size > baseSize ? size : baseSize);
        } else {
          // 선호하지 않는 장르는 작게 표시
          bubbleApp.createGenreBubble(g.name, g.color, baseSize);
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
</script>
  </body>
</html>
