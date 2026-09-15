<?php
// Заголовок страницы (TITLE) сохраняется в переменной, которая затем выводится с помощью PHP
$page_title = "Беляков Игорь Романович 241-3210. Лабораторная работа № А-1. О языке PHP";
$current_file = basename(__FILE__);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Фиксированная шапка (header) -->
    <header class="site-header">
        <div class="logo">Веб-разработка на PHP</div>
        <nav class="main-menu">
            <a href="<?php
            $name = 'Главная';
            $link = 'index.php';
            $current_page = ($current_file == $link);
            echo $link;
            ?>"<?php
            if ($current_page)
                echo ' class="selected_menu"';
            ?>><?php
            echo $name;
            ?></a>
            <a href="<?php
            $name = 'О языке PHP';
            $link = 'php_intro.php';
            $current_page = ($current_file == $link);
            echo $link;
            ?>"<?php
            if ($current_page)
                echo ' class="selected_menu"';
            ?>><?php
            echo $name;
            ?></a>
            <a href="<?php
            $name = 'Архитектура Web';
            $link = 'architecture.php';
            $current_page = ($current_file == $link);
            echo $link;
            ?>"<?php
            if ($current_page)
                echo ' class="selected_menu"';
            ?>><?php
            echo $name;
            ?></a>
        </nav>
    </header>

    <!-- Основной контент страницы -->
    <main class="container">
        <h1>Язык программирования PHP: назначение и возможности</h1>

        <h2>Роль PHP в современной серверной разработке</h2>
        <p>
            Язык PHP (Hypertext Preprocessor) был разработан датско-канадским программистом Расмусом Лердорфом в 1994 году
            как набор инструментов для поддержания личной веб-страницы. За три десятилетия развития PHP проделал путь
            от простых скриптов CGI до одного из наиболее мощных и распространенных интерпретируемых языков серверного
            программирования. Сегодня PHP обслуживает огромную долю глобального интернета, являясь базой для таких
            систем, как WordPress, Drupal, MediaWiki, а также масштабируемых фреймворков уровня Laravel и Symfony.
        </p>
        <p>
            Главная особенность и фундаментальное предназначение PHP заключается в динамической генерации текстовых
            потоков данных (в первую очередь HTML-документов). Важно понимать, что в самом языке отсутствуют специфические
            команды создания элементов DOM-дерева: интерпретатор PHP генерирует строковый поток символов, который веб-сервер
            (Apache, Nginx) передает клиенту по сетевому протоколу HTTP. Браузер на стороне пользователя принимает готовый
            текстовый HTML-документ и осуществляет его визуальную отрисовку (рендеринг).
        </p>

        <!-- Галерея изображений -->
        <div class="gallery">
            <div class="gallery-item">
                <?php
                // Смена фото в зависимости от секунды
                $s = date('s');
                $os = $s % 2;
                if ($os === 0) {
                    $name = 'fotos/foto1.jpg';
                } else {
                    $name = 'fotos/foto2.jpg';
                }
                echo '<img src="' . $name . '" alt="Меняющаяся фотография">';
                ?>
                <div class="gallery-caption">Динамическое фото (текущая секунда сервера: <?php echo $s; ?>)</div>
            </div>
            <div class="gallery-item">
                <img src="fotos/static_img.jpg" alt="Инфраструктура серверного ПО">
                <div class="gallery-caption">Статическое фото: среда исполнения скрипта</div>
            </div>
        </div>

        <h2>Преимущества и спектр решаемых задач</h2>
        <p>
            Ключевые преимущества PHP охватывают низкий порог вхождения, всеобъемлющую кроссплатформенность, встроенную
            поддержку большинства реляционных и документо-ориентированных СУБД (MySQL, PostgreSQL, SQLite, Redis),
            а также гигантскую экосистему готовых библиотек, доступных через пакетный менеджер Composer. PHP решает
            задачи обработки веб-форм, валидации пользовательских данных, управления сессиями, генерации графики,
            взаимодействия с внешними REST API и потоковой передачи файлов по защищенным каналам связи.
        </p>

        <!-- Таблица (строка 1 полностью средствами PHP, строка 2 - ячейки динамические без переменных) -->
        <table class="content-table">
            <?php
            // HTML-код первой строки таблицы (включая теги <tr>...</tr>) полностью выводится средствами PHP
            echo '<tr><th>Критерий</th><th>Интерпретация PHP</th><th>Компилируемые языки (C++/Go)</th></tr>';
            ?>
            <tr>
                <td><?php echo "Способ запуска исходного кода"; ?></td>
                <td><?php echo "JIT-компиляция / OpCache на лету"; ?></td>
                <td><?php echo "Предварительная сборка в бинарный исполняемый файл"; ?></td>
            </tr>
        </table>
    </main>

    <!-- Фиксированный подвал (footer) -->
    <footer class="site-footer">
        <div>Беляков Игорь Романович, группа 241-3210</div>
        <div class="footer-info">
            <?php
            echo "Сформировано " . date('d.m.Y в H-i:s');
            ?>
        </div>
    </footer>

</body>
</html>
