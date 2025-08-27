<?php
/**
 * @file logout.php
 * @brief 사용자 로그아웃 처리
 *
 * - 세션을 초기화하고 쿠키를 제거한 뒤 로그인 페이지로 이동
 */

session_start();

// 세션 데이터 제거
$_SESSION = [];

// 세션 쿠키 제거
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// 세션 파괴
session_destroy();

// 로그인 페이지로 이동
header("Location: login.php");
exit;
?>
