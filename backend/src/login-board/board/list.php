<?php
/**
 * @file list.php
 * @brief 게시글 목록 페이지 (작성자 이름, 페이지네이션 포함)
 *
 * 기능:
 * - 로그인 여부와 관계없이 접근 가능
 * - 작성자 이름은 users 테이블과 JOIN하여 출력
 * - 최신순으로 게시글 출력
 * - 페이지네이션 적용 (LIMIT + OFFSET)
 */

session_start();
require_once '../conf/config.php';

$limit = 3; // 한 페이지당 게시글 수
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1; // 현재 페이지
$offset = ($page - 1) * $limit;

try {
    // DB 연결
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');

    // 전체 게시글 수 조회 (페이지네이션용)
    $count_sql = "SELECT COUNT(*) AS total FROM posts";
    $count_result = $conn->query($count_sql);
    $total = ($count_result && $row = $count_result->fetch_assoc()) ? $row['total'] : 0;
    $total_pages = ceil($total / $limit);

    // 게시글 + 작성자 이름 조회 (JOIN + 페이징)
    $sql = "
        SELECT p.id, p.title, u.name AS author, p.created_at, p.views
        FROM posts p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.id DESC
        LIMIT $limit OFFSET $offset
    ";
    $result = $conn->query($sql);

} catch (Exception $e) {
    // 에러 로그 기록 및 사용자에게 오류 메시지 전달
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . PHP_EOL, 3, LOG_FILE_PATH);
    $_SESSION['error'] = '게시글 목록을 불러오는 중 오류가 발생했습니다.';
    header("Location: " . LOGIN_PATH . "/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시판</title>
</head>
<body>

<h2>게시판</h2>

<?php
// 로그인 상태 출력
if (isset($_SESSION['user_id'])) {
    echo '<p>안녕하세요, ' . htmlspecialchars($_SESSION['name']) . '님 | ';
    echo '<a href="' . LOGIN_PATH . '/logout.php">로그아웃</a></p>';
} else {
    echo '<p><a href="' . LOGIN_PATH . '/login.php">로그인</a></p>';
}

// 오류 메시지 출력
if (isset($_SESSION['error'])) {
    echo '<p style="color:red">' . htmlspecialchars($_SESSION['error']) . '</p>';
    unset($_SESSION['error']);
}
?>

<!-- 게시글 목록 테이블 -->
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
    <tr>
        <th>번호</th>
        <th>제목</th>
        <th>작성자</th>
        <th>작성일</th>
        <th>조회수</th>
    </tr>
    </thead>
    <tbody>
    <?php
    // 게시글 출력
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td><a href="' . BOARD_PATH . '/view.php?id=' . $row['id'] . '">'
                . htmlspecialchars($row['title']) . '</a></td>';
            echo '<td>' . htmlspecialchars($row['author']) . '</td>';
            echo '<td>' . htmlspecialchars(date('Y-m-d', strtotime($row['created_at']))) . '</td>';
            echo '<td>' . $row['views'] . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="5">게시글이 없습니다.</td></tr>';
    }
    ?>
    </tbody>
</table>

<!-- 페이지네이션 출력 -->
<div>
    <?php
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i === $page) {
            echo " <strong>[$i]</strong> ";
        } else {
            echo ' <a href="list.php?page=' . $i . '">[' . $i . ']</a> ';
        }
    }
    ?>
</div>

<?php
// 로그인한 경우 글쓰기 버튼 표시
if (isset($_SESSION['user_id'])) {
    echo '<p><a href="' . BOARD_PATH . '/write.php">글쓰기</a></p>';
}
?>

</body>
</html>
