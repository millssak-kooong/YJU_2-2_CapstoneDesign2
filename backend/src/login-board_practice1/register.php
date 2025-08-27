<?php
# 에러 메시지 출력용 세션 활성화
session_start();

# 로그인 상태 접근 시 조치
if (isset($_SESSION['login']['id'])) {
    $_SESSION['login']['error'] = '로그 아웃 후 이용 부탁드립니다.';
    header('Location: home.php');
    exit; // 불필요한 코드 실행 및 리소스 방지용 강제 코드 종료
}
?>

<!-- 화면 출력 -->
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원 가입 문서</title>
</head>
<body>
    <h1>회원 가입</h1>

<?php
// 회원 가입 오류 메시지 출력
if (isset($_SESSION['register']['error'])) {
    echo "<p style='color:red'>" . htmlspecialchars($_SESSION['register']['error']) . "</p>";
    unset($_SESSION['register']['error']); // 플래시 메시지 패턴
}
?>

    <form action="register_process.php" method="post">
        <fieldset>
            <legend>회원 정보 입력</legend>
            <br>
            <label for="name">이름</label>
            <input type="text" id="name" name="name" required> <!-- 필수 입력 -->
            <br>
            <br>
            <label for="id">ID</label>
            <input type="text" id="id" name="id" required> <!-- 필수 입력 -->
            <br>
            <br>
            <label for="pw">Password</label>
            <input type="password" id="pw" name="pw" required> <!-- 필수 입력 -->
            <br>
            <br>
            <input type="submit" value="가입"> <!-- 데이터 전송 버튼 -->
            <br>
        </fieldset>
    </form>
    <br>
    <button type="button" onclick="location.href='index.php'">돌아 가기</button>
    &nbsp;&nbsp;
    <button type="button" onclick="location.href='login.php'">로그인</button>
</body>
</html>