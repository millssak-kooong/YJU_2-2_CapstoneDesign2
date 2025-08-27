<?php
/**
 * @file login_process.php
 * @brief 로그인 인증 처리 로직
 *
 * 사용자 입력값 검증 후 users 테이블에서 사용자 조회
 * 입력 비밀번호와 DB에 저장된 해시 비교를 통해 인증 수행
 * 인증 성공 시 세션에 사용자 정보 저장 후 welcome.php로 리디렉션
 * 실패 시 에러 메시지를 세션에 저장하고 login.php로 이동
 */

// MySQLi 오류를 오류가 발생해도 경고도 예외도 발생하지 않음 설정
// try-catch를 사용하여 예외 처리 시 "MYSQLI_REPORT_STRICT" 사용
mysqli_report(MYSQLI_REPORT_OFF);

// 세션 시작 처리
// - 사용자 인증 상태 유지 및 에러 메시지 전달에 사용
session_start();

// DB 접속 정보 포함
// - db_info 클래스 내부에 정의된 DB 접속 상수 사용
require_once('./db_conf.php');

// MySQL 데이터베이스 연결 시도
$db_conn = new mysqli(
    db_info::DB_URL,
    db_info::USER_ID,
    db_info::PASSWD,
    db_info::DB
);

// DB 연결 실패 시 처리
// - 세션에 오류 메시지를 저장하고 로그인 처리 페이지로 이동
if ($db_conn->connect_errno) {
    $_SESSION['error'] = "DB 연결 실패";
    header("Location: login_process.php");
    exit;
}

// 사용자 입력값 전처리
// - trim()을 통해 앞뒤 공백 제거
// - POST 미입력 시 null 병합 연산자로 빈 문자열 대입
$username_raw = trim($_POST['username'] ?? '');
$password_raw = trim($_POST['password'] ?? '');

// ▶ 입력값 유효성 검사 처리
// - 아이디 또는 비밀번호가 비어 있는 경우:
//   1. 세션에 오류 메시지 저장 ("아이디와 비밀번호를 모두 입력하세요.")
//   2. 로그인 입력 페이지 (login.php)로 리디렉션
if ($username_raw === '' || $password_raw === '') {
    $_SESSION['error'] = "아이디와 비밀번호를 모두 입력하세요.";
    header("Location: login.php");
    exit;
}

// SQL 인젝션 방지를 위한 문자열 이스케이프 처리
// - 실습용 escaping 처리 방식 사용 (실무는 prepared statement 권장)
$username = $db_conn->real_escape_string($username_raw);

// 사용자 정보 조회용 SELECT 쿼리 실행
$query = "SELECT * FROM users WHERE username = '$username'";
$result = $db_conn->query($query);

// DB 연결 종료 (쿼리 결과는 계속 사용됨)
$db_conn->close();

// ▶ 사용자 조회 결과 확인 및 인증 처리
if ($result && $row = $result->fetch_assoc()) {
    // ▶ 비밀번호 검증 처리
    // - password_hash()로 저장된 암호화된 값과 입력값 비교
    if (password_verify($password_raw, $row['password'])) {
        // 로그인 성공
        // - 세션에 사용자 식별 정보 저장
        // - welcome.php로 리디렉션
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['name'] = $row['name'];
        header("Location: welcome.php");
        exit;
    } else {
        // 비밀번호 불일치
        // - 세션에 오류 메시지 저장 ("비밀번호가 틀렸습니다.")
        // - login.php로 리디렉션
        $_SESSION['error'] = "비밀번호가 틀렸습니다.";
        header("Location: login.php");
        exit;
    }
} else {
    // 사용자 아이디 없음
    // - 세션에 오류 메시지 저장 ("아이디가 존재하지 않습니다.")
    // - login.php로 리디렉션
    $_SESSION['error'] = "아이디가 존재하지 않습니다.";
    header("Location: login.php");
    exit;
}
