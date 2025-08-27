<?php
session_start();

# 비로그인 접근 처리
if (!isset($_SESSION['login']['id'])) {
    $_SESSION['login']['error'] = '비회원은 볼 수 없습니다.';
    header('Location: index.php');
    exit;
}
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

    <?php
    
    ?>
</body>
</html>