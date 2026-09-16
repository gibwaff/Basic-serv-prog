<?php
// ==============================================================================
// Модуль viewer.php: вывод содержимого записной книжки в браузер
// Листинг В-1.4
// "Содержит пользовательскую функцию getFriendsList($type, $page),
//  которая по типу сортировки и диапазону выводимых записей формирует контент"
// ==============================================================================

require_once __DIR__ . '/db.php';

function getFriendsList($type, $page) {
    $pdo = getDBConnection();

    // 1. Определение общего количества записей в таблице (COUNT(*))
    $stmt_count = $pdo->query("SELECT COUNT(*) FROM friends");
    $total = (int)$stmt_count->fetchColumn();

    if ($total === 0) {
        return '<div class="msg-error">В таблице нет данных. Добавьте первую запись через меню «Добавление записи».</div>';
    }

    // 2. Расчет количества страниц пагинации (по 10 записей на страницу)
    $pages_count = (int)ceil($total / 10);
    if ($page < 0) {
        $page = 0;
    }
    if ($page >= $pages_count) {
        $page = $pages_count - 1;
    }

    // 3. Определение порядка сортировки (ORDER BY)
    $order_clause = 'id ASC';
    if ($type === 'fam') {
        $order_clause = 'lastname ASC, firstname ASC';
    } elseif ($type === 'birth') {
        $order_clause = 'birthdate ASC';
    }

    // 4. Запрос порции записей через LIMIT (offset, limit)
    $offset = $page * 10;
    $sql = "SELECT * FROM friends ORDER BY {$order_clause} LIMIT 10 OFFSET {$offset}";
    $stmt = $pdo->query($sql);
    $records = $stmt->fetchAll();

    // 5. Формирование HTML-таблицы записной книжки
    $ret = '<h1>Список контактов (Записная книжка)</h1>';
    $ret .= '<table class="contacts-table">';
    $ret .= '<thead><tr>
        <th>ID</th>
        <th>Фамилия</th>
        <th>Имя</th>
        <th>Отчество</th>
        <th>Пол</th>
        <th>Дата рождения</th>
        <th>Телефон</th>
        <th>Адрес</th>
        <th>E-mail</th>
        <th>Комментарий</th>
    </tr></thead>';
    $ret .= '<tbody>';

    foreach ($records as $row) {
        $ret .= '<tr>';
        $ret .= '<td>' . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . '</td>';
        $ret .= '<td><strong>' . htmlspecialchars($row['lastname'], ENT_QUOTES, 'UTF-8') . '</strong></td>';
        $ret .= '<td>' . htmlspecialchars($row['firstname'], ENT_QUOTES, 'UTF-8') . '</td>';
        $ret .= '<td>' . htmlspecialchars($row['patronymic'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        $ret .= '<td>' . htmlspecialchars($row['gender'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        $ret .= '<td>' . htmlspecialchars($row['birthdate'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        $ret .= '<td>' . htmlspecialchars($row['phone'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        $ret .= '<td>' . htmlspecialchars($row['address'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        $ret .= '<td>' . htmlspecialchars($row['email'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        $ret .= '<td>' . htmlspecialchars($row['comment'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        $ret .= '</tr>';
    }
    $ret .= '</tbody></table>';

    // 6. Формирование блока пагинации
    if ($pages_count > 1) {
        $ret .= '<div id="pages">';
        $ret .= '<span class="page-title">Страницы:</span>';
        for ($i = 0; $i < $pages_count; $i++) {
            if ($i != $page) {
                // Ссылка на другую страницу (при наведении рамка 2px в CSS)
                $ret .= '<a href="index.php?p=viewer&sort=' . urlencode($type) . '&pg=' . $i . '">' . ($i + 1) . '</a>';
            } else {
                // Текущая активная страница (span)
                $ret .= '<span class="current-page">' . ($i + 1) . '</span>';
            }
        }
        $ret .= '</div>';
    }

    return $ret;
}
