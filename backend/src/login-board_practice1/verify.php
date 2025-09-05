<?php
# 파일 실행 준비 코드들
session_start();
require_once __DIR__ . '/db_conf.php';
$db = new mysqli(db_info::URL, db_info::ID, db_info::PW, db_info::DB);

# 비로그인 접근 거부
if (!isset($_SESSION['login']['id'])) {
    $_SESSION['login']['error'] = '로그인 후 인증할 수 있습니다.';
    header('Location: index.php');
    exit;
}

# 검증 폼 렌더링
function render_form(int $post_num, string $request, string $error = ''): void {
    ?>

    <!-- 2. 비밀 번호 받기 -->
    <!DOCTYPE html>
    <html lang="ko">
    <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>검증 문서</title>
    </head>
    <body>
        <h1>검증</h1>
        
        <?php // 비밀 번호 불일치 오류 메시지 출력
        if ($error) {
            echo "<p style='color:red'>$error</p>"; // 한 번만 표시: 지역 변수라 함수 끝나면 자동 사라짐
        }
        ?>

        <!-- ※ POST 전송은 form으로밖에 못한다.
             ※ 쿼리스트링 붙이면 GET, POST 동시 가능.

                여기서 post 방식은 비밀 번호 틀릴 때만 사용되며 verify.php에서만 돈다. -->
        <form action="verify.php" method="post">
            <input type="hidden" name="post_num" value="<?= $_POST['post_num'] ?>">
            <input type="hidden" name="request" value="<?= $_POST['request'] ?>">
            <input type="password" name="password" required placeholder="비밀 번호를 입력하세요.">
            <input type="submit" value="확인">
        </form>
        <button type="button" onclick="location.href='view.php?num=<?= $_POST['post_num'] ?>'">취소</button>
    </body>
    </html>

    <?php
    exit; // 페이지 렌더링 후 바로 코드 종료
}

# 0. 비밀 번호 미입력 상태 (처음 접근 통과용)
if (!isset($_POST['password'])) {
    
    # 1. 작성자 확인
    $stmt = $db->prepare('select 1 from post where num = ? and user_num = ?');
    $stmt->bind_param('ii', $_POST['post_num'], $_SESSION['login']['num']);
    $stmt->execute(); // 반환형: boolean
    $result = $stmt->get_result()->num_rows === 1; // 불린 저장: 1개일 때만 true, 아니면 false.
    $stmt->close();

    // 작성자 불일치
    if (!$result) {
        $_SESSION['verify']['error'] = '작성자가 일치하지 않습니다.';
        header('Location: view.php?num=' . $_POST['post_num']);
        exit;
    }

    // 작성자 일치
    render_form($_POST['post_num'], $_POST['request']);
}


# 3. 비밀 번호 검증

// 비밀 번호 조회 시 작성자 확인도 한 번 더 동시 보장
$stmt = $db->prepare('select u.password
                     from post p
                     join user u on u.num = p.user_num
                     where p.num = ? and u.num = ?');
$stmt->bind_param('ii', $_POST['post_num'], $_SESSION['login']['num']);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

// 비밀 번호 불일치
if (!password_verify($_POST['password'], $row['password'])) {
    render_form($_POST['post_num'], $_POST['request'], '비밀 번호가 일치하지 않습니다.');
}

// 비밀 번호 일치 -> 요청한 곳으로 리디렉션
    # ※ 쿼리스트링이 붙으면 무조건 GET 전송 -> 쿼리스트링 없으면 GET 불가
header('Location: ' . $_POST['request'] . '.php?num=' . $_POST['post_num']);
exit;