<?php
  require('functions.php');

  $shop_id = isset($_POST['shop']) ? trim($_POST['shop']) : null;
  $item = isset($_POST['item']) ? trim($_POST['item']) : null;
  $flavour = isset($_POST['flavour']) ? trim($_POST['flavour']) : null;
  $opinion = isset($_POST['opinion']) ? trim($_POST['opinion']) : null;

  $shops = [];
  $is_invalid = false;
  $is_shop_invalid = false;
  $is_flavour_invalid = false;

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //*-- フォームから登録された場合 --*//

    // 必須チェック
    if (is_nullempty($shop_id) || is_nullempty($item) || is_nullempty($flavour)) {
      $is_invalid = true;
    }

    try {
      $pdo = create_pdo();

      // 値の妥当性チェック
      $is_shop_invalid = !is_shop_id_valid($pdo, $shop_id);
      $is_flavour_invalid = !is_flavour_valid($flavour);

      // 登録
      if (!$is_invalid && !$is_shop_invalid && !$is_flavour_invalid) {
        save($pdo, $shop_id, $item, $flavour, $opinion);
        header('Location:./save.html');
        exit();
      }
      
    } catch (PDOException $e) {
      // エラーが発生した場合は「500 Internal Server Error」を表示する。
      error_log($e->getMessage());
      http_response_code(500);
      include_once('./error.html');
      exit(); 
    }
  }
  
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
<script type="text/javascript">
if (<?php echo($is_invalid ? 'true' : 'false') ?>) {
  validate();
}
if (<?php echo($is_shop_invalid ? 'true' : 'false') ?>) {
  console.log('shop is invalid');
}
if (<?php print($is_flavour_invalid ? 'true' : 'false') ?>) {
  console.log('flavour is invalid');
}
</script>
<div id="wrapper">
<div id="container">

<h1>アンケート - 登録</h1>

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
                  if ($shop_id === $shop[id]) {
                    echo("<option value='$shop[id]' selected>$shop[name]</option>");
                  }
                  else {
                    echo("<option value='$shop[id]'>$shop[name]</option>");
                  }
                }
              ?>
            </select>
          </div>
        </dd>
      </div>
      <div>
        <dt><div><label for="item">ご注文いただいたメニュー</label></div></dt>
        <dd class="required"><div><input type="text" id="item" name="item" maxlength="50" value="<?php echo($item)?>" /></div></dd>
      </div>
      <div>
        <dt><div><label for="flavour">味のバランス</label></div></dt>
        <dd class="required">
          <div>
            <div class="radio_group">
              <div><input type="radio" id="flavour" name="flavour" value="1" <?php echo($flavour === 1 ? 'checked="checked"' : '')?>/>悪い</div>
              <div><input type="radio" id="flavour" name="flavour" value="3" <?php echo($flavour === 3 ? 'checked="checked"' : '')?>/>普通</div>
              <div><input type="radio" id="flavour" name="flavour" value="5" <?php echo($flavour === 5 ? 'checked="checked"' : '')?>/>良い</div>
            </div>
          </div>
        </dd>
      </div>
      <div>
        <dt><div><label for="opinion">ご意見</label></div></dt>
        <dd><div><textarea id="opinion" name="opinion" maxlength="500"><?php echo($opinion)?></textarea></div></dd>
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
