<?php
// ==============================================================================
// Модуль edit.php: редактирование существующей записи базы данных
// Листинги В-1.7 и В-1.8
// "Перед формой выводятся ссылки с текстом, соответствующим именам и фамилиям 
//  из базы данных (список сортируется по фамилии, затем по имени).
//  При переходе по ссылке страница перезагружается и в полях формы отображаются значения.
//  Текущая запись в списке выделяется цветом или рамкой.
//  Если запись не была выбрана – текущей считается первая запись по порядку."
// ==============================================================================

require_once __DIR__ . '/db.php';
$pdo = getDBConnection();
$edit_msg = '';

// 1. Обработка отправки формы редактирования (UPDATE)
if (isset($_POST['button']) && $_POST['button'] === 'Изменить запись' && isset($_POST['id'])) {
    $edit_id    = (int)$_POST['id'];
    $lastname   = trim($_POST['lastname'] ?? '');
    $firstname  = trim($_POST['firstname'] ?? '');
    $patronymic = trim($_POST['patronymic'] ?? '');
    $gender     = trim($_POST['gender'] ?? 'М');
    $birthdate  = trim($_POST['birthdate'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $comment    = trim($_POST['comment'] ?? '');

    try {
        $stmt_update = $pdo->prepare("UPDATE friends SET 
            lastname = ?, firstname = ?, patronymic = ?, gender = ?, 
            birthdate = ?, phone = ?, address = ?, email = ?, comment = ? 
            WHERE id = ?");
        $stmt_update->execute([
            $lastname, $firstname, $patronymic, $gender, 
            $birthdate, $phone, $address, $email, $comment, $edit_id
        ]);
        $edit_msg = '<div class="msg-success">Данные успешно изменены!</div>';
        $_GET['id'] = $edit_id; // делаем измененную запись активной
    } catch (Exception $e) {
        $edit_msg = '<div class="msg-error">Ошибка изменения данных: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</div>';
    }
}

// 2. Получение списка всех записей для ссылок (сортировка по фамилии, затем по имени)
$stmt_all = $pdo->query("SELECT id, lastname, firstname, patronymic FROM friends ORDER BY lastname ASC, firstname ASC");
$all_friends = $stmt_all->fetchAll();

if (count($all_friends) === 0) {
    echo '<h1>Редактирование записей</h1>';
    echo '<div class="msg-error">В записной книжке пока нет ни одной записи.</div>';
    return;
}

// 3. Определение текущей выбранной записи ($currentROW)
$current_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$currentROW = null;

if ($current_id !== null) {
    $stmt_cur = $pdo->prepare("SELECT * FROM friends WHERE id = ? LIMIT 1");
    $stmt_cur->execute([$current_id]);
    $currentROW = $stmt_cur->fetch();
}

// Если запись не найдена или не выбрана – текущей считается первая запись по порядку
if (!$currentROW && count($all_friends) > 0) {
    $first_id = $all_friends[0]['id'];
    $stmt_cur = $pdo->prepare("SELECT * FROM friends WHERE id = ? LIMIT 1");
    $stmt_cur->execute([$first_id]);
    $currentROW = $stmt_cur->fetch();
}
?>

<h1>Редактирование записи</h1>
<?php echo $edit_msg; ?>

<p style="font-size: 14px; color: #4b5563; margin-bottom: 12px;">Выберите контакт для редактирования:</p>

<!-- Список ссылок с именами и фамилиями (сортировка по фамилии, затем по имени) -->
<div class="links-list" id="edit_links">
    <?php foreach ($all_friends as $f): ?>
        <?php if ($currentROW && $currentROW['id'] == $f['id']): ?>
            <!-- Текущая запись выделяется (красный фон/рамка) -->
            <div class="current-item"><?php echo htmlspecialchars($f['lastname'] . ' ' . $f['firstname'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php else: ?>
            <a href="index.php?p=edit&id=<?php echo $f['id']; ?>">
                <?php echo htmlspecialchars($f['lastname'] . ' ' . $f['firstname'], ENT_QUOTES, 'UTF-8'); ?>
            </a>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<!-- Форма редактирования с автозаполнением полей текущей записи -->
<form name="form_edit" method="post" action="index.php?p=edit&id=<?php echo $currentROW['id']; ?>" class="contact-form">
    <input type="hidden" name="id" value="<?php echo $currentROW['id']; ?>">

    <div class="form-row">
        <label class="form-label" for="lastname">Фамилия *:</label>
        <input type="text" name="lastname" id="lastname" class="form-field" value="<?php echo htmlspecialchars($currentROW['lastname'], ENT_QUOTES, 'UTF-8'); ?>" required>
    </div>

    <div class="form-row">
        <label class="form-label" for="firstname">Имя *:</label>
        <input type="text" name="firstname" id="firstname" class="form-field" value="<?php echo htmlspecialchars($currentROW['firstname'], ENT_QUOTES, 'UTF-8'); ?>" required>
    </div>

    <div class="form-row">
        <label class="form-label" for="patronymic">Отчество:</label>
        <input type="text" name="patronymic" id="patronymic" class="form-field" value="<?php echo htmlspecialchars($currentROW['patronymic'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>

    <div class="form-row">
        <label class="form-label" for="gender">Пол:</label>
        <select name="gender" id="gender" class="form-select">
            <option value="М" <?php echo ($currentROW['gender'] === 'М') ? 'selected' : ''; ?>>Мужской</option>
            <option value="Ж" <?php echo ($currentROW['gender'] === 'Ж') ? 'selected' : ''; ?>>Женский</option>
        </select>
    </div>

    <div class="form-row">
        <label class="form-label" for="birthdate">Дата рождения:</label>
        <input type="date" name="birthdate" id="birthdate" class="form-field" value="<?php echo htmlspecialchars($currentROW['birthdate'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>

    <div class="form-row">
        <label class="form-label" for="phone">Телефон:</label>
        <input type="text" name="phone" id="phone" class="form-field" value="<?php echo htmlspecialchars($currentROW['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>

    <div class="form-row">
        <label class="form-label" for="address">Адрес:</label>
        <input type="text" name="address" id="address" class="form-field" value="<?php echo htmlspecialchars($currentROW['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>

    <div class="form-row">
        <label class="form-label" for="email">E-mail:</label>
        <input type="email" name="email" id="email" class="form-field" value="<?php echo htmlspecialchars($currentROW['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>

    <div class="form-row">
        <label class="form-label" for="comment">Комментарий:</label>
        <textarea name="comment" id="comment" class="form-textarea"><?php echo htmlspecialchars($currentROW['comment'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
    </div>

    <input type="submit" name="button" value="Изменить запись" class="form-submit-btn">
</form>
