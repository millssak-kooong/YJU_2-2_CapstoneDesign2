<?php
/**
 * @file login_process.php
 * @brief 로그인 처리
 *
 * - 사용자 입력 검증
 * - 사용자 조회 및 비밀번호 인증
 * - 인증 성공 시 세션 저장, 실패 시 에러 메시지 설정
 */
session_start();
require_once '../conf/config.php';

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');

    // 입력값 확인
    $username_raw = trim($_POST['username'] ?? '');
    $password_raw = trim($_POST['password'] ?? '');

    if ($username_raw === '' || $password_raw === '') {
        throw new Exception('아이디와 비밀번호를 모두 입력하세요.');
    }

    $username = $conn->real_escape_string($username_raw);

    // 사용자 조회 쿼리 실행
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    // 사용자 존재 여부 확인
    if ($result && $row = $result->fetch_assoc()) {
        // 비밀번호 일치 여부 확인
        if (password_verify($password_raw, $row['password'])) {
            // 인증 성공: 세션에 사용자 정보 저장
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['name'] = $row['name'];
            header("Location: welcome.php");
            exit;
        } else {
            // 비밀번호 불일치
            throw new Exception('비밀번호가 틀렸습니다.');
        }
    } else {
        // 사용자 아이디 없음
        throw new Exception('아이디가 존재하지 않습니다.');
    }

} catch (Exception $e) {
    // 예외 발생 시 에러 메시지 저장 후 로그인 페이지로 이동
    $_SESSION['error'] = $e->getMessage();
    header("Location: login.php");
    exit;
}
