<?php
// ==============================================================================
// Модуль add.php: добавление новой записи в записную книжку
// Листинг В-1.6
// "После заполнения и отправки страница перезагружается и выводится та же форма
//  с надписью: 'Запись добавлена' (зеленым) или 'Ошибка: запись не добавлена' (красным)"
// ==============================================================================

require_once __DIR__ . '/db.php';
$msg = '';

// Обработка отправки формы добавления
if (isset($_POST['button']) && $_POST['button'] === 'Добавить запись') {
    $lastname   = trim($_POST['lastname'] ?? '');
    $firstname  = trim($_POST['firstname'] ?? '');
    $patronymic = trim($_POST['patronymic'] ?? '');
    $gender     = trim($_POST['gender'] ?? 'М');
    $birthdate  = trim($_POST['birthdate'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $comment    = trim($_POST['comment'] ?? '');

    if ($lastname !== '' && $firstname !== '') {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("INSERT INTO friends 
                (lastname, firstname, patronymic, gender, birthdate, phone, address, email, comment) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $lastname, $firstname, $patronymic, $gender, 
                $birthdate, $phone, $address, $email, $comment
            ]);
            $msg = '<div class="msg-success">Запись добавлена</div>';
        } catch (Exception $e) {
            $msg = '<div class="msg-error">Ошибка: запись не добавлена (' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . ')</div>';
        }
    } else {
        $msg = '<div class="msg-error">Ошибка: заполните обязательные поля (Фамилия и Имя)!</div>';
    }
}
?>

<h1>Добавление нового контакта</h1>
<?php echo $msg; ?>

<form name="form_add" method="post" action="index.php?p=add" class="contact-form">
    <div class="form-row">
        <label class="form-label" for="lastname">Фамилия *:</label>
        <input type="text" name="lastname" id="lastname" class="form-field" placeholder="Иванов" required>
    </div>

    <div class="form-row">
        <label class="form-label" for="firstname">Имя *:</label>
        <input type="text" name="firstname" id="firstname" class="form-field" placeholder="Иван" required>
    </div>

    <div class="form-row">
        <label class="form-label" for="patronymic">Отчество:</label>
        <input type="text" name="patronymic" id="patronymic" class="form-field" placeholder="Иванович">
    </div>

    <div class="form-row">
        <label class="form-label" for="gender">Пол:</label>
        <select name="gender" id="gender" class="form-select">
            <option value="М">Мужской</option>
            <option value="Ж">Женский</option>
        </select>
    </div>

    <div class="form-row">
        <label class="form-label" for="birthdate">Дата рождения:</label>
        <input type="date" name="birthdate" id="birthdate" class="form-field" value="2000-01-01">
    </div>

    <div class="form-row">
        <label class="form-label" for="phone">Телефон:</label>
        <input type="text" name="phone" id="phone" class="form-field" placeholder="+7 (999) 000-00-00">
    </div>

    <div class="form-row">
        <label class="form-label" for="address">Адрес:</label>
        <input type="text" name="address" id="address" class="form-field" placeholder="г. Москва, ул. ...">
    </div>

    <div class="form-row">
        <label class="form-label" for="email">E-mail:</label>
        <input type="email" name="email" id="email" class="form-field" placeholder="user@example.com">
    </div>

    <div class="form-row">
        <label class="form-label" for="comment">Комментарий:</label>
        <textarea name="comment" id="comment" class="form-textarea" placeholder="Краткая заметка о контакте"></textarea>
    </div>

    <input type="submit" name="button" value="Добавить запись" class="form-submit-btn">
</form>
