<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>로그인</title>
<link rel="stylesheet" href="/movein/css/root.css">
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
        font-size: 28.8px; /* 기존 대비 1.2배 */
        line-height: 1.5;
    }

    .header-text .highlight {
        color: var(--c-main);
    }

    /* 입력 폼 */
    .login-form {
        position: absolute;
        top: 350px;
        left: 50%;
        transform: translateX(-50%);
        width: 80%;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .login-form input {
        padding: 15px;
        border: none;
        border-radius: 10px;
        background-color: #333;
        color: #ccc; /* 조금 더 어두운 글씨색 */
        font-size: 16px;
    }

    .login-form input::placeholder {
        color: #888; /* placeholder 회색 */
    }

    .login-form button {
        padding: 15px;
        border: none;
        border-radius: 10px;
        background-color: var(--c-main);
        color: #fff;
        font-size: 16px;
        cursor: pointer;
        width: 100%; /* 입력창과 동일 길이 */
    }

    /* 하단 링크 - 로그인 바로 아래 */
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
        color: var(--c-main);
    }

    .bottom-links a:not(.register) {
        color: #aaa; /* 회색 */
    }
</style>
</head>
<body>

<div class="container">
    <div class="header-text">
        <span class="highlight">무브인</span>에 로그인하고<br>
        당신의 다음 취향을 만나보세요
    </div>

    <form class="login-form" method="POST" action="login_process.php">
        <input type="text" name="userid" placeholder="아이디 입력" required>
        <input type="password" name="password" placeholder="비밀번호 입력" required>
        <button type="submit">로그인</button>

        <div class="bottom-links">
            <a href="find_id.php">아이디 찾기</a>
            <a href="reset_password.php">비밀번호 찾기</a>
            <a class="register" href="register.php">회원가입</a>
        </div>
    </form>
</div>

<script>
    // F5 새로고침 방지
    document.addEventListener("keydown", function(e) {
        if (e.key === "F5" || (e.ctrlKey && e.key === "r")) {
            e.preventDefault();
        }
    });
</script>

</body>
</html>
