<?php
// ==============================================================================
// Лабораторная работа № А-5: Динамическое формирование контента и меню.
// Таблица умножения.
// Студент: Беляков Игорь Романович, группа 241-3210
// ==============================================================================

// 1. Определение параметров:
// html_type: 'TABLE' (табличная) или 'DIV' (блочная). По умолчанию при первой загрузке используется табличная верстка.
$raw_html_type = isset($_GET['html_type']) ? strtoupper($_GET['html_type']) : null;
$html_type = ($raw_html_type === 'DIV') ? 'DIV' : 'TABLE';

// content: null (вся таблица) либо число от 2 до 9 (конкретная колонка умножения).
$content = (isset($_GET['content']) && is_numeric($_GET['content']) && $_GET['content'] >= 2 && $_GET['content'] <= 9) 
           ? (int)$_GET['content'] 
           : null;

// ==============================================================================
// 2. Пользовательские функции согласно Листингам А-5.4 - А-5.14
// ==============================================================================

/**
 * Преобразует число или цифру в ссылку на таблицу умножения (Листинг А-5.8, А-5.14).
 * Требование 4: "Все цифры (и только цифры) должны быть ссылками на соответствующие таблицы умножения.
 * Например, строка 2х3=6 должна содержать три ссылки: на 2, 3 и 6.
 * Строка 3х5=15 – две ссылки: на 3 и 5 (число 15 не является цифрой от 2 до 9).
 * Указанные ссылки всегда «сбрасывают» тип верстки – соответствующий параметр не указывается."
 */
function outNumAsLink($x) {
    // Ссылками становятся только цифры от 2 до 9
    if ($x >= 2 && $x <= 9) {
        return '<a href="?content=' . $x . '" class="num-link">' . $x . '</a>';
    }
    // Если число меньше 2 или больше 9 (например, 10, 12, 14, 15, ..., 81) - выводим просто как текст
    return (string)$x;
}

/**
 * Формирует строки одного столбца таблицы умножения от 2 до 9 (Листинг А-5.8)
 */
function outRow($n) {
    $out = '';
    for ($i = 2; $i <= 9; $i++) {
        $out .= outNumAsLink($n) . ' &times; ' . outNumAsLink($i) . ' = ' . outNumAsLink($n * $i) . '<br>';
    }
    return $out;
}

/**
 * Вывод таблицы умножения в ТАБЛИЧНОЙ форме (Листинг А-5.4, А-5.5)
 */
function outTableForm() {
    global $content;

    if ($content === null) {
        // Вся таблица: 8 колонок (от 2 до 9)
        echo '<table class="ttTableFull"><tr>';
        for ($i = 2; $i <= 9; $i++) {
            echo '<td>' . outRow($i) . '</td>';
        }
        echo '</tr></table>';
    } else {
        // Одиночный столбец (более крупно)
        echo '<table class="ttTableSingle"><tr>';
        echo '<td>' . outRow($content) . '</td>';
        echo '</tr></table>';
    }
}

/**
 * Вывод таблицы умножения в БЛОЧНОЙ форме (Листинг А-5.4, А-5.6)
 */
function outDivForm() {
    global $content;

    if ($content === null) {
        // Вся таблица: 8 колонок в виде flex-блоков
        echo '<div class="ttDivFull">';
        for ($i = 2; $i <= 9; $i++) {
            echo '<div class="ttRow">' . outRow($i) . '</div>';
        }
        echo '</div>';
    } else {
        // Одиночный столбец (более крупно)
        echo '<div class="ttDivSingle">';
        echo '<div class="ttSingleRow">' . outRow($content) . '</div>';
        echo '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Беляков Игорь Романович, группа 241-3210. Лабораторная работа № А-5</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Шапка страницы (header) с логотипом и главным меню -->
    <header>
        <div class="header-left">
            <img src="img/logo.png" alt="Логотип Московского Политеха" class="header-logo">
            <div class="header-title">Лабораторная работа № А-5: Таблица умножения</div>
        </div>

        <!-- 1. Главное меню (в шапке страницы) по Листингам А-5.1 и А-5.3 -->
        <div id="main_menu">
            <?php
            // Ссылка 1: Табличная верстка
            echo '<a href="?html_type=TABLE';
            if ($content !== null) {
                echo '&content=' . $content;
            }
            echo '"';
            // Ни один из пунктов по умолчанию (при первой загрузке, когда $raw_html_type === null) не выделен!
            if ($raw_html_type === 'TABLE') {
                echo ' class="selected"';
            }
            echo '>Табличная верстка</a>';

            // Ссылка 2: Блочная верстка
            echo '<a href="?html_type=DIV';
            if ($content !== null) {
                echo '&content=' . $content;
            }
            echo '"';
            if ($raw_html_type === 'DIV') {
                echo ' class="selected"';
            }
            echo '>Блочная верстка</a>';
            ?>
        </div>
    </header>

    <!-- Основной контейнер страницы: левая панель и центральный контент -->
    <div class="page-wrapper">

        <!-- 2. Основное меню (в левой части страницы) по Листингам А-5.2 и А-5.3 -->
        <aside>
            <div class="aside-title">Столбцы таблицы</div>
            <div id="product_menu">
                <?php
                // Пункт "Всё" (выделен по умолчанию при первой загрузке, когда $content === null)
                $all_url = ($raw_html_type !== null) ? '?html_type=' . $raw_html_type : '?';
                echo '<a href="' . $all_url . '"';
                if ($content === null) {
                    echo ' class="selected"';
                }
                echo '>Вся таблица умножения</a>';

                // Пункты с цифрами от 2 до 9
                for ($i = 2; $i <= 9; $i++) {
                    $item_url = '?content=' . $i;
                    if ($raw_html_type !== null) {
                        $item_url .= '&html_type=' . $raw_html_type;
                    }
                    echo '<a href="' . $item_url . '"';
                    if ($content === $i) {
                        echo ' class="selected"';
                    }
                    echo '>Таблица умножения на ' . $i . '</a>';
                }
                ?>
            </div>
        </aside>

        <!-- 4. Таблица умножения (в основной части страницы) -->
        <main>
            <h1>
                <?php
                if ($content === null) {
                    echo 'Полная таблица умножения (8 колонок)';
                } else {
                    echo 'Таблица умножения на ' . $content;
                }
                ?>
            </h1>

            <?php
            // Вывод соответствующей формы верстки (Листинг А-5.5)
            if ($html_type === 'TABLE') {
                outTableForm();
            } else {
                outDivForm();
            }
            ?>
        </main>
    </div>

    <!-- 3. Информация о содержании страницы (в подвале) по Листингу А-5.9 -->
    <footer>
        <div>Московский политехнический университет &copy; 2026</div>
        <div class="footer-info-details">
            <?php
            // Формирование строки информации по Листингу А-5.9
            $info = '';
            if ($html_type === 'TABLE') {
                $info .= 'Тип верстки: <strong>Табличная</strong>. ';
            } else {
                $info .= 'Тип верстки: <strong>Блочная</strong>. ';
            }

            if ($content === null) {
                $info .= 'Содержание: <strong>Вся таблица (полная)</strong>. ';
            } else {
                $info .= 'Содержание: <strong>Таблица умножения на ' . $content . '</strong>. ';
            }

            echo $info . 'Дата и время: <strong>' . date('d.m.Y H:i:s') . '</strong>';
            ?>
        </div>
    </footer>

</body>
</html>
