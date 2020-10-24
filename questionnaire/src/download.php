<?php
  require('functions.php');
  
  $file_name = 'questionnaires_' . date('YmdHi') . '.csv';
  $fp = fopen('php://output', 'w');
  fwrite($fp, "\xEF\xBB\xBF"); // BOM付きUTF-8（∵ CSV を Windows の Excel で開くため）
  fputcsv($fp, array('No', '店名', 'ご注文いただいたメニュー', '味のバランス', 'ご意見'), ',', '"'); // CSVのヘッダー

  try {
    $pdo = create_pdo();
    $sql = 'SELECT q.id AS id, s.name AS shop, q.item AS item, q.flavour AS flavour, q.opinion AS opinion FROM questionnaires q INNER JOIN shops s ON s.id=q.shop_id';
    $rec = $pdo->query($sql);
    foreach ($rec as $questionnaire) {
      fputcsv($fp, $questionnaire, ',', '"');
    }
  } catch (PDOException $e) {
    // エラーが発生した場合は「500 Internal Server Error」を表示する。
    error_log($e->getMessage());
    http_response_code(500);
    include_once('./error.html');
    exit(); 
  }
  
  header('Content-Type: application/octet-stream');
  header("Content-Disposition: attachment; filename={$file_name}");
  header('Content-Transfer-Encoding: binary');
?>