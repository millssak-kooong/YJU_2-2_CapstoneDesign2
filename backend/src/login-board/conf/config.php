<?php
/**
 * @file config.php
 * @brief 시스템 공통 설정 파일
 *
 * - DB 접속 정보 상수 정의
 * - 파일 시스템 경로 상수 정의
 * - mysqli 예외 처리 모드 활성화
 * - DB 연결은 호출 측에서 try-catch로 처리
 */

// 파일 시스템 경로 상수 (require/include 시 사용)
const ROOT_PATH  = '/login-board';              // root 디렉토리
const CONF_PATH  = ROOT_PATH . '/conf';         // 설정 디렉토리
const LOGIN_PATH = ROOT_PATH . '/login';        // 로그인 모듈
const BOARD_PATH = ROOT_PATH . '/board';        // 게시판 모듈
const LOG_FILE_PATH = '/var/tmp/db_error.log';  // 에러 로그 경로

// mysqli 예외 모드 설정 (query 오류 발생 시 Exception throw)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// DB 접속 정보 상수
const DB_HOST = 'db'; // Docker container alias
const DB_USER = 'root';
const DB_PASS = 'root';
const DB_NAME = 'gsc';
?>
