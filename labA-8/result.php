<?php
// ==============================================================================
// Лабораторная работа № А-8: Основы работы со строковыми данными в PHP. Кодировка.
// Анализатор текста: result.php
// Студент: Беляков Игорь Романович, группа 241-3210
// ==============================================================================

// Проверка наличия переданного текста
$has_data = isset($_POST['data']) && trim($_POST['data']) !== '';
$raw_text = $has_data ? (string)$_POST['data'] : '';

/**
 * Функция комплексного анализа текста
 */
function analyze_text($text) {
    // Нормализуем переносы строк
    $text = str_replace(["\r\n", "\r"], "\n", $text);

    // 1. Количество символов (включая пробелы)
    $total_chars = mb_strlen($text, 'UTF-8');

    // Наборы символов для классификации
    $punct_symbols = ['.', ',', '!', '?', ';', ':', '-', '—', '–', '(', ')', '[', ']', '{', '}', '"', '«', '»', '\'', '`', '/', '\\', '…'];
    $punct_map = array_fill_keys($punct_symbols, true);

    $digits_count = 0;
    $letters_count = 0;
    $lower_letters_count = 0;
    $upper_letters_count = 0;
    $punctuation_count = 0;
    $symbols_frequency = []; // вхождения каждого символа (в нижнем регистре)

    for ($i = 0; $i < $total_chars; $i++) {
        $char = mb_substr($text, $i, 1, 'UTF-8');
        $char_lower = mb_strtolower($char, 'UTF-8');

        // Подсчет частоты символов (без различия верхнего и нижнего регистров)
        if (isset($symbols_frequency[$char_lower])) {
            $symbols_frequency[$char_lower]++;
        } else {
            $symbols_frequency[$char_lower] = 1;
        }

        // Цифры
        if (preg_match('/[0-9]/u', $char)) {
            $digits_count++;
        }
        // Буквы (русские и латинские)
        elseif (preg_match('/[a-zA-Zа-яёА-ЯЁ]/u', $char)) {
            $letters_count++;
            if (preg_match('/[a-zа-яё]/u', $char)) {
                $lower_letters_count++;
            } elseif (preg_match('/[A-ZА-ЯЁ]/u', $char)) {
                $upper_letters_count++;
            }
        }
        // Знаки препинания
        elseif (isset($punct_map[$char]) || preg_match('/[\p{P}]/u', $char)) {
            $punctuation_count++;
        }
    }

    // Сортировка символов по алфавиту/ключам
    ksort($symbols_frequency, SORT_STRING);

    // 8. Подсчет слов и их вхождений (алгоритм с разделителями пробелов и пунктуации)
    // Извлекаем слова из буквенно-цифровых последовательностей с дефисом
    preg_match_all('/[a-zA-Zа-яёА-ЯЁ0-9]+(?:-[a-zA-Zа-яёА-ЯЁ0-9]+)*/u', $text, $matches);
    $words_list = $matches[0];
    $total_words = count($words_list);

    $words_frequency = [];
    foreach ($words_list as $w) {
        $w_lower = mb_strtolower($w, 'UTF-8');
        if (isset($words_frequency[$w_lower])) {
            $words_frequency[$w_lower]++;
        } else {
            $words_frequency[$w_lower] = 1;
        }
    }

    // Сортировка слов по алфавиту
    uksort($words_frequency, function($a, $b) {
        return strcmp(
            iconv('UTF-8', 'CP1251//IGNORE', $a), 
            iconv('UTF-8', 'CP1251//IGNORE', $b)
        );
    });

    return [
        'total_chars'         => $total_chars,
        'letters_count'       => $letters_count,
        'lower_letters_count' => $lower_letters_count,
        'upper_letters_count' => $upper_letters_count,
        'punctuation_count'   => $punctuation_count,
        'digits_count'        => $digits_count,
        'total_words'         => $total_words,
        'symbols_frequency'   => $symbols_frequency,
        'words_frequency'     => $words_frequency
    ];
}

$analysis = $has_data ? analyze_text($raw_text) : null;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Беляков Игорь Романович, группа 241-3210. Лабораторная работа № А-8: Результат анализа</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Шапка страницы (header) -->
    <header>
        <div class="header-left">
            <img src="img/logo.png" alt="Логотип Московского Политеха" class="header-logo">
            <div class="header-title">Лабораторная работа № А-8: Анализ текста</div>
        </div>
        <div class="header-info">
            <div><strong>Студент:</strong> Беляков Игорь Романович</div>
            <div><strong>Группа:</strong> 241-3210</div>
        </div>
    </header>

    <!-- Основной блок страницы (main) -->
    <main>
        <h1>Результаты статистического анализа текста</h1>

        <?php if (!$has_data): ?>
            <!-- В случае если текста нет – выводится надпись: "Нет текста для анализа" -->
            <div class="src_error">Нет текста для анализа</div>
        <?php else: ?>
            <!-- 1. Исходный текст: выделяется цветом и курсивом -->
            <h2>Исходный текст</h2>
            <div class="src_text"><?php echo htmlspecialchars($raw_text, ENT_QUOTES, 'UTF-8'); ?></div>

            <!-- 2. Информация о тексте: оформляется в виде таблицы с границами между ячейками -->
            <h2>Общие характеристики текста</h2>
            <table class="result-table">
                <thead>
                    <tr>
                        <th>Параметр анализа</th>
                        <th>Количество</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1. Количество символов в тексте (включая пробелы)</td>
                        <td><?php echo $analysis['total_chars']; ?></td>
                    </tr>
                    <tr>
                        <td>2. Общее количество букв</td>
                        <td><?php echo $analysis['letters_count']; ?></td>
                    </tr>
                    <tr>
                        <td>3. Количество строчных букв</td>
                        <td><?php echo $analysis['lower_letters_count']; ?></td>
                    </tr>
                    <tr>
                        <td>4. Количество заглавных (прописных) букв</td>
                        <td><?php echo $analysis['upper_letters_count']; ?></td>
                    </tr>
                    <tr>
                        <td>5. Количество знаков препинания</td>
                        <td><?php echo $analysis['punctuation_count']; ?></td>
                    </tr>
                    <tr>
                        <td>6. Количество цифр</td>
                        <td><?php echo $analysis['digits_count']; ?></td>
                    </tr>
                    <tr>
                        <td>7. Общее количество слов</td>
                        <td><?php echo $analysis['total_words']; ?></td>
                    </tr>
                </tbody>
            </table>

            <!-- 3. Количество вхождений каждого символа текста (без различия регистров) -->
            <h2>Вхождения символов в тексте (без учета регистра)</h2>
            <table class="data-grid-table">
                <thead>
                    <tr>
                        <th>Символ</th>
                        <th>Описание / Код</th>
                        <th>Число вхождений</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($analysis['symbols_frequency'] as $symb => $cnt): ?>
                        <tr>
                            <td>
                                <span class="symbol-badge">
                                    <?php 
                                    if ($symb === ' ') echo '&blank; (пробел)';
                                    elseif ($symb === "\n") echo '\n (перенос)';
                                    elseif ($symb === "\t") echo '\t (табуляция)';
                                    else echo htmlspecialchars($symb, ENT_QUOTES, 'UTF-8');
                                    ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                if ($symb === ' ') echo 'Пробел';
                                elseif ($symb === "\n") echo 'Символ перевода строки';
                                elseif (preg_match('/[0-9]/u', $symb)) echo 'Цифра';
                                elseif (preg_match('/[a-zа-яё]/u', $symb)) echo 'Буква';
                                else echo 'Знак / Символ';
                                ?>
                            </td>
                            <td><strong><?php echo $cnt; ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- 4. Список всех слов в тексте и количество их вхождений, отсортированный по алфавиту -->
            <h2>Список слов в тексте по алфавиту</h2>
            <table class="data-grid-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">№</th>
                        <th style="text-align: left; padding-left: 20px;">Слово</th>
                        <th style="width: 160px;">Количество вхождений</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $w_idx = 1;
                    foreach ($analysis['words_frequency'] as $word => $cnt): 
                    ?>
                        <tr>
                            <td><?php echo $w_idx++; ?></td>
                            <td style="text-align: left; padding-left: 20px; font-weight: 500;">
                                <?php echo htmlspecialchars($word, ENT_QUOTES, 'UTF-8'); ?>
                            </td>
                            <td><strong><?php echo $cnt; ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- 3. Кнопка «Другой анализ»: тег <a>, при нажатии вновь загружается index.html -->
        <div style="margin-top: 25px;">
            <a href="index.html" id="back_button">Другой анализ</a>
        </div>
    </main>

    <!-- Подвал страницы (footer) -->
    <footer>
        <div>Московский политехнический университет &copy; 2026</div>
        <div>Результат анализа текста</div>
    </footer>

</body>
</html>
