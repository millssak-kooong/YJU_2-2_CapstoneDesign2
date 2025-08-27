<?php
/**
 * @file view.php
 * @brief 게시글 상세보기 페이지
 *
 * 기능 개요:
 * - GET 파라미터(id)를 통해 특정 게시글 조회
 * - 필수 파라미터 누락 또는 존재하지 않는 게시글일 경우 404 응답
 * - DB 접속 및 쿼리 실행 중 예외 발생 시 로그 기록 후 목록 페이지로 이동
 * - 수정일이 존재하면 출력
 * - 수정, 삭제, 목록 이동 버튼 제공
 */

require_once 'config.php';
session_start();

$post = null;

try {
    // 필수 GET 파라미터(id) 검사
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(404); // 잘못된 접근 - 404 응답
        exit;
    }

    // 파라미터 정수형 변환
    $id = (int) $_GET['id'];

    // DB 연결
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8mb4");

    // 게시글 조회 쿼리 실행 (수정일 포함)
    $sql = "SELECT id, name, title, content, created_at, updated_at FROM posts WHERE id = $id";
    $result = $conn->query($sql);

    // 결과가 없거나 실패한 경우 404
    if (!$result || $result->num_rows === 0) {
        http_response_code(404); // 게시글 없음 - 404 응답
        exit;
    }

    // 게시글 정보 배열로 저장
    $post = $result->fetch_assoc();

} catch (Exception $e) {
    // 예외 발생 시 에러 로그에 기록 후 목록으로 이동
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . PHP_EOL, 3, LOG_FILE_PATH);
    $_SESSION['error'] = '게시글 조회 중 오류가 발생했습니다.';
    header("Location: list.php");
    exit;
} finally {
    // DB 연결 종료
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($post['title']); ?> - 게시글 보기</title>
</head>
<body>
<h1><?php echo htmlspecialchars($post['title']); ?></h1>

<!-- 작성자 및 작성일 출력 -->
<p><strong>작성자:</strong> <?php echo htmlspecialchars($post['name']); ?></p>
<p><strong>작성일:</strong> <?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($post['created_at']))); ?></p>

<!-- 수정일이 있는 경우 출력 -->
<?php if (!empty($post['updated_at'])) {
    echo "<p><strong>수정일:</strong>";
    echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($post['updated_at'])));
    echo "</p>";
} ?>

<hr>

<!-- 게시글 내용 출력 -->
<div>
    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
</div>

<hr>

<!-- 수정/삭제/목록 버튼 -->
<p>
    <a href="edit.php?id=<?php echo $post['id']; ?>">수정</a> |
    <a href="delete.php?id=<?php echo $post['id']; ?>">삭제</a> |
    <a href="list.php">목록으로</a>
</p>
</body>
</html>
