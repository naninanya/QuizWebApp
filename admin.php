<?php
require('php/DBAdapter.php');
require('php/GameMaster.php');
session_start();
if (!isset($_SESSION["gameMaster"])) {
    header("Location: ./sessionError.php");
    exit();
}
$gameMaster = unserialize($_SESSION['gameMaster']);

echo '現在のsession_id : ' . $gameMaster->GetMySessionID() . '<br>';
echo 'DBのsession_id: ' . $gameMaster->GetGameMasterSessionID() . '<br>';
?>
<html>

<head>
    <title>管理者画面</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/tab.css">
    <link rel="stylesheet" type="text/css" href="css/radiobutton.css">
    <script type="text/javascript">
        function submitChk() {
            return window.confirm('新規作成しますか？');
        }
    </script>
</head>

<body>
    <center>
        <?php
        if (isset($_SESSION['note'])) {
            $note = $_SESSION["note"];
            print "<h4>${note}</h4>";
            unset($_SESSION['note']);
        }
        ?>
        <h3>管理者用のページです。</h3>
        <h3>各ページに遷移できますが、必ずブラウザバックしてください。</h3>
    </center>
    <ol>
        <li>
            <p>新規ゲームを作成するには、下のボタンを押してください。</p>
            <p>gamemasterteテーブルのセッションIDを新しくすることで、これまでのgamedataに存在するセッションIDと紐づかないようにする仕組みである。</p>
            <p>新規ゲームを作成したあとから、ログインしたユーザーがゲームに参加したとみなされる。</p>
            <p>新規ゲームを作成したあとは、ログイン状態そのままで、答えの更新ができるようになる。</p>
            <form class="form1" action="php/create_new_game.php" method="post" onsubmit="return submitChk()">
                <input type="submit" class="submit" value=" 新規ゲームを作成する。 ">
            </form>
        </li><br>
        <li><a href=" ./information.php">説明のページに飛ぶ </a> </li> <br>
        <br>
        <li><a href="./send1.php">回答入力(1回目)</a></li><br>
        <li><a href="./result1.php">ミニゲーム結果(1回目)</a></li><br>
        <br>
        <li><a href="./send2.php">回答入力(2回目)</a></li><br>
        <li><a href="./result2.php">ミニゲーム結果(2回目)</a></li><br>
        <br>
        <li><a href="./send3.php">回答入力(3回目)</a></li><br>
        <li><a href="./result3.php">ミニゲーム結果(3回目)</a></li><br>
    </ol>
</body>

</html>