<?php
# 로그인 상태 유지, 에러 출력 위해 세션 활성화
session_start();

// 비로그인 접근 에러 메시지 저장
if (!isset($_SESSION['login']['id'])) {
    $_SESSION['login']['error'] = '로그인 후 이용 부탁드립니다.';
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>홈 문서</title>
</head>
<body>
    <h1>게시판</h1>

    <p id="now" style="color:darkslategray"></p>
    <script> // 사용자 PC 시간대 기준 실시간 시각
        const el = document.getElementById('now');
        const tick = () => el.textContent = new Date().toLocaleString();
        tick();
        setInterval(tick, 1000);
    </script>

    <?php
    // 로그인 상태로 로그인 페이지 접근 시 에러 메시지
    if (isset($_SESSION['login']['error'])) {
        echo "<p style='color:red'>" . htmlspecialchars($_SESSION['login']['error']) . "</p>";
        unset($_SESSION['login']['error']); // 플래시 메시지 패턴
    }
    ?>

    <p>🌏현재 🎌도쿄 날씨: ⛅흐림</p>
    <P><strong><?= $_SESSION['login']['name'] ?></strong> 회원님 접속 중.</p>
    
    <?php
    // 글 등록 성공 알림
    if (isset($_SESSION['write']['success'])) {
        echo "<p style='color:darkgreen'>" . htmlspecialchars($_SESSION['write']['success']) . "</p>";
        unset($_SESSION['write']['success']); // 플래시 메시지 패턴
    }
    ?>
    
    <button type="button" onclick="location.href='logout.php'">로그 아웃</button>
</body>
</html>