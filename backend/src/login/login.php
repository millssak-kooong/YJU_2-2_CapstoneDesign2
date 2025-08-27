<?php
/**
 * @file login.php
 * @brief 사용자 로그인 입력 폼 페이지
 *
 * 사용자가 로그인에 필요한 정보를 입력하는 HTML 폼 구성
 * 입력 항목: 아이디(username), 비밀번호(password)
 * 입력된 정보는 POST 방식으로 login_process.php에 전달됨
 * 세션에 저장된 에러 또는 성공 메시지를 조건부 출력
 */

// 세션 초기화 처리
// - 세션 기반 사용자 인증 상태 관리에 필요
session_start();
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
// 세션 오류 메시지 출력 처리
// - 로그인 실패 또는 입력 오류 발생 시 출력
// - 출력 후 메시지 제거 (1회성 메시지 사용 구조)
if (isset($_SESSION['error'])) {
    echo "<p style='color:red'>" . htmlspecialchars($_SESSION['error']) . "</p>";
    unset($_SESSION['error']);
} else {
    // 세션 성공 메시지 출력 처리
    // - 회원가입 완료 등 성공 이벤트 발생 시 출력
    // - 출력 후 메시지 제거
    if (isset($_SESSION['success'])) {
        echo "<p style='color: green;'>" . htmlspecialchars($_SESSION['success']) . "</p>";
        unset($_SESSION['success']);
    }
}
?>

<!--
    로그인 입력 폼 구성
    - 사용자의 인증 정보를 입력받아 서버로 전송하는 구조
    - 전송 방식: POST
    - 처리 대상: login_process.php
-->
<form action="login_process.php" method="post">
    <fieldset>
        <legend>로그인 정보 입력</legend>
        <!-- 사용자 아이디 입력 필드 -->
        <label>아이디: <input type="text" name="username" required></label><br>

        <!-- 사용자 비밀번호 입력 필드 -->
        <label>비밀번호: <input type="password" name="password" required></label><br>

        <!-- 로그인 요청 전송 버튼 -->
        <input type="submit" value="로그인">
    </fieldset>
</form>
</body>
</html>
