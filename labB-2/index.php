<?php
// ==============================================================================
// Лабораторная работа № В-2: Преобразование типов. Сессии. Калькулятор.
// Студент: Беляков Игорь Романович, группа 241-3210
// ==============================================================================

// 1. Инициализация механизма сессий строго ДО любого вывода (Листинг В-2.6, В-2.7)
session_start();

if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = [];
    $_SESSION['iteration'] = 0;
}
$_SESSION['iteration']++;

// ==============================================================================
// 2. Функции проверки и вычисления выражений по методичке (Листинги В-2.3 - В-2.11)
// ==============================================================================

/**
 * Проверяет, является ли переданная строка числом (целым или дробным).
 * Листинг В-2.3
 */
function isnum($x) {
    if ($x === '' || $x === null) return false;
    $len = strlen($x);
    if ($len === 0) return false;

    // Число не может начинаться с точки, или начинаться с "0" если длина > 1 и второй символ не точка
    if ($x[0] === '.') return false;
    if ($x[0] === '0' && $len > 1 && $x[1] !== '.') return false;
    // Число не может заканчиваться на точку
    if ($x[$len - 1] === '.') return false;

    $point_count = false;
    for ($i = 0; $i < $len; $i++) {
        $c = $x[$i];
        if ($c !== '0' && $c !== '1' && $c !== '2' && $c !== '3' && 
            $c !== '4' && $c !== '5' && $c !== '6' && $c !== '7' && 
            $c !== '8' && $c !== '9' && $c !== '.') {
            return false; // недопустимый символ
        }
        if ($c === '.') {
            if ($point_count) {
                return false; // вторая точка в числе
            }
            $point_count = true;
        }
    }
    return true;
}

/**
 * Валидатор баланса и порядка скобок (Листинг В-2.10)
 */
function SqValidator($val) {
    $open = 0;
    $len = strlen($val);
    for ($i = 0; $i < $len; $i++) {
        if ($val[$i] === '(') {
            $open++;
        } elseif ($val[$i] === ')') {
            $open--;
            if ($open < 0) {
                return false; // закрывающая скобка раньше открывающей
            }
        }
    }
    return ($open === 0);
}

/**
 * Рекурсивное вычисление выражения без скобок (Листинги В-2.4, В-2.5)
 * Порядок операций: сложение (+), вычитание (-), умножение (*), деление (/ или :)
 */
function calculate($val) {
    $val = trim($val);
    if ($val === '') {
        return 'Выражение не задано!';
    }
    if (isnum($val)) {
        return $val;
    }

    // 1. Сложение (+)
    $args = explode('+', $val);
    if (count($args) > 1) {
        $sum = 0;
        for ($i = 0; $i < count($args); $i++) {
            $res = calculate($args[$i]);
            if (!isnum((string)$res)) {
                return $res;
            }
            $sum += (float)$res;
        }
        return (string)$sum;
    }

    // 2. Вычитание (-)
    // Учитываем унарный минус в начале: если первый символ '-', то первый элемент пуст
    $args = explode('-', $val);
    if (count($args) > 1) {
        $start_idx = 0;
        $sub = 0;

        // Если выражение начинается с минуса: "-5-3" -> $args[0] = "", $args[1] = "5"
        if ($args[0] === '') {
            $res = calculate($args[1]);
            if (!isnum((string)$res)) return $res;
            $sub = -1 * (float)$res;
            $start_idx = 2;
        } else {
            $res = calculate($args[0]);
            if (!isnum((string)$res)) return $res;
            $sub = (float)$res;
            $start_idx = 1;
        }

        for ($i = $start_idx; $i < count($args); $i++) {
            $res = calculate($args[$i]);
            if (!isnum((string)$res)) {
                return $res;
            }
            $sub -= (float)$res;
        }
        return (string)$sub;
    }

    // 3. Умножение (*)
    $args = explode('*', $val);
    if (count($args) > 1) {
        $mul = 1;
        for ($i = 0; $i < count($args); $i++) {
            $res = calculate($args[$i]);
            if (!isnum((string)$res)) {
                return $res;
            }
            $mul *= (float)$res;
        }
        return (string)$mul;
    }

    // 4. Деление (/ или :)
    // Нормализуем двоеточие ':' к слэшу '/'
    $val_div = str_replace(':', '/', $val);
    $args = explode('/', $val_div);
    if (count($args) > 1) {
        $res0 = calculate($args[0]);
        if (!isnum((string)$res0)) {
            return $res0;
        }
        $div = (float)$res0;

        for ($i = 1; $i < count($args); $i++) {
            $res = calculate($args[$i]);
            if (!isnum((string)$res)) {
                return $res;
            }
            $divisor = (float)$res;
            if ($divisor == 0.0) {
                return 'Ошибка: деление на ноль!';
            }
            $div /= $divisor;
        }
        return (string)$div;
    }

    return 'Недопустимые символы в выражении!';
}

/**
 * Рекурсивное вычисление выражения со скобками (Листинг В-2.11)
 */
function calculateSq($val) {
    $val = trim($val);
    if ($val === '') {
        return 'Выражение не задано!';
    }

    // Проверка корректности расстановки скобок
    if (!SqValidator($val)) {
        return 'Неправильная расстановка скобок!';
    }

    // Ищем первую открывающую скобку
    $start = strpos($val, '(');
    if ($start === false) {
        // Скобок нет – вычисляем напрямую через calculate()
        return calculate($val);
    }

    // Ищем соответствующую закрывающую скобку по счетчику баланса
    $end = $start + 1;
    $open = 1;
    $len = strlen($val);

    while ($open > 0 && $end < $len) {
        if ($val[$end] === '(') {
            $open++;
        } elseif ($val[$end] === ')') {
            $open--;
        }
        $end++;
    }

    // Вычисляем содержимое внутри скобок: substr($val, $start + 1, ($end - 1) - ($start + 1))
    $inner_expr = substr($val, $start + 1, $end - $start - 2);
    $inner_res = calculateSq($inner_expr);

    if (!isnum((string)$inner_res)) {
        return $inner_res; // возвращаем ошибку, если внутри скобок была ошибка
    }

    // Заменяем скобочную конструкцию на вычисленный результат
    $left_part = substr($val, 0, $start);
    $right_part = substr($val, $end);
    $new_expr = $left_part . $inner_res . $right_part;

    // Рекурсивно вычисляем оставшиеся скобки
    return calculateSq($new_expr);
}

// ==============================================================================
// 3. Обработка POST-запроса калькулятора (Листинги В-2.1, В-2.8, В-2.9)
// ==============================================================================
$calc_result = null;
$calc_error = null;
$submitted_val = '';

if (isset($_POST['val'])) {
    $submitted_val = trim($_POST['val']);

    // Защита от повторного добавления в историю при обычном обновлении страницы F5 (Листинг В-2.9):
    // Проверяем, совпадает ли iteration + 1 с текущим номером сессии
    $is_fresh_submit = isset($_POST['iteration']) && (((int)$_POST['iteration'] + 1) === (int)$_SESSION['iteration']);

    if ($submitted_val !== '') {
        $calc_res = calculateSq($submitted_val);

        if (isnum((string)$calc_res)) {
            $calc_result = $calc_res;
        } else {
            $calc_error = $calc_res;
        }

        // Сохраняем результат в сессию для ПОСЛЕДУЮЩЕГО отображения в истории (Листинг В-2.6):
        // "Текущий полученный результат вычислений не должен отображаться в истории, 
        //  но должен попадать туда при следующем обновлении страницы."
        if ($is_fresh_submit) {
            $entry = $submitted_val . ' = ' . ($calc_result !== null ? $calc_result : $calc_error);
            $_SESSION['pending_history'] = $entry;
        }
    }
}

// Если с прошлого запроса был отложенный результат – перемещаем его в постоянную историю
if (isset($_SESSION['pending_history'])) {
    // Если произошел следующий запрос или обновление
    if (!isset($_POST['val']) || (isset($_POST['iteration']) && ((int)$_POST['iteration'] + 1 !== (int)$_SESSION['iteration']))) {
        $_SESSION['history'][] = $_SESSION['pending_history'];
        unset($_SESSION['pending_history']);
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Беляков Игорь Романович, группа 241-3210. Лабораторная работа № В-2: Калькулятор</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function setExpr(expr) {
            document.getElementById('val').value = expr;
            document.getElementById('val').focus();
        }
    </script>
</head>
<body>

    <!-- Шапка страницы (header) -->
    <header>
        <div class="header-left">
            <img src="img/logo.png" alt="Логотип Московского Политеха" class="header-logo">
            <div class="header-title">Лабораторная работа № В-2: Арифметический калькулятор</div>
        </div>
        <div class="header-info">
            <div><strong>Студент:</strong> Беляков Игорь Романович</div>
            <div><strong>Группа:</strong> 241-3210</div>
        </div>
    </header>

    <!-- Основной блок страницы (main) -->
    <main>
        <h1>Арифметический калькулятор</h1>
        <p class="desc">
            Поддерживает вычисление целых чисел и десятичных дробей, скобок <code>( )</code>,
            операций сложения <code>+</code>, вычитания <code>-</code>, умножения <code>*</code> и деления <code>/</code> или <code>:</code>.
        </p>

        <!-- Блок вывода результата вычисления ПЕРЕД формой (по условию методички) -->
        <?php if ($calc_result !== null): ?>
            <div class="result-success">
                Значение выражения: <strong><?php echo htmlspecialchars($calc_result, ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
        <?php elseif ($calc_error !== null): ?>
            <div class="result-error">
                Ошибка вычисления выражения: <?php echo htmlspecialchars($calc_error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <!-- Форма ввода выражения (POST-форма с полем 'val' и кнопкой 'Вычислить') -->
        <form method="post" action="index.php" class="calc-form">
            <!-- Скрытое поле итерации для защиты от дублирования при F5 (Листинг В-2.8) -->
            <input type="hidden" name="iteration" value="<?php echo $_SESSION['iteration']; ?>">

            <label for="val" class="input-label">Вычисляемое математическое выражение:</label>
            <input type="text" id="val" name="val" class="calc-input" 
                   value="<?php echo htmlspecialchars($submitted_val, ENT_QUOTES, 'UTF-8'); ?>" 
                   placeholder="Например: 2+3*(4+5)-10/2" autofocus required>

            <button type="submit" class="btn-calc">Вычислить</button>
        </form>

        <!-- Быстрые примеры выражений -->
        <div class="hints">
            <strong>Быстрые примеры для проверки:</strong><br>
            <span class="hint-pill" onclick="setExpr('2+3*4+7*9')">2+3*4+7*9</span>
            <span class="hint-pill" onclick="setExpr('(2+3*(4+5))-10:2')">(2+3*(4+5))-10:2</span>
            <span class="hint-pill" onclick="setExpr('15.5+4.5*2')">15.5+4.5*2</span>
            <span class="hint-pill" onclick="setExpr('10/(5-5)')">10/(5-5) [деление на 0]</span>
            <span class="hint-pill" onclick="setExpr('(2+3))-(1')">(2+3))-(1 [ошибка скобок]</span>
        </div>
    </main>

    <!-- Подвал сайта (footer) с историей вычислений сессии -->
    <footer>
        <div class="footer-top">
            <strong>История вычислений сессии:</strong>
            <span>Сессия #<?php echo session_id(); ?> | Итерация: <?php echo $_SESSION['iteration']; ?></span>
        </div>

        <div class="history-list">
            <?php 
            // Построчный вывод истории из $_SESSION['history'] (Листинг В-2.6)
            if (count($_SESSION['history']) > 0): 
                for ($h = 0; $h < count($_SESSION['history']); $h++):
            ?>
                    <div class="history-item">&bull; <?php echo htmlspecialchars($_SESSION['history'][$h], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php 
                endfor;
            else: 
            ?>
                <div class="history-empty">История пока пуста (предыдущие вычисления появятся здесь при следующем шаге)</div>
            <?php endif; ?>
        </div>
    </footer>

</body>
</html>
