<?php
  require('functions.php');
  $shops = [];
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // フォームから登録された
    $shop_id = trim($_POST['shop']);
    $item = trim($_POST['item']);
    $flavour = trim($_POST['flavour']);
    $opinion = trim($_POST['opinion']);
    
    // 登録
    try {
      $pdo = create_pdo();
      $sql = 'INSERT INTO questionnaires (shop_id, item, flavour, opinion) VALUES (:shop_id, :item, :flavour, :opinion)';
      $stmt = $pdo->prepare($sql);
      $params = array(':shop_id'=>$shop_id, ':item'=>$item, ':flavour'=>$flavour, ':opinion'=>$opinion);
      $stmt->execute($params);
    } catch (PDOException $e) {
      // エラーが発生した場合は「500 Internal Server Error」を表示する。
      error_log($e->getMessage());
      http_response_code(500);
      include_once('./error.html');
      exit(); 
    }

    header('Location:./save.html');
    exit();
  }
  else {
    // フォームの表示
    try {
      $pdo = create_pdo();
      $sql = 'SELECT id, name FROM shops';
      $shops = $pdo->query($sql);
    } catch (PDOException $e) {
      // エラーが発生した場合は「500 Internal Server Error」を表示する。
      error_log($e->getMessage());
      http_response_code(500);
      include_once('./error.html');
      exit(); 
    }
  }
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <script
    src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
    integrity="sha256-4+XzXVhsDmqanXGHaHvgh1gMQKX40OUvDEBTu8JcmNs="
    crossorigin="anonymous"></script>
  <script
    src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.2/dist/jquery.validate.min.js"></script>
  <script
    src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
    integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
    crossorigin="anonymous"></script>
  <script src="./js/create.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="./css/base.css">
  <link rel="stylesheet" href="./css/create.css">
</head>
<title>アンケートフォーム</title>
<body>
<div id="wrapper">
<div id="container">

<h1>アンケートフォーム</h1>

<div id="beginning_message">
  当レストランにご来店いただき、誠にありがとうございました。<br />
  より良いサービスのご提供に努めて参りたいと考えておりますので、<br />
  ぜひとも以下のアンケートにご協力をいただきますよう、<br />
  よろしくお願い申し上げます。
</div>

<div id="beginning_error_message">
  エラーがあります。入力内容を修正してください。
</div>

<form id="questionnaire" action="./create.php" method="post">
  <div>
    <dl>
      <div>
        <dt><div><label for="shop">店名</label></div></dt>
        <dd class="required">
          <div>
            <select id="shop" name="shop">
              <option value="" selected></option>
              <?php
                foreach ($shops as $shop) {
                  echo "<option value='$shop[id]'>$shop[name]</option>";
                }
              ?>
            </select>
          </div>
        </dd>
      </div>
      <div>
        <dt><div><label for="item">ご注文いただいたメニュー</label></div></dt>
        <dd class="required"><div><input type="text" id="item" name="item" maxlength="50" /></div></dd>
      </div>
      <div>
        <dt><div><label for="flavour">味のバランス</label></div></dt>
        <dd class="required">
          <div>
            <div class="radio_group">
              <div><input type="radio" id="flavour" name="flavour" value="1" />悪い</div>
              <div><input type="radio" id="flavour" name="flavour" value="3" />普通</div>
              <div><input type="radio" id="flavour" name="flavour" value="5" />良い</div>
            </div>
          </div>
        </dd>
      </div>
      <div>
        <dt><div><label for="opinion">ご意見</label></div></dt>
        <dd><div><textarea id="opinion" name="opinion" maxlength="500"></textarea></div></dd>
      </div>
    </dl>
  </div>

  <div class="button">
    <input id="saver" type="submit" value="登録" />
  </div>
</form>

</div>
</div>
</body>
</html>
