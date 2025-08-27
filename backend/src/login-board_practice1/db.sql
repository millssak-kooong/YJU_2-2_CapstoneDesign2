-- 데이터베이스 생성 (존재하지 않으면)
CREATE DATABASE IF NOT EXISTS gsc CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- gsc 데이터베이스 사용
USE gsc;

-- user 테이블 생성
CREATE TABLE user (
    num INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    id VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL  -- 해시된 비밀번호 저장
);

-- post 테이블 생성
CREATE TABLE post (
    num INT AUTO_INCREMENT PRIMARY KEY,
    user_num INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    content text NOT NULL,
    view INT NOT NULL default 0,
    created_at datetime default current_timestamp,
    updated_at datetime default NULL on update current_timestamp,
    
    constraint fk_post_user -- 부모 칼럼은 반드시 유일해야 한다. (PK 또는 UNIQUE)
        foreign KEY (user_num) -- 외래키(자식 칼럼)
        references user(num) -- 참조 칼럼(부모 칼럼)
        on delete cascade -- 부모 칼럼 따라 연쇄 삭제
        on update cascade -- 부모 칼럼 따라 연쇄 업데이트
);