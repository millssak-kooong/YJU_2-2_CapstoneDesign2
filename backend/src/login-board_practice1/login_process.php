<?php

# 세션 활성화 (에러, 성공)
session_start();

# ---------- 입력 값 전처리 및 유효성 검사 ----------

// 1. 앞뒤 공백 제거 또는 입력 값 없을 때 기본 값 지정
//    비밀 번호는 공백도 의도일 수 있으므로 그대로 비교
$id = trim($_POST['id'] ?? '');
$pw = $_POST['pw'];

// 2. 유효성 검사
if ($id === '' || $pw === '') {
    $_SESSION['login']['error'] = 'ID와 Password를 모두 입력하세요.';
    header('Location: login.php'); exit;
}

# ---------- 중복 검사 ----------

// 1. DB 접속 정보 불러 오기
require_once('./db_conf.php');

// 2. DB 연결
$db_conn = new mysqli(db_info::URL, db_info::ID, db_info::PW, db_info::DB);

// 3. 검색용 sql 쿼리 작성
$sql_select = "select * from user where id = '$id'";

// 4. 쿼리 실행하여 결과 생성
$result = $db_conn->query($sql_select); // 객체(결과 집합) or false 저장

// 5. ID 확인, 에러 메시지 세션에 저장
if ($result === false) {  // 여기서 result는 truthy or false로 인식
    $db_conn->close();
    $_SESSION['login']['error'] = ' SQL 쿼리 실패.';
    header('Location: login.php'); exit;
}

if ($result->num_rows === 0) {
    $db_conn->close();
    $_SESSION['login']['error'] = '존재하지 않는 ID입니다.';
    header('Location: login.php'); exit;
}

// 6. Password 확인, 에러 메시지 세션에 저장
    
    # 결과 집합에서 행을 컬럼명 기반 연관 배열로 가져와 변수에 저장
    # 한 번만 호출 (할 때마다 다음 행을 부름)
    $row = $result->fetch_assoc();

if (!password_verify($pw, $row['password'])) {
        $db_conn->close();
        $_SESSION['login']['error'] = '비밀 번호가 일치하지 않습니다.';
        header('Location: login.php'); exit;
    }

// 7. 로그인 성공, 로그인 상태 유지 위해 세션에 num, name, id 저장
$_SESSION['login']['num'] = $row['num']; // 외래키로써 게시글 작성에 사용
$_SESSION['login']['name'] = $row['name'];
$_SESSION['login']['id'] = $row['id'];
$db_conn->close();
header('Location: home.php'); exit;