<?php
// ==============================================================================
// Лабораторная работа № А-7: Отображение процесса сортировки массива
// Файл: sort.php
// Студент: Беляков Игорь Романович, группа 241-3210
// ==============================================================================

// Массив названий алгоритмов
$algo_titles = [
    'choice'  => 'Сортировка выбором',
    'bubble'  => 'Пузырьковый алгоритм',
    'shaker'  => 'Шейкерная сортировка',
    'insert'  => 'Сортировка вставками',
    'gnome'   => 'Алгоритм садового гнома',
    'shell'   => 'Алгоритм Шелла',
    'quick'   => 'Быстрая сортировка (QuickSort)',
    'builtin' => 'Встроенная функция PHP (sort)'
];

// 1. Проверка наличия данных формы
if (!isset($_POST['element0']) || !isset($_POST['arrLength'])) {
    $error_msg = 'Массив не задан, сортировка невозможна';
} else {
    $arr_length = (int)$_POST['arrLength'];
    $raw_elements = [];
    $is_valid = true;
    $invalid_val = '';

    // Пользовательская функция проверки на целое или дробное число (Листинг А-7.4)
    function arg_is_not_Num($arg) {
        $trimmed = trim((string)$arg);
        if ($trimmed === '') {
            return true; // пустая строка не число
        }
        $normalized = str_replace(',', '.', $trimmed);
        return !is_numeric($normalized);
    }

    // Проверка всех элементов на валидность
    for ($i = 0; $i < $arr_length; $i++) {
        if (!isset($_POST['element' . $i])) {
            continue;
        }
        $val = $_POST['element' . $i];
        if (arg_is_not_Num($val)) {
            $is_valid = false;
            $invalid_val = $val;
            break;
        }
        $raw_elements[] = (float)str_replace(',', '.', trim($val));
    }

    if (!$is_valid) {
        $error_msg = 'Элемент массива "' . htmlspecialchars($invalid_val, ENT_QUOTES, 'UTF-8') . '" — не число!';
    } elseif (count($raw_elements) === 0) {
        $error_msg = 'Массив пуст, сортировка невозможна';
    } else {
        $error_msg = null;
        $algo_key = isset($_POST['algoritm']) ? $_POST['algoritm'] : 'choice';
        $algo_name = isset($algo_titles[$algo_key]) ? $algo_titles[$algo_key] : 'Сортировка';
        $source_array = $raw_elements;
    }
}

// ==============================================================================
// 2. Реализация алгоритмов сортировки с фиксацией промежуточных шагов
// ==============================================================================
$history = [];  // массив снимков состояния массива на каждой итерации
$iterations = 0; // сквозной счетчик итераций

function record_step(&$history, &$iterations, $current_arr) {
    $iterations++;
    $history[] = [
        'num' => $iterations,
        'arr' => $current_arr
    ];
}

// 1. Сортировка выбором (Листинг А-7.4)
function sort_choice($arr, &$history, &$iterations) {
    $n = count($arr);
    for ($i = 0; $i < $n - 1; $i++) {
        $min = $i;
        for ($j = $i + 1; $j < $n; $j++) {
            if ($arr[$j] < $arr[$min]) {
                $min = $j;
            }
        }
        if ($min != $i) {
            $temp = $arr[$i];
            $arr[$i] = $arr[$min];
            $arr[$min] = $temp;
        }
        record_step($history, $iterations, $arr);
    }
    return $arr;
}

// 2. Пузырьковая сортировка (Листинг А-7.7)
function sort_bubble($arr, &$history, &$iterations) {
    $n = count($arr);
    for ($j = 0; $j < $n - 1; $j++) {
        for ($i = 0; $i < $n - 1 - $j; $i++) {
            if ($arr[$i] > $arr[$i + 1]) {
                $temp = $arr[$i];
                $arr[$i] = $arr[$i + 1];
                $arr[$i + 1] = $temp;
            }
            record_step($history, $iterations, $arr);
        }
    }
    return $arr;
}

// 3. Шейкерная сортировка (Листинг А-7.8)
function sort_shaker($arr, &$history, &$iterations) {
    $left = 1;
    $right = count($arr) - 1;
    while ($left <= $right) {
        for ($i = $right; $i >= $left; $i--) {
            if ($arr[$i - 1] > $arr[$i]) {
                $temp = $arr[$i - 1];
                $arr[$i - 1] = $arr[$i];
                $arr[$i] = $temp;
            }
            record_step($history, $iterations, $arr);
        }
        $left++;

        for ($i = $left; $i <= $right; $i++) {
            if ($arr[$i - 1] > $arr[$i]) {
                $temp = $arr[$i - 1];
                $arr[$i - 1] = $arr[$i];
                $arr[$i] = $temp;
            }
            record_step($history, $iterations, $arr);
        }
        $right--;
    }
    return $arr;
}

// 4. Сортировка вставками (Листинг А-7.9)
function sort_insert($arr, &$history, &$iterations) {
    $n = count($arr);
    for ($i = 1; $i < $n; $i++) {
        $val = $arr[$i];
        $j = $i - 1;
        while ($j >= 0 && $arr[$j] > $val) {
            $arr[$j + 1] = $arr[$j];
            $j--;
            record_step($history, $iterations, $arr);
        }
        $arr[$j + 1] = $val;
        record_step($history, $iterations, $arr);
    }
    return $arr;
}

// 5. Алгоритм садового гнома (Листинг А-7.11)
function sort_gnome($arr, &$history, &$iterations) {
    $i = 1;
    $j = 2;
    $n = count($arr);
    while ($i < $n) {
        if ($i == 0 || $arr[$i - 1] <= $arr[$i]) {
            $i = $j;
            $j++;
        } else {
            $temp = $arr[$i];
            $arr[$i] = $arr[$i - 1];
            $arr[$i - 1] = $temp;
            $i--;
        }
        record_step($history, $iterations, $arr);
    }
    return $arr;
}

// 6. Алгоритм Шелла (Листинг А-7.12)
function sort_shell($arr, &$history, &$iterations) {
    $n = count($arr);
    for ($k = (int)ceil($n / 2); $k >= 1; $k = (int)floor($k / 2)) {
        for ($i = $k; $i < $n; $i++) {
            $val = $arr[$i];
            $j = $i - $k;
            while ($j >= 0 && $arr[$j] > $val) {
                $arr[$j + $k] = $arr[$j];
                $j -= $k;
                record_step($history, $iterations, $arr);
            }
            $arr[$j + $k] = $val;
            record_step($history, $iterations, $arr);
        }
        if ($k == 1) break;
    }
    return $arr;
}

// 7. Быстрая сортировка QuickSort (Листинг А-7.13)
function quick_sort_rec(&$arr, $left, $right, &$history, &$iterations) {
    $l = $left;
    $r = $right;
    $point = $arr[floor(($left + $right) / 2)];
    do {
        while ($arr[$l] < $point) $l++;
        while ($arr[$r] > $point) $r--;
        if ($l <= $r) {
            $temp = $arr[$l];
            $arr[$l] = $arr[$r];
            $arr[$r] = $temp;
            $l++;
            $r--;
            record_step($history, $iterations, $arr);
        }
    } while ($l <= $r);

    if ($r > $left) {
        quick_sort_rec($arr, $left, $r, $history, $iterations);
    }
    if ($l < $right) {
        quick_sort_rec($arr, $l, $right, $history, $iterations);
    }
}

function sort_quick($arr, &$history, &$iterations) {
    quick_sort_rec($arr, 0, count($arr) - 1, $history, $iterations);
    return $arr;
}

// 8. Встроенная функция PHP sort()
function sort_builtin($arr, &$history, &$iterations) {
    sort($arr);
    record_step($history, $iterations, $arr);
    return $arr;
}

// ==============================================================================
// 3. Запуск выбранного алгоритма с замером времени microtime(true)
// ==============================================================================
if ($error_msg === null) {
    $arr_to_sort = $source_array;
    $time_start = microtime(true);

    switch ($algo_key) {
        case 'choice':
            $sorted_arr = sort_choice($arr_to_sort, $history, $iterations);
            break;
        case 'bubble':
            $sorted_arr = sort_bubble($arr_to_sort, $history, $iterations);
            break;
        case 'shaker':
            $sorted_arr = sort_shaker($arr_to_sort, $history, $iterations);
            break;
        case 'insert':
            $sorted_arr = sort_insert($arr_to_sort, $history, $iterations);
            break;
        case 'gnome':
            $sorted_arr = sort_gnome($arr_to_sort, $history, $iterations);
            break;
        case 'shell':
            $sorted_arr = sort_shell($arr_to_sort, $history, $iterations);
            break;
        case 'quick':
            $sorted_arr = sort_quick($arr_to_sort, $history, $iterations);
            break;
        case 'builtin':
            $sorted_arr = sort_builtin($arr_to_sort, $history, $iterations);
            break;
        default:
            $sorted_arr = sort_choice($arr_to_sort, $history, $iterations);
            break;
    }

    $time_taken = round(microtime(true) - $time_start, 6);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результат сортировки массива</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Шапка страницы (header) -->
    <header>
        <div class="header-left">
            <img src="img/logo.png" alt="Логотип Московского Политеха" class="header-logo">
            <div class="header-title">Лабораторная работа № А-7: Протокол сортировки</div>
        </div>
        <div class="header-info">
            <div><strong>Студент:</strong> Беляков Игорь Романович</div>
            <div><strong>Группа:</strong> 241-3210</div>
        </div>
    </header>

    <!-- Основной блок страницы (main) -->
    <main>
        <?php if ($error_msg !== null): ?>
            <!-- Вывод предупреждения об ошибке (сортировка не выполняется) -->
            <h1>Ошибка входных данных</h1>
            <div class="error-card"><?php echo htmlspecialchars($error_msg, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php else: ?>
            <!-- Успешная валидация и протокол сортировки -->
            <h1>Протокол сортировки: <?php echo $algo_name; ?></h1>

            <h2>Исходный массив</h2>
            <div class="array-row">
                <?php foreach ($source_array as $idx => $item): ?>
                    <div class="arr-item"><span>[<?php echo $idx; ?>]</span><?php echo $item; ?></div>
                <?php endforeach; ?>
            </div>

            <p style="color: #059669; font-weight: bold; margin-bottom: 20px;">
                &check; Массив проверен: все элементы являются числами, сортировка возможна.
            </p>

            <h2>Пошаговый процесс сортировки (Итерации):</h2>
            <?php foreach ($history as $step): ?>
                <div class="step-card">
                    <div class="step-num">Итерация № <?php echo $step['num']; ?>:</div>
                    <div class="step-values">
                        <?php foreach ($step['arr'] as $elem): ?>
                            <span class="step-val-badge"><?php echo $elem; ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Сообщение о завершении работы алгоритма по условиям методички -->
            <div class="summary-card">
                Сортировка завершена, проведено <?php echo $iterations; ?> итераций. Сортировка заняла <?php echo $time_taken; ?> секунд.
            </div>
        <?php endif; ?>
    </main>

    <!-- Подвал страницы (footer) -->
    <footer>
        <div>Московский политехнический университет &copy; 2026</div>
        <div>Результаты сортировки массива</div>
    </footer>

</body>
</html>
