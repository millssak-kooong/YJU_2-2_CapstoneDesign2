<?php
/**
 * @file register_process.php
 * @brief 회원가입 처리 로직
 *
 * 사용자 입력값 검증, 비밀번호 해싱, SQL 인젝션 방지 처리 후
 * users 테이블에 회원 정보 INSERT 수행
 * 중복 아이디 또는 DB 오류 발생 시 에러 메시지를 세션에 저장하고 register.php로 리디렉션
 * 성공 시 login.php로 이동
 */

// MySQLi 오류를 오류가 발생해도 경고도 예외도 발생하지 않음 설정
// try-catch를 사용하여 예외 처리 시 "MYSQLI_REPORT_STRICT" 사용
mysqli_report(MYSQLI_REPORT_OFF);

// 세션 시작 처리
// - 사용자 상태 정보 유지 및 오류/성공 메시지 전달에 사용
session_start();

// DB 접속 정보 포함
// - db_info 클래스의 정적 상수 활용
require_once('./db_conf.php');

// 데이터베이스 연결 수행
// - 접속 정보에 따라 mysqli 객체 생성
$db_conn = new mysqli(
    db_info::DB_URL,
    db_info::USER_ID,
    db_info::PASSWD,
    db_info::DB
);

// DB 연결 실패 처리
// - 세션에 오류 메시지 저장 후 register.php로 리디렉션
if ($db_conn->connect_errno) {
    $_SESSION['error'] = "DB 연결에 실패했습니다.";
    header("Location: register.php");
    exit;
}

// 사용자 입력값 전처리
// - POST로 전달된 값의 앞뒤 공백 제거
$username_raw = trim($_POST['username'] ?? '');
$password_raw = trim($_POST['password'] ?? '');
$name_raw     = trim($_POST['name'] ?? '');

// 입력값 유효성 검사
// - 세 입력값 중 하나라도 비어 있을 경우
//   → 세션에 오류 메시지 저장 후 register.php로 리디렉션
if ($username_raw === '' || $password_raw === '' || $name_raw === '') {
    $_SESSION['error'] = "모든 필드를 입력하세요.";
    header("Location: register.php");
    exit;
}

// 비밀번호 해싱 처리
// - password_hash() 함수 사용 (기본 알고리즘: bcrypt)
$password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);

// SQL 인젝션 방지를 위한 이스케이프 처리
// - 실습 목적의 문자열 escaping 사용
// - 실무 환경에서는 prepared statement 사용 권장
$username = $db_conn->real_escape_string($username_raw);
$password = $db_conn->real_escape_string($password_hashed);
$name     = $db_conn->real_escape_string($name_raw);

// 6. 사용자 정보 INSERT 쿼리 실행
// - 입력값을 users 테이블에 저장
$sql = "
    INSERT INTO users (username, password, name)
    VALUES ('$username', '$password', '$name')
";

if ($db_conn->query($sql)) {
    // 회원가입 성공 처리
    // - 세션에 성공 메시지 저장 후 login.php로 리디렉션
    $db_conn->close();
    $_SESSION['success'] = "회원가입이 완료되었습니다. 로그인 해주세요.";
    header("Location: login.php");
    exit;
} else {
    // 회원가입 실패 처리
    // - 실패 원인 구분: 중복 아이디 (에러코드 1062), 그 외 일반 오류
    if ($db_conn->errno === 1062) {
        // 중복된 아이디 사용 시
        $_SESSION['error'] = "이미 사용 중인 아이디입니다.";
    } else {
        // 기타 데이터베이스 오류
        $_SESSION['error'] = "회원가입에 실패했습니다. 관리자에게 문의하세요.";
        // 내부 에러 로그 기록
        error_log("[REGISTER ERROR] " . $db_conn->error);
    }
    $db_conn->close();
    // - 에러 메시지와 함께 register.php로 리디렉션
    header("Location: register.php");
    exit;
}
