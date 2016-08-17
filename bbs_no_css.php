<?php
    // ここにDBに登録する処理を記述する
    $dsn = 'mysql:dbname=oneline_bbs;host=localhost';
    $user = 'root';
    $password = '';
    $dbh = new PDO($dsn, $user, $password);
    $dbh->query('SET NAMES utf8');

    // 「つぶやく」ボタンが押された際に発動する
    if (!empty($_POST)) {
        // ① データを登録するプログラム
        echo 'データ登録処理発動';
        $nickname = $_POST['nickname'];
        $comment = $_POST['comment'];

        // フォームのバリデーション
        // ①エラーを保持するための$error配列を空で用意
        $error = array();
        // ②それぞれのフォームに対して必要なバリデーションを行う

        // count()は配列の要素数を返す
        // strlen()は文字数を返す
        // 全角文字は1文字で3バイト扱いされてしまい、strlen()だと
        // 日本語対応のシステムには向きません。
        // mb_strlen()を使用してUTF-8で文字コードを指定して使用します。
        echo '<br>';
        echo 'strlen = ' . strlen($nickname);
        echo '<br>';
        echo 'mb_strlen = ' . mb_strlen($nickname, 'UTF-8');
        if (mb_strlen($nickname, 'UTF-8') < 4) {
            // ニックネームが４文字以下だったらlengthメッセージを保持
            $error['nickname'] = 'length';
        }

        if ($nickname == '') {
            // ニックネームが空だったら、blankメッセージを保持
            $error['nickname'] = 'blank';
        }

        if ($comment == '') {
            // コメントが空だったら、blankメッセージを保持
            $error['comment'] = 'blank';
        }

        // ③もしバリデーション時に問題があれば$error配列にメッセージを保持
        // ④$error配列にひとつ以上メッセージが入っていれば登録せずに画面にエラーを出力
        if (count($error) == 0) {
            // ⑤$error配列が空であれば登録処理
            echo '<br>';
            echo '登録処理';
            echo '$errorのカウント = ' . count($error);
            // SQL文のNOW()関数を使用して現在日時を登録できる
            $sql = 'INSERT INTO `posts` (`nickname`, `comment`, `created`) VALUES ("' . $nickname . '", "' . $comment . '", NOW())';
            // SET句を使ったINSERT文
              // INSERT INTO `テーブル名` SET `カラム1`=値1, `カラム2`=値2
            $sql = 'INSERT INTO `posts` SET `nickname`="' . $nickname . '",
                                           `comment`="' . $comment . '",
                                           `created`=NOW()';
            // sprintf()関数を使用して文字列を連結させてSQL文を作成
            $sql = sprintf('INSERT INTO `posts` SET `nickname`="%s", `comment`="%s", `created`=NOW()',
                  $nickname,
                  $comment
              );
            echo '<br>';
            echo $sql;
            echo '<br>';

            $stmt = $dbh->prepare($sql);
            $stmt->execute(); // 登録処理
            // ページのリフレッシュ (postをgetで上書き)
            header('Location: bbs_no_css.php');
            // 再度getでbbs_no_css.phpにアクセスする
            exit(); // これ以下の処理を強制終了する
        } else {
            // 何もしない (厳密にはhtml部分にエラーを出す)
            echo '<br>';
            echo 'エラー処理';
            echo '$errorのカウント = ' . count($error);
        }
    }

    // $str = sprintf('私は%sです。テストの点数は%dでした。', "ネクシード", 100);
    // echo $str;

    // ② データを取得しHTMLと連携して表示するプログラム
    echo '<br>';
    echo 'データ取得表示処理発動';
    $sql = 'SELECT * FROM `posts` ORDER BY `created` DESC';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    // $stmtにDBの全件データが入っている
    // $stmtはobject型のデータなので、配列に変換して使用する必要がある
    // 配列に変換するためにFETCH_ASSOCを使用

    // DBとの接続を終了
    $dbh = null;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>セブ掲示版</title>
  <style>
    p.nickname {
      font-size: 14px;
      color: #4C4C4C;
      margin-bottom: 0px;
    }
    p span.created {
      font-size: 10px;
      color: #999999;
    }
    p.comment {
      font-size: 18px;
      margin-top: 0px;
    }
    span {
      color: red;
    }
  </style>
</head>
<body>
    <form method="post" action="bbs_no_css.php">
      <p>
        <input type="text" name="nickname" placeholder="nickname"><br>
        <?php
            if (!empty($error['nickname']) && $error['nickname'] == 'blank') {
                echo '<span>ニックネームを入力して下さい。</span>';
            }
            if (!empty($error['nickname']) && $error['nickname'] == 'length') {
                echo '<span>ニックネームは4文字以上で入力して下さい。</span>';
            }
        ?>
      </p>
      <p>
        <textarea type="text" name="comment" placeholder="comment"></textarea><br>
        <?php
            if (!empty($error['comment']) && $error['comment'] == 'blank') {
                echo '<span>コメントを入力して下さい。</span>';
            }
        ?>
      </p>
      <p><button type="submit" >つぶやく</button></p>
    </form>
    <!-- ここにニックネーム、つぶやいた内容、日付を表示する -->
    <?php while($record = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
    <!-- whileの開始カッコ { の変わりに : を使う -->

        <!-- 繰り返される処理 -->
        <p class="nickname">
          <?php echo $record['nickname']; ?>
          <span class="created">
            <?php echo $record['created']; ?>
          </span>
        </p>
        <p class="comment">
          <?php echo $record['comment']; ?>
        </p>
        <hr> <!-- 線を入れる -->
    <?php endwhile; ?> <!-- whileのとじカッコ } の変わりに endwhile; を使う -->
</body>
</html>






