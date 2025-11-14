<?php
/**
 * RESTful API для управления учебными курсами
 * Единая точка входа для всех запросов
 */

// Установка заголовков для JSON и CORS
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-API-Key');

// Обработка preflight запросов
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Подключение конфигурационных файлов
require_once 'config/Database.php';
require_once 'config/Auth.php';
require_once 'models/CourseModel.php';

// Инициализация подключения к БД
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Ошибка подключения к базе данных'
    ]);
    exit;
}

// Инициализация классов
$auth = new Auth($db);
$courseModel = new CourseModel($db);

// Получение метода запроса и URI
$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];

// Удаление query string из URI
$uri = parse_url($request_uri, PHP_URL_PATH);
$uri = str_replace('/index.php', '', $uri);

// Разбор URI для получения пути и параметров
$uri_parts = array_filter(explode('/', trim($uri, '/')), function($part) {
    return !empty($part);
});
$uri_parts = array_values($uri_parts); // Переиндексация массива

// Поиск индекса 'api' в URI (работает для корня и поддиректорий)
$api_index = array_search('api', $uri_parts);

// Проверка, что запрос идет к /api/courses (или /ipr2/api/courses)
if ($api_index !== false && isset($uri_parts[$api_index + 1]) && $uri_parts[$api_index + 1] === 'courses') {
    $course_id = isset($uri_parts[$api_index + 2]) ? (int)$uri_parts[$api_index + 2] : null;

    // Аутентификация для всех операций
    $auth->requireAuth();

    // Обработка различных HTTP-методов
    switch ($method) {
        case 'GET':
            if ($course_id) {
                // GET /api/courses/{id} - получить один курс
                $course = $courseModel->getById($course_id);
                
                if ($course) {
                    http_response_code(200);
                    echo json_encode([
                        'status' => 'success',
                        'data' => $course
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Курс не найден'
                    ], JSON_UNESCAPED_UNICODE);
                }
            } else {
                // GET /api/courses - получить все курсы
                $courses = $courseModel->getAll();
                http_response_code(200);
                echo json_encode([
                    'status' => 'success',
                    'data' => $courses,
                    'count' => count($courses)
                ], JSON_UNESCAPED_UNICODE);
            }
            break;

        case 'POST':
            // POST /api/courses - создать новый курс
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Неверный формат JSON или пустое тело запроса'
                ], JSON_UNESCAPED_UNICODE);
                break;
            }

            // Валидация обязательных полей
            if (empty($input['title']) || empty($input['instructor'])) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Обязательные поля: title, instructor'
                ], JSON_UNESCAPED_UNICODE);
                break;
            }

            $result = $courseModel->create($input);

            if ($result) {
                http_response_code(201);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Курс успешно создан',
                    'data' => $result
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Ошибка при создании курса'
                ], JSON_UNESCAPED_UNICODE);
            }
            break;

        case 'PUT':
            // PUT /api/courses/{id} - обновить курс
            if (!$course_id) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'ID курса не указан'
                ], JSON_UNESCAPED_UNICODE);
                break;
            }

            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Неверный формат JSON или пустое тело запроса'
                ], JSON_UNESCAPED_UNICODE);
                break;
            }

            $result = $courseModel->update($course_id, $input);

            if ($result) {
                http_response_code(200);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Курс успешно обновлен',
                    'data' => $result
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Курс не найден или ошибка при обновлении'
                ], JSON_UNESCAPED_UNICODE);
            }
            break;

        case 'DELETE':
            // DELETE /api/courses/{id} - удалить курс
            if (!$course_id) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'ID курса не указан'
                ], JSON_UNESCAPED_UNICODE);
                break;
            }

            $result = $courseModel->delete($course_id);

            if ($result) {
                http_response_code(200);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Курс успешно удален'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Курс не найден'
                ], JSON_UNESCAPED_UNICODE);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode([
                'status' => 'error',
                'message' => 'Метод не разрешен'
            ], JSON_UNESCAPED_UNICODE);
            break;
    }
} else {
    // Неверный endpoint
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'Endpoint не найден. Используйте /api/courses'
    ], JSON_UNESCAPED_UNICODE);
}
?>

