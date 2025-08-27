<?php
/**
 * @file welcome.php
 * @brief 로그인 후 접근 가능한 환영 페이지
 *
 * 세션에 저장된 사용자 이름을 표시하고 로그아웃 링크 제공
 * 비인증 사용자가 접근할 경우 login.php로 리디렉션 처리
 */

// 세션 시작 처리
// - 로그인 여부 확인 및 사용자 이름 출력 등에 사용
session_start();

// 사용자 인증 여부 확인
// - 세션에 user_id가 존재하지 않으면 비인증 상태로 판단
// - 로그인 페이지로 강제 리디렉션 처리 (불법 접근 차단)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!-- 인증된 사용자에게 환영 메시지 출력 -->
<!-- - 세션에 저장된 사용자 이름을 HTML 이스케이프 처리하여 출력 -->
<h2>환영합니다, <?= htmlspecialchars($_SESSION['name']) ?>님!</h2>

<!-- 로그아웃 링크 제공 -->
<!-- - logout.php 실행을 통해 세션 종료 및 로그인 화면으로 이동 -->
<a href="logout.php">로그아웃</a>
