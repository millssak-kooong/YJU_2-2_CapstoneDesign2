<?php
/**
 * @file delete.php
 * @brief 게시글 삭제 확인 화면 (비밀번호 입력)
 *
 * 기능:
 * - GET 파라미터 id를 이용해 게시글 조회
 * - 존재하지 않는 게시글은 404 처리
 * - 비밀번호 입력 form 출력 (POST → delete_process.php)
 */

require_once 'config.php';
session_start();

$post = null;

try {
    // 게시글 ID 확인
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(404);
        exit('잘못된 접근입니다.');
    }

    $id = (int)$_GET['id'];

    // DB 연결
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8mb4");

    // 게시글 존재 여부 확인
    $sql = "SELECT id, title, name FROM posts WHERE id = $id";
    $result = $conn->query($sql);

    if (!$result || $result->num_rows === 0) {
        http_response_code(404);
        exit('해당 게시글을 찾을 수 없습니다.');
    }

    $post = $result->fetch_assoc();

} catch (Exception $e) {
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . PHP_EOL, 3, LOG_FILE_PATH);
    $_SESSION['error'] = '게시글 조회 중 오류가 발생했습니다.';
    header("Location: list.php");
    exit;
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시글 삭제 확인</title>
</head>
<body>
<h1>게시글 삭제</h1>

<p><strong>제목:</strong> <?php echo htmlspecialchars($post['title']); ?></p>
<p><strong>작성자:</strong> <?php echo htmlspecialchars($post['name']); ?></p>

<?php
// 에러 메시지 출력
if (isset($_SESSION['error'])) {
    echo "<p style='color:red'>" . htmlspecialchars($_SESSION['error']) . "</p>";
    unset($_SESSION['error']);
}
?>

<form action="delete_process.php" method="post">
    <!-- 삭제할 게시글의 ID를 hidden으로 전달 -->
    <input type="hidden" name="id" value="<?php echo $post['id']; ?>">

    <p>
        <label for="password">비밀번호 확인:</label><br>
        <input type="password" name="password" id="password" required>
    </p>

    <button type="submit">삭제</button>
    <a href="view.php?id=<?php echo $post['id']; ?>">취소</a>
</form>
</body>
</html>
