<?php
require_once __DIR__ . '/../classes/CFormular.php';
ob_start();

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
    ob_end_flush();
    exit;
}

$formular = new CFormular([
    'first_name' => $firstName,
    'last_name'  => $lastName,
    'email'      => $email,
    'birthdate'  => $birthdate,
    'address'    => $address,
    'zip_code'   => $zipCode,
    'city'       => $city,
    'subject'    => $subject,
    'message'    => $message
]);

$formular->save();

header("Refresh: 2; url=index.html");
echo "<p>Data Successfully Saved! Forwarding in 2 seconds...</p>";
ob_end_flush();
?>