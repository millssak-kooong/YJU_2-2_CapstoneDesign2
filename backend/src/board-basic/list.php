<?php
/**
 * @file list.php
 * @brief 게시글 목록 출력 페이지
 *
 * 주요 기능:
 * - 최신 게시글 목록 조회 (페이지당 5개)
 * - 게시글 제목 클릭 시 상세 보기 페이지(view.php)로 이동
 * - 예외 발생 시 로그 기록 및 사용자에게 오류 메시지 출력
 * - 페이지네이션 기능 포함
 */

require_once 'config.php'; // DB 설정 상수 포함
session_start();

// 현재 페이지 번호 설정 (기본값: 1)
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// 페이지당 게시글 수 및 OFFSET 계산
$limit = 5;
$offset = ($page - 1) * $limit;

try {
    // DB 연결
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8mb4");

    // 전체 게시글 수 조회 → 총 페이지 수 계산을 위해 필요
    $count_sql = "SELECT COUNT(*) AS total FROM posts";
    $count_result = $conn->query($count_sql);
    $total_posts = $count_result->fetch_assoc()['total'];
    $total_pages = ceil($total_posts / $limit); // 전체 페이지 수 계산

    // 현재 페이지에 해당하는 게시글 목록 조회
    $sql = "SELECT id, name, title, created_at 
            FROM posts 
            ORDER BY id DESC 
            LIMIT $limit OFFSET $offset";
    $posts = $conn->query($sql);

} catch (Exception $e) {
    // 에러 로그 기록 및 사용자 오류 메시지 저장
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . PHP_EOL, 3, LOG_FILE_PATH);
    $_SESSION['error'] = '게시글 목록을 불러오는 중 오류가 발생했습니다.';
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
    <title>게시판</title>
</head>
<body>
<h1>게시글</h1>

<?php
// 세션에 저장된 오류 메시지 출력 (1회성 메시지)
if (isset($_SESSION['error'])) {
    echo "<p style='color:red'>" . $_SESSION['error'] . "</p>";
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
    </tr>
    </thead>
    <tbody>
    <?php
    // 게시글 출력
    if (!empty($posts) && $posts instanceof mysqli_result) {
        while ($post = $posts->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($post['id']) . "</td>";
            echo "<td><a href='view.php?id=" . urlencode($post['id']) . "'>" . htmlspecialchars($post['title']) . "</a></td>";
            echo "<td>" . htmlspecialchars($post['name']) . "</td>";
            echo "<td>" . htmlspecialchars(date('Y-m-d', strtotime($post['created_at']))) . "</td>";
            echo "</tr>";
        }
    } else {
        // 게시글이 없는 경우
        echo "<tr><td colspan='4'>게시글이 없습니다.</td></tr>";
    }
    ?>
    </tbody>
</table>

<!-- 페이지네이션 출력 -->
<div style="margin-top:20px;">
    <?php
    if ($total_pages > 1) {
        echo "<p>페이지: ";
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == $page) {
                // 현재 페이지는 강조
                echo "<strong>$i</strong> ";
            } else {
                // 다른 페이지는 링크
                echo "<a href='?page=$i'>$i</a> ";
            }
        }
        echo "</p>";
    }
    ?>
</div>

<!-- 글쓰기 버튼 -->
<p><a href="write.php">글쓰기</a></p>
</body>
</html>

