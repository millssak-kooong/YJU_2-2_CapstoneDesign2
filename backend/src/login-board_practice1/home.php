<?php
# 로그인 상태 유지, 에러 출력 위해 세션 활성화
session_start();

// 비로그인 접근 에러 메시지 저장
if (!isset($_SESSION['login']['id'])) {
    $_SESSION['login']['error'] = '로그인 후 이용 부탁드립니다.';
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>홈 문서</title>
</head>
<body>
    <h1>게시판</h1>

    <p id="now" style="color:darkslategray"></p>
    <script> // 사용자 PC 시간대 기준 실시간 시각
        const el = document.getElementById('now');
        const tick = () => el.textContent = new Date().toLocaleString();
        tick();
        setInterval(tick, 1000);
    </script>

    <?php
    // 로그인 상태로 로그인 페이지 접근 시 에러 메시지
    if (isset($_SESSION['login']['error'])) {
        echo "<p style='color:red'>" . htmlspecialchars($_SESSION['login']['error']) . "</p>";
        unset($_SESSION['login']['error']); // 플래시 메시지 패턴
    }
    ?>

    <P><strong><?= $_SESSION['login']['name'] ?></strong> 회원님 접속 중.</p>
    
    <?php
    // 글 등록 성공 알림
    if (isset($_SESSION['write']['success'])) {
        echo "<p style='color:darkgreen'>" . htmlspecialchars($_SESSION['write']['success']) . "</p>";
        unset($_SESSION['write']['success']); // 플래시 메시지 패턴
    }
    ?>
    
    <a href="logout.php">로그 아웃</a>
    <hr>
    <h3>목록</h3>
    <button type="button" onclick="location.href='write.php'">글 쓰기</button>

    <?php # 게시글 목록 (페이지네이션)
    
    # 1. DB 접속 정보 불러오기 및 연결
    require_once __DIR__ . '/db_conf.php';
    $db = new mysqli(db_info::URL, db_info::ID, db_info::PW, db_info::DB);

    # 2. 페이지 파라미터 계산
    $post_per_page = 5; // 한 페이지당 글 수
    $page_now = isset($_GET['page_now']) ? (int)$_GET['page_now'] : 1; // 있으면 그 값을 정수, 없으면 1
    if ($page_now < 1) {
        $page_now = 1;
    }
    
    # 3. 글 수 조회 및 페이지 계산
    $total_post = 0;
    $sql_count = $db->query('SELECT count(*) as count from post');
    // COUNT(*)는 테이블의 전체 행 수를 한 줄로 반환
    // as count: 배열 키 이름 설정
    if ($sql_count) {
        $count_row = $sql_count->fetch_assoc(); // 결과 1행을 연관배열로 받음
        $total_post = (int)$count_row['count']; // int로 명시 캐스팅
        $sql_count->free(); // 메모리 해제
    }
    $total_page = max(1, (int)ceil($total_post / $post_per_page)); // 올림(ceil)
    // 둘 중 가장 큰 값을 돌려줌 → 값에 하한을 줌 → 1 페이지 보장

    # 4. 페이지 보정
    if ($page_now > $total_page) {
        $page_now = $total_page;
    }
    /*  < 상한 보정(클램프) >
        사용자가 URL로 ?page=999처럼 존재하지 않는 큰 페이지를 요청해도,
        실제 마지막 페이지($total_page)로 강제합니다.
        이미 2번에서 하한(1 미만) 보정은 끝냈으니, 여기서 상한만 처리하는 구조
    */
    
    # 5. 최신순 목록 조회(limit와 offset은 이미 정수 캐스팅 했으므로 직접 삽입)
    $offset = ($page_now - 1) * $post_per_page; // offset 설정

    $sql_inquiry = "SELECT num, name, title, view, created_at
                    from post
                    order by num desc
                    limit $post_per_page offset $offset";
                // LIMIT <최대 반환 행 수> OFFSET <건너 뛸 행 수>
                // (현재 페이지에서 보여 줄)

    $result_inquiry = $db->query($sql_inquiry);

    # 6. 화면 출력

    // XSS 방지 이스케이프 처리 헬퍼(반복 조치용) - $e: 콜백 함수 변수, fn: 익명 함수, $s: 매개 변수
    $e = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8');

    if ($total_post === 0 || $result_inquiry === false) { // 게시된 글이 없을 때, 쿼리 실패 처리
        echo '<p>게시된 글이 없습니다.</p>';
    } else {

        // 표로 간단 출력
        // <tr>: table row. 표의 가로 한 줄
        // <th>: table header cell. 헤더(제목) 칸
        // <td>: table data cell. 일반 데이터 칸
        echo '<table border="1" cellpadding="4" cellspacing="0">';
        echo '<tr>
                <th>제목</th>
                <th>작성자</th>
                <th>조회수</th>
                <th>작성 일시</th>
              </tr>';

        // DB 결과 집합(객체)에서 행을 한 줄씩 연관배열로 꺼냄
        while ($row = $result_inquiry->fetch_assoc()) {
            // null 병합 연산은 생략. 빈 값 검사를 이미 하고 글 저장되기 때문.
            $num = (int)$row['num']; // 정수 캐스팅
            $name = $e($row['name']); // 문자열 캐스팅
            $title = $e($row['title']);
            $view = (int)$row['view'];
            $created_at = $e($row['created_at']);
            echo "<tr>
                    <td><a href='view.php?num=$num'>$title</a></td>
                    <td>$name</td>
                    <td>$view</td>
                    <td>$created_at</td>
                  </tr>";
        }
        echo '</table>';

        /*  < 링크에 GET 붙이기 >
            페이지 숫자 링크는 브라우저가 서버에 새 요청(GET) 을 보내게 하려는 것.
            예로 <a href="home.php?page=3">3</a> 를 누르면 URL이 ...?page=3 로 요청되고,
            서버는 그 값을 보고 3페이지 구간만 SELECT 해서 다시 파일을 처음부터 실행해 HTML을 만들어 돌려줌.
            표준 형식: URL 쿼리스트링(?키=값&키=값...), 키 이름과 값은 자유(but 서버와 일치시키기)
        */

        # 7. 페이지네이션
        $self = basename($_SERVER['PHP_SELF']); // 현재 스크립트 파일명 (예: home.php)
        // $_SERVER['PHP_SELF']: 현재 실행 중인 스크립트의 경로(문서 루트 기준).
        // basename(): 경로에서 파일명만 추출.
        $first = 1;
        $last = $total_page;
        $before = max($first, $page_now - 1);
        $next = min($last, $page_now + 1); // 둘 중 가장 작은 값을 돌려줌 → 값에 상한을 줌 → 끝 페이지 보장

        // 현재 페이지 기준 5 개 페이지 계산
        // start: page-2를 기본으로 하되, 최소 1, 최대 last-4로 제한
        // end: start+4를 기본으로 하되, last를 넘지 않게 제한
        $start = max($first, min($page_now - 2, $last - 4)); // 하한+상한을 동시에 적용 → 범위 고정
        $end = min($last, $start + 4); // 총 페이지가 5보다 적어도 있는 만큼만 보임

        // <nav>: HTML5의 시맨틱 태그로, 내비게이션(이동 링크 묶음)을 감싸는 용도
        echo "<nav style='margin-top:10px'>";
        echo "<a href='$self?page_now=$first'>&laquo; </a>"; // <<
        echo "<a href='$self?page_now=$before'>&lsaquo; </a>"; // <

        for ($i = $start; $i <= $end; $i++) {
            if ($i == $page_now) {
                echo "<strong>$i </strong>";
            } else {
                echo "<a href='$self?page_now=$i'>$i </a>";
            }
        }

        echo "<a href='$self?page_now=$next'>&rsaquo; </a>"; // >
        echo "<a href='$self?page_now=$last'>&raquo;</a>"; // >>
        echo '</nav>';
    }

    # 8. 자원 정리
    if ($result_inquiry instanceof mysqli_result) {
        $result_inquiry->free();
    }
    $db->close();
    ?>
</body>
</html>