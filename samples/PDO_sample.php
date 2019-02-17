<?php
session_start();
/* PDOでMySQLに接続する場合の標準的なサンプルとして実際に動くコードの形で備忘録的に記述
	本番環境として提出するコードにここまでコメントは書かない
	※前提として「TODOリスト」ログイン状態で動作 */

// SQL文の条件として使用されるユーザー入力（この場合ログイン情報）を取得
$userdata = $_SESSION['username'];

// DSN:Data Source Name。hostはWindows環境では'localhost'よりIPの方が処理が速い？
// 文字セットはutf-8(utf8と記述)よりも、4バイトの絵文字も扱えるmb4が望ましい(但しmb4設定は一部非対応)
$dsn = 'mysql:dbname=shino;host=127.0.0.1;charset=utf8mb4';
//以下のような記述は全て不可。 dbnameの前後に空白を含めるとデータベース接続エラー発生
//$dsn = 'mysql: dbname=shino; host=..." "dbname = shino" "dbname =shino" "dbname= shino"
// 或いは以下のようにdbnameを2番目以降に順番変えるだけで何故かdbnameの前に空白を挟んでも通る
$dsn = 'mysql: host=127.0.0.1; dbname=shino; charset=utf8mb4'; // 結論：dbnameを先頭にしないのが無難

$dbuser = "user1"; // デフォルトは"root"
$dbuser_pass = "pass1"; // デフォルトは空白

// PDOコンストラクタの第4引数に接続時のオプションを連想配列で渡す(省略して後でsetAttribute()指定しても同じ)
$driver_options = 
array( 	// array関数を使わず [ 属性名1 => 属性値1, ... ]と書いても同じ
	// エラー発生時例外を投げる (※開発時必須)
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	// エミュレートを無効(処理は高速だが脆弱性のある動的プレースホルダ→よりセキュアな静的プレースホルダ)に変更
	PDO::ATTR_EMULATE_PREPARES => false
);

$sql = "SELECT todo_item.name AS subject, todo_user.id AS userID, todo_item.EXPIRE_DATE AS term
		 FROM todo_user JOIN todo_item ON todo_user.id = todo_item.user
		 WHERE todo_user.name = :username
		 ORDER BY expire_date ASC";

try {
	// DBH:データベースハンドラ ($pdoと書かれることが多い)
    $dbh = new PDO($dsn, $dbuser, $dbuser_pass, $driver_options);
    //ユーザー入力を含めたSQL文を生成しない場合は以下の1ステップでいきなり全件を2次元配列として取得してOK
    //$rows = $dbh->query($sql)->fetchAll(PDO::FETCH_ASSOC);

	/* fetch(), fetchAll()で引数が省略された場合や、ステートメントが直接foreachに渡された場合
		のフェッチモードのデフォルト設定をカラム名をキーとする連想配列で取得(省略時はFETCH_BOTH) */
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	
	// プリペアドステートメントを使用する場合の3ステップ
	$stmt = $dbh -> prepare($sql);
	$stmt->bindValue(":username", $userdata, PDO::PARAM_STR);
	$stmt->execute();
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    exit($e->getMessage());
}

// SQL文の検索結果のレコードを連想配列として格納
// よく見かけるが、取得した全件データを特に変更操作しないならfoeach($stmt->fetchAll() as $row)のfetchAll()は無駄
// 戻りが1行だけならwhileも無しのfetch()だけで問題ないし、 複数行ならそもそもPDOstatementをそのままforeachすれば良い。
$rows = $stmt->fetchAll();

function hsc($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

$head_title = "PDO利用サンプル";
$css_file = "defaultCSS.css";
require_once("../php/head_template.php");
?>

<body>
	<p><?= $userdata ?>さんの担当タスク一覧</p>
	<table style="border: inset 2px #cec">
		<tr style="border: solid">
			<th>項目名</th><th>ユーザーID</th><th>期限</th>
		</tr>
		<?php foreach($rows as $row): ?>
		<tr style="border: solid">
			<?php foreach ($row as $column): ?>
    		<td style="border: solid"><?= $column ?></td>
    		<?php endforeach ?>
		</tr>
		<?php endforeach ?>
	</table>
</body>
</html>