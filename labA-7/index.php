<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Беляков Игорь Романович, группа 241-3210. Лабораторная работа № А-7: Ввод массива</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        // Функция добавления новой строки в таблицу (Листинги А-7.1 и А-7.2)
        function addElement() {
            var table = document.getElementById("elements");
            var index = table.rows.length; // индекс новой строки (начиная с 0)

            var row = table.insertRow(index);

            // Колонка с номером (ключом) элемента массива слева
            var cellIndex = row.insertCell(0);
            cellIndex.className = "element-index";
            cellIndex.innerHTML = "Элемент " + index + ":";

            // Колонка с полем ввода
            var cellInput = row.insertCell(1);
            cellInput.innerHTML = '<input type="text" name="element' + index + '" class="array-input" placeholder="Число">';

            // Обновляем скрытое поле общего числа элементов
            document.getElementById("arrLength").value = table.rows.length;
        }
    </script>
</head>
<body>

    <!-- Шапка страницы (header) -->
    <header>
        <div class="header-left">
            <img src="img/logo.png" alt="Логотип Московского Политеха" class="header-logo">
            <div class="header-title">Лабораторная работа № А-7: Сортировка массивов</div>
        </div>
        <div class="header-info">
            <div><strong>Студент:</strong> Беляков Игорь Романович</div>
            <div><strong>Группа:</strong> 241-3210</div>
        </div>
    </header>

    <!-- Основной блок страницы (main) -->
    <main>
        <h1>Ввод числового массива для сортировки</h1>
        <p class="desc">
            Введите элементы массива. Чтобы добавить дополнительные элементы, нажмите кнопку «Добавить еще один элемент».
            При отправке результат откроется в новой вкладке.
        </p>

        <!-- Форма отправки данных на вторую страницу sort.php в новой вкладке (target="_blank") -->
        <form action="sort.php" method="post" target="_blank">
            <!-- Скрытое поле с количеством элементов массива -->
            <input type="hidden" name="arrLength" id="arrLength" value="5">

            <!-- Таблица элементов массива (изначально 5 элементов с произвольными числами) -->
            <table id="elements" class="form-table">
                <tr>
                    <td class="element-index">Элемент 0:</td>
                    <td><input type="text" name="element0" class="array-input" value="42"></td>
                </tr>
                <tr>
                    <td class="element-index">Элемент 1:</td>
                    <td><input type="text" name="element1" class="array-input" value="17"></td>
                </tr>
                <tr>
                    <td class="element-index">Элемент 2:</td>
                    <td><input type="text" name="element2" class="array-input" value="89"></td>
                </tr>
                <tr>
                    <td class="element-index">Элемент 3:</td>
                    <td><input type="text" name="element3" class="array-input" value="5"></td>
                </tr>
                <tr>
                    <td class="element-index">Элемент 4:</td>
                    <td><input type="text" name="element4" class="array-input" value="23"></td>
                </tr>
            </table>

            <!-- Селектор алгоритмов сортировки (все 6 опций по методичке) -->
            <div class="form-actions">
                <select name="algoritm" class="algo-select">
                    <option value="choice">Сортировка выбором</option>
                    <option value="bubble">Пузырьковый алгоритм</option>
                    <option value="shaker">Шейкерная сортировка</option>
                    <option value="insert">Сортировка вставками</option>
                    <option value="gnome">Алгоритм садового гнома</option>
                    <option value="shell">Алгоритм Шелла</option>
                    <option value="quick">Быстрая сортировка (QuickSort)</option>
                    <option value="builtin">Встроенная функция PHP (sort)</option>
                </select>

                <!-- Кнопка динамического добавления поля через JavaScript -->
                <button type="button" class="btn btn-secondary" onclick="addElement()">Добавить еще один элемент</button>

                <!-- Кнопка отправки формы на sort.php -->
                <button type="submit" class="btn btn-primary">Сортировать массив</button>
            </div>
        </form>
    </main>

    <!-- Подвал страницы (footer) -->
    <footer>
        <div>Московский политехнический университет &copy; 2026</div>
        <div>Страница ввода массива</div>
    </footer>

</body>
</html>
