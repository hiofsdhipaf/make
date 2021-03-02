<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>

<body>
    <h1>簡易掲示板</h1>
    
    <?php
    // DB接続設定
    $dsn = 'データーベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
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
if(!empty($_POST["name"])&&!empty($_POST["comment"])&&!empty($_POST["pass"]) && empty($_POST["edit_num"])){
    $name=$_POST["name"];
    $comment=$_POST["comment"];
    $password=$_POST["pass"];
	$sql = $pdo -> prepare("INSERT INTO Atable (name, comment,password,DATETIME) VALUES (:name, :comment,:password,cast(now() as datetime))");
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindParam(':password', $password, PDO::PARAM_STR);
	$sql -> execute();
}
    //削除処理
if(!empty($_POST["delete"])&&!empty($_POST["delete_pass"])){
    $delete=$_POST["delete"];
    $deletepass=$_POST["delete_pass"];
	$sql = $pdo -> prepare("delete from Atable where id=:id and password=:password");
	$sql -> bindParam(':id', $delete, PDO::PARAM_INT);
	$sql -> bindParam(':password', $deletepass, PDO::PARAM_STR);
	$sql -> execute();
}
    //編集処理
if(!empty($_POST["edit"])&&!empty($_POST["edit_pass"])){
    $edit=$_POST["edit"];
    $edit_pass=$_POST["edit_pass"];
    $sql = $pdo -> prepare("SELECT * FROM Atable where id=:id and password=:password");
	$sql -> bindParam(':id', $edit, PDO::PARAM_INT);
	$sql -> bindParam(':password', $edit_pass, PDO::PARAM_STR);
	$sql -> execute();
    $editresults = $sql ->fetchAll();
        foreach ($editresults as $editresult){
            $edit_num = $editresult[0];
            $edit_name = $editresult[1];
            $edit_comment = $editresult[2];
            $edit_password = $editresult[3];
    }   
}
    //編集投稿機能
if(!empty($_POST["edit_num"]) && !empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"])){
    $id = $_POST["edit_num"];
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $pass = $_POST["pass"];
    $sql = "UPDATE Atable SET name=:name,comment=:comment,DATETIME=cast(now() as datetime) WHERE id=:id and password=:password";
	$sql = $pdo->prepare($sql);
	$sql->bindParam(':name', $name, PDO::PARAM_STR);
	$sql->bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql->bindParam(':id', $id, PDO::PARAM_INT);
	$sql->bindParam(':password', $pass, PDO::PARAM_STR);
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