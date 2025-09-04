<?php
# 파일 실행 준비
session_start();
require_once __DIR__ . '/db_conf.php';
$db = new mysqli(db_info::URL, db_info::ID, db_info::PW, db_info::DB);

# 비로그인 접근 처리
if (!isset($_SESSION['login']['id'])) {
    $_SESSION['login']['error'] = '로그인 후 수정 할 수 있습니다.';
    header('Location: index.php');
    exit;
}

# 1. 글 수정 폼 표시 및 제출 ?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>글 수정 문서</title>
</head>
<body>
    <h1>글 수정</h1>

    <?php // 수정 처리 에러 표시: 조회 실패, 변경 사항 없음
    if (isset($_SESSION['edit']['error'])) {
        echo '<p style="color:red">' . htmlspecialchars($_SESSION['edit']['error']) . '</p>';
        unset($_SESSION['edit']['error']);
    }
    ?>

    <form action="edit_process.php?num=<?= $_GET['num'] ?>" method="post">
        <fieldset>
            <legend>게시글 양식</legend>
            <br>
            <label for="title">제목</label>
            <input type="text" id="title" name="title" required>
            <br>
            <br>
            <label for="content">내용</label>
            <textarea id="content" name="content" required></textarea>
            <br>
            <br>
            <input type="submit" value="등록">
        </fieldset>
    </form>
    <br>
    <button type="button" onclick="location.href='view.php?num=<?= $_GET['num'] ?>'">취소</button>
</body>
</html>