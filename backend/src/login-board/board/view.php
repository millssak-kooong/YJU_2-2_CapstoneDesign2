<?php
/**
 * @file view.php
 * @brief 게시글 상세 보기 화면
 *
 * 기능:
 * - GET 파라미터 id를 이용해 게시글 로드
 * - users 테이블과 JOIN하여 작성자 이름 출력
 * - 조회수 1 증가 처리 (게시글 존재 확인 후)
 * - 수정된 경우 수정일도 출력
 *
 * [GET name 목록]
 * - id: 게시글 고유 ID
 */

session_start();
require_once '../conf/config.php';

// GET 파라미터 유효성 검증
$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($post_id <= 0) {
    $_SESSION['error'] = '잘못된 접근입니다.';
    header("Location: list.php");
    exit;
}

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');

    // 게시글 조회 (작성자 이름 + 수정일 포함)
    $sql = "
        SELECT p.id, p.user_id, p.title, p.content, p.created_at, p.updated_at, p.views, u.name AS author
        FROM posts p
        JOIN users u ON p.user_id = u.id
        WHERE p.id = $post_id
    ";
    $result = $conn->query($sql);

    // 게시글 존재 여부 확인
    if (!$result || $result->num_rows === 0) {
        $_SESSION['error'] = '해당 게시글이 존재하지 않습니다.';
        header("Location: list.php");
        exit;
    }

    $post = $result->fetch_assoc();

    // 조회수 증가 (존재 확인 후 실행)
    $update_sql = "UPDATE posts SET views = views + 1 WHERE id = $post_id";
    $conn->query($update_sql);

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
    <title><?php echo htmlspecialchars($post['title']); ?> - 게시글 보기</title>
</head>
<body>

<h2>게시글 상세</h2>

<!-- 게시글 정보 출력 -->
<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>제목</th>
        <td><?php echo htmlspecialchars($post['title']); ?></td>
    </tr>
    <tr>
        <th>작성자</th>
        <td><?php echo htmlspecialchars($post['author']); ?></td>
    </tr>
    <tr>
        <th>작성일</th>
        <td><?php echo $post['created_at']; ?></td>
    </tr>
    <?php
    // 수정일이 있을 경우 출력
    if (!empty($post['updated_at'])) {
        echo '<tr><th>수정일</th><td>' . $post['updated_at'] . '</td></tr>';
    }
    ?>
    <tr>
        <th>조회수</th>
        <td><?php echo $post['views'] + 1; // 즉시 반영 ?></td>
    </tr>
    <tr>
        <th>내용</th>
        <td><pre><?php echo htmlspecialchars($post['content']); ?></pre></td>
    </tr>
</table>

<!-- 이동 링크 -->
<p>
    <a href="list.php">← 목록으로</a>
    <?php
    // 로그인한 사용자가 작성자인 경우 수정/삭제 링크 표시
    if (isset($_SESSION['user_id']) && intval($_SESSION['user_id']) === intval($post['user_id'])) {
        echo '<a href="edit.php?id=' . $post['id'] . '">수정</a> ';
        echo '<a href="delete.php?id=' . $post['id'] . '" onclick="return confirm(\'정말 삭제하시겠습니까?\');">삭제</a>';
    }
    ?>
</p>

</body>
</html>
