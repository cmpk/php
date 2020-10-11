<?php
function create_pdo() {
    $pdo = new PDO(
        'mysql:dbname=questionnaire;host=localhost;charset=utf8mb4',
        'php',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    return $pdo;
}

function is_nullempty($val) {
    if (is_null($val)) {
        return true;
    }
    if ($val === '') {
        return true;
    }
    return false;
}
?>