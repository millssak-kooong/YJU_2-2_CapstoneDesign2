<?php
/**
 * @file edit.php
 * @brief 게시글 수정 입력 화면
 *
 * 기능:
 * - GET 파라미터 id를 이용해 게시글 로드
 * - 제목, 내용 입력 form 제공
 * - 입력된 정보는 edit_process.php로 POST 전송
 *
 * [GET name 목록]
 * - id: 게시글 고유 ID
 *
 * [POST name 목록]
 * - id: 게시글 ID
 * - title: 수정된 제목
 * - content: 수정된 본문
 */

session_start();
require_once '../conf/config.php';

// 로그인 여부 확인
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = '로그인 후 이용 가능합니다.';
    header("Location: " . LOGIN_PATH . "/login.php");
    exit;
}

// GET 파라미터 확인
$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($post_id <= 0) {
    $_SESSION['error'] = '잘못된 접근입니다.';
    header("Location: list.php");
    exit;
}

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');

    // 게시글 조회 (작성자 확인 포함)
    $sql = "
        SELECT id, user_id, title, content
        FROM posts
        WHERE id = $post_id
    ";
    $result = $conn->query($sql);

    if (!$result || $result->num_rows === 0) {
        $_SESSION['error'] = '게시글이 존재하지 않습니다.';
        header("Location: list.php");
        exit;
    }

    $post = $result->fetch_assoc();

    // 본인 글인지 확인
    if (intval($_SESSION['user_id']) !== intval($post['user_id'])) {
        $_SESSION['error'] = '본인 글만 수정할 수 있습니다.';
        header("Location: list.php");
        exit;
    }

} catch (Exception $e) {
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . PHP_EOL, 3, LOG_FILE_PATH);
    $_SESSION['error'] = '게시글을 불러오는 중 오류가 발생했습니다.';
    header("Location: list.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시글 수정</title>
</head>
<body>

<h2>게시글 수정</h2>

<p><a href="view.php?id=<?php echo $post_id; ?>">← 게시글로 돌아가기</a></p>

<?php
// 오류 메시지 출력
if (isset($_SESSION['error'])) {
    echo '<p style="color:red">' . htmlspecialchars($_SESSION['error']) . '</p>';
    unset($_SESSION['error']);
}
?>

<!-- 게시글 수정 입력 폼 -->
<form action="edit_process.php" method="post">
    <input type="hidden" name="id" value="<?php echo $post['id']; ?>">

    <fieldset>
        <legend>게시글 수정</legend>

        <!-- 제목 -->
        <label for="title">제목:</label><br>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" size="60" required><br><br>

        <!-- 본문 -->
        <label for="content">내용:</label><br>
        <textarea id="content" name="content" rows="10" cols="60" required><?php echo htmlspecialchars($post['content']); ?></textarea><br><br>

        <!-- 제출 버튼 -->
        <input type="submit" value="수정 완료">
    </fieldset>
</form>

</body>
</html>
