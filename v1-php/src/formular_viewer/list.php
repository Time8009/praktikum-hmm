<?php
require_once __DIR__ . '/../classes/CDbAccess.php';
require_once __DIR__ . '/../classes/CFormular.php';

$db = new dbaccess();
$errorMessage = '';

$currentColumn = 'id';
$currentDirection = 'ASC';
$rows = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['change'])) {
        $selectedId = $_POST['selected'] ?? null;

        if ($selectedId === null) {
            $errorMessage = "Keine Spalte ausgewählt";
        } else {
            header('Location: update.php?id=' . urlencode($selectedId));
            exit();
        }
    }

    if (isset($_POST['delete'])) {
        $selectedId = $_POST['selected'] ?? null;
        $submittedPassword = $_POST['admin_password'] ?? '';

        if (!$db->passwordcheck($submittedPassword)) {
            exit('Falsches Passwort!');
        }

        if ($selectedId !== null) {
            $formular = new CFormular((int)$selectedId);
            $formular->delete();
            header('Location: list.php');
            exit();
        } else {
            $errorMessage = "Keine Spalte ausgewählt";
        }
    }

    if (isset($_POST['reset'])) {
        $submittedPassword = $_POST['admin_password'] ?? '';

        if (!$db->passwordcheck($submittedPassword)) {
            exit('Falsches Passwort!');
        }

        $db->truncate();
        header('Location: list.php');
        exit();
    }

    if (isset($_POST['search_submit'])) {
        $searchColumn = $_POST['search_column'] ?? 'first_name';
        $searchValue = trim($_POST['search_value'] ?? '');

        $rows = $db->search($searchColumn, $searchValue);
    }

    $currentColumn = $_POST['current_sort_col'] ?? 'id';
    $currentDirection = $_POST['current_sort_dir'] ?? 'ASC';

    if (isset($_POST['filter_column'])) {
        $button = $_POST['filter_column'];

        if ($button === $currentColumn) {
            $currentDirection = ($currentDirection === 'ASC') ? 'DESC' : 'ASC';
        } else {
            $currentColumn = $button;
            $currentDirection = 'ASC';
        }

        $rows = $db->filter($currentColumn, $currentDirection);
    }
}

if ($rows === null) {
    $rows = CFormular::find_all();
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Einträge</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php if (!empty($errorMessage)): ?>
        <p><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <dialog id="searchDialog">
        <form action="list.php" method="POST">
            <h3>Einträge durchsuchen</h3>
            <label for="search_column">Spalte wählen:</label><br>
            <select name="search_column" id="search_column">
                <option value="id">ID</option>
                <option value="first_name">Vorname</option>
                <option value="last_name">Nachname</option>
                <option value="birthdate">Geburtsdatum</option>
                <option value="email">E-Mail</option>
                <option value="address">Adresse</option>
                <option value="zip_code">PLZ</option>
                <option value="city">Stadt</option>
                <option value="subject">Betreff</option>
                <option value="message">Nachricht</option>
            </select>
            <br><br>
            <label for="search_value">Suchbegriff:</label><br>
            <input type="text" name="search_value" id="search_value" required>
            <br><br>
            <input type="submit" name="search_submit" value="Suchen">
            <button type="button" id="closeSearch">Abbrechen</button>
        </form>
    </dialog>

    <form action="list.php" method="POST">
    <input type="hidden" name="current_sort_col" value="<?= htmlspecialchars($currentColumn, ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="current_sort_dir" value="<?= htmlspecialchars($currentDirection, ENT_QUOTES, 'UTF-8') ?>">

    <table>
        <tr>
            <th><input type="button" name="search" id="openSearch" value="🔎"></th>
            <th>ID <button type="submit" name="filter_column" value="id">ˇ</button></th>
            <th>Vorname <button type="submit" name="filter_column" value="first_name">ˇ</button></th>
            <th>Nachname <button type="submit" name="filter_column" value="last_name">ˇ</button></th>
            <th>Geburtsdatum <button type="submit" name="filter_column" value="birthdate">ˇ</button></th>
            <th>E-Mail <button type="submit" name="filter_column" value="email">ˇ</button></th>
            <th>Adresse <button type="submit" name="filter_column" value="address">ˇ</button></th>
            <th>PLZ <button type="submit" name="filter_column" value="zip_code">ˇ</button></th>
            <th>Stadt <button type="submit" name="filter_column" value="city">ˇ</button></th>
            <th>Betreff <button type="submit" name="filter_column" value="subject">ˇ</button></th>
            <th>Nachricht <button type="submit" name="filter_column" value="message">ˇ</button></th>
        </tr>
        <?php foreach ($rows as $row): ?>
            <?php 
                $isObj = is_object($row);
                $id = $isObj ? $row->id : ($row['id'] ?? '');
                $firstName = $isObj ? $row->first_name : ($row['first_name'] ?? '');
                $lastName = $isObj ? $row->last_name : ($row['last_name'] ?? '');
                $birthdateVal = $isObj ? $row->birthdateVal : ($row['birthdate'] ?? '');
                $email = $isObj ? $row->email : ($row['email'] ?? '');
                $address = $isObj ? $row->address : ($row['address'] ?? '');
                $plz = $isObj ? $row->plz : ($row['zip_code'] ?? $row['plz'] ?? '');
                $city = $isObj ? $row->city : ($row['city'] ?? '');
                $subject = $isObj ? $row->subject : ($row['subject'] ?? '');
                $message = $isObj ? $row->message : ($row['message'] ?? '');
            ?>
        <tr>
            <td><input type="radio" name="selected" value="<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?>"></td>
            <td><?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= !empty($birthdateVal) ? date('d.m.Y', strtotime($birthdateVal)) : '' ?></td>
            <td><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($address, ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($plz, ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($city, ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <input type="hidden" name="admin_password" id="admin_password" value="">
    <input type="submit" value="Löschen" name="delete" id="delete">
    <script>
        document.getElementById("delete").addEventListener("click", function(event) 
        {
            const pw = prompt("Bitte Admin-Passwort eingeben:");
            if (!pw) {
                event.preventDefault();
                return;
            }

            document.getElementById("admin_password").value = pw;
        });
    </script>
    <input type="submit" value="Ändern" name="change" id="change">
    <input type="button" value="Tabelle Neuladen" id="reload">
    <script>
        document.getElementById("reload").addEventListener("click", function() 
        {
            window.location.href = window.location.pathname;
        });
    </script>
    <input type="submit" value="Tabelle Zurücksetzen" name="reset" id="reset">
    <script>
        document.getElementById("reset").addEventListener("click", function(event) 
        {
            const pw = prompt("Bitte Admin-Passwort eingeben:");
            if (!pw) {
                event.preventDefault();
                return;
            }

            document.getElementById("admin_password").value = pw;
        });

        const searchDialog = document.getElementById("searchDialog");
        document.getElementById("openSearch").addEventListener("click", function() {
            searchDialog.showModal();
        });
        document.getElementById("closeSearch").addEventListener("click", function() {
            searchDialog.close();
        });
    </script>
    <br>
    </form>
</body>
</html>