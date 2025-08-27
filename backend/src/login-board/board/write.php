<?php
/**
 * @file write.php
 * @brief 게시글 작성 입력 화면
 *
 * - 로그인한 사용자만 접근 가능
 * - 제목과 내용을 입력 받아 write_process.php로 전송
 *
 *  [POST name 목록]
 *  - title: 게시글 제목
 *  - content: 게시글 본문
 */

session_start();
require_once '../conf/config.php';

// 로그인하지 않은 경우 로그인 페이지로 이동
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = '로그인 후 글쓰기가 가능합니다.';
    header("Location: " . LOGIN_PATH . "/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>글쓰기</title>
</head>
<body>

<h2>글쓰기</h2>

<?php
// 사용자 정보 출력
echo '<p>작성자: ' . htmlspecialchars($_SESSION['name']) . '님</p>';
echo '<p><a href="' . BOARD_PATH . '/list.php">← 목록으로</a></p>';

// 에러 메시지 출력
if (isset($_SESSION['error'])) {
    echo '<p style="color:red">' . htmlspecialchars($_SESSION['error']) . '</p>';
    unset($_SESSION['error']);
}

// 성공 메시지 출력 (optional)
if (isset($_SESSION['success'])) {
    echo '<p style="color:green">' . htmlspecialchars($_SESSION['success']) . '</p>';
    unset($_SESSION['success']);
}
?>

<!-- 게시글 작성 입력 폼 -->
<form action="write_process.php" method="post">
    <fieldset>
        <legend>게시글 작성</legend>

        <!-- 제목 입력 필드 -->
        <label for="title">제목:</label><br>
        <input type="text" id="title" name="title" required><br><br>

        <!-- 내용 입력 필드 -->
        <label for="content">내용:</label><br>
        <textarea id="content" name="content"  required></textarea><br><br>

        <!-- 제출 버튼 -->
        <input type="submit" value="작성 완료">
    </fieldset>
</form>

</body>
</html>
