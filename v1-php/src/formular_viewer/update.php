<?php
require_once __DIR__ . '/../classes/CDbAccess.php';
require_once __DIR__ . '/../classes/CFormular.php';

$db = new dbaccess();

function checkFieldBackend($input, $fieldName, $label)
{
    if ($input === "" && $fieldName !== "zip" && $fieldName !== "city" && $fieldName !== "birthdate" && $fieldName !== "address") 
    {
        return "$label darf nicht leer sein!";
    }
    else if (strlen($input) < 2 && ($fieldName === "firstName" || $fieldName === "lastName"))
    {
        return "$label muss mindestens 2 Zeichen haben!";
    }
    else if ($fieldName === "email" && $input !== "" && !filter_var($input, FILTER_VALIDATE_EMAIL))
    {
        return "Bitte geben Sie eine gültige E-Mail-Adresse ein!";
    }
    else if ($fieldName === "zip" && $input !== "" && !preg_match('/^[0-9]{5}$/', $input))
    {
        return "Die PLZ muss genau 5 Ziffern lang sein!";
    }
    else if (strlen($input) < 3 && $input !== "" && $fieldName === "city")
    {
        return "Die Stadt muss mindestens 3 Zeichen haben!";
    }
    else if ($fieldName === "birthdate" && $input !== "")
    {
        $parsedDate = DateTime::createFromFormat('Y-m-d', $input);
        $today = new DateTime('today');
    
        if (!$parsedDate || $parsedDate->format('Y-m-d') !== $input)
        {
            return "Ungültiges Datumsformat!";
        }
        else if ($parsedDate > $today) 
        {
            return "Das Geburtsdatum darf nicht in der Zukunft liegen!";
        }
    }

    return null;
}

$id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: list.php');
    exit;
}

$formular = new CFormular($id);

if ($formular->id <= 0) {
    header('Location: list.php');
    exit;
}

if (isset($_POST['submit']))
{
    $submittedPassword = $_POST['admin_password'] ?? '';

    if ($db->passwordcheck($submittedPassword) !== true)
    {
        exit('Falsches Passwort!');
    }
    
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName  = trim($_POST['lastName'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $address   = trim($_POST['address'] ?? '');
    $zipCode   = trim($_POST['zipCode'] ?? '');
    $city      = trim($_POST['city'] ?? '');
    $subject   = trim($_POST['subject'] ?? '');
    $message   = trim($_POST['message'] ?? '');

    $errors = [];

    $fieldsToCheck = [
        [$firstName, "firstName", "Vorname"],
        [$lastName,  "lastName",  "Nachname"],
        [$birthdate, "birthdate", "Geburtsdatum"],
        [$email,     "email",     "E-Mail-Adresse"],
        [$address,   "address",   "Adresse"],
        [$zipCode,   "zip",       "PLZ"],
        [$city,      "city",      "Stadt"],
        [$subject,   "subject",   "Betreff"],
        [$message,   "message",   "Nachricht"]
    ];

    foreach ($fieldsToCheck as $field) 
    {
        $err = checkFieldBackend($field[0], $field[1], $field[2]);
        if ($err !== null) 
        {
            $errors[] = $err;
        }
    }

    if (!empty($errors)) 
    {
        echo "<h1>Fehler im Formular</h1>";
        echo "<ul>";
        foreach ($errors as $error) 
        {
            echo "<li>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</li>";
        }
        echo "</ul>";
        exit;
    }    

    $formular->first_name   = $firstName;
    $formular->last_name    = $lastName;
    $formular->email        = $email;
    $formular->birthdateVal = !empty($birthdate) ? (new DateTime($birthdate))->format('Y-m-d') : null;
    $formular->address      = $address;
    $formular->plz          = $zipCode;
    $formular->city         = $city;
    $formular->subject      = $subject;
    $formular->message      = $message;

    $formular->save();

    header('Location: list.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Update</title>
    <link rel="stylesheet" href="styleu.css">
    <script src="scriptu.js" defer></script>
</head>
<body>
    <h1>Formular</h1>
    <form id="form" action="update.php?id=<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?>" method="POST" novalidate>
        <input type="hidden" name="id" value="<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?>">

        <label for="first-name">*Vorname:</label>
        <input type="text" name="firstName" id="first-name" value="<?= htmlspecialchars($formular->first_name, ENT_QUOTES, 'UTF-8') ?>">
        <span class="error-msg" id="error-first-name"></span><br><br>
        
        <label for="last-name">*Nachname:</label>
        <input type="text" name="lastName" id="last-name" value="<?= htmlspecialchars($formular->last_name, ENT_QUOTES, 'UTF-8') ?>">
        <span class="error-msg" id="error-last-name"></span><br><br>
        
        <label for="birthdate">Geburtsdatum:</label>
        <input type="date" name="birthdate" id="birthdate" value="<?= htmlspecialchars($formular->birthdateVal ?? '', ENT_QUOTES, 'UTF-8') ?>"><br><br>
        <span class="error-msg" id="error-birthdate"></span><br><br>
        
        <label for="email">*Email:</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($formular->email, ENT_QUOTES, 'UTF-8') ?>">
        <span class="error-msg" id="error-email"></span><br><br>
        
        <label for="address">Adresse:</label>
        <input type="text" name="address" id="address" value="<?= htmlspecialchars($formular->address, ENT_QUOTES, 'UTF-8') ?>"><br><br>
        
        <label for="zip-code">PLZ:</label>
        <input type="text" name="zipCode" id="zip" value="<?= htmlspecialchars($formular->plz, ENT_QUOTES, 'UTF-8') ?>">
        <span class="error-msg" id="error-zip"></span><br><br>
        
        <label for="city">Ort:</label>
        <input type="text" name="city" id="city" value="<?= htmlspecialchars($formular->city, ENT_QUOTES, 'UTF-8') ?>">
        <span class="error-msg" id="error-city"></span><br><br>
        
        <label for="subject">*Betreff:</label>
        <input type="text" name="subject" id="subject" value="<?= htmlspecialchars($formular->subject, ENT_QUOTES, 'UTF-8') ?>">
        <span class="error-msg" id="error-subject"></span><br><br>
        
        <p class="message-label">Nachricht:</p>
        <textarea id="message" name="message" rows="5" cols="40" placeholder="Schreiben Sie hier Ihre Nachricht..."><?= htmlspecialchars($formular->message, ENT_QUOTES, 'UTF-8') ?></textarea><br><br>
        
        <input type="hidden" name="admin_password" id="admin_password" value="">
        <input type="submit" value="Absenden" name="submit" id="submit-btn">
        <script>
            document.getElementById("submit-btn").addEventListener("click", function(event) 
            {
                const pw = prompt("Bitte Admin-Passwort eingeben:");
                if (!pw) {
                    event.preventDefault();
                    return;
                }

                document.getElementById("admin_password").value = pw;
            });
        </script>
    </form>
</body>
</html>