<?php
/**
 * @file edit.php
 * @brief 게시글 수정 입력 화면
 *
 * 기능:
 * - GET 파라미터 id를 이용해 게시글 로드
 * - 제목, 내용, 비밀번호 입력 form 제공
 * - 입력된 정보는 edit_process.php로 POST 전송
 */

require_once 'config.php';
session_start();

$post = null;

try {
    // 필수 파라미터 확인: id가 없거나 숫자가 아니면 404 처리
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(404);
        exit('잘못된 접근입니다.');
    }

    $id = (int)$_GET['id'];

    // DB 연결
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8mb4");

    // 게시글 조회 쿼리 실행
    $sql = "SELECT id, name, title, content FROM posts WHERE id = $id";
    $result = $conn->query($sql);

    // 게시글이 존재하지 않는 경우 404 처리
    if (!$result || $result->num_rows === 0) {
        http_response_code(404);
        exit('해당 게시글을 찾을 수 없습니다.');
    }

    // 조회된 게시글 정보는 $post에 저장되어 아래 form에 표시됨
    $post = $result->fetch_assoc();

} catch (Exception $e) {
    // 예외 발생 시 로그 기록 및 오류 메시지 전달 후 목록으로 이동
    error_log("[" . date('Y-m-d H:i:s') . "] " . $e->getMessage() . PHP_EOL, 3, LOG_FILE_PATH);
    $_SESSION['error'] = '게시글 로드 중 오류가 발생했습니다.';
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
    <title>게시글 수정</title>
</head>
<body>
<h1>게시글 수정</h1>

<?php
// 오류 메시지가 세션에 설정되어 있으면 출력 후 제거
if (isset($_SESSION['error'])) {
    echo "<p style='color:red'>" . htmlspecialchars($_SESSION['error']) . "</p>";
    unset($_SESSION['error']);
}
?>

<form action="edit_process.php" method="post">
    <!-- 게시글 ID는 수정 처리 시 필요하므로 hidden 필드로 전달 -->
    <input type="hidden" name="id" value="<?php echo $post['id']; ?>">

    <!-- 작성자명은 수정 불가이므로 단순 출력 -->
    <p><strong>작성자:</strong> <?php echo htmlspecialchars($post['name']); ?></p>

    <!-- 제목 입력 필드: 기존 제목이 자동으로 입력됨 -->
    <p>
        <label for="title">제목:</label><br>
        <input type="text" name="title" id="title"
               value="<?php echo htmlspecialchars($post['title']); ?>" required>
    </p>

    <!-- 내용 입력 필드: 기존 내용이 자동으로 textarea에 표시됨 -->
    <p>
        <label for="content">내용:</label><br>
        <textarea name="content" id="content" rows="10" cols="50" required><?php echo htmlspecialchars($post['content']); ?></textarea>
    </p>

    <!-- 비밀번호 확인 입력 필드 -->
    <p>
        <label for="password">비밀번호 확인:</label><br>
        <input type="password" name="password" id="password" required>
    </p>

    <!-- 수정 완료 및 취소 버튼 -->
    <button type="submit">수정 완료</button>
    <a href="view.php?id=<?php echo $post['id']; ?>">취소</a>
</form>
</body>
</html>
