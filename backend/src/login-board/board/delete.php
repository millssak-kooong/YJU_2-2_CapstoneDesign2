<?php
/**
 * @file delete.php
 * @brief 게시글 삭제 처리
 *
 * 기능:
 * - GET 파라미터 id를 통해 게시글 삭제
 * - 로그인 사용자만 삭제 가능
 * - 본인 게시글만 삭제 가능 (작성자 확인)
 *
 * [GET name 목록]
 * - id: 삭제할 게시글 ID
 */

session_start();
require_once '../conf/config.php';

// 로그인 여부 확인
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = '로그인 후 이용 가능합니다.';
    header("Location: " . LOGIN_PATH . "/login.php");
    exit;
}

// GET 파라미터 유효성 확인
$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($post_id <= 0) {
    $_SESSION['error'] = '잘못된 요청입니다.';
    header("Location: list.php");
    exit;
}

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');

    // 게시글 존재 및 작성자 확인
    $sql = "SELECT user_id FROM posts WHERE id = $post_id";
    $result = $conn->query($sql);

    if (!$result || $result->num_rows === 0) {
        $_SESSION['error'] = '해당 게시글이 존재하지 않습니다.';
        header("Location: list.php");
        exit;
    }

    $post = $result->fetch_assoc();

    // 작성자 확인
    if (intval($_SESSION['user_id']) !== intval($post['user_id'])) {
        $_SESSION['error'] = '본인 게시글만 삭제할 수 있습니다.';
        header("Location: list.php");
        exit;
    }

    // 게시글 삭제
    $delete_sql = "DELETE FROM posts WHERE id = $post_id";
    $conn->query($delete_sql);

    $_SESSION['success'] = '게시글이 삭제되었습니다.';
    header("Location: list.php");
    exit;

} catch (Exception $e) {
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . PHP_EOL, 3, LOG_FILE_PATH);
    $_SESSION['error'] = '게시글 삭제 중 오류가 발생했습니다.';
    header("Location: list.php");
    exit;
}

