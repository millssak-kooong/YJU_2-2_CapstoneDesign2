<?php
/**
 * @file write_process.php
 * @brief 게시글 작성 처리
 *
 * 기능:
 * - 로그인 사용자가 입력한 제목/내용을 검증 후 DB에 저장
 * - 실패 시 에러 메시지 세션에 저장 후 write.php로 이동
 * - 성공 시 글쓰기 완료 메시지 저장 후 list.php로 이동
 *
 * [POST name 목록]
 * - title: 게시글 제목
 * - content: 게시글 본문
 */

session_start();
require_once '../conf/config.php';

// 로그인 여부 확인
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = '로그인 후 작성이 가능합니다.';
    header("Location: " . LOGIN_PATH . "/login.php");
    exit;
}

// 사용자 입력값 수신 및 전처리
$title_raw = trim($_POST['title'] ?? '');
$content_raw = trim($_POST['content'] ?? '');

// 유효성 검증
if ($title_raw === '' || $content_raw === '') {
    $_SESSION['error'] = '제목과 내용을 모두 입력해 주세요.';
    header("Location: write.php");
    exit;
}

try {
    // DB 연결
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');

    // 입력값 이스케이프 처리
    $user_id = intval($_SESSION['user_id']);
    $title = $conn->real_escape_string($title_raw);
    $content = $conn->real_escape_string($content_raw);

    // INSERT 쿼리 실행
    $sql = "INSERT INTO posts (user_id, title, content) VALUES ($user_id, '$title', '$content')";
    $conn->query($sql);

    // 성공 메시지 저장 후 목록으로 이동
    $_SESSION['success'] = '게시글이 등록되었습니다.';
    header("Location: list.php");
    exit;

} catch (Exception $e) {
    // 오류 로그 기록 및 에러 메시지 전달
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . PHP_EOL, 3, LOG_FILE_PATH);
    $_SESSION['error'] = '글 등록 중 오류가 발생했습니다.';
    header("Location: write.php");
    exit;
}
?>
