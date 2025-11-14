-- Создание базы данных
CREATE DATABASE IF NOT EXISTS api_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE api_db;

-- Таблица для хранения API-ключей
CREATE TABLE IF NOT EXISTS api_keys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    api_key VARCHAR(255) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_api_key (api_key),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица для учебных курсов
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

-- Вставка тестовых API-ключей
-- ВАЖНО: В реальном проекте используйте password_hash() для генерации хешей
-- Пример: INSERT INTO api_keys (api_key, user_id, is_active) VALUES (SHA2('my_secret_key', 256), 1, 1);

-- Тестовый API-ключ 1 (активный)
INSERT INTO api_keys (api_key, user_id, is_active) 
VALUES ('test_api_key_12345', 1, 1);

-- Тестовый API-ключ 2 (активный)
INSERT INTO api_keys (api_key, user_id, is_active) 
VALUES ('demo_api_key_67890', 2, 1);

-- Тестовый API-ключ 3 (неактивный)
INSERT INTO api_keys (api_key, user_id, is_active) 
VALUES ('inactive_key_11111', 3, 0);

-- Вставка тестовых курсов
INSERT INTO courses (title, instructor, duration_hours, price) VALUES
('Введение в программирование', 'Иванов И.И.', 40, 15000.00),
('Веб-разработка на PHP', 'Петров П.П.', 60, 25000.00),
('Базы данных MySQL', 'Сидоров С.С.', 30, 18000.00);

