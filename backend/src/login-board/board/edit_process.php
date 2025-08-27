<?php
/**
 * @file edit_process.php
 * @brief 게시글 수정 처리
 *
 * 기능:
 * - 로그인 사용자가 본인 게시글을 수정
 * - 제목과 내용을 검증 후 DB에 반영
 * - 수정 성공 시 view.php로, 실패 시 edit.php로 이동
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

// POST 값 수신 및 유효성 검사
$post_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$title_raw = trim($_POST['title'] ?? '');
$content_raw = trim($_POST['content'] ?? '');

if ($post_id <= 0 || $title_raw === '' || $content_raw === '') {
    $_SESSION['error'] = '제목과 내용을 모두 입력해 주세요.';
    header("Location: edit.php?id=" . $post_id);
    exit;
}

try {
    // DB 연결
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');

    // 게시글 존재 및 소유자 확인
    $check_sql = "SELECT user_id FROM posts WHERE id = $post_id";
    $check_result = $conn->query($check_sql);

    if (!$check_result || $check_result->num_rows === 0) {
        $_SESSION['error'] = '해당 게시글이 존재하지 않습니다.';
        header("Location: list.php");
        exit;
    }

    $row = $check_result->fetch_assoc();

    // 본인 글인지 확인
    if (intval($_SESSION['user_id']) !== intval($row['user_id'])) {
        $_SESSION['error'] = '본인 게시글만 수정할 수 있습니다.';
        header("Location: list.php");
        exit;
    }

    // 입력값 이스케이프 처리
    $title = $conn->real_escape_string($title_raw);
    $content = $conn->real_escape_string($content_raw);

    // 수정 쿼리 실행 (updated_at 자동 갱신됨)
    $update_sql = "
        UPDATE posts
        SET title = '$title', content = '$content'
        WHERE id = $post_id
    ";
    $conn->query($update_sql);

    $_SESSION['success'] = '게시글이 수정되었습니다.';
    header("Location: view.php?id=" . $post_id);
    exit;

} catch (Exception $e) {
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . PHP_EOL, 3, LOG_FILE_PATH);
    $_SESSION['error'] = '게시글 수정 중 오류가 발생했습니다.';
    header("Location: edit.php?id=" . $post_id);
    exit;
}
