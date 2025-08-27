<?php
/**
 * @file register.php
 * @brief 회원가입 입력 폼
 *
 * - 사용자로부터 아이디, 비밀번호, 이름을 입력받음
 * - 입력 정보는 POST 방식으로 register_process.php로 전송
 * - 세션 메시지(error/success)를 조건부로 출력
 */

session_start();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>회원가입</title>
</head>
<body>

<h2>회원가입</h2>

<?php
// 오류 메시지 출력
if (isset($_SESSION['error'])) {
    echo "<p style='color:red'>" . htmlspecialchars($_SESSION['error']) . "</p>";
    unset($_SESSION['error']);
}
// 성공 메시지 출력
elseif (isset($_SESSION['success'])) {
    echo "<p style='color:green'>" . htmlspecialchars($_SESSION['success']) . "</p>";
    unset($_SESSION['success']);
}
?>

<!--
    회원가입 입력 폼
    - 아이디, 비밀번호, 이름 입력 필드 구성
    - POST 방식으로 register_process.php로 전송
-->
<form action="register_process.php" method="post">
    <fieldset>
        <legend>회원 정보 입력</legend>

        <!-- 아이디 입력 -->
        <label>아이디:
            <input type="text" name="username" required>
        </label><br>

        <!-- 비밀번호 입력 -->
        <label>비밀번호:
            <input type="password" name="password" required>
        </label><br>

        <!-- 이름 입력 -->
        <label>이름:
            <input type="text" name="name" required>
        </label><br>

        <!-- 회원가입 제출 버튼 -->
        <input type="submit" value="회원가입">
    </fieldset>
</form>

<!-- 로그인 페이지 안내 링크 -->
<p><a href="login.php">이미 계정이 있으신가요? 로그인</a></p>

</body>
</html>
