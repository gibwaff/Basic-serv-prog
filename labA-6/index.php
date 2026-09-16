<?php
// ==============================================================================
// Лабораторная работа № А-6: Использование форм для передачи данных в программу PHP.
// Тест математических знаний.
// Студент: Беляков Игорь Романович, группа 241-3210
// ==============================================================================

// Массив математических задач (не менее 6 опций согласно заданию):
// 1. Площадь треугольника (по формуле Герона)
// 2. Периметр треугольника (A + B + C)
// 3. Объем параллелепипеда (A * B * C)
// 4. Среднее арифметическое ((A + B + C) / 3)
// 5. Сумма квадратов (A^2 + B^2 + C^2)
// 6. Максимальное из трех чисел max(A, B, C)
$tasks_names = [
    'triangle_area'  => 'Площадь треугольника',
    'triangle_perim' => 'Периметр треугольника',
    'box_volume'     => 'Объем параллелепипеда',
    'mean'           => 'Среднее арифметическое',
    'sum_squares'    => 'Сумма квадратов',
    'max_number'     => 'Максимальное из трех чисел'
];

$result = null;      // вычисленный машиной результат
$is_processed = false; // флаг: была ли отправлена и обработана форма

// ==============================================================================
// Обработка данных формы (POST)
// ==============================================================================
if (isset($_POST['A']) && isset($_POST['B']) && isset($_POST['C'])) {
    $is_processed = true;

    // Вспомогательная функция для преобразования строк с запятой или точкой в float
    function parseNumber($val) {
        $clean = str_replace(',', '.', trim($val));
        return is_numeric($clean) ? (float)$clean : 0.0;
    }

    $a = parseNumber($_POST['A']);
    $b = parseNumber($_POST['B']);
    $c = parseNumber($_POST['C']);

    $task = isset($_POST['TASK']) ? $_POST['TASK'] : 'mean';
    $user_answer_raw = isset($_POST['result']) ? trim($_POST['result']) : '';

    // Вычисление математической задачи программой
    switch ($task) {
        case 'triangle_perim':
            // Периметр треугольника: A + B + C
            $calc_val = $a + $b + $c;
            break;

        case 'triangle_area':
            // Площадь треугольника по формуле Герона: sqrt(p*(p-a)*(p-b)*(p-c))
            // Проверка неравенства треугольника
            if ($a + $b > $c && $a + $c > $b && $b + $c > $a && $a > 0 && $b > 0 && $c > 0) {
                $p = ($a + $b + $c) / 2;
                $calc_val = sqrt($p * ($p - $a) * ($p - $b) * ($p - $c));
            } else {
                $calc_val = 0; // треугольник не существует
            }
            break;

        case 'box_volume':
            // Объем параллелепипеда: A * B * C
            $calc_val = $a * $b * $c;
            break;

        case 'mean':
            // Среднее арифметическое: (A + B + C) / 3
            $calc_val = ($a + $b + $c) / 3;
            break;

        case 'sum_squares':
            // Сумма квадратов: A^2 + B^2 + C^2
            $calc_val = ($a * $a) + ($b * $b) + ($c * $c);
            break;

        case 'max_number':
            // Максимальное из трех: max(A, B, C)
            $calc_val = max($a, $b, $c);
            break;

        default:
            $calc_val = ($a + $b + $c) / 3;
            break;
    }

    $result = round($calc_val, 2);

    // Сравнение ответа пользователя с вычисленным значением программы
    $has_user_answer = ($user_answer_raw !== '');
    $is_test_passed = false;

    if ($has_user_answer) {
        $user_num = parseNumber($user_answer_raw);
        // Сравнение с точностью до 0.01
        $is_test_passed = (abs(round($user_num, 2) - $result) < 0.001);
    }

    // Формирование текстового отчета для браузера и e-mail
    $fio = isset($_POST['FIO']) ? htmlspecialchars($_POST['FIO'], ENT_QUOTES, 'UTF-8') : '';
    $group = isset($_POST['GROUP']) ? htmlspecialchars($_POST['GROUP'], ENT_QUOTES, 'UTF-8') : '';
    $about = isset($_POST['ABOUT']) ? htmlspecialchars($_POST['ABOUT'], ENT_QUOTES, 'UTF-8') : '';
    $task_title = isset($tasks_names[$task]) ? $tasks_names[$task] : $task;
    $view_mode = isset($_POST['VIEW_MODE']) ? $_POST['VIEW_MODE'] : 'browser';

    // Отправка на email, если установлен флажок send_mail
    $mail_sent_notice = '';
    if (array_key_exists('send_mail', $_POST) && !empty($_POST['MAIL'])) {
        $to_email = filter_var(trim($_POST['MAIL']), FILTER_SANITIZE_EMAIL);
        $mail_subject = "Результат тестирования математических знаний";
        $mail_body = "ФИО: " . $_POST['FIO'] . "\r\n" .
                     "Группа: " . $_POST['GROUP'] . "\r\n" .
                     "Задача: " . $task_title . "\r\n" .
                     "Входные данные: A=" . $a . ", B=" . $b . ", C=" . $c . "\r\n" .
                     "Предполагаемый ответ: " . ($has_user_answer ? $user_answer_raw : "Не указан") . "\r\n" .
                     "Правильный результат: " . $result . "\r\n" .
                     "Итог: " . ($is_test_passed ? "ТЕСТ ПРОЙДЕН" : "ОШИБКА: ТЕСТ НЕ ПРОЙДЕН") . "\r\n";

        // Попытка отправки через стандартный mail()
        @mail($to_email, $mail_subject, $mail_body, "From: auto@mospolytech.ru\r\nContent-Type: text/plain; charset=utf-8\r\n");
        $mail_sent_notice = 'Результаты теста были автоматически отправлены на e-mail ' . htmlspecialchars($to_email, ENT_QUOTES, 'UTF-8');
    }
}

// Начальные значения для формы:
// Если форма загружается впервые или повторно:
// ФИО и Группа берутся из GET-параметров при повторе, либо сохраняются дефолтные
$init_fio = isset($_GET['F']) ? htmlspecialchars($_GET['F'], ENT_QUOTES, 'UTF-8') : 'Беляков Игорь Романович';
$init_group = isset($_GET['G']) ? htmlspecialchars($_GET['G'], ENT_QUOTES, 'UTF-8') : '241-3210';

// Начальные значения A, B, C - произвольные числа от 0 до 100 (mt_rand)
$init_a = mt_rand(10, 100);
$init_b = mt_rand(10, 100);
$init_c = mt_rand(10, 100);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Беляков Игорь Романович, группа 241-3210. Лабораторная работа № А-6</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        // Скрыть / показать поле e-mail в зависимости от флажка send_mail (JavaScript)
        function toggleEmailField(checkbox) {
            var box = document.getElementById('email_box');
            if (checkbox.checked) {
                box.style.display = 'flex';
            } else {
                box.style.display = 'none';
            }
        }
    </script>
</head>
<body>

    <!-- Шапка страницы (header) -->
    <header>
        <div class="header-left">
            <img src="img/logo.png" alt="Логотип Московского Политеха" class="header-logo">
            <div class="header-title">Лабораторная работа № А-6: Тест математических знаний</div>
        </div>
        <div class="header-info">
            <div><strong>Студент:</strong> Беляков Игорь Романович</div>
            <div><strong>Группа:</strong> 241-3210</div>
        </div>
    </header>

    <!-- Основной блок страницы (main) -->
    <main class="<?php echo ($is_processed && $view_mode === 'print') ? 'print-version' : ''; ?>">
        <?php if (!$is_processed): ?>
            <!-- 1. ВЫВОД ФОРМЫ (При первой загрузке или при повторении теста) -->
            <h1>Тест математических знаний</h1>
            <p class="desc">Заполните поля формы, введите предполагаемый ответ и нажмите кнопку «Проверить»</p>

            <form name="math_test" method="post" action="" class="test-form">
                <!-- Однострочные поля: ФИО, Группа -->
                <div class="form-group">
                    <label class="form-label" for="fio">ФИО:</label>
                    <input type="text" id="fio" name="FIO" class="form-input" value="<?php echo $init_fio; ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="group">Номер группы:</label>
                    <input type="text" id="group" name="GROUP" class="form-input" value="<?php echo $init_group; ?>" required>
                </div>

                <!-- Числовые входные данные: A, B, C (произвольные от 0 до 100) -->
                <div class="form-group">
                    <label class="form-label" for="val_a">Значение А:</label>
                    <input type="text" id="val_a" name="A" class="form-input" value="<?php echo $init_a; ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="val_b">Значение В:</label>
                    <input type="text" id="val_b" name="B" class="form-input" value="<?php echo $init_b; ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="val_c">Значение С:</label>
                    <input type="text" id="val_c" name="C" class="form-input" value="<?php echo $init_c; ?>" required>
                </div>

                <!-- Селектор математических задач (не менее 6 опций) -->
                <div class="form-group">
                    <label class="form-label" for="task_select">Математическая задача:</label>
                    <select id="task_select" name="TASK" class="form-select">
                        <option value="mean">Среднее арифметическое (A+B+C)/3</option>
                        <option value="triangle_perim">Периметр треугольника (A+B+C)</option>
                        <option value="triangle_area">Площадь треугольника (формула Герона)</option>
                        <option value="box_volume">Объем параллелепипеда (A*B*C)</option>
                        <option value="sum_squares">Сумма квадратов (A²+B²+C²)</option>
                        <option value="max_number">Максимальное из трех чисел max(A,B,C)</option>
                    </select>
                </div>

                <!-- Предполагаемый ответ пользователя -->
                <div class="form-group">
                    <label class="form-label" for="ans">Ваш ответ:</label>
                    <input type="text" id="ans" name="result" class="form-input" placeholder="Введите вычисленный вами ответ">
                </div>

                <!-- Многострочное поле: немного о себе -->
                <div class="form-group">
                    <label class="form-label" for="about">Немного о себе:</label>
                    <textarea id="about" name="ABOUT" class="form-textarea" placeholder="Укажите дополнительную информацию..."></textarea>
                </div>

                <!-- Селектор версии оформления: для браузера / для печати -->
                <div class="form-group">
                    <label class="form-label" for="view_mode">Режим отображения:</label>
                    <select id="view_mode" name="VIEW_MODE" class="form-select">
                        <option value="browser">Версия для просмотра в браузере</option>
                        <option value="print">Версия для печати</option>
                    </select>
                </div>

                <!-- Флажок: отправить результат на e-mail (изначально не отмечен) -->
                <div class="checkbox-group">
                    <input type="checkbox" id="send_mail" name="send_mail" value="1" onclick="toggleEmailField(this)">
                    <label for="send_mail" class="checkbox-label">Отправить результат теста по e-mail</label>
                </div>

                <!-- Поле E-mail (скрыто, появляется по клику на флажок через JS) -->
                <div class="form-group" id="email_box">
                    <label class="form-label" for="email">Ваш e-mail:</label>
                    <input type="email" id="email" name="MAIL" class="form-input" placeholder="user@example.com">
                </div>

                <!-- Кнопка "Проверить" -->
                <button type="submit" class="btn-submit">Проверить</button>
            </form>

        <?php else: ?>
            <!-- 2. ВЫВОД ОТЧЕТА ПО РЕЗУЛЬТАТАМ ТЕСТИРОВАНИЯ -->
            <h1>Отчет о результатах тестирования</h1>

            <?php if (!empty($mail_sent_notice)): ?>
                <div class="mail-notice"><?php echo $mail_sent_notice; ?></div>
            <?php endif; ?>

            <div class="report-card">
                <div class="report-row">
                    <strong>ФИО студента:</strong>
                    <span><?php echo $fio; ?></span>
                </div>
                <div class="report-row">
                    <strong>Группа студента:</strong>
                    <span><?php echo $group; ?></span>
                </div>
                <?php if (!empty($about)): ?>
                    <div class="report-row">
                        <strong>Сведения о студенте:</strong>
                        <span><?php echo nl2br($about); ?></span>
                    </div>
                <?php endif; ?>
                <div class="report-row">
                    <strong>Тип решаемой задачи:</strong>
                    <span><?php echo $task_title; ?></span>
                </div>
                <div class="report-row">
                    <strong>Входные данные:</strong>
                    <span>A = <?php echo $a; ?>; &nbsp; B = <?php echo $b; ?>; &nbsp; C = <?php echo $c; ?></span>
                </div>
                <div class="report-row">
                    <strong>Предполагаемый результат:</strong>
                    <span>
                        <?php 
                        if ($has_user_answer) {
                            echo htmlspecialchars($user_answer_raw, ENT_QUOTES, 'UTF-8');
                        } else {
                            echo '<em>Задача самостоятельно решена не была</em>';
                        }
                        ?>
                    </span>
                </div>
                <div class="report-row">
                    <strong>Вычисленный программой результат:</strong>
                    <span><strong><?php echo $result; ?></strong></span>
                </div>

                <!-- Выводы об успешности теста -->
                <?php if (!$has_user_answer): ?>
                    <div class="test-fail">Задача самостоятельно решена не была (ответ не введен)</div>
                <?php elseif ($is_test_passed): ?>
                    <div class="test-success">ТЕСТ ПРОЙДЕН (Ваш ответ совпал с вычислениями системы)</div>
                <?php else: ?>
                    <div class="test-fail">ОШИБКА: ТЕСТ НЕ ПРОЙДЕН! (Вычисленное значение отличается от эталона)</div>
                <?php endif; ?>
            </div>

            <!-- Ссылка "Повторить тест" (выводится только в режиме просмотра в браузере) -->
            <?php if ($view_mode === 'browser'): ?>
                <?php
                $repeat_url = '?F=' . urlencode($_POST['FIO']) . '&G=' . urlencode($_POST['GROUP']);
                ?>
                <a href="<?php echo $repeat_url; ?>" id="back_button">Повторить тест</a>
            <?php endif; ?>

        <?php endif; ?>
    </main>

    <!-- Подвал страницы (footer) -->
    <footer>
        <div>Московский политехнический университет &copy; 2026</div>
        <div class="footer-info-text">
            <?php echo $is_processed ? 'Режим: Отчет (' . ($view_mode === 'print' ? 'Печать' : 'Браузер') . ')' : 'Режим: Ввод данных'; ?>
        </div>
    </footer>

</body>
</html>
