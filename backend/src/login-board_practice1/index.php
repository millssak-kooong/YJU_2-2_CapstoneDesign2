<?php
# 세션 활성화
session_start();

# 로그인 상태 접근 시 조치
if (isset($_SESSION['login']['id'])) {
    $_SESSION['login']['error'] = '로그 아웃 후 이용 부탁드립니다.';
    header('Location: home.php');
    exit; // 불필요한 코드 실행 및 리소스 방지용 강제 코드 종료
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>기본 문서</title>
</head>
<body>
    <center>
        <h1>시작</h1>
        <p>회원 전용 서비스에 오신 것을 환영합니다.</p>
        
        <?php
        # 회원 가입 완료 메시지
        if (isset($_SESSION['register']['success'])) {
            echo "<p style='color:green'>" . htmlspecialchars($_SESSION['register']['success']) . "</p>";
            unset($_SESSION['register']['success']); // 플래시 메시지 패턴
        }

        # 비로그인 접근 제한 메시지: 홈 페이지, 글 보기 페이지, 글 수정 페이지, 비밀 번호 검증 페이지
        if (isset($_SESSION['login']['error'])) {
            echo "<p style='color:red'>" . htmlspecialchars($_SESSION['login']['error']) . "</p>";
            unset($_SESSION['login']['error']); // 플래시 메시지 패턴
        }
        ?>

        <button type="button" onclick="location.href='register.php'">회원 가입</button>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <button type="button" onclick="location.href='login.php'">로그인</button>
    </center>
</body>
</html>