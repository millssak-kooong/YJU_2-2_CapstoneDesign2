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
$post_num = (int)($_POST['post_num']);
$new_title = $_POST['title'];
$new_content = $_POST['content'];

# 2. CR'U'D - 작성자 일치 그리고 값 변경 되었을 시
$sql = 'UPDATE post
        set title = ?, content = ?
        where num = ?
        and user_num = ?
        and (not (title <=> ?) or not (content <=> ?))';
$stmt = $db->prepare($sql);
$stmt->bind_param('ssiiss', $new_title, $new_content, $post_num, $_SESSION['login']['num'], $new_title, $new_content);
$stmt->execute();

    /* affected_row: 바뀐/삽입된/삭제된 행 수
       0이면: (1) 글 없음/권한 없음 OR (2) 내용 동일
       1이면: 쿼리 성공 */
    if ($stmt->affected_rows === 0) {
        $_SESSION['edit']['error'] = '변경된 사항이 없습니다.';
        header("Location: edit.php?num=$post_num");
        exit;
    }
$stmt->close();

# 3. 리디렉션
$_SESSION['edit']['success'] = '글을 수정하였습니다.';
header("Location: view.php?num=$post_num");
exit;