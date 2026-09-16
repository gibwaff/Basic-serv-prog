<?php
// ==============================================================================
// Модуль menu.php: формирование и регламентация работы меню сайта
// Листинг В-1.1
// "Модуль menu.php содержит функцию без параметров, которая возвращает в виде 
//  строки HTML-код, содержащий основное меню сайта."
// ==============================================================================

function renderMenu() {
    // Допустимые значения для параметра p
    $allowed_pages = ['viewer', 'add', 'edit', 'delete'];
    if (!isset($_GET['p']) || !in_array($_GET['p'], $allowed_pages)) {
        $_GET['p'] = 'viewer';
    }
    $current_p = $_GET['p'];

    // Проверка параметра сортировки для подменю
    $allowed_sorts = ['byid', 'fam', 'birth'];
    if (!isset($_GET['sort']) || !in_array($_GET['sort'], $allowed_sorts)) {
        $_GET['sort'] = 'byid';
    }
    $current_sort = $_GET['sort'];

    $ret = '<div id="menu">';
    $ret .= '<div class="main-nav-buttons">';

    // Пункт 1: Просмотр
    $ret .= '<a href="index.php?p=viewer"';
    if ($current_p === 'viewer') {
        $ret .= ' class="selected"';
    }
    $ret .= '>Просмотр</a>';

    // Пункт 2: Добавление записи
    $ret .= '<a href="index.php?p=add"';
    if ($current_p === 'add') {
        $ret .= ' class="selected"';
    }
    $ret .= '>Добавление записи</a>';

    // Пункт 3: Редактирование записи
    $ret .= '<a href="index.php?p=edit"';
    if ($current_p === 'edit') {
        $ret .= ' class="selected"';
    }
    $ret .= '>Редактирование записи</a>';

    // Пункт 4: Удаление записи
    $ret .= '<a href="index.php?p=delete"';
    if ($current_p === 'delete') {
        $ret .= ' class="selected"';
    }
    $ret .= '>Удаление записи</a>';

    $ret .= '</div>'; // .main-nav-buttons

    // Подменю: выводится только если выбран пункт "Просмотр"
    if ($current_p === 'viewer') {
        $ret .= '<div id="submenu">';
        $ret .= '<span class="submenu-caption">Сортировка:</span>';

        // 1. По умолчанию (по порядку добавления / id)
        $ret .= '<a href="index.php?p=viewer&sort=byid"';
        if ($current_sort === 'byid') {
            $ret .= ' class="selected"';
        }
        $ret .= '>По-умолчанию</a>';

        // 2. По фамилии
        $ret .= '<a href="index.php?p=viewer&sort=fam"';
        if ($current_sort === 'fam') {
            $ret .= ' class="selected"';
        }
        $ret .= '>По фамилии</a>';

        // 3. По дате рождения
        $ret .= '<a href="index.php?p=viewer&sort=birth"';
        if ($current_sort === 'birth') {
            $ret .= ' class="selected"';
        }
        $ret .= '>По дате рождения</a>';

        $ret .= '</div>'; // #submenu
    }

    $ret .= '</div>'; // #menu
    return $ret;
}
