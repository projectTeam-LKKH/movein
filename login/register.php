<!DOCTYPE html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>회원가입</title>
    <link rel="stylesheet" href="/css/reset.css" />
    <link rel="stylesheet" href="/css/root.css" />
    <link rel="stylesheet" href="/css/login_form.css" />
    <style></style>
  </head>
  <body>
    <div class="container">
      <div id="headerText" class="header-text">
        <span class="highlight">무브오너</span>가 되어<br />나만의 취향을 확장해
        보세요.
      </div>

      <form
        id="registerForm"
        class="register-form"
        method="POST"
        action="register_process.php"
      >
        <!-- STEP 1 -->
        <div id="step1" class="step active">
          <div class="input-group">
            <input
              type="text"
              name="userid"
              id="userid"
              placeholder="사용하실 아이디를 입력해주세요"
              required
            />
            <button type="button" class="check-btn" onclick="checkId()">
              중복확인
            </button>
          </div>
          <div id="smallText">영문·숫자 조합, 5~15자 이내로 입력해주세요.</div>
          <input
            type="password"
            name="password"
            id="password"
            placeholder="사용하실 비밀번호를 입력해 주세요"
            required
          />
          <input
            type="password"
            name="password_confirm"
            id="password_confirm"
            placeholder="비밀번호 확인"
            required
          />
          <div id="smallText">
            비밀번호는 8자 이상, 영문자 숫자, 특수문자를 섞어주세요.
          </div>
          <button type="button" class="next-btn" onclick="nextStep(2)">
            다음 >
          </button>
        </div>

        <!-- STEP 2 -->
        <div id="step2" class="step">
          <div class="input-group">
            <input
              type="text"
              name="username"
              id="username"
              placeholder="사용하실 닉네임을 입력해 주세요"
              required
            />
            <button type="button" class="check-btn" onclick="checkNickname()">
              중복확인
            </button>
          </div>
          <div id="smallText">
            한글과 영문만 사용 가능하며, 최대 10자까지 입력할 수 있어요
          </div>
          <input
            type="email"
            name="email"
            id="email"
            placeholder="이메일을 입력해 주세요"
            required
          />
          <div id="smallText">
            추후 잃어버린 아이디나 비밀번호를 찾아야 할때 필요한 정보에요
          </div>
          <button type="button" class="next-btn" onclick="nextStep(3)">
            다음 >
          </button>
        </div>

        <!-- STEP 3 -->
        <div id="step3" class="step">
          <label style="color: var(--c--font)">좋아하는 장르</label>
          <div id="genres" class="toggle-section">
            <span class="toggle-btn" onclick="toggleSelection(this)">애니</span>
            <span class="toggle-btn" onclick="toggleSelection(this)"
              >드라마</span
            >
            <span class="toggle-btn" onclick="toggleSelection(this)">액션</span>
            <span class="toggle-btn" onclick="toggleSelection(this)">SF</span>
            <span class="toggle-btn" onclick="toggleSelection(this)"
              >코미디</span
            >
            <span class="toggle-btn" onclick="toggleSelection(this)"
              >판타지</span
            >
            <span class="toggle-btn" onclick="toggleSelection(this)"
              >스릴러</span
            >
            <span class="toggle-btn" onclick="toggleSelection(this)"
              >로맨스</span
            >
          </div>

          <label style="color: var(--c-font)">주로 사용하는 OTT</label>
          <div id="ott" class="toggle-section">
            <span class="toggle-btn" onclick="toggleSelection(this)"
              >넷플릭스</span
            >
            <span class="toggle-btn" onclick="toggleSelection(this)"
              >쿠팡플레이</span
            >
            <span class="toggle-btn" onclick="toggleSelection(this)">티빙</span>
            <span class="toggle-btn" onclick="toggleSelection(this)"
              >웨이브</span
            >
            <span class="toggle-btn" onclick="toggleSelection(this)"
              >디즈니+</span
            >
            <span class="toggle-btn" onclick="toggleSelection(this)">왓챠</span>
            <span class="toggle-btn" onclick="toggleSelection(this)">기타</span>
          </div>

          <label style="color: var(--c--font)">선호 콘텐츠 지역</label>
          <div id="region" class="toggle-section">
            <span class="toggle-btn" onclick="toggleSelection(this)">독일</span>
            <span class="toggle-btn" onclick="toggleSelection(this)">대만</span>
            <span class="toggle-btn" onclick="toggleSelection(this)">미국</span>
            <span class="toggle-btn" onclick="toggleSelection(this)">영국</span>
            <span class="toggle-btn" onclick="toggleSelection(this)">인도</span>
            <span class="toggle-btn" onclick="toggleSelection(this)">일본</span>
            <span class="toggle-btn" onclick="toggleSelection(this)">중국</span>
            <span class="toggle-btn" onclick="toggleSelection(this)"
              >캐나다</span
            >
            <span class="toggle-btn" onclick="toggleSelection(this)">한국</span>
            <span class="toggle-btn" onclick="toggleSelection(this)">기타</span>
            <span class="toggle-btn" onclick="toggleSelection(this)"
              >잘 모르겠어요</span
            >
          </div>

          <input type="hidden" name="favorite_genres" id="favorite_genres" />
          <input type="hidden" name="preferred_ott" id="preferred_ott" />
          <input
            type="hidden"
            name="preferred_regions"
            id="preferred_regions"
          />

          <button type="submit" class="submit-btn">가입하기</button>
        </div>

        <!-- STEP 4 -->
        <div id="step4" class="step">
          <div class="complete-container">
            <img
              src="../img/movein_character.png"
              alt="완료 이미지"
              class="complete-img"
            />
            <div class="button-group">
              <button
                type="button"
                class="login-btn"
                onclick="location.href='login.php'"
              >
                로그인 페이지 바로가기
              </button>
              <button
                type="button"
                class="home-btn"
                onclick="location.href='../index.php'"
              >
                홈으로 돌아가기
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>

    <script>
      const header = document.getElementById("headerText");

      function checkId() {
        const userid = document.getElementById("userid").value;
        if (!userid) return alert("아이디를 입력하세요.");
        fetch("check_id.php?userid=" + encodeURIComponent(userid))
          .then((res) => res.text())
          .then((data) => alert(data));
      }

      function checkNickname() {
        const username = document.getElementById("username").value;
        if (!username) return alert("닉네임을 입력하세요.");
        fetch("check_nickname.php?username=" + encodeURIComponent(username))
          .then((res) => res.text())
          .then((data) => alert(data));
      }

      function nextStep(step) {
        // 현재 단계 찾기
        const currentStep = document.querySelector(".step.active");
        const currentStepId = currentStep.id;

        // 스텝별 입력값 검사
        if (currentStepId === "step1") {
          const userid = document.getElementById("userid").value.trim();
          const password = document.getElementById("password").value.trim();
          const confirm = document
            .getElementById("password_confirm")
            .value.trim();

          if (!userid) return alert("아이디를 입력해주세요.");
          if (!validateUserId(userid))
            return alert("아이디는 영문과 숫자 조합의 5~15자 이내여야 합니다.");
          if (!password) return alert("비밀번호를 입력해주세요.");
          if (!validatePassword(password))
            return alert(
              "비밀번호는 8자 이상, 영문, 숫자, 특수문자를 모두 포함해야 합니다."
            );
          if (!confirm) return alert("비밀번호 확인을 입력해주세요.");
          if (password !== confirm)
            return alert("비밀번호가 일치하지 않습니다.");
        }

        if (currentStepId === "step2") {
          const username = document.getElementById("username").value.trim();
          const email = document.getElementById("email").value.trim();

          if (!username) return alert("닉네임을 입력해주세요.");
          if (!validateNickname(username))
            return alert(
              "닉네임은 한글 또는 영문만 사용 가능하며, 최대 10자까지 입력할 수 있습니다."
            );
          if (!email) return alert("이메일을 입력해주세요.");
          if (!validateEmail(email))
            return alert("올바른 이메일 형식이 아닙니다.");
        }

        document
          .querySelectorAll(".step")
          .forEach((s) => s.classList.remove("active"));
        document.getElementById("step" + step).classList.add("active");

        // 단계별 문구 변경
        if (step === 1) {
          header.classList.remove("up"); // ← 3‧4단계용 클래스 제거
          header.innerHTML = `<span class="highlight">무브오너</span>가 되어<br>나만의 취향을 확장해 보세요.`;
        } else if (step === 2) {
          header.classList.remove("up");
          header.innerHTML = `<span class="highlight">무브인</span> 계정을 만들고<br>새로운 탐험을 시작하세요.`;
        } else if (step === 3) {
          header.classList.add("up");
          header.innerHTML = `<span class="highlight">당신의 취향</span>을 알고 싶어요<br>장르, 플랫폼 등을 선택해주세요.`;
        } else if (step === 4) {
          header.classList.add("up");
          header.innerHTML = `회원가입이 완료되었습니다.<br><span class="highlight">무브인</span>에 오신것을 환영합니다.`;
        }
      }

      document.getElementById("registerForm").onsubmit = async function (e) {
        e.preventDefault(); // 실제 전송 막기
        const genres = [...document.querySelectorAll("#genres .selected")].map(
          (e) => e.textContent
        );
        const ott = [...document.querySelectorAll("#ott .selected")].map(
          (e) => e.textContent
        );
        const region = [...document.querySelectorAll("#region .selected")].map(
          (e) => e.textContent
        );

        // ✅ 빈 배열이라도 JSON 형태로 넣기
        document.getElementById("favorite_genres").value = JSON.stringify(
          genres.length ? genres : []
        );
        document.getElementById("preferred_ott").value = JSON.stringify(
          ott.length ? ott : []
        );
        document.getElementById("preferred_regions").value = JSON.stringify(
          region.length ? region : []
        );

        const password = document.getElementById("password").value;
        const confirm = document.getElementById("password_confirm").value;
        if (password !== confirm) {
          alert("비밀번호가 일치하지 않습니다.");
          nextStep(1);
          return false;
        }

        const formData = new FormData(this);

        try {
          const res = await fetch("register_process.php", {
            method: "POST",
            body: formData,
          });

          const text = await res.text();

          // PHP에서 성공했다면 step4로 전환
          if (text.includes("success") || res.ok) {
            nextStep(4);
          } else {
            alert("회원가입 중 오류가 발생했습니다.\n\n" + text);
          }
        } catch (err) {
          alert("서버 통신 오류: " + err.message);
        }
      };

      function toggleSelection(el) {
        el.classList.toggle("selected");
      }

      function validateUserId(userid) {
        const regex = /^[a-zA-Z0-9]{5,15}$/; // 영문, 숫자만 허용
        return regex.test(userid);
      }
      function validatePassword(password) {
        const regex =
          /^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*()_+={}\[\]:;"'<>,.?/~`-]).{8,}$/;
        return regex.test(password);
      }
      function validateNickname(username) {
        const regex = /^[가-힣a-zA-Z]{1,10}$/;
        return regex.test(username);
      }
      function validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
      }

      // 새로고침 방지
      document.addEventListener("keydown", function (e) {
        if (e.key === "F5" || (e.ctrlKey && e.key === "r")) e.preventDefault();
      });
    </script>
  </body>
</html>
