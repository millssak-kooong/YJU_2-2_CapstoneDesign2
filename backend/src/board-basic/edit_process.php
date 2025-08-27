<?php
require_once 'config.php';
session_start();

// 사용자 입력 수집 및 공백 제거
$id       = isset($_POST['id'])       ? (int)$_POST['id'] : 0;
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$title    = isset($_POST['title'])    ? trim($_POST['title'])    : '';
$content  = isset($_POST['content'])  ? trim($_POST['content'])  : '';

// 문자열 유효성 검사 함수
function isValidInput($value, $min = 1, $max = 255) {
    return isset($value) && mb_strlen($value) >= $min && mb_strlen($value) <= $max;
}

// 입력값 검증
$errors = [];

if ($id <= 0) {
    $errors[] = '잘못된 게시글 요청입니다.';
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

// 에러 발생 시 수정 페이지로 되돌림
if (!empty($errors)) {
    $_SESSION['error'] = implode('<br>', $errors);
    header("Location: edit.php?id=$id");
    exit;
}

try {
    // DB 연결
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8mb4");

    // 게시글 존재 여부 및 비밀번호 확인
    $sql = "SELECT password FROM posts WHERE id = $id";
    $result = $conn->query($sql);

    // 게시글이 없거나 조회 실패
    if (!$result || $result->num_rows === 0) {
        $_SESSION['error'] = '해당 게시글을 찾을 수 없습니다.';
        header("Location: list.php");
        exit;
    }

    $row = $result->fetch_assoc();

    // 비밀번호 불일치
    if (!password_verify($password, $row['password'])) {
        $_SESSION['error'] = '비밀번호가 일치하지 않습니다.';
        header("Location: edit.php?id=$id");
        exit;
    }

    // 게시글 업데이트 실행
    $sql = "UPDATE posts SET 
            title = '" . $conn->real_escape_string($title) . "',
            content = '" . $conn->real_escape_string($content) . "'
        WHERE id = $id";


    if (!$conn->query($sql)) {
        throw new Exception('DB 업데이트 실패');
    }

    // 성공 시 상세 페이지로 이동
    header("Location: view.php?id=$id");
    exit;

} catch (Exception $e) {
    // 서버 내부 오류 로깅 및 에러 메시지 출력
    error_log("[" . date('Y-m-d H:i:s') . "] " . $e->getMessage() . PHP_EOL, 3, LOG_FILE_PATH);
    $_SESSION['error'] = '서버 내부 오류로 인해 게시글 수정에 실패했습니다.';
    header("Location: edit.php?id=$id");
    exit;
} finally {
    // DB 연결 종료
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
