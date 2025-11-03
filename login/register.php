<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<title>회원가입</title>
<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    label { display: block; margin: 10px 0 5px; }
    input, button { padding: 5px; margin-bottom: 10px; }
    .toggle-btn { display: inline-block; padding: 5px 10px; margin: 2px; border: 1px solid #ccc; border-radius: 5px; cursor: pointer; }
    .selected { background-color: #5cb85c; color: white; }
</style>
</head>
<body>

<h2>회원가입</h2>
<form id="registerForm" method="POST" action="register_process.php">
    <label>아이디</label>
    <input type="text" name="userid" id="userid" required>
    <button type="button" onclick="checkId()">중복확인</button>
    
    <label>비밀번호</label>
    <input type="password" name="password" id="password" required>
    
    <label>비밀번호 확인</label>
    <input type="password" name="password_confirm" id="password_confirm" required>
    
    <label>닉네임</label>
    <input type="text" name="username" id="username" required>
    <button type="button" onclick="checkNickname()">중복확인</button>
    
    <label>이메일</label>
    <input type="email" name="email" id="email" required>
    
    <label>좋아하는 장르</label>
    <div id="genres">
        <span class="toggle-btn" onclick="toggleSelection(this)">영화</span>
        <span class="toggle-btn" onclick="toggleSelection(this)">드라마</span>
        <span class="toggle-btn" onclick="toggleSelection(this)">예능</span>
        <span class="toggle-btn" onclick="toggleSelection(this)">다큐</span>
        <span class="toggle-btn" onclick="toggleSelection(this)">애니</span>
        <span class="toggle-btn" onclick="toggleSelection(this)">시트콤</span>
    </div>

    <label>주로 사용하는 OTT</label>
    <div id="ott">
        <span class="toggle-btn" onclick="toggleSelection(this)">Netflix</span>
        <span class="toggle-btn" onclick="toggleSelection(this)">Disney+</span>
        <span class="toggle-btn" onclick="toggleSelection(this)">Wavve</span>
        <span class="toggle-btn" onclick="toggleSelection(this)">Coupang Play</span>
    </div>

    <label>선호 콘텐츠 지역</label>
    <div id="region">
        <span class="toggle-btn" onclick="toggleSelection(this)">한국</span>
        <span class="toggle-btn" onclick="toggleSelection(this)">미국</span>
        <span class="toggle-btn" onclick="toggleSelection(this)">일본</span>
        <span class="toggle-btn" onclick="toggleSelection(this)">중국</span>
        <span class="toggle-btn" onclick="toggleSelection(this)">유럽</span>
    </div>

    <input type="hidden" name="favorite_genres" id="favorite_genres">
    <input type="hidden" name="preferred_ott" id="preferred_ott">
    <input type="hidden" name="preferred_regions" id="preferred_regions">

    <button type="submit">가입하기</button>
</form>

<script>
// 토글 버튼 선택
function toggleSelection(el) {
    el.classList.toggle('selected');
}

// 폼 제출 전에 JSON 배열로 변환
document.getElementById('registerForm').onsubmit = function() {
    const genres = [...document.querySelectorAll('#genres .selected')].map(e => e.textContent);
    const ott = [...document.querySelectorAll('#ott .selected')].map(e => e.textContent);
    const region = [...document.querySelectorAll('#region .selected')].map(e => e.textContent);

    document.getElementById('favorite_genres').value = JSON.stringify(genres);
    document.getElementById('preferred_ott').value = JSON.stringify(ott);
    document.getElementById('preferred_regions').value = JSON.stringify(region);

    const password = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirm').value;
    if(password !== confirm){
        alert('비밀번호가 일치하지 않습니다.');
        return false;
    }

    return true;
}

// AJAX 중복 확인 예시
function checkId() {
    const userid = document.getElementById('userid').value;
    fetch('check_id.php?userid=' + encodeURIComponent(userid))
    .then(res => res.text())
    .then(data => alert(data));
}

function checkNickname() {
    const username = document.getElementById('username').value;
    fetch('check_nickname.php?username=' + encodeURIComponent(username))
    .then(res => res.text())
    .then(data => alert(data));
}
</script>

</body>
</html>
