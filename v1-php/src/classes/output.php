<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'CFormular.php';

$action = $_GET['action'] ?? '';

$response = match ($action) {
    'find_all'  => ['Formulare' => CFormular::find_all()],
    'all_id'  => ['Informations' => CFormular::find_person()],
    default => [
        'error' => 'Ungültige oder fehlende Action'
    ]
};

echo json_encode($response);