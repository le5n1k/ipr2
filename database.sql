
CREATE DATABASE IF NOT EXISTS api_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE api_db;

CREATE TABLE IF NOT EXISTS api_keys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    api_key VARCHAR(255) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_api_key (api_key),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    instructor VARCHAR(255) NOT NULL,
    duration_hours INT NOT NULL DEFAULT 0,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_instructor (instructor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO api_keys (api_key, user_id, is_active) 
VALUES (SHA2('test_api_key_12345', 256), 1, 1);

INSERT INTO api_keys (api_key, user_id, is_active) 
VALUES (SHA2('demo_api_key_67890', 256), 2, 1);

INSERT INTO api_keys (api_key, user_id, is_active) 
VALUES (SHA2('inactive_key_11111', 256), 3, 0);

INSERT INTO courses (title, instructor, duration_hours, price) VALUES
('Введение в программирование', 'Иванов И.И.', 40, 15000.00),
('Веб-разработка на PHP', 'Петров П.П.', 60, 25000.00),
('Базы данных MySQL', 'Сидоров С.С.', 30, 18000.00);
