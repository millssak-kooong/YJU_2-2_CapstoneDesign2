<?php
/**
 * @file logout.php
 * @brief 사용자 로그아웃 처리 페이지
 *
 * 세션 변수 초기화, 세션 쿠키 삭제, 세션 파기 처리 후
 * 로그인 페이지(login.php)로 리디렉션 수행
 */

// 세션 시작 처리
// - 현재 로그인 사용자 세션에 접근하기 위해 필요
session_start();

// 세션 변수 초기화 처리
// - 메모리에 저장된 세션 데이터 제거
$_SESSION = [];

// 세션 쿠키 제거 처리
// - 브라우저에 저장된 세션 식별 쿠키 삭제
// - 조건: 세션이 쿠키 기반으로 동작하는 경우에만 실행
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();  // 세션 쿠키 설정값 조회
    setcookie(
        session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 세션 종료 처리
// - 서버 측 세션 저장 파일 삭제
session_destroy();

// 로그인 페이지로 리디렉션
header("Location: login.php");
exit;
