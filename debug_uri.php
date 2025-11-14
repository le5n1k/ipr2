<?php
/**
 * Отладочный скрипт для проверки маршрутизации
 * Откройте: http://localhost/ipr2/debug_uri.php
 */

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Отладка маршрутизации</h1>";

echo "<h2>Информация о запросе:</h2>";
echo "<pre>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'не установлен') . "\n";
echo "REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'не установлен') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'не установлен') . "\n";
echo "PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'не установлен') . "\n";
echo "</pre>";

// Симуляция обработки URI как в index.php
$request_uri = $_SERVER['REQUEST_URI'] ?? '';
$uri = parse_url($request_uri, PHP_URL_PATH);
$uri = str_replace('/index.php', '', $uri);

echo "<h2>Обработанный URI:</h2>";
echo "<pre>";
echo "URI после parse_url: $uri\n";
echo "</pre>";

$uri_parts = array_filter(explode('/', trim($uri, '/')), function($part) {
    return !empty($part);
});
$uri_parts = array_values($uri_parts);

echo "<h2>Разобранные части URI:</h2>";
echo "<pre>";
print_r($uri_parts);
echo "</pre>";

$api_index = array_search('api', $uri_parts);

echo "<h2>Анализ маршрутизации:</h2>";
echo "<pre>";
if ($api_index !== false) {
    echo "✓ 'api' найден на индексе: $api_index\n";
    if (isset($uri_parts[$api_index + 1])) {
        echo "✓ Следующий элемент: " . $uri_parts[$api_index + 1] . "\n";
        if ($uri_parts[$api_index + 1] === 'courses') {
            echo "✓ ✓ ✓ Маршрут /api/courses найден!\n";
            if (isset($uri_parts[$api_index + 2])) {
                echo "✓ ID курса: " . $uri_parts[$api_index + 2] . "\n";
            } else {
                echo "  (ID курса не указан - запрос всех курсов)\n";
            }
        } else {
            echo "✗ Следующий элемент не 'courses'\n";
        }
    } else {
        echo "✗ После 'api' нет следующего элемента\n";
    }
} else {
    echo "✗ 'api' не найден в URI\n";
}
echo "</pre>";

echo "<hr>";
echo "<h2>Тестовые URL:</h2>";
echo "<ul>";
echo "<li><a href='?test=/ipr2/api/courses'>/ipr2/api/courses</a></li>";
echo "<li><a href='?test=/ipr2/api/courses/1'>/ipr2/api/courses/1</a></li>";
echo "<li><a href='api/courses'>api/courses (относительный)</a></li>";
echo "</ul>";

if (isset($_GET['test'])) {
    echo "<h3>Тест с URL: " . htmlspecialchars($_GET['test']) . "</h3>";
    $_SERVER['REQUEST_URI'] = $_GET['test'];
    echo "<p><a href='debug_uri.php'>Вернуться к обычной проверке</a></p>";
}
?>

