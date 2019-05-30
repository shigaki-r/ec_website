
<?php
require 'password.php';   // password_hash()はphp 5.5.0以降の関数のため、バージョンが古くて使えない場合に使用
// セッション開始
session_start();

$db['host'] = "localhost";  // DBサーバのURL
$db['user'] = "shigashan";  // ユーザー名
$db['pass'] = "shigashan";  // ユーザー名のパスワード
$db['dbname'] = "shigashan";  // データベース名

// エラーメッセージ、登録完了メッセージの初期化
$errorMessage = "";
$signUpMessage = "";

// ログインボタンが押された場合
if (isset($_POST["signUp"])) {
    // 1. ユーザIDの入力チェック
    if (empty($_POST["username"])) {  // 値が空のとき
        $errorMessage = 'ユーザーIDが未入力です。';
    } else if (empty($_POST["password"])) {
        $errorMessage = 'パスワードが未入力です。';
    } else if (empty($_POST["password2"])) {
        $errorMessage = 'パスワードが未入力です。';
    } else if (empty($_POST["yahoo_store"])) {
        $errorMessage = 'Yahooショッピング ストアアカウントが未入力です。';
    } else if (empty($_POST["yahoo_id"])) {
        $errorMessage = 'Yahooショッピング アプリケーションIDが未入力です。';
    } else if (empty($_POST["yahoo_key"])) {
        $errorMessage = 'Yahooショッピング APIシークレットキーが未入力です。';
    } else if (empty($_POST["yahoo_permission_code"])) {
        $errorMessage = 'Yahooショッピング 認可コードが未入力です。';
    } else if (empty($_POST["wowma_number"])) {
        $errorMessage = 'Wowma 会員番号が未入力です。';
    } else if (empty($_POST["wowma_api_key"])) {
        $errorMessage = 'Wowma APIキーが未入力です。';
    }

    if (!empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["password2"]) && $_POST["password"] === $_POST["password2"]
        && !empty($_POST["yahoo_store"]) && !empty($_POST["yahoo_id"]) && !empty($_POST["yahoo_key"]) && !empty($_POST["yahoo_permission_code"])
        && !empty($_POST["wowma_number"]) && !empty($_POST["wowma_api_key"])
    ){
        // 入力したユーザIDとパスワードを格納
        $username = $_POST["username"];
        $password = $_POST["password"];
        $yahoo_store = $_POST["yahoo_store"];
        $yahoo_id = $_POST["yahoo_id"];
        $yahoo_key = $_POST["yahoo_key"];
        $yahoo_permission_code = $_POST["yahoo_permission_code"];
        $wowma_number = $_POST["wowma_number"];
        $wowma_api_key = $_POST["wowma_api_key"];

        // 2. ユーザIDとパスワードが入力されていたら認証する
        $dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);

        // 3. エラー処理
        try {
            $pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

            $stmt = $pdo->prepare("INSERT INTO ec_UserData(name, password, yahoo_store, yahoo_id, yahoo_key, yahoo_permission_code, wowma_number, wowma_api_key) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute(array($username, password_hash($password, PASSWORD_DEFAULT), $yahoo_store, $yahoo_id, $yahoo_key, $yahoo_permission_code, $wowma_number, $wowma_api_key));  // パスワードのハッシュ化を行う（今回は文字列のみなのでbindValue(変数の内容が変わらない)を使用せず、直接excuteに渡しても問題ない）
            $userid = $pdo->lastinsertid();  // 登録した(DB側でauto_incrementした)IDを$useridに入れる

            $signUpMessage = '登録が完了しました。あなたのユーザー名は '. $username. ' です。パスワードは '. $password. ' です。';  // ログイン時に使用するIDとパスワード
        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー';
            // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
            echo $e->getMessage();
        }
    } else if($_POST["password"] != $_POST["password2"]) {
        $errorMessage = 'パスワードに誤りがあります。';
    }
}
?>

<!doctype html>
<html>
    <head>
            <meta charset="UTF-8">
            <title>新規登録</title>
    </head>
    <body>
        <h1>新規登録画面</h1>
        <form id="loginForm" name="loginForm" action="" method="POST">
            <fieldset>
                <legend>新規登録フォーム</legend>
                <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
                <div><font color="#0000ff"><?php echo htmlspecialchars($signUpMessage, ENT_QUOTES); ?></font></div>
                <label for="username">ユーザー名</label><input type="text" id="username" name="username" placeholder="ユーザー名を入力" value="<?php if (!empty($_POST["username"])) {echo htmlspecialchars($_POST["username"], ENT_QUOTES);} ?>">
                <br>
                <label for="password">パスワード</label><input type="password" id="password" name="password" value="" placeholder="パスワードを入力">
                <br>
                <label for="password2">パスワード(確認用)</label><input type="password" id="password2" name="password2" value="" placeholder="再度パスワードを入力">
                <br><br>
                Yahoo!ショッピングAPIの情報を入力してください
                <br>
                <label for="yahoo_store">ストアアカウント</label><input id="yahoo_store" name="yahoo_store" placeholder="ストアアカウントを入力">
                <br>
                <label for="yahoo_id">アプリケーションID</label><input id="yahoo_id" name="yahoo_id" placeholder="アプリケーションIDを入力">
                <br>
                <label for="yahoo_key">シークレットキー</label><input id="yahoo_key" name="yahoo_key" placeholder="シークレットキーを入力">
                <br>
                <label for="yahoo_permission_code">認可コード</label><input id="yahoo_permission_code" name="yahoo_permission_code" placeholder="認可コードを入力">
                <br><br>
                Wowma APIの情報を入力してください
                <br>
                <label for="wowma_number">会員番号</label><input id="wowma_number" name="wowma_number" placeholder="会員番号を入力">
                <br>
                <label for="wowma_api_key">APIキー</label><input id="wowma_api_key" name="wowma_api_key" placeholder="APIキーを入力">
                <br><br>
                オープンロジAPIの情報を入力してください
                <br>
                〜〜〜
                <br>
                〜〜〜
                <br>
                <input type="submit" id="signUp" name="signUp" value="新規登録">
            </fieldset>
        </form>
        <br>
        <form action="login.php">
            <input type="submit" value="戻る">
        </form>
    </body>
</html>