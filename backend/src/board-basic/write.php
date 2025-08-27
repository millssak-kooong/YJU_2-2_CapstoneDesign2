<?php
/**
 * @file write.php
 * @brief 게시글 작성 화면
 * 사용자가 게시글을 작성하고 write_process.php로 전송하는 역할을 한다.
 *
 * [POST name 목록]
 * - name: 작성자 이름
 * - password: 비밀번호
 * - title: 글 제목
 * - content: 글 내용
 */
session_start();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시글 작성</title>
</head>
<body>
<h2>게시글 작성</h2>

<?php
// 세션에 오류 메시지가 있을 경우 출력
if (isset($_SESSION['error'])) {
    echo "<p style='color:red;'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']);
}
?>

<!--
  게시글 작성 폼
  - 사용자가 입력한 데이터를 write_process.php로 전송함
  - 전송 방식: POST
  - write_process.php는 입력값 검증 및 DB 저장을 담당
-->
<form action="write_process.php" method="post">
    <!-- 작성자 이름 입력 -->
    <label for="name">작성자 이름</label>
    <input type="text" id="name" name="name" required><br><br>

    <!-- 비밀번호 입력 -->
    <label for="password">비밀번호</label>
    <input type="password" id="password" name="password" required><br><br>

    <!-- 제목 입력 -->
    <label for="title">제목</label>
    <input type="text" id="title" name="title" required><br><br>

    <!-- 내용 입력 -->
    <label for="content">내용</label>
    <textarea id="content" name="content" rows="10" required></textarea><br><br>

    <!-- 등록 버튼 -->
    <input type="submit" value="등록하기">
</form>
</body>
</html>
