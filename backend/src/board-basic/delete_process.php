<?php
/**
 * @file delete_process.php
 * @brief 게시글 삭제 처리
 *
 * 기능:
 * - POST로 전달된 id와 password 확인
 * - 비밀번호 일치 시 게시글 삭제
 * - 실패 시 오류 메시지 출력 후 되돌아감
 */

require_once 'config.php';
session_start();

// 사용자 입력 수집 및 공백 제거
$id       = isset($_POST['id'])       ? (int)$_POST['id'] : 0;
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// 입력값 유효성 검증
$errors = [];

if ($id <= 0) {
    $errors[] = '잘못된 게시글 요청입니다.';
}
if (mb_strlen($password) < 4 || mb_strlen($password) > 30) {
    $errors[] = '비밀번호는 4자 이상 30자 이하로 입력해야 합니다.';
}

if (!empty($errors)) {
    $_SESSION['error'] = implode('<br>', $errors);
    header("Location: delete.php?id=$id");
    exit;
}

try {
    // DB 연결
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8mb4");

    // 게시글 존재 및 비밀번호 확인
    $sql = "SELECT password FROM posts WHERE id = $id";
    $result = $conn->query($sql);

    if (!$result || $result->num_rows === 0) {
        $_SESSION['error'] = '해당 게시글을 찾을 수 없습니다.';
        header("Location: list.php");
        exit;
    }

    $row = $result->fetch_assoc();

    if (!password_verify($password, $row['password'])) {
        $_SESSION['error'] = '비밀번호가 일치하지 않습니다.';
        header("Location: delete.php?id=$id");
        exit;
    }

    // 게시글 삭제
    $sql = "DELETE FROM posts WHERE id = $id";
    if (!$conn->query($sql)) {
        throw new Exception('게시글 삭제 실패');
    }

    // 삭제 완료 후 목록으로 이동
    header("Location: list.php");
    exit;

} catch (Exception $e) {
    error_log("[" . date('Y-m-d H:i:s') . "] " . $e->getMessage() . PHP_EOL, 3, LOG_FILE_PATH);
    $_SESSION['error'] = '서버 내부 오류로 인해 게시글 삭제에 실패했습니다.';
    header("Location: delete.php?id=$id");
    exit;
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
