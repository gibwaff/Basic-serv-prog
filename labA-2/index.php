<?php
// ==============================================================================
// 1. Инициализация числовых и строковых переменных (условия лабораторной работы)
// ==============================================================================
$start_value = 0;      // начальное значение аргумента
$encounting = 25;      // количество вычисляемых значений (шагов)
$step = 1.5;           // шаг изменения аргумента
$min_value = -100;     // минимальное значение функции, останавливающее вычисления
$max_value = 500;      // максимальное значение функции, останавливающее вычисления

// Тип верстки: можно передать через GET-параметр ?type=A (или B, C, D, E), по умолчанию 'D' (таблица)
$allowed_types = ['A', 'B', 'C', 'D', 'E'];
$type = isset($_GET['type']) && in_array(strtoupper($_GET['type']), $allowed_types) 
        ? strtoupper($_GET['type']) 
        : 'D';

// Тип цикла: 'for' (цикл со счетчиком Листинг А-2.4), 'while' (цикл с предусловием Листинг А-2.5), 'do-while' (цикл с постусловием Листинг А-2.6)
$loop_type = isset($_GET['loop']) && in_array(strtolower($_GET['loop']), ['for', 'while', 'do-while'])
             ? strtolower($_GET['loop'])
             : 'for';

// Название типа верстки для вывода в подвале
$layout_names = [
    'A' => 'Тип верстки A (Простая верстка текстом через <br>)',
    'B' => 'Тип верстки B (Маркированный список <ul>)',
    'C' => 'Тип верстки C (Нумерованный список <ol>)',
    'D' => 'Тип верстки D (Табличная верстка <table>)',
    'E' => 'Тип верстки E (Блочная верстка <div>)'
];
$current_layout_name = $layout_names[$type];

// ==============================================================================
// Функция вычисления математической функции по Варианту №10
// f(x) =
// 1) (3 / x) + (x / 3) - 5, при x <= 10
// 2) (x - 7) * (x / 8),     при x > 10 и x < 20
// 3) 3 * x + 2,             при x >= 20
// Округление до 3 знаков после запятой; при делении на 0 -> "error"
// ==============================================================================
function calculate_f($x) {
    if ($x <= 10) {
        if ($x == 0) {
            return "error"; // деление на ноль: 3 / x
        }
        $val = (3 / $x) + ($x / 3) - 5;
    } elseif ($x > 10 && $x < 20) {
        $val = ($x - 7) * ($x / 8);
    } else { // x >= 20
        $val = 3 * $x + 2;
    }
    return round($val, 3);
}

// ==============================================================================
// Вычисление значений функции с использованием одного из трех типов циклов
// ==============================================================================
$results = []; // Массив для хранения результатов: ['x' => ..., 'f' => ..., 'is_error' => ...]
$valid_values = []; // Числовые значения функции (без error) для подсчета мин, макс, суммы, среднего

$x = $start_value;
$i = 0;

if ($loop_type === 'for') {
    // Цикл со счетчиком и оператором break (Листинг А-2.4)
    for ($i = 0; $i < $encounting; $i++, $x += $step) {
        $f = calculate_f($x);
        $results[] = ['x' => $x, 'f' => $f];

        if ($f !== 'error') {
            $valid_values[] = $f;
            // Проверка выхода за границы min/max
            if ($f >= $max_value || $f < $min_value) {
                break; // досрочная остановка цикла
            }
        }
    }
} elseif ($loop_type === 'while') {
    // Цикл с предусловием (Листинг А-2.5)
    $f = 0;
    while ($i < $encounting) {
        $f = calculate_f($x);
        $results[] = ['x' => $x, 'f' => $f];

        if ($f !== 'error') {
            $valid_values[] = $f;
            if ($f >= $max_value || $f < $min_value) {
                break;
            }
        }
        $i++;
        $x += $step;
    }
} elseif ($loop_type === 'do-while') {
    // Цикл с постусловием (Листинг А-2.6)
    do {
        $f = calculate_f($x);
        $results[] = ['x' => $x, 'f' => $f];

        if ($f !== 'error') {
            $valid_values[] = $f;
            if ($f >= $max_value || $f < $min_value) {
                break;
            }
        }
        $i++;
        $x += $step;
    } while ($i < $encounting);
}

// ==============================================================================
// Вычисление статистических показателей (пункт 6 задания)
// ==============================================================================
$count_vals = count($valid_values);
if ($count_vals > 0) {
    $min_f = min($valid_values);
    $max_f = max($valid_values);
    $sum_f = array_sum($valid_values);
    $avg_f = round($sum_f / $count_vals, 3);
} else {
    $min_f = $max_f = $sum_f = $avg_f = 0;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Беляков Игорь Романович, группа 241-3210. Лабораторная работа № А-2, Вариант 10</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Шапка страницы (header) -->
    <header>
        <div class="header-left">
            <img src="img/logo.png" alt="Логотип университета" class="header-logo">
            <div class="header-title">Лабораторная работа № А-2 (Вариант 10)</div>
        </div>
        <div class="header-info">
            <div><strong>Студент:</strong> Беляков Игорь Романович</div>
            <div><strong>Группа:</strong> 241-3210</div>
            <div><strong>Выбранный цикл:</strong> <?php echo strtoupper($loop_type); ?></div>
        </div>
    </header>

    <!-- Основной блок страницы (main) -->
    <main>
        <h1>Табулирование математической функции f(x)</h1>

        <!-- Блок переключения типов верстки и циклов -->
        <div class="params-card">
            <div><strong>Тип верстки:</strong> 
                <a href="?type=A&loop=<?php echo $loop_type; ?>">A (Текст)</a> | 
                <a href="?type=B&loop=<?php echo $loop_type; ?>">B (Маркеры)</a> | 
                <a href="?type=C&loop=<?php echo $loop_type; ?>">C (Номера)</a> | 
                <a href="?type=D&loop=<?php echo $loop_type; ?>">D (Таблица)</a> | 
                <a href="?type=E&loop=<?php echo $loop_type; ?>">E (Блоки)</a>
            </div>
            <div><strong>Тип цикла:</strong> 
                <a href="?type=<?php echo $type; ?>&loop=for">FOR</a> | 
                <a href="?type=<?php echo $type; ?>&loop=while">WHILE</a> | 
                <a href="?type=<?php echo $type; ?>&loop=do-while">DO-WHILE</a>
            </div>
            <div><strong>Параметры:</strong> X_нач = <span><?php echo $start_value; ?></span>; Шаг = <span><?php echo $step; ?></span>; N_макс = <span><?php echo $encounting; ?></span>; [Min = <span><?php echo $min_value; ?></span>, Max = <span><?php echo $max_value; ?></span>]</div>
        </div>

        <!-- Конструкция выбора (switch) для вывода результатов по типам верстки -->
        <?php
        switch ($type) {
            case 'A':
                // Тип А: Простая верстка текстом, разделитель - тег <br>
                echo '<div class="layout-a">';
                $total_items = count($results);
                for ($k = 0; $k < $total_items; $k++) {
                    echo 'f(' . $results[$k]['x'] . ')=' . $results[$k]['f'];
                    if ($k < $total_items - 1) {
                        echo '<br>';
                    }
                }
                echo '</div>';
                break;

            case 'B':
                // Тип B: Маркированный список <ul> и <li>
                echo '<ul class="layout-b">';
                foreach ($results as $item) {
                    echo '<li>f(' . $item['x'] . ')=' . $item['f'] . '</li>';
                }
                echo '</ul>';
                break;

            case 'C':
                // Тип C: Нумерованный список <ol> и <li>
                echo '<ol class="layout-c">';
                foreach ($results as $item) {
                    echo '<li>f(' . $item['x'] . ')=' . $item['f'] . '</li>';
                }
                echo '</ol>';
                break;

            case 'D':
                // Тип D: Табличная верстка (одинарные границы 1px черный, колонка 1: номер строки, колонка 2: x, колонка 3: f(x))
                echo '<table class="layout-d">';
                echo '<thead><tr><th>№ строки</th><th>Значение аргумента (x)</th><th>Значение функции f(x)</th></tr></thead>';
                echo '<tbody>';
                $row_idx = 1;
                foreach ($results as $item) {
                    echo '<tr>';
                    echo '<td>' . $row_idx++ . '</td>';
                    echo '<td>' . $item['x'] . '</td>';
                    echo '<td>' . $item['f'] . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
                break;

            case 'E':
                // Тип E: Блочная верстка (<div> по горизонтали, красная рамка 2px, отступ 8px)
                echo '<div class="layout-e">';
                foreach ($results as $item) {
                    echo '<div class="block-item">f(' . $item['x'] . ')=' . $item['f'] . '</div>';
                }
                echo '</div>';
                break;
        }
        ?>

        <!-- Статистические показатели (пункт 6 задания) -->
        <div class="stats-card">
            <h2>Статистические характеристики ряда:</h2>
            <div class="stats-grid">
                <div class="stat-item">Минимум: <strong><?php echo $min_f; ?></strong></div>
                <div class="stat-item">Максимум: <strong><?php echo $max_f; ?></strong></div>
                <div class="stat-item">Сумма: <strong><?php echo $sum_f; ?></strong></div>
                <div class="stat-item">Среднее: <strong><?php echo $avg_f; ?></strong></div>
            </div>
        </div>
    </main>

    <!-- Подвал страницы (footer) -->
    <footer>
        <div>Московский политехнический университет &copy; 2026</div>
        <div class="footer-type"><?php echo $current_layout_name; ?></div>
    </footer>

</body>
</html>
