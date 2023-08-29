<!DOCTYPE html>
<meta charset="UTF-8">
<title>簡易掲示板</title>
<h1></h1>
<body>
    
    <h1><span style = "color:#003FFF">ひとこと掲示板</span></h1>
    
<?php
error_reporting(E_ALL & ~E_NOTICE);
//それぞれを変数代入
$id = null;
$name = $_POST["name"];
$contents = $_POST["comment"];
date_default_timezone_set('Asia/Tokyo');
$created_at = date("Y-m-d H:i:s");
$pass=$_POST['password'];//投稿パスワード
$pass2=$_POST['pass1'];//削除パスワード
$pass3=$_POST['pass2'];//編修パスワード
$editName="";
$editComment="";
$editNumber="";
// DB接続設定
$dsn = 'mysql:dbname=データベース名;host=localhost';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password ,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS test3"//新しいテーブルを作成
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
    . "comment TEXT,"
    . "datedata DATETIME,"
    . "password TEXT"
	.");";
    $stmt = $pdo->query($sql);
    
    
    //編集
  if(!empty($_POST['editNum'])&&!empty($_POST['pass2'])){
		
		$editNum=$_POST['editNum'];//変数に代入
		
    $sql = 'SELECT * FROM test3 WHERE id=:id ';//where句
    $edit = $pdo->prepare($sql);                 
    $edit->bindParam(':id', $editNum, PDO::PARAM_INT); 
    $edit->execute();                             // SQL実行
    $lines = $edit->fetchAll(); 
		
		foreach ($lines as $line){
		//$rowの中にはテーブルのカラム名が入る
    if($line['password']==$_POST['pass2']){
      $editName=$line['name'];
			$editComment=$line['comment'];
	    $editNumber=$line['id'];
    }
			
	
	}
  }
    
?>
    <!--新規投稿フォーム-->
<section>
    
    
     <h3>投稿</h3>
    <form action="" method="post">
    <input type="text" name="name" placeholder="名前"  value="<?php echo $editName;?>"><br><br>
        コメント：
    　　<p><textarea name="comment" rows="10" cols="50"><?php echo $editComment;?></textarea></p>
        <input type="text" name="password" placeholder="パスワード" >
        <input type="hidden" name="edit_post" value="<?php echo $editNumber;?>">
				<button type="submit">投稿</button>
    </form>
</section>

    <!--削除フォーム-->
    <h3>削除</h3>
<form action="" method="post">
        <input type="number" name="deleteNum" placeholder="削除対象番号">
        <input type="text" name="pass1" placeholder="パスワード" >
        <input type="submit" name="submit2" value="削除">
</form>

　　<!--編集フォーム-->
　　<h3>編集</h3>
<form action="" method="post">
        <input type="text" name="editNum" placeholder="編集対象番号">
        <input type="text" name="pass2" placeholder="パスワード" >
        <input type="submit" name="submit3" value="編集">
</form>
<hr>
<?php
	//投稿
	if(!empty($_POST['name']) && !empty($_POST["comment"])&& !empty($_POST['password'])){
		if(!empty($_POST["edit_post"])){
			$editkey=$_POST["edit_post"]; //変更する投稿番号
			
			$sql = 'UPDATE test3 SET name=:name,comment=:comment,password=:password WHERE id=:id';
			$edit1 = $pdo->prepare($sql);
			$edit1->bindParam(':name', $name, PDO::PARAM_STR);
			$edit1->bindParam(':comment', $contents, PDO::PARAM_STR);
      $edit1->bindParam(':password', $pass, PDO::PARAM_STR);
      $edit1->bindParam(':id', $editkey, PDO::PARAM_INT);
			$edit1->execute();
		}else{
			$regist = $pdo->prepare("INSERT INTO test3(id, name, comment, datedata,password) VALUES (:id,:name,:comment,:datedata,:password)");
			$regist->bindParam(":id", $id,PDO::PARAM_INT);
			$regist->bindParam(":name", $name,PDO::PARAM_STR);
			$regist->bindParam(":comment", $contents,PDO::PARAM_STR);
      $regist->bindParam(":datedata", $created_at,PDO::PARAM_STR);
      $regist->bindParam(":password", $pass,PDO::PARAM_STR);
			$regist->execute();

		}
	 
	}

  if(!empty($_POST['deleteNum'])&& !empty($_POST['pass1'])){
    // 削除対象番号入力→削除
   
    $deleteNum = $_POST['deleteNum'];
    $sql = 'SELECT * FROM test3 WHERE id=:id ';
    $delete = $pdo->prepare($sql);                  
    $delete->bindParam(':id', $deleteNum, PDO::PARAM_INT); 
    $delete->execute();                             // SQL実行
    $lines = $delete->fetchAll(); 
    foreach ($lines as $line){
      if($pass2==$line['password']){
       $sql = 'delete from test3 where id=:id';
       $del = $pdo->prepare($sql);
       $del->bindParam(':id', $deleteNum, PDO::PARAM_INT);
       $del->execute();
      }
    }
  } 
  
 

	$sql = 'SELECT * FROM test3';
	$st = $pdo->query($sql);
	$results = $st->fetchAll();
foreach($results as $loop){
		
		// 投稿番号が削除対象番号でない場合に投稿詳細を表示させる
		if ($loop['id'] != '') {
				echo  "投稿番号:".$loop['id']."<br>" ;
				echo "名前:".$loop['name']."<br>";
				echo "コメント:".$loop['comment']."<br>";
				echo "投稿日時:".$loop['datedata']."<br>";
				echo "<hr>";
		}
}	
?>



</body>
</html>