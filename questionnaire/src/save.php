<?php
  $shop_id = $_POST['shop'];
  $item = $_POST['item'];
  $flavour = $_POST['flavour'];
  $opinion = $_POST['opinion'];

  require('pdo.php');
  try {
    $pdo = create_pdo();
  } catch (PDOException $e) {
    // エラーが発生した場合は「500 Internal Server Error」を表示する。
    error_log($e->getMessage());
    http_response_code(500);
    include_once('./error.html');
    exit(); 
  }
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="./css/base.css">
</head>
<title>アンケートフォーム</title>
<body>
<div id="wrapper">
<div id="container">

<div style="text-align: center;">
  <div style="font-size: large; margin-bottom: 20px;">アンケートを受け付けました。</div>
  <div style="margin-bottom: 10px;">ご協力いただき大変ありがとうございました。</div>
  <div><a href="./create.php">戻る</a></div>
</div>

</div>
</div>
</body>
</html>
