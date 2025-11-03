<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>아이디 찾기</title>
<style>
    html, body {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        overflow: hidden; /* 스크롤 방지 */
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
        top: 200px;
        left: 10%;
        width: 80%;
        text-align: left;
        font-size: 28.8px; /* 로그인 페이지와 동일한 1.2배 확대 */
        line-height: 1.5;
    }

    .header-text .highlight {
        color: #a64ca6; /* 무브인 보라색 */
    }

    /* 입력 폼 */
    .findid-form {
        position: absolute;
        top: 350px;
        left: 50%;
        transform: translateX(-50%);
        width: 80%;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .findid-form input {
        padding: 15px;
        border: none;
        border-radius: 10px;
        background-color: #333;
        color: #ccc; /* 약간 어두운 글씨색 */
        font-size: 16px;
    }

    .findid-form input::placeholder {
        color: #888; /* placeholder 회색 */
    }

    .findid-form button {
        padding: 15px;
        border: none;
        border-radius: 10px;
        background-color: #a64ca6;
        color: #fff;
        font-size: 16px;
        cursor: pointer;
        width: 100%; /* 입력창과 동일 */
    }

    /* 하단 링크 */
    .bottom-links {
        margin-top: 15px;
        display: flex;
        justify-content: center;
        gap: 50px;
    }

    .bottom-links a {
        text-decoration: none;
        font-size: 14px;
    }

    .bottom-links a.register {
        color: #a64ca6; /* 회원가입 보라색 */
    }

    .bottom-links a:not(.register) {
        color: #aaa; /* 회색 */
    }
</style>
</head>
<body>

<div class="container">
    <div class="header-text">
        <span class="highlight">무브인</span>에서 아이디를 찾아보세요
    </div>

    <form class="findid-form" method="POST" action="find_id_process.php">
        <input type="text" name="username" placeholder="이름 입력" required>
        <input type="email" name="email" placeholder="이메일 입력" required>
        <button type="submit">아이디 찾기</button>

        <div class="bottom-links">
            <a href="login.php">로그인</a>
            <a href="reset_password.php">비밀번호 찾기</a>
            <a class="register" href="register.php">회원가입</a>
        </div>
    </form>
</div>

<script>
    // 새로고침 방지
    document.addEventListener("keydown", function(e) {
        if (e.key === "F5" || (e.ctrlKey && e.key === "r")) {
            e.preventDefault();
        }
    });
</script>

</body>
</html>
