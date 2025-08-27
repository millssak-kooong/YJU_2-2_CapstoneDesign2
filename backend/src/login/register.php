<?php
/**
 * @file register.php
 * @brief 사용자 회원가입 입력 폼 페이지
 *
 * 사용자가 회원가입에 필요한 정보를 입력하는 HTML 폼 구성
 * 입력 항목: 아이디(username), 비밀번호(password), 이름(name)
 * 입력된 정보는 POST 방식으로 register_process.php에 전달됨
 */

// 세션 초기화 처리
// - 오류 메시지 등 사용자 상태 전달을 위한 세션 기능 활성화
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
// 오류 메시지 출력 처리
// - register_process.php에서 세션에 저장된 오류 메시지 표시
// - 1회성 메시지 출력 후 세션에서 제거
if (isset($_SESSION['error'])) {
    echo "<p style='color:red'>" . htmlspecialchars($_SESSION['error']) . "</p>";
    unset($_SESSION['error']);
}
?>

<!--
    회원가입 입력 폼 구성
    - 사용자로부터 회원가입에 필요한 정보 수집
    - 전송 방식: POST
    - 처리 대상: register_process.php
-->
<form action="register_process.php" method="post">
    <fieldset>
        <legend>정보 입력</legend>

        <!-- 사용자 아이디 입력 필드 -->
        <label for="username">아이디:</label>
        <input type="text" id="username" name="username" required><br><br>

        <!-- 사용자 비밀번호 입력 필드 -->
        <label for="password">비밀번호:</label>
        <input type="password" id="password" name="password" required><br><br>

        <!-- 사용자 이름 입력 필드 -->
        <label for="name">이름:</label>
        <input type="text" id="name" name="name" required><br><br>

        <!-- 회원가입 요청 전송 버튼 -->
        <input type="submit" value="회원가입">
    </fieldset>
</form>

</body>
</html>
