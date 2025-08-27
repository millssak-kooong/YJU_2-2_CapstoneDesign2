<?php
/**
 * @file register_process.php
 * @brief 회원가입 처리
 *
 * - 사용자 입력값 검증
 * - 아이디 중복 확인
 * - 비밀번호 해시 후 users 테이블에 저장
 * - 성공 시 로그인 페이지로 이동
 * - 실패 시 에러 메시지 출력
 */

session_start();
require_once '../conf/config.php';

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');

    // 입력값 확인
    $username_raw = trim($_POST['username'] ?? '');
    $password_raw = trim($_POST['password'] ?? '');
    $name_raw     = trim($_POST['name'] ?? '');

    if ($username_raw === '' || $password_raw === '' || $name_raw === '') {
        throw new Exception('모든 항목을 입력해주세요.');
    }

    // 아이디 중복 확인
    $username = $conn->real_escape_string($username_raw);
    $check_sql = "SELECT id FROM users WHERE username = '$username'";
    $check_result = $conn->query($check_sql);

    if ($check_result && $check_result->num_rows > 0) {
        throw new Exception('이미 존재하는 아이디입니다.');
    }

    // 비밀번호 해시 및 사용자 저장
    $hashed_pw = password_hash($password_raw, PASSWORD_DEFAULT);
    $name = $conn->real_escape_string($name_raw);
    $insert_sql = "
        INSERT INTO users (username, password, name)
        VALUES ('$username', '$hashed_pw', '$name')
    ";
    $conn->query($insert_sql);

    $_SESSION['success'] = '회원가입이 완료되었습니다. 로그인해주세요.';
    header("Location: login.php");
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: register.php");
    exit;
}
