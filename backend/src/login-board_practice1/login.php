<?php
# 에러 표시와 로그인 상태 접근 조치 위해 세션 활성화
session_start();

// 로그인 상태로 접근 시 조치
if (isset($_SESSION['login']['id'])) {
    $_SESSION['login']['error'] = '이미 로그인 상태입니다.';
    header('Location: home.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인 문서</title>
</head>
<body>
    <h1>로그인</h1>

    <!-- 로그인 실패 메시지 출력 -->
    <?php
    if (isset($_SESSION['login']['error'])) {
        echo "<p style='color:red'>" . htmlspecialchars($_SESSION['login']['error']) . "</p>";
        unset($_SESSION['login']['error']); // 플래시 메시지 패턴
    }
    ?>

    <form action="login_process.php" method="post">
        <fieldset>
            <legend>회원 정보 입력</legend>
            <br>
            <label for="id">ID</label>
            <input type="text" id="id" name="id" required>
            <br>
            <br>
            <label for="pw">Password</label>
            <input type="password" id="pw" name="pw" required>
            <br>
            <br>
            <input type="submit" value="확인">
            <br>
        </fieldset>
    </form>
    <br>
    <button type="button" onclick="location.href='index.php'">돌아 가기</button>
</body>
</html>