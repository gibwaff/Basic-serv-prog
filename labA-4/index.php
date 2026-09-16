<?php
// ==============================================================================
// Лабораторная работа № А-4: Пользовательские функции. Вывод таблиц.
// Студент: Беляков Игорь Романович, группа 241-3210
// ==============================================================================

// 1. Инициализация переменной числа колонок таблиц (по умолчанию 3)
// Можно переключать через GET-параметр ?cols=... для тестирования (включая 0)
$columns_count = isset($_GET['cols']) ? (int)$_GET['cols'] : 3;

// 2. Массив со структурами таблиц (не менее 10 разных элементов)
// Формат: "C1*C2*C3#C4*C5*C6#...", где '*' - разделитель колонок, '#' - разделитель строк
$tables_structures = [
    // 1. Стандартная полная таблица 3x3
    'Язык*Тип*Релиз#PHP*Серверный*1995#JavaScript*Клиентский*1995#Python*Универсальный*1991',

    // 2. Таблица с разным числом ячеек в строках (проверка дополнения пустыми ячейками / обрезки)
    'Процессор*Ядра*Частота#Intel Core i7*8*3.8 GHz#AMD Ryzen 7*8*4.2 GHz*PCIe 4.0#Apple M2*8',

    // 3. Таблица из 2 строк
    'HTTP Метод*Назначение*Идемпотентность#GET*Получение ресурса*Да#POST*Создание ресурса*Нет',

    // 4. Таблица с одной строкой
    'Сервер*Порт*Протокол',

    // 5. Таблица базы данных
    'СУБД*Модель*Порт#PostgreSQL*Реляционная*5432#MongoDB*Документная*27017#Redis*Ключ-значение*6379#MySQL*Реляционная*3306',

    // 6. Таблица с пустыми ячейками внутри строки (**)
    'Модуль*Статус*Версия#FastCGI*Active*1.2#OpCache**8.3#OpenSSL*Enabled*',

    // 7. Таблица с пустой строкой в середине структуры (проверка: "Если в строке нет ячеек – ее HTML-код не выводится")
    'Параметр*Значение*Комментарий#Host*localhost*Основной хост##Timeout*60*Таймаут соединения',

    // 8. Структура только из разделителей колонок без текста в одной из строк
    'ID*Пользователь*Роль#1*admin*Администратор#2*guest*Гость#3*editor*Редактор',

    // 9. Ошибочная структура: только разделители строк без ячеек (проверка: "В таблице нет строк с ячейками")
    '###',

    // 10. Пустая структура (проверка: "В таблице нет строк")
    ''
];

// ==============================================================================
// Пользовательские функции согласно заданию
// ==============================================================================

/**
 * Функция формирования содержимого отдельной строки таблицы <tr>...</tr>
 * @param string $row_data Строка с ячейками, разделенными символом '*'
 * @param int $required_cols Требуемое количество колонок
 * @return string HTML-код строки <tr>...</tr> или пустая строка, если в строке нет ячеек
 */
function getTR($row_data, $required_cols) {
    // Если строка абсолютно пустая - в ней нет ячеек
    if ($row_data === '') {
        return '';
    }

    // Разбиваем строку ячеек по разделителю '*'
    $cells = explode('*', $row_data);

    // Дополняем или усекаем до требуемого количества колонок:
    // "Таблицы всегда должны выводиться с требуемым числом колонок (возможно с пустыми ячейками)"
    $cell_count = count($cells);
    if ($cell_count < $required_cols) {
        for ($k = $cell_count; $k < $required_cols; $k++) {
            $cells[] = ''; // добавляем пустые ячейки
        }
    } elseif ($cell_count > $required_cols) {
        $cells = array_slice($cells, 0, $required_cols); // усекаем лишние ячейки
    }

    $ret = '<tr>';
    for ($j = 0; $j < count($cells); $j++) {
        $ret .= '<td>' . htmlspecialchars($cells[$j], ENT_QUOTES, 'UTF-8') . '</td>';
    }
    $ret .= '</tr>';

    return $ret;
}

/**
 * Функция вывода HTML-кода таблицы исходя из её структуры
 * @param string $structure Строка структуры таблицы
 * @param int $required_cols Требуемое количество колонок
 * @param int $table_number Номер таблицы (для заголовка <h2>Таблица №x</h2>)
 */
function outTable($structure, $required_cols, $table_number) {
    // 1. Выводим заголовок второго уровня перед таблицей:
    // "Перед выводом каждой новой таблицы необходимо добавить заголовок второго уровня (тег <h2>) формата 'Таблица №x'"
    echo '<h2>Таблица №' . $table_number . '</h2>';

    // 2. Проверка условия: нулевое число колонок
    if ($required_cols <= 0) {
        echo '<div class="table-warning">Неправильное число колонок</div>';
        return;
    }

    // 3. Проверка условия: нет строк в структуре
    if ($structure === '') {
        echo '<div class="table-warning">В таблице нет строк</div>';
        return;
    }

    // Разбиваем структуру на строки по разделителю '#'
    $raw_rows = explode('#', $structure);

    $datas = '';
    $has_valid_cells = false;

    // Цикл по всем строкам структуры
    for ($i = 0; $i < count($raw_rows); $i++) {
        $row_str = $raw_rows[$i];

        // "Если в строке нет ячеек – ее HTML-код не выводится"
        if ($row_str === '') {
            continue;
        }

        // Проверяем, содержит ли строка хоть какие-то данные ячеек (не просто пустые разделители)
        $cells_check = explode('*', $row_str);
        $row_has_content = false;
        foreach ($cells_check as $c) {
            if ($c !== '') {
                $row_has_content = true;
                break;
            }
        }
        if ($row_has_content) {
            $has_valid_cells = true;
        }

        $row_html = getTR($row_str, $required_cols);
        if ($row_html !== '') {
            $datas .= $row_html;
        }
    }

    // 4. Проверка условий вывода таблицы
    if (!$has_valid_cells) {
        echo '<div class="table-warning">В таблице нет строк с ячейками</div>';
        return;
    }

    if ($datas !== '') {
        echo '<table class="data-table"><tbody>' . $datas . '</tbody></table>';
    } else {
        echo '<div class="table-warning">В таблице нет строк</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Беляков Игорь Романович, группа 241-3210. Лабораторная работа № А-4</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Шапка страницы (header) -->
    <header>
        <div class="header-left">
            <img src="img/logo.png" alt="Логотип Московского Политеха" class="header-logo">
            <div class="header-title">Лабораторная работа № А-4: Пользовательские функции</div>
        </div>
        <div class="header-info">
            <div><strong>Студент:</strong> Беляков Игорь Романович</div>
            <div><strong>Группа:</strong> 241-3210</div>
        </div>
    </header>

    <!-- Основной блок страницы (main) -->
    <main>
        <h1>Генерация таблиц из строковых структур</h1>
        <p class="desc">Демонстрация работы пользовательских функций getTR() и outTable() при парсинге строк вида "C1*C2#C3*C4"</p>

        <!-- Блок выбора числа колонок для тестирования -->
        <div class="params-card">
            <div><strong>Заданное число колонок в таблицах:</strong> <span><?php echo $columns_count; ?></span></div>
            <div><strong>Быстрый выбор:</strong> 
                <a href="?cols=3" class="<?php echo $columns_count === 3 ? 'active-link' : ''; ?>">3 колонки</a> | 
                <a href="?cols=4" class="<?php echo $columns_count === 4 ? 'active-link' : ''; ?>">4 колонки</a> | 
                <a href="?cols=2" class="<?php echo $columns_count === 2 ? 'active-link' : ''; ?>">2 колонки</a> | 
                <a href="?cols=0" class="<?php echo $columns_count === 0 ? 'active-link' : ''; ?>">0 колонок (Тест ошибки)</a>
            </div>
        </div>

        <!-- Вывод всех 10 таблиц через вызов функции outTable() в цикле -->
        <?php
        for ($t = 0; $t < count($tables_structures); $t++) {
            outTable($tables_structures[$t], $columns_count, $t + 1);
        }
        ?>
    </main>

    <!-- Подвал страницы (footer) -->
    <footer>
        <div>Московский политехнический университет &copy; 2026</div>
        <div class="footer-info-text">Всего обработано структур: <?php echo count($tables_structures); ?></div>
    </footer>

</body>
</html>
