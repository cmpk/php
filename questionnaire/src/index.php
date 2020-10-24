<?php
  require('functions.php');
  $questionnaires = [];
  try {
    $pdo = create_pdo();
    $sql = 'SELECT q.id AS id, s.name AS shop, q.item AS item, q.flavour AS flavour, q.opinion AS opinion FROM questionnaires q INNER JOIN shops s ON s.id=q.shop_id';
    $rec = $pdo->query($sql);
    foreach ($rec as $questionnaire) {
      $questionnaires[] = array (
        'id' => $questionnaire['id'],
        'shop' => $questionnaire['shop'],
        'item' => $questionnaire['item'],
        'flavour' => convertToJapanese($questionnaire['flavour']),
        'opinion' => $questionnaire['opinion']
      );
    }
  } catch (PDOException $e) {
    // エラーが発生した場合は「500 Internal Server Error」を表示する。
    error_log($e->getMessage());
    http_response_code(500);
    include_once('./error.html');
    exit(); 
  }

  function convertToJapanese(int $val) {
    switch($val) {
      case 1: return '悪い';
      case 3: return 'ふつう';
      case 5: return '良い';
    }
    return '';
  }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, user-scalable=yes">
  <script
    src="https://code.jquery.com/jquery-3.5.1.min.js"
    integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@riversun/sortable-table/lib/sortable-table.js"></script>
  <script src="./js/index.js" charset="utf-8"></script>
  <script>
    const data = <?php echo json_encode($questionnaires) ?>;
    $(function(){
      const sortableTable = new SortableTable();
      sortableTable.setTable(document.querySelector('#sortable-table'));
      sortableTable.setData(data);
    });
  </script>
  <link rel="stylesheet" href="./css/base.css">
  <link rel="stylesheet" href="./css/index.css">
</head>
<title>アンケート</title>
<body>
<div class="wrapper">
<div class="container">

<h1>アンケート</h1>

<div class="actions">
  <a href="./download.php"><button>CSVダウンロード</button></a>
  <button onClick="window.open('./create.php', '_blank')">アンケート登録</button>
</div>

<div>
  <table id="sortable-table" class="sortable-table">
    <thead>
      <tr>
        <th data-id="id" data-header>No</th>
        <th data-id="shop" id="shop" sortable>店名</th>
        <th data-id="item">ご注文いただいたメニュー</th>
        <th data-id="flavour" id="flavour" sortable>味のバランス</th>
        <th data-id="opinion">ご意見</th>
      </tr>
    </thead>
  </table>
</div>

</div>
</div>
</body>
</html>