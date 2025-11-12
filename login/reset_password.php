<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>비밀번호 재설정</title>
<link rel="stylesheet" href="/movein/css/reset.css">
<link rel="stylesheet" href="/movein/css/root.css">
<link rel="stylesheet" href="/movein/css/login_form.css">

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
        font-size: var(--fs6);
        line-height: 1.5;
    }

    .header-text .highlight {
        color: var(--c-main);
    }

    /* 입력 폼 */
    .reset-form {
        position: absolute;
        top: 350px;
        left: 50%;
        transform: translateX(-50%);
        width: 80%;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .reset-form input {
        padding: 15px;
        border: none;
        border-radius: 10px;
        background-color: #333;
        color: #ccc; /* 조금 어두운 글씨색 */
        font-size: var(--fs4);
    }

    .reset-form input::placeholder {
        color: #888; /* placeholder 회색 */
    }

    .reset-form button {
        padding: 15px;
        border: none;
        border-radius: 10px;
        background-color: var(--c-main);
        color: #fff;
        font-size: var(--fs4);
        cursor: pointer;
        width: 100%; /* 입력창과 동일한 길이 */
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
        font-size: var(--fs3);
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
        <span class="highlight">무브인</span>에서<br>비밀번호를 재설정하세요
    </div>

    <form class="reset-form" method="POST" action="reset_password_process.php">
        <input type="text" name="userid" placeholder="아이디 입력" required>
        <input type="email" name="email" placeholder="이메일 입력" required>
        <input type="password" name="new_password" placeholder="새 비밀번호 입력" required>
        <button type="submit">비밀번호 재설정</button>

        <div class="bottom-links">
            <a href="login.php">로그인</a>
            <a href="find_id.php">아이디 찾기</a>
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
