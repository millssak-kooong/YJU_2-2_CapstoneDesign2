<?php # 로그인 유지와 에러 처리 위해 세션 활성화
session_start();

# 비로그인 접근 시 에러 처리
if (!isset($_SESSION['login']['id'])) {
    $_SESSION['login']['error'] = '죄송합니다. 회원 전용 서비스입니다.';
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>글 작성 문서</title>
</head>
<body>
    <h1>글 작성</h1>

    <?php
    /* 에러 안내
    1. 모든 필드 입력 값 유효성
    2. SQL 오류로 인한 게시글 등록 실패
    */
    if (isset($_SESSION['write']['error'])) {
        echo "<p style='color:red'>" . htmlspecialchars($_SESSION['write']['error']) . "</p>";
        unset($_SESSION['write']['error']); // 플래시 메시지 패턴
    }
    ?>

    <P><strong><?= $_SESSION['login']['name'] ?></strong> 회원님 접속 중.</p>
    <br>
    <form action="write_process.php" method="post">
        <fieldset>
            <legend>게시글 양식</legend>
            <br>
            <label for="title">제목</label>
            <input type="text" id="title" name="title" required>
            <br>
            <br>
            <label for="content">내용</label>
            <textarea name="content" id="content" required></textarea>
            <br>
            <br>
            <input type="submit" value="등록">
        </fieldset>
    </form>
    <br>
    <button type="button" onclick="location.href='home.php'">취소</button>
</body>
</html>