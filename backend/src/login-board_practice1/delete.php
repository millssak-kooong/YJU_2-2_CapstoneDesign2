<?php
# 파일 실행 준비
session_start();
require_once __DIR__ . '/db_conf.php';
$db = new mysqli(db_info::URL, db_info::ID, db_info::PW, db_info::DB);

# 비로그인 접근 제한
if (!isset($_SESSION['login']['id'])) {
    $_SESSION['login']['error'] = '로그인 후 삭제할 수 있습니다.';
    header('Location: index.php');
    exit;
}


# 1. CRU'D'
// 작성자 확인도 함께
$sql = 'DELETE from post where num = ? and user_num = ?';
$stmt = $db->prepare($sql);
$stmt->bind_param('ii', $_GET['num'], $_SESSION['login']['num']);
$stmt->execute();
    if ($stmt->affected_rows !== 1) { // 글 없음, 작성자 다름
        $_SESSION['delete']['error'] = '글을 삭제하지 못하였습니다.';
        header('Location: view.php?num=' . $_GET['num']);
        exit;
    }
$stmt->close();

# 2. 리디렉션
$_SESSION['delete']['success'] = '글을 삭제하였습니다.';
header('Location: home.php');
exit;