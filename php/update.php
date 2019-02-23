<?php
// この画面が直接表示された場合エラー画面に飛ばす
if (empty($_POST['action']) || empty($_POST['item_id'])) {
	$url = "error.php?err=unauthorized_access";
	header("location: ".$url, true, 301);
	exit;
}

$dsn = 'mysql: host=127.0.0.1; dbname=shino; charset=utf8mb4';
$driver_options = [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_EMULATE_PREPARES => false,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	];

try {
    $pdo = new PDO($dsn,'user1','pass1', $driver_options);

	$sql = "SELECT todo_item.name AS item_name,
					todo_user.name AS user_name,
					todo_item.expire_date AS term,
					todo_item.finished_date AS done
			 FROM todo_user JOIN todo_item ON todo_user.id = todo_item.user
			 WHERE todo_item.id = ".$_POST['item_id'];
	// 結果は1行だけ(・・・の筈)なのでfetch()で可
    $row = $pdo->query($sql)->fetch();

    // プルダウンリスト用に担当者一覧を別に取得
    $sql = "SELECT name FROM todo_user";
    $staffs = $pdo->query($sql)->fetchAll();
    //print_r($staffs);

} catch (PDOException $e) {
	// エラー発生時に壊れたHTML画面ではなくプレーンテキストでエラーメッセージのみ表示
    header('Content-Type: text/plain; charset=UTF-8mb4', true, 500);
    exit($e->getMessage());
}

function hsc($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8'); //※mb4指定はエラー
}

// 「期限」を年／月／日に分解
$date = preg_split('/-/', $row['term']);
// 「完了」済みの作業であればデフォルトでチェックを付ける
$checked = ($row['done'] === null)?: 'checked="checked"';

$head_title = "更新画面";
$css_file = "regist.css";
require_once('head_template.php');
?>

<body>
	<h1>作業更新</h1>
<?php echo "作業項目ID:".$_POST['item_id']." done:【".$row['done']."】"; ?>
</p>
	<div class="container">
		<form action="db_ctrl.php" method="GET">
			<div class="inputs-wrapper">
				<div class="titles">
					<ul>
						<li>項目名</li>
						<li>担当者</li>
						<li>期限</li>
						<li>完了</li>
					</ul>
				</div>
				<div class="inputs">
					<input class="tbox" type="text" name="item_name" value="<?=$row['item_name']?>">

					<select class="pulldown" name="user_name">
<?php foreach ($staffs as $staff):
	$selected = ($staff['name'] === $row['user_name'])? 'selected="selected"' : ''; ?> 
						<option value="<?=$staff['name'].'" '.$selected?>">
							<?=$staff['name']?>
						</option>
<?php endforeach ?>
					</select>

					<div class="terms-wrapper">
						<input class="term" type="text" name="year" value="<?=$date[0]?>">
						<p>／</p>
						<input class="term" type="text" name="month" value="<?=$date[1]?>">
						<p>／</p>
						<input class="term" type="text" name="day" value="<?=$date[2]?>">
					</div>
					<div class="chkbox-wrapper">
						<input class="chkbox" type="checkbox" name="finished_chk" <?=$checked?>>完了した
					</div>
				</div>	<!-- inputs(right-block) end -->
			</div> <!-- inputs-wrapper -->

			<div class="buttons-wrapper">		

<?php
// (予定) この下2つのボタン押下後の処理まだ未実装


?>
				<input class="btn" type="submit" value="更新" name="update">
				<input class="btn" type="submit" value="キャンセル" name="cancel">
			</div> <!-- buttons-wrapper -->
		</form>
	</div> <!-- container -->

</body>
</html>