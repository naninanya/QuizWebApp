<?php
session_start();
session_destroy();
?>

<head>
    <title>
        セッションリセット
    </title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta charset="utf-8">
</head>

<body>
    <center>
        <font size="5">
            <p>意図的にセッションをリセットしました。</p>
            <a href="index.php">
                <button type="button">トップページに戻る</button>
            </a>
        </font>
    </center>

</body>

</html>