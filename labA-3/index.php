<?php
// ==============================================================================
// Лабораторная работа № А-3: Виртуальная клавиатура. Использование GET-параметров
// Студент: Беляков Игорь Романович, группа 241-3210
// ==============================================================================

// 1. Инициализация и обработка хранилища строки (store)
// Если параметр 'store' не передан (первая загрузка) - создаем пустую строку
if (!isset($_GET['store'])) {
    $store = '';
} else {
    $store = (string)$_GET['store'];
}

// 2. Инициализация и подсчет общего числа нажатий любых кнопок (count)
if (!isset($_GET['count'])) {
    $count = 0;
} else {
    $count = (int)$_GET['count'];
}

// 3. Обработка нажатий кнопок:
// Нажата кнопка цифры: передан параметр 'key'
if (isset($_GET['key'])) {
    $store .= $_GET['key'];
    $count++;
}
// Нажата кнопка СБРОС: передан параметр 'reset'
elseif (isset($_GET['reset'])) {
    $store = '';
    $count++;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Беляков Игорь Романович, группа 241-3210. Лабораторная работа № А-3</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Шапка страницы (header) -->
    <header>
        <div class="header-left">
            <img src="img/logo.png" alt="Логотип Московского Политеха" class="header-logo">
            <div class="header-title">Лабораторная работа № А-3: Виртуальная клавиатура</div>
        </div>
        <div class="header-info">
            <div><strong>Студент:</strong> Беляков Игорь Романович</div>
            <div><strong>Группа:</strong> 241-3210</div>
        </div>
    </header>

    <!-- Основной блок страницы (main) -->
    <main>
        <h1>Виртуальная клавиатура</h1>
        <p class="desc">Ввод и передача данных через GET-параметры без промежуточного серверного хранилища</p>

        <div class="calculator-container">
            <!-- Окно просмотра результата: блок <div>, текст по центру -->
            <div class="result-window"><?php echo htmlspecialchars($store, ENT_QUOTES, 'UTF-8'); ?></div>

            <!-- Сетка кнопок с цифрами от 0 до 9 в 2 ряда: 1-5 и 6-0 -->
            <div class="keyboard-grid">
                <?php
                // Расположение кнопок строго как на рисунке методички:
                // Ряд 1: 1, 2, 3, 4, 5
                // Ряд 2: 6, 7, 8, 9, 0
                $keys = [1, 2, 3, 4, 5, 6, 7, 8, 9, 0];
                foreach ($keys as $k) {
                    $url = '?key=' . $k . '&store=' . urlencode($store) . '&count=' . $count;
                    echo '<a href="' . $url . '" class="key-btn">' . $k . '</a>';
                }
                ?>
            </div>

            <!-- Кнопка СБРОС (очищает строку, передает накопленный счетчик) -->
            <a href="?reset=1&count=<?php echo $count; ?>" class="reset-btn">СБРОС</a>
        </div>
    </main>

    <!-- Подвал страницы (footer) -->
    <footer>
        <div>Московский политехнический университет &copy; 2026</div>
        <!-- В подвале отображается общее число нажатий любых кнопок с момента первой загрузки -->
        <div class="footer-counter">Всего нажатий кнопок: <strong><?php echo $count; ?></strong></div>
    </footer>

</body>
</html>
