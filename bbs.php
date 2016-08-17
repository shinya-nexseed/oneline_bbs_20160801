<?php
    $dsn = 'mysql:dbname=oneline_bbs;host=localhost';
    $user = 'root';
    $password = '';
    $dbh = new PDO($dsn, $user, $password);
    $dbh->query('SET NAMES utf8');

    // 更新処理
    $record = array('nickname'=>'', 'comment'=>'');
    $btn_content = 'つぶやく';
    if (!empty($_GET['action']) && $_GET['action'] == 'edit') {
        $action = $_GET['action'];
        $id = $_GET['id'];
        $btn_content = '更新する';

        $sql = 'SELECT `nickname`, `comment` FROM `posts` WHERE `id`=' . $id;
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC); // データが1件の時はこの記述
    }

    if (!empty($_POST)) {
        $nickname = $_POST['nickname'];
        $comment = $_POST['comment'];

        $error = array();

        if (mb_strlen($nickname, 'UTF-8') < 4) {
            $error['nickname'] = 'length';
        }

        if ($nickname == '') {
            $error['nickname'] = 'blank';
        }

        if ($comment == '') {
            $error['comment'] = 'blank';
        }

        if (count($error) == 0) {
            // if文でCREATEのときとUPDATEの時を分け、$sqlを作る
            $sql = sprintf('INSERT INTO `posts` SET `nickname`="%s", `comment`="%s", `created`=NOW()',
                  $nickname,
                  $comment
              );

            $stmt = $dbh->prepare($sql);
            $stmt->execute();

            header('Location: bbs.php');
            exit();
        } else {

        }
    }

    $sql = 'SELECT * FROM `posts` ORDER BY `created` DESC';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    $dbh = null;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>セブ掲示版</title>

  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/bootstrap.css">
  <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="assets/css/form.css">
  <link rel="stylesheet" href="assets/css/timeline.css">
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
  <!-- ナビゲーションバー -->
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="#page-top"><span class="strong-title"><i class="fa fa-linux"></i> Oneline bbs</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <!-- Bootstrapのcontainer -->
  <div class="container">
    <!-- Bootstrapのrow -->
    <div class="row">

      <!-- 画面左側 -->
      <div class="col-md-4 content-margin-top">
        <!-- form部分 -->
        <form action="bbs.php" method="post">
          <!-- nickname -->
          <div class="form-group">
            <div class="input-group">
              <input type="text" name="nickname" class="form-control" id="validate-text" placeholder="nickname" value="<?php echo $record['nickname']; ?>" required>
              <span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
            </div>
          </div>
          <!-- comment -->
          <div class="form-group">
            <div class="input-group" data-validate="length" data-length="4">
              <textarea type="text" class="form-control" name="comment" id="validate-length" placeholder="comment" required><?php echo $record['comment']; ?></textarea>
              <span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
            </div>
          </div>
          <!-- つぶやくボタン -->
          <button type="submit" class="btn btn-primary col-xs-12" disabled><?php echo $btn_content; ?></button>
        </form>
      </div>

      <!-- 画面右側 -->

      <div class="col-md-8 content-margin-top">
        <div class="timeline-centered">
          <?php while($record = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <article class="timeline-entry">
                <div class="timeline-entry-inner">
                    <div class="timeline-icon bg-success">
                        <i class="entypo-feather"></i>
                        <a href="bbs.php?action=edit&id=<?php echo $record['id']; ?>">
                          <i class="fa fa-cogs"></i>
                        </a>
                    </div>
                    <div class="timeline-label">
                        <h2><a href="#"><?php echo $record['nickname']; ?></a> <span><?php echo $record['created']; ?></span></h2>
                        <p><?php echo $record['comment']; ?></p>
                    </div>
                </div>
            </article>
          <?php endwhile; ?>

          <article class="timeline-entry begin">
              <div class="timeline-entry-inner">
                  <div class="timeline-icon" style="-webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg);">
                      <i class="entypo-flight"></i> +
                  </div>
              </div>
          </article>
        </div>
      </div>


    </div>
  </div>

  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="assets/js/bootstrap.js"></script>
  <script src="assets/js/form.js"></script>
</body>
</html>



