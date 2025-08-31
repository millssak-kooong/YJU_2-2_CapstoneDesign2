<?php
# 코드 실행 위한 기본 설정
session_start();
require_once __DIR__ . '/db_conf.php';
$db = new mysqli(db_info::URL, db_info::ID, db_info::PW, db_info::DB);

# 비로그인 접근 처리
if (!isset($_SESSION['login']['id'])) {
    $_SESSION['login']['error'] = '로그인 후 글을 볼 수 있습니다.';
    header('Location: index.php');
    exit;
}

# 1. 글 번호 파라미터 설정
$num = (int)$_GET['num']; // primary key라 예외 방지 처리 생략

# 2. 조회수 증가 (준비문 사용)
$stmt = $db->prepare("update post set view = view + 1, updated_at = updated_at where num = ?");
$stmt->bind_param('i', $num); // ?의 개수/순서 = 바인딩 순서
$stmt->execute();
$stmt->close(); // 준비문 자원 해제

# 3. 글 정보 가져오기
$stmt = $db->prepare('select name, title, content, view, created_at, updated_at
                      from post where num = ? limit 1');
$stmt->bind_param('i', $num);
$stmt->execute();
$result = $stmt->get_result();

    // 쿼리 실패 시, 글이 없을 때 에러 처리
    if (!$result) {
        $_SESSION['post']['error'] = '글을 불러오지 못했습니다.';
        header('Location: home.php');
        exit;
    } elseif ($result->num_rows === 0) {
        $_SESSION['post']['error'] = '존재하지 않는 글입니다.';
        header('Location: home.php');
        exit;
    }

$post = $result->fetch_assoc(); // 결과 1행을 연관배열로 받음 $post['name'], ...
$result->free(); // 결과셋 메모리 해제
$stmt->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>글 보기 문서</title>
</head>
<body>
    <h1>글 보기</h1>
    <p>작성 일시: <?= $post['created_at'] ?></p>
    <!-- updated_at 값이 null이 아닐 때만 출력, ":, endif;": HTML이랑 섞어 쓸 때 중괄호 { } 대신 -->
    <?php if (!empty($post['updated_at'])) : ?><p>마지막 수정 일시: <?= $post['updated_at'] ?></p><?php endif; ?>
    <p>조회 수: <?= $post['view'] ?></p>
    <!-- 수정/삭제 버튼: verify.php로 글 번호와 요청 종류 전달
         => verify.php에서 비밀번호 검증 후 edit.php 또는 delete.php로 이동 -->
    <form action="verify.php" method="post">
        <input type="hidden" name="post_num" value="<?= $num ?>">
        <button type="submit" name="request" value="edit">수정</button>
        <button type="submit" name="request" value="delete">삭제</button>
    </form>
    <hr>
    <p>글쓴이: <?= $post['name'] ?></p>
    <p>제목: <?= $post['title'] ?></p>
    <pre style="white-space: pre-wrap;"><?= $post['content'] ?></pre>
    <hr>
    <button type="button" onclick="location.href='home.php'">목록</button>
</body>
</html>