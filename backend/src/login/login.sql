-- ============================================================================
-- @file login.sql
-- @brief 회원가입 및 로그인 기능 실습용 MySQL 스키마 정의
--
-- 본 SQL 스크립트는 gsc 데이터베이스를 생성하고,
-- 사용자 인증 정보를 저장하는 users 테이블을 정의함.
--
-- 구성 테이블:
--   - users: 사용자 계정 정보 저장 (아이디, 비밀번호, 이름)
--
-- 주의사항:
--   - 문자셋은 utf8mb4 사용 (다국어 및 특수문자 대응)
--   - username 컬럼은 UNIQUE 제약 조건 적용 (중복 방지)
-- ============================================================================

-- 데이터베이스 생성 (존재하지 않으면)
CREATE DATABASE IF NOT EXISTS gsc
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

-- gsc 데이터베이스 사용
USE gsc;

-- 사용자 정보 테이블 생성
CREATE TABLE users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
    -- 고유 사용자 ID (자동 증가 정수형, PK)

                       username VARCHAR(50) NOT NULL UNIQUE,
    -- 사용자 계정명 (중복 불가, 로그인 식별자)

                       password VARCHAR(255) NOT NULL,
    -- 비밀번호 해시값 저장 (보안상 해싱 필요)

                       name VARCHAR(100) NOT NULL
    -- 사용자 실명 또는 닉네임
);
