<!DOCTYPE html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>비밀번호 재설정</title>
    <link rel="stylesheet" href="../css/reset.css" />
    <link rel="stylesheet" href="../css/root.css" />
    <link rel="stylesheet" href="../css/login_form.css" />

    <style></style>
  </head>
  <body>
    <div class="container">
      <div class="header-text">
        <span class="highlight">무브인</span>에서<br />비밀번호를 재설정하세요
      </div>

      <form
        class="reset-form"
        method="POST"
        action="reset_password_process.php"
      >
        <input type="text" name="userid" placeholder="아이디 입력" required />
        <input type="email" name="email" placeholder="이메일 입력" required />
        <input
          type="password"
          name="new_password"
          placeholder="새 비밀번호 입력"
          required
        />
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
      document.addEventListener("keydown", function (e) {
        if (e.key === "F5" || (e.ctrlKey && e.key === "r")) {
          e.preventDefault();
        }
      });
    </script>
  </body>
</html>
