<?php
// ==============================================================================
// Лабораторная работа № В-1: Основы баз данных и использования программных модулей.
// Записная книжка.
// Главный файл: index.php
// "index.php – единственный загружаемый в браузер документ, осуществляющий всю работу сайта"
// Студент: Беляков Игорь Романович, группа 241-3210
// ==============================================================================

// Подключаем модуль навигационного меню (Листинг В-1.2, В-1.5)
require_once __DIR__ . '/menu.php';

// Проверка и нормализация параметра страницы
$allowed_pages = ['viewer', 'add', 'edit', 'delete'];
if (!isset($_GET['p']) || !in_array($_GET['p'], $allowed_pages)) {
    $_GET['p'] = 'viewer';
}
$page = $_GET['p'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Беляков Игорь Романович, группа 241-3210. Лабораторная работа № В-1: Записная книжка</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Шапка страницы (header) -->
    <header>
        <div class="header-left">
            <img src="img/logo.png" alt="Логотип Московского Политеха" class="header-logo">
            <div class="header-title">Лабораторная работа № В-1: Записная книжка</div>
        </div>
        <div class="header-info">
            <div><strong>Студент:</strong> Беляков Игорь Романович</div>
            <div><strong>Группа:</strong> 241-3210</div>
        </div>
    </header>

    <!-- Секция меню (подключается через функцию renderMenu() из menu.php) -->
    <div class="nav-section">
        <?php echo renderMenu(); ?>
    </div>

    <!-- Основной рабочий контейнер сайта (main) -->
    <main>
        <?php
        // Модульный вывод контента страницы (Листинг В-1.5)
        if ($page === 'viewer') {
            require_once __DIR__ . '/viewer.php';

            // Нормализация номера страницы пагинации
            $pg = (isset($_GET['pg']) && is_numeric($_GET['pg']) && (int)$_GET['pg'] >= 0) ? (int)$_GET['pg'] : 0;

            // Нормализация типа сортировки
            $sort = (isset($_GET['sort']) && in_array($_GET['sort'], ['byid', 'fam', 'birth'])) ? $_GET['sort'] : 'byid';

            // Вызов библиотечной функции getFriendsList()
            echo getFriendsList($sort, $pg);
        } elseif ($page === 'add') {
            include __DIR__ . '/add.php';
        } elseif ($page === 'edit') {
            include __DIR__ . '/edit.php';
        } elseif ($page === 'delete') {
            include __DIR__ . '/delete.php';
        } else {
            echo '<div class="msg-error">Страница не найдена</div>';
        }
        ?>
    </main>

    <!-- Подвал страницы (footer) -->
    <footer>
        <div>Московский политехнический университет &copy; 2026</div>
        <div>Модульная архитектура веб-приложений (PHP & PDO/SQL)</div>
    </footer>

</body>
</html>
