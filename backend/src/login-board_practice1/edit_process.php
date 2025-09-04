<?php
# 파일 실행 준비
session_start();
require_once __DIR__ . '/db_conf.php';
$db = new mysqli(db_info::URL, db_info::ID, db_info::PW, db_info::DB);

# 비로그인 접근 제한 조치
if (!isset($_SESSION['login']['id'])) {
    $_SESSION['login']['error'] = '로그인 후 edit_process.php에 접근할 수 있습니다.';
    header('Location: index.php');
    exit;
}


/* ---------- 'Update' of CRUD ----------

    get으로 넘어 온 글 정보에 post로 넘어 온 새 글 정보를 insert
    에러: 글 정보 비교 후 수정 사항이 없을 시
    성공: 리디렉션 후 안내 메시지 표시
                                    */

# 1. 데이터 정규화 및 자료형 캐스팅
$post_num = (int)($_GET['num']);
$new_title = $_POST['title'];
$new_content = $_POST['content'];

# 2. 'Read' of CRUD
$sql = 'SELECT title, content from post where num = ?';
$stmt = $db->prepare($sql);
$stmt->bind_param('i', $post_num);
$stmt->execute();
$result = $stmt->get_result(); // 결과셋 객체
$row = $result->fetch_assoc(); // 연관 배열
$result->free();               // 결과 버퍼랑 메모리 정리
$stmt->close();                // 준비문 리소스 해제

    // 조회 실패
    if (!$row) {
        $db->close();
        $_SESSION['edit']['error'] = '글을 찾을 수 없습니다.';
        header("Location: edit.php?num=$post_num");
        exit;
    }

    // 수정 사항 유무 검사
    if ($row['title'] === $new_title && $row['content'] === $new_content) {
        $db->close();
        $_SESSION['edit']['error'] = '변경된 사항이 없습니다.';
        header("Location: edit.php?num=$post_num");
        exit;
    }

# 3. 'Update' of CRUD
$sql = 'UPDATE post set title = ?, content = ? where num = ?';
$stmt = $db->prepare($sql);
$stmt->bind_param('ssi', $new_title, $new_content, $post_num);
$stmt->execute();
$stmt->close();

# 4. 리디렉션
$_SESSION['edit']['success'] = '글을 수정하였습니다.';
header("Location: view.php?num=$post_num");
exit;