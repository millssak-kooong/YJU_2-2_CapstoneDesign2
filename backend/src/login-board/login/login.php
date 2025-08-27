<?php
/**
 * @file login.php
 * @brief 사용자 로그인 입력 폼 페이지
 *
 * 기능:
 * - 로그인된 사용자는 welcome.php로 리디렉트
 * - 세션 기반 메시지 출력 (오류 또는 성공)
 * - 로그인 폼 구성 및 입력값은 login_process.php로 POST 전송
 *
 * 입력 (POST via login_process.php):
 * - username: 로그인 ID (string, required)
 * - password: 비밀번호 (string, required)
 */

session_start();

// 로그인된 사용자는 환영 페이지로 이동
if (isset($_SESSION['user_id'])) {
    header("Location: welcome.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>로그인</title>
</head>
<body>

<h2>로그인</h2>

<?php
// 세션 오류 메시지 출력 (로그인 실패 등)
if (isset($_SESSION['error'])) {
    echo "<p style='color:red'>" . htmlspecialchars($_SESSION['error']) . "</p>";
    unset($_SESSION['error']);
} else if (isset($_SESSION['success'])) {
// 세션 성공 메시지 출력 처리 (회원 가입 성공)
    echo "<p style='color: green;'>" . htmlspecialchars($_SESSION['success']) . "</p>";
    unset($_SESSION['success']);
}
?>

<!--
    로그인 입력 폼
    - 사용자가 아이디와 비밀번호를 입력
    - POST 방식으로 login_process.php로 전송
-->
<form action="login_process.php" method="post">
    <fieldset>
        <legend>로그인 정보 입력</legend>

        <!-- 아이디 입력 필드 -->
        <label>아이디:
            <input type="text" name="username" required>
        </label><br>

        <!-- 비밀번호 입력 필드 -->
        <label>비밀번호:
            <input type="password" name="password" required>
        </label><br>

        <!-- 로그인 버튼 -->
        <input type="submit" value="로그인">
    </fieldset>
</form>

<!-- 회원가입 링크 안내 -->
<p><a href="register.php">아직 회원이 아니라면? 회원가입</a></p>

</body>
</html>
