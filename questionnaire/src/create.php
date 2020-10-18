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

    $pdo = NULL;
    try {
      $pdo = create_pdo();

      // 値の妥当性チェック
      $is_shop_invalid = !is_shop_id_valid($pdo, $shop_id);
      $is_flavour_invalid = !is_flavour_valid($flavour);

      // 登録
      if (!$is_invalid && !$is_shop_invalid && !$is_flavour_invalid) {
        $pdo->beginTransaction();
        save($pdo, $shop_id, $item, $flavour, $opinion);
        $pdo->commit();

        header('Location:./save.html');
        exit();
      }
      
    } catch (PDOException $e) {
      // エラーが発生した場合は「500 Internal Server Error」を表示する。
      if (!is_null($pdo)) {
        $pdo->rollBack();
      }
      $pdo->rollBacl();

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
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, user-scalable=yes">
  <script
    src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
    integrity="sha256-4+XzXVhsDmqanXGHaHvgh1gMQKX40OUvDEBTu8JcmNs="
    crossorigin="anonymous"></script>
  <script
    src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.2/dist/jquery.validate.min.js"></script>
  <script src="./js/create.js" charset="utf-8"></script>
  <link rel="stylesheet" href="./css/base.css">
  <link rel="stylesheet" href="./css/base_sp.css">
  <link rel="stylesheet" href="./css/create.css">
  <link rel="stylesheet" href="./css/create_sp.css">
</head>
<title>アンケート</title>
<body>
<script type="text/javascript">
$(function(){
  if (<?php echo($is_invalid || $is_shop_invalid || $is_flavour_invalid ? 'true' : 'false') ?>) {
    console.log("<?php echo("flavour: $is_flavour_invalid, $flavour") ?>");
    validate();
  }
});
</script>
<div class="wrapper">
<div class="container">

<h1>アンケート - 登録</h1>

<div id="beginning-message">
  当レストランにご来店いただき、誠にありがとうございました。<br />
  より良いサービスのご提供に努めて参りたいと考えておりますので、<br />
  ぜひとも以下のアンケートにご協力をいただきますよう、<br />
  よろしくお願い申し上げます。
</div>

<div id="beginning-error-message">
  エラーがあります。入力内容を修正してください。
</div>

<form id="questionnaire" action="./create.php" method="post">
 <div>
    <dl>
      <div>
        <dt class="required"><div><label for="shop">店名</label></div></dt>
        <dd>
          <div>
            <select id="shop" name="shop">
              <option value="" selected></option>
              <?php
                foreach ($shops as $shop) {
                  $id = $shop['id'];
                  $name = $shop['name'];
                  if ($shop_id === $shop['id']) {
                    echo("<option value='$id' selected>$name</option>");
                  }
                  else {
                    echo("<option value='$id'>$name</option>");
                  }
                }
              ?>
            </select>
          </div>
        </dd>
      </div>
      <div>
        <dt class="required"><div><label for="item">ご注文いただいたメニュー</label></div></dt>
        <dd><div><input type="text" id="item" name="item" maxlength="50" value="<?php echo($item)?>" /></div></dd>
      </div>
      <div>
        <dt class="required"><div><label for="flavour">味のバランス</label></div></dt>
        <dd>
          <div>
            <div class="radio-group">
              <div>
                <input type="radio" id="flavour1" name="flavour" value="1" <?php echo($flavour == 1 ? 'checked="checked"' : '')?>/>
                <label for="flavour1">悪い</label>
              </div>
              <div>
                <input type="radio" id="flavour3" name="flavour" value="3" <?php echo($flavour == 3 ? 'checked="checked"' : '')?>/>
                <label for="flavour3">ふつう</label>
              </div>
              <div>
                <input type="radio" id="flavour5" name="flavour" value="1" <?php echo($flavour == 5 ? 'checked="checked"' : '')?>/>
                <label for="flavour5">良い</label>
              </div>
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
