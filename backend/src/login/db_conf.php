<?php
/**
 * @file db_conf.php
 * @brief MySQL 데이터베이스 접속 정보 설정 파일
 *
 * db_info 클래스 내부에 접속 관련 상수(DB 주소, 사용자, 비밀번호, DB명)를 정의
 * 다른 PHP 파일에서 require_once()를 통해 불러와 사용
 */

// DB 접속 정보 정의 클래스
// - DB 연결 시 필요한 접속 정보를 상수로 제공
// - 다른 PHP 파일에서 require_once()로 불러와 사용

class db_info {
    // 데이터베이스 호스트 주소
    // - docker-compose 환경에서는 서비스 이름 (예: db) 사용
    const DB_URL = 'db';

    // 데이터베이스 사용자 계정
    const USER_ID = 'root';

    // 사용자 비밀번호
    const PASSWD = 'root';

    // 접속할 데이터베이스 이름
    const DB = 'gsc';
}


