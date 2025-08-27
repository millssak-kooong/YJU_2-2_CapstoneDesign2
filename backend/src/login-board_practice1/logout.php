<?php
# 로그 아웃 할 때 세션을 무효화 하여 사용자 정보를 완전히 제거.
// 0. 세션 활성화
session_start();

// 1. 현재 요청의 세변 변수 초기화
$_SESSION = [];

// 2. 서버 측 세션 저장소의 현재 세션 데이터 파기
session_destroy();

// 3. 클라이언트 세션 쿠키 무효화
if (ini_get('session.use_cookies')) {

    // 세션 쿠키 설정 값 가져 오기
    $params = session_get_cookie_params();

    // 빈 값으로 쿠키 재설정하여 만료 시킴
    setcookie(
        // 세션 쿠키 이름, 빈 값, 과거 시간으로 돌려 삭제 효과
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

# ---------- 세션 무효화 완료 ----------

// 1. 페이지 리디렉션
header('Location: index.php');

// 2. 코드는 더이상 없지만 안전성 차원에서 코드 강제 종료
exit;
