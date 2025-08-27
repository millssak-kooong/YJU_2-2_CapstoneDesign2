<?php

#0. 오류 및 성공 메시지 저장용 세션 활성화
session_start();

#1. post로 전달 받은 입력 값 전처리
// trim: 앞뒤 공백 제거(중간x)
// ?? '': 값이 null(없음)일 때 오른쪽이 기본 값
$name_processed = trim($_POST['name'] ?? '');
$id_processed = trim($_POST['id'] ?? '');
$pw_processed = trim($_POST['pw'] ?? '');

#2. 입력 값 유효성 검사 (공백)
if ($name_processed === '' || $id_processed === '' || $pw_processed === '') {

    // 에러용 세션 변수 선언
    $_SESSION['register']['error'] = '모든 정보를 입력하세요.';

    // 회원 가입 페이지 리디렉션
    header('Location: register.php');

    // 불필요한 코드 실행 및 리소스 방지 위해 코드 강제 종료
    exit;
}

#3. ID 중복 검사

// DB 접속 정보 불러오기
require_once('./db_conf.php');

// DB 연결
$db_conn = new mysqli(db_info::URL, db_info::ID, db_info::PW, db_info::DB);

// SQL 쿼리 생성 (중복 ID 검색)
$sql_select = "select id from user where id = '$id_processed'";

// 쿼리 실행 결과 저장
$result = $db_conn->query($sql_select);

// 에러 조치
if ($result && $result->num_rows > 0) {

    // DB 연결 종료
    $db_conn->close();

    // 에러용 세션 변수 선언
    $_SESSION['register']['error'] = '사용 중인 ID입니다.';

    // 회원 가입 페이지 리디렉션
    header("Location: register.php");

    // 불필요한 코드 및 리소스 방지 위해 코드 강제 종료
    exit;
}

// 성공 조치
// 비밀 번호 해시 값으로 암호화 저장
$pw_hashed = password_hash($pw_processed, PASSWORD_DEFAULT);

// SQL 쿼리 생성 (회원 정보 INSERT)
$sql_insert = "insert into user (name, id, password) values ('$name_processed', '$id_processed', '$pw_hashed')";

// 쿼리 실행
$db_conn->query($sql_insert);

// DB 연결 종료
$db_conn->close();

// 회원 가입 성공 메시지 저장
$_SESSION['register']['success'] = '회원 가입 완료.';

// 기본 페이지 리디렉션
header('Location: index.php');

// 불필요한 코드 실행 및 리소스 방지 위해 코드 강제 종료
exit;
