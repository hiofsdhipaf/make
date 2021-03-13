<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>簡易掲示板</title>
</head>
<body>
    <h1>簡易掲示板</h1>
    <?php
    // DB接続設定
    $dsn = 'データーベース名';//DB名は$dsuと名付ける
	$user = 'ユーザー名';//ユーザー名は$userと名付ける
	$password = 'パスワード';//パスワードは$passと名付ける
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	
	$sql = "CREATE TABLE IF NOT EXISTS Atable"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32) NOT NULL,"
	. "comment TEXT NOT NULL,"
	. "password TEXT NOT NULL,"
	. "DATETIME DATETIME"
	.");";
	$stmt = $pdo->query($sql);
    // 新規投稿
//もし名前(name)が空でなく、コメント(comment)の空ではなく、パスワードも(pass)も空でなく、編集番号(edit)が空なら
if(!empty($_POST["name"])&&!empty($_POST["comment"])&&!empty($_POST["pass"]) && empty($_POST["edit_num"])){
    $name=$_POST["name"];//名前を受け取り$nameと名付ける
    $comment=$_POST["comment"];//コメントを受け取り$commentと名付ける
    $password=$_POST["pass"];//パスワードを受け取り$passwordと名付ける
    //↓を$sqlに入れる
    //$sqlは$pdo　準備 Atabelに入力の処理をしていく：『名前、コメント、パスワード、時間』　
    //どの値を受け取るか決める；『名前、コメント、パスワード、時間』
	$sql = $pdo -> prepare("INSERT INTO Atable (name, comment,password,DATETIME) VALUES (:name, :comment,:password,cast(now() as datetime))");
	//bindParam：値の参照を受け取る   SQL CHAR, VARCHAR, または他の文字列データ型を表す。
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);//↑から$nameに送られてきたものを表示する
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);//から$commentに送られてきたものを表示する
	$sql -> bindParam(':password', $password, PDO::PARAM_STR);//から$$passwordに送られてきたものを表示する
	$sql -> execute();//execute関数：PHPの標準関数でプリペアドステートメントを実行する際に使われる関数
}
    //削除処理
//もし削除(delete)が空ではなく、削除パスワード(delete_passs)が空ではなかったなら
if(!empty($_POST["delete"])&&!empty($_POST["delete_pass"])){
    $delete=$_POST["delete"];//削除データを受け取りと$delete名付ける
    $deletepass=$_POST["delete_pass"];//削除パスワードデータを受け取りdeletepassと名付ける
    //$sqlは$pdo　削除しますよAtableのid(投稿番号)とid(削除対象番号)が同じで投稿パスワードと削除パスワードが同じなら
	$sql = $pdo -> prepare("delete from Atable where id=:id and password=:password");
	$sql -> bindParam(':id', $delete, PDO::PARAM_INT);//idを$deleteから受け取って表示
	$sql -> bindParam(':password', $deletepass, PDO::PARAM_STR);//passwordを$deletepassから受け取って表示
	$sql -> execute();//要するに実行
}
    //編集処理
//もし編集が空でないなく、変種番号が空でないなら
if(!empty($_POST["edit"])&&!empty($_POST["edit_pass"])){
    $edit=$_POST["edit"];//編集データを受け取る
    $edit_pass=$_POST["edit_pass"];//編集パスワードを受け取る
    $sql = $pdo -> prepare("SELECT * FROM Atable where id=:id and password=:password");
	$sql -> bindParam(':id', $edit, PDO::PARAM_INT);
	$sql -> bindParam(':password', $edit_pass, PDO::PARAM_STR);
	$sql -> execute();
    $editresults = $sql ->fetchAll();//$sqlの内容を全て受け取るり、$editresultにいれこむ
        foreach ($editresults as $editresult){
            $edit_num = $editresult[0];
            $edit_name = $editresult[1];
            $edit_comment = $editresult[2];
            $edit_password = $editresult[3];
    }   
}
    //編集投稿機能
//もし編集番号が空ではなく、名前が空ではなく、コメントが空ではなく、パスワードが空ではない場合
if(!empty($_POST["edit_num"]) && !empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"])){
    $id = $_POST["edit_num"];//編集番号を$idに入れる。
    $name = $_POST["name"];//名前を$nameに入れる
    $comment = $_POST["comment"];//コメントを$commentに入れる
    $pass = $_POST["pass"];//パスワードを$passに入れる
    //アップデートするAtableを
    //準備：namaeと:nameが同じだったとき、commentと:commentが等しかったとき、DTATETIME(日時)と：DTAETIMEが等しく
    //      id(投稿番号)と:id(投稿番号)が同じでパスワードとパスワードが同じなら
    $sql = "UPDATE Atable SET name=:name,comment=:comment,DATETIME=cast(now() as datetime) WHERE id=:id and password=:password";
	$sql = $pdo->prepare($sql);
	$sql->bindParam(':name', $name, PDO::PARAM_STR);//から$nameに送られてきたものを表示する
	$sql->bindParam(':comment', $comment, PDO::PARAM_STR);//から$commentに送られてきたものを表示する
	$sql->bindParam(':id', $id, PDO::PARAM_INT);//から$idに送られてきたものを表示する
	$sql->bindParam(':password', $pass, PDO::PARAM_STR);//から$passwordに送られてきたものを表示する
	$sql->execute();
}   
?>
    <!--入力フォーム-->
    <form action="" method="post" name="write">
        <input type="text" name="name" placeholder="名前" value="<?php if(isset($edit_name)){echo $edit_name;} ?>" required><br>
        <input type="text" name="comment" placeholder="コメント"  size="50" value="<?php if(isset($edit_comment)){echo $edit_comment;} ?>" required>
        <input type="hidden" name="edit_num" value="<?php if(isset($edit_num)){echo $edit_num;} ?>">
        <br>
        <input type ="password" name ="pass" placeholder ="パスワード"  value="<?php if(isset($edit_password)){echo $edit_password;} ?>" required>
        <input type="submit" name="submit">    
    </form>
    <!--削除フォーム-->
    <form action="" method="post">
        <input type="number" name="delete" placeholder="削除対象番号">
        <br>
        <input type ="password" name ="delete_pass" placeholder ="パスワード">
        <input type="submit" name="submit2" value="削除">
    </form>
    <!--編集フォーム-->
    <form action="" method="post">
        <input type="number" name="edit" placeholder="編集対象番号">
        <br>
        <input type ="password" name ="edit_pass" placeholder ="パスワード">
        <input type="submit" name="submit3" value="編集">
    </form>
    <!--DBのテーブルの中身をid毎に表示-->
<?php
$sql = 'SELECT * FROM Atable';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo  "投稿番号:".$row["id"]."<br>";
		echo  "名前:".$row["name"]."<br>";
		echo  "コメント:".$row["comment"]."<br>";
		echo  "日時:".$row["DATETIME"]."<br>";
    	echo "<hr>";
	}

?>
 </body>
 </html>
