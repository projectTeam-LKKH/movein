<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>회원가입</title>
<link rel="stylesheet" href="/movein/css/root.css">
<style>
    html, body {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        background-color: #000;
        font-family: Arial, sans-serif;
    }

    .container {
        position: relative;
        width: 100%;
        height: 100%;
        color: #fff;
    }

    /* 상단 문구 */
    .header-text {
        position: absolute;
        top: 50px;
        left: 10%;
        width: 80%;
        text-align: left;
        font-size: 20px;
        line-height: 1.5;
        transition: opacity 0.3s ease;
    }

    .header-text .highlight {
        color: var(--c-main);
    }

    /* 폼 */
    .register-form {
        position: absolute;
        top: 120px;
        left: 50%;
        transform: translateX(-50%);
        width: 80%;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    input {
        padding: 15px;
        border: none;
        border-radius: 10px;
        background-color: #333;
        color: #ccc;
        font-size: 16px;
        width: 100%;
        box-sizing: border-box;
    }

    #smallText {
        color: #888;
        font-size: 12px;
    }

    input::placeholder {
        color: #888;
    }

    .input-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .input-group input {
        flex: 1;
    }

    .check-btn {
        background-color: #555;
        color: #000;
        border: none;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 14px;
        cursor: pointer;
        white-space: nowrap;
    }

    button.next-btn {
        align-self: flex-end;
        background-color: var(--c-main);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 12px 20px;
        font-size: 16px;
        cursor: pointer;
        margin-top: 10px;
    }

    /* 스텝 */
    .step {
        display: none;
    }

    .step.active {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    /* 토글 */
    .toggle-section {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .toggle-btn {
        display: inline-block;
        padding: 10px 15px;
        border: 1px solid #666;
        border-radius: 10px;
        cursor: pointer;
        color: #ccc;
        font-size: 14px;
        transition: all 0.2s;
    }

    .toggle-btn.selected {
        background-color: var(--c-main);
        color: #fff;
        border-color: var(--c-main);
    }

    .submit-btn {
        background-color: var(--c-main);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 15px;
        font-size: 16px;
        cursor: pointer;
        width: 100%;
        margin-top: 10px;
    }
</style>
</head>
<body>

<div class="container">
    <div id="headerText" class="header-text">
        <span class="highlight">무브오너</span>가 되어<br>나만의 취향을 확장해 보세요.
    </div>

    <form id="registerForm" class="register-form" method="POST" action="register_process.php">

        <!-- STEP 1 -->
        <div id="step1" class="step active">
            <div class="input-group">
                <input type="text" name="userid" id="userid" placeholder="사용하실 아이디를 입력해주세요" required>
                <button type="button" class="check-btn" onclick="checkId()">중복확인</button>
            </div>
            <div id="smallText"> 영문·숫자 조합, 5~15자 이내로 입력해주세요.</div>
            <input type="password" name="password" id="password" placeholder="사용하실 비밀번호를 입력해 주세요" required>
            <input type="password" name="password_confirm" id="password_confirm" placeholder="비밀번호 확인" required>
            <div id="smallText"> 비밀번호는 8자 이상, 영문자 숫자, 특수문자를 섞어주세요.</div>
            <button type="button" class="next-btn" onclick="nextStep(2)">다음 ></button>
        </div>

        <!-- STEP 2 -->
        <div id="step2" class="step">
            <div class="input-group">
                <input type="text" name="username" id="username" placeholder="사용하실 닉네임을 입력해 주세요" required>
                <button type="button" class="check-btn" onclick="checkNickname()">중복확인</button>
            </div>
            <div id="smallText"> 한글과 영문만 사용 가능하며, 최대 10자까지 입력할 수 있어요</div>
            <input type="email" name="email" id="email" placeholder="이메일을 입력해 주세요" required>
            <div id="smallText"> 추후 잃어버린 아이디나 비밀번호를 찾아야 할때 필요한 정보에요</div>
            <button type="button" class="next-btn" onclick="nextStep(3)">다음 ></button>
        </div>

        <!-- STEP 3 -->
        <div id="step3" class="step">
            <label style="color:#ccc;">좋아하는 장르</label>
            <div id="genres" class="toggle-section">
                <span class="toggle-btn" onclick="toggleSelection(this)">영화</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">드라마</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">예능</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">다큐멘터리</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">애니메이션</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">시트콤</span>
            </div>

            <label style="color:#ccc;">주로 사용하는 OTT</label>
            <div id="ott" class="toggle-section">
                <span class="toggle-btn" onclick="toggleSelection(this)">넷플릭스</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">쿠팡플레이</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">티빙</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">웨이브</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">디즈니+</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">왓챠</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">기타</span>
            </div>

            <label style="color:#ccc;">선호 콘텐츠 지역</label>
            <div id="region" class="toggle-section">
                <span class="toggle-btn" onclick="toggleSelection(this)">독일</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">대만</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">미국</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">영국</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">인도</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">일본</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">중국</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">캐나다</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">한국</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">기타</span>
                <span class="toggle-btn" onclick="toggleSelection(this)">잘 모르겠어요</span>
            </div>

            <input type="hidden" name="favorite_genres" id="favorite_genres">
            <input type="hidden" name="preferred_ott" id="preferred_ott">
            <input type="hidden" name="preferred_regions" id="preferred_regions">

            <button type="submit" class="submit-btn">가입하기</button>
        </div>
    </form>
</div>

<script>
const header = document.getElementById("headerText");

function nextStep(step) {
    document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
    document.getElementById('step' + step).classList.add('active');

    // 단계별 문구 변경
    if (step === 1) {
        header.innerHTML = `<span class="highlight">무브오너</span>가 되어<br>나만의 취향을 확장해 보세요.`;
    } else if (step === 2) {
        header.innerHTML = `<span class="highlight">무브인</span> 계정을 만들고<br>새로운 탐험을 시작하세요.`;
    } else if (step === 3) {
        header.innerHTML = `<span class="highlight">당신의 취향</span>을 알고 싶어요<br>장르, 플랫폼 등을 선택해주세요.`;
    }
}

function toggleSelection(el) {
    el.classList.toggle('selected');
}

document.getElementById('registerForm').onsubmit = function() {
    const genres = [...document.querySelectorAll('#genres .selected')].map(e => e.textContent);
    const ott = [...document.querySelectorAll('#ott .selected')].map(e => e.textContent);
    const region = [...document.querySelectorAll('#region .selected')].map(e => e.textContent);

    document.getElementById('favorite_genres').value = JSON.stringify(genres);
    document.getElementById('preferred_ott').value = JSON.stringify(ott);
    document.getElementById('preferred_regions').value = JSON.stringify(region);

    const password = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirm').value;
    if (password !== confirm) {
        alert('비밀번호가 일치하지 않습니다.');
        nextStep(1);
        return false;
    }

    return true;
};

function checkId() {
    const userid = document.getElementById('userid').value;
    if (!userid) return alert('아이디를 입력하세요.');
    fetch('check_id.php?userid=' + encodeURIComponent(userid))
        .then(res => res.text())
        .then(data => alert(data));
}

function checkNickname() {
    const username = document.getElementById('username').value;
    if (!username) return alert('닉네임을 입력하세요.');
    fetch('check_nickname.php?username=' + encodeURIComponent(username))
        .then(res => res.text())
        .then(data => alert(data));
}

// 새로고침 방지
document.addEventListener("keydown", function(e) {
    if (e.key === "F5" || (e.ctrlKey && e.key === "r")) e.preventDefault();
});
</script>

</body>
</html>
