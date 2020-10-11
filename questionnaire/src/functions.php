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

function is_shop_id_valid($pdo, $shop_id) {
    $sql = 'SELECT count(id) As cnt FROM shops WHERE id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $shop_id, PDO::PARAM_INT);
    $stmt->execute();
    foreach($stmt as $rec) {
        if ($rec['cnt'] == 0) {
            return false;
        }
    }
    return true;
}

function is_flavour_valid($flavour) {
    if (in_array($flavour, array(1, 3, 5))) {
        return true;
    }
    return false;
}

function save($pdo, $shop_id, $item, $flavour, $opinion) {
    $sql = 'INSERT INTO questionnaires (shop_id, item, flavour, opinion) VALUES (:shop_id, :item, :flavour, :opinion)';
    $stmt = $pdo->prepare($sql);
    $params = array(':shop_id'=>$shop_id, ':item'=>$item, ':flavour'=>$flavour, ':opinion'=>$opinion);
    $stmt->execute($params);
}
?>