<?php
/**
 * @file write_process.php
 * @brief 게시글 작성 처리 스크립트
 *
 * 기능 개요:
 * - 사용자 입력 수집 및 유효성 검사
 * - 유효하지 않은 입력은 에러 메시지와 함께 다시 작성 페이지로 이동
 * - 게시글을 DB에 저장 (비밀번호는 해싱하여 저장)
 * - 오류 발생 시 관리자 로그에 기록
 */

session_start();
require_once 'config.php';

// 사용자 입력 수집 및 공백 제거
$name     = isset($_POST['name'])     ? trim($_POST['name'])     : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$title    = isset($_POST['title'])    ? trim($_POST['title'])    : '';
$content  = isset($_POST['content'])  ? trim($_POST['content'])  : '';

/**
 * 문자열 유효성 검사 함수
 *
 * @param string $value 검사할 문자열
 * @param int $min 최소 길이
 * @param int $max 최대 길이
 * @return bool 유효한 경우 true, 아니면 false
 */
function isValidInput($value, $min = 1, $max = 255) {
    return isset($value) && mb_strlen($value) >= $min && mb_strlen($value) <= $max;
}

// 입력값 검증
$errors = [];

if (!isValidInput($name, 2, 30)) {
    $errors[] = '작성자 이름은 2자 이상 30자 이하로 입력해야 합니다.';
}
if (!isValidInput($password, 4, 30)) {
    $errors[] = '비밀번호는 4자 이상 30자 이하로 입력해야 합니다.';
}
if (!isValidInput($title, 1, 100)) {
    $errors[] = '제목은 1자 이상 100자 이하로 입력해야 합니다.';
}
if (!isValidInput($content, 1, 1000)) {
    $errors[] = '내용은 1자 이상 1000자 이하로 입력해야 합니다.';
}

// 유효성 검사 실패 시 에러 메시지 저장 후 작성 페이지로 이동
if (!empty($errors)) {
    $_SESSION['error'] = implode('<br>', $errors);
    header('Location: write.php');
    exit;
}

try {
    // DB 연결
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8mb4");

    // 비밀번호 해싱
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // SQL 작성 및 실행 (추후 Prepared Statement 사용 예정)
    $sql = "
        INSERT INTO posts (name, password, title, content, created_at)
        VALUES ('$name', '$hashedPassword', '$title', '$content', NOW())";

    if (!$conn->query($sql)) {
        throw new Exception('DB 등록 실패');
    }

    // 등록 성공 시 목록 페이지로 이동
    header('Location: list.php');
    exit;

} catch (Exception $e) {
    // 오류 발생 시 관리자 로그 기록
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . PHP_EOL, 3, LOG_FILE_PATH);

    // 사용자에게는 일반적인 오류 메시지 전달
    $_SESSION['error'] = '글 작성 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.';
    header('Location: write.php');
    exit;

} finally {
    // DB 연결 종료
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
