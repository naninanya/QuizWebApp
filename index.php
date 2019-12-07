<?php
session_start();
?>
<html>

<head>
    <title>ようこそ</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
</head>

<body>
    <center>
        <?php
        if (isset($_SESSION['note'])) {
            $note = $_SESSION['note'];
            print "<p>{$note}</p>";
            unset($_SESSION['note']);
        }
        ?>
        <div class="main">
            <p class="sign">hogehoge<br>クイズダービー</p>
            <form class="form1" action="php/do_login.php" method="post">
                <input class="un" type="text" placeholder="&#xf007; 名前：任意" name="nickName" autofocus><br>
                <input class="pass" type="password" placeholder="&#xf084; パスワード：幹事より周知" name="pass"><br>
                <input type="submit" class="submit" value=" 参加する ">
            </form>
        </div>
    </center>
</body>

</html>