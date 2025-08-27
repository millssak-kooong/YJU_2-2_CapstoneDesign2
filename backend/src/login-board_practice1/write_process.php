<?php
# 로그인 상태 및 입력 값 유효성 검사 위해 세션 활성화
session_start();

// 비로그인 접근 조치용 에러 메시지 세션 저장
if (!isset($_SESSION['login']['id'])) {
    $_SESSION['login']['error'] = '죄송합니다. 회원 전용 서비스입니다.';
    header('Location: index.php');
    exit;
}

# ---------- 입력 값 처리 ----------

// 입력 값 전처리
$title_processed = trim($_POST['title'] ?? '');
$content_processed = trim($_POST['content'] ?? '');

// 입력 값 유효성 검사
if ($title_processed === '' || $content_processed === '') {
    $_SESSION['write']['error'] = '양식을 모두 입력하세요.';
    header('Location: write.php');
    exit;
}

# DB 접속 정보 불러오기 및 DB 연결
require_once('./db_conf.php');
$db_conn = new mysqli(db_info::URL, db_info::ID, db_info::PW, db_info::DB);

# SQL 쿼리 절차 - 준비문(Prepared Statement) 사용

/*
SQL을 미리 컴파일(준비) 해 두고, 값은 나중에 플레이스홀더(?) 자리에 바인딩해서 실행하는 방식
왜 쓰나? (3가지)
보안: 값과 SQL을 분리 → SQL 인젝션 방지
성능: 같은 SQL을 여러 번 실행할 때 파싱/플랜 재사용
정확성: 타입 안전 바인딩(숫자/문자 구분)으로 따옴표/이스케이프 실수 방지
*/

// SQL 문법 위해 전처리
$num = $_SESSION['login']['num'];
$name = $_SESSION['login']['name'];

// 타입 문자열 'isss' = int, string, string, string
$stmt = $db_conn->prepare("insert into post (user_num, name, title, content) values (?, ?, ?, ?)");
$stmt->bind_param('isss', $num, $name, $title_processed, $content_processed);

// 등록 성공 처리
if ($stmt->execute()) {
    $_SESSION['write']['success'] = '게시글을 등록하였습니다.';
    header('Location: home.php');
    exit;
}

// SQL 오류 조치 메시지 세션 저장
else {
    $_SESSION['write']['error'] = '게시글 등록을 실패하였습니다.';
    header('Location: write.php');
    exit;
}
