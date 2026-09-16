<?php
// ==============================================================================
// Модуль delete.php: удаление записи из базы данных
// "Доступная при переходе по ссылке 'Удалить запись' страница содержит список ссылок,
//  текст которых соответствует фамилии и инициалам из базы данных.
//  При переходе по ссылке страница перезагружается, соответствующая запись удаляется из БД,
//  выводится надпись: 'Запись с фамилией Иванов удалена' (вместо 'Иванов' фамилия из удаляемой записи)."
// ==============================================================================

require_once __DIR__ . '/db.php';
$pdo = getDBConnection();
$del_msg = '';

// 1. Если был передан id для удаления
if (isset($_GET['del_id'])) {
    $del_id = (int)$_GET['del_id'];

    // Сначала получаем фамилию удаляемого контакта
    $stmt_find = $pdo->prepare("SELECT lastname FROM friends WHERE id = ?");
    $stmt_find->execute([$del_id]);
    $deleted_person = $stmt_find->fetch();

    if ($deleted_person) {
        $del_lastname = $deleted_person['lastname'];
        // Выполняем удаление
        $stmt_del = $pdo->prepare("DELETE FROM friends WHERE id = ?");
        $stmt_del->execute([$del_id]);
        $del_msg = '<div class="msg-success">Запись с фамилией ' . htmlspecialchars($del_lastname, ENT_QUOTES, 'UTF-8') . ' удалена</div>';
    } else {
        $del_msg = '<div class="msg-error">Ошибка: запись для удаления не найдена</div>';
    }
}

// 2. Получение актуального списка записей (фамилия и инициалы)
$stmt_list = $pdo->query("SELECT id, lastname, firstname, patronymic FROM friends ORDER BY lastname ASC, firstname ASC");
$friends_list = $stmt_list->fetchAll();
?>

<h1>Удаление записей</h1>
<?php echo $del_msg; ?>

<?php if (count($friends_list) === 0): ?>
    <div class="msg-error">В записной книжке не осталось записей для удаления.</div>
<?php else: ?>
    <p style="font-size: 14px; color: #4b5563; margin-bottom: 15px;">
        Нажмите на фамилию и инициалы контакта, чтобы удалить его из базы данных:
    </p>

    <div class="links-list">
        <?php foreach ($friends_list as $item): ?>
            <?php
            // Формируем инициалы: "Иванов А. П."
            $initials = '';
            if (!empty($item['firstname'])) {
                $initials .= ' ' . mb_substr($item['firstname'], 0, 1, 'UTF-8') . '.';
            }
            if (!empty($item['patronymic'])) {
                $initials .= ' ' . mb_substr($item['patronymic'], 0, 1, 'UTF-8') . '.';
            }
            $caption = htmlspecialchars($item['lastname'] . $initials, ENT_QUOTES, 'UTF-8');
            ?>
            <a href="index.php?p=delete&del_id=<?php echo $item['id']; ?>" 
               class="delete-item-link" 
               onclick="return confirm('Вы действительно хотите удалить контакт <?php echo $item['lastname']; ?>?');">
                &times; <?php echo $caption; ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
