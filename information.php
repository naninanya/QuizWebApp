<?php
session_start();
if (!isset($_SESSION["gameUser"]) && !isset($_SESSION["gameMaster"])) {
    header("Location: ./sessionError.php");
    exit();
}
?>

<head>
    <title>注意事項</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/tab3.css">
    <link rel="stylesheet" type="text/css" href="css/radiobutton.css">
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <link rel="stylesheet" type="text/css" href="css/table.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
</head>

<body>
    <div class="tabs">
        <input id="programming" type="radio" name="tab_item" checked>
        <label class="tab_item" for="programming"><i class="fas fa-question-circle"></i> ルール説明</label>
        <input id="all" type="radio" name="tab_item">
        <label class="tab_item" for="all"><i class="fas fa-exclamation-triangle"></i> 注意事項</label>
        <input id="design" type="radio" name="tab_item">
        <label class="tab_item" for="design"><i class="fas fa-info-circle"></i> その他</label>

        <div class="tab_content" id="programming_content">
            <div class="tab_content_description">
                本ゲームについて<br>
                <ol>
                    <li>このゲームは、カジノのように、皆さん自身に与えられたポイントの合計数を競います。</li><br>
                    <li>これからミニゲームを3回行いますので、ミニゲームの勝敗を予想して、ポイントをベッドしてください。</li><br>
                    <li>ミニゲームの勝敗予想が当たった場合、ベッドしたポイントが2倍になります。</li><br>
                    <li>逆に勝敗予想が外れた場合、ベッドしたポイントは没収になります。</li><br>
                    <li>3回のミニゲーム後にポイントが多い順に景品をプレゼントします。</li><br>
                    <li>ベッドするポイントによっては一発逆転が狙えますので、頑張って上位を目指してください。</li><br>
                </ol>
                ポイントについて<br>
                <ol>
                    <li>初期ポイント数は100点です。各ミニゲームには5ポイント単位でベッドするポイントを決定できます。</li><br>
                    <li>ポイントが0になった場合、ゲームオーバーとなります。 </li><br>
                </ol>
                ミニゲームについて<br>
                <ol>
                    <li>ミニゲームを行う人は、くじ引きで決定します。</li><br>
                    <li>ミニゲームを行う人は、その回の勝敗予想には参加せず、無条件で100ポイントを獲得します。</li><br>
                </ol>
            </div>
        </div>
        <div class="tab_content" id="all_content">
            <div class="tab_content_description">
                <ol>
                    <li>不要なブラウザバックはしないでください。一応、対策はしていますが、しないでください。</li><br>
                    <li>スマホのブラウザアプリは絶対に終了しないでください。
                        <p>ただし、<br>・ホーム画面に戻る<br>・スマホのロック</pF>
                            <p>上記の行為は行っても大丈夫です。</p>
                    </li><br>
                </ol>
            </div>
        </div>
        <div class="tab_content" id="design_content">
            <div class="tab_content_description">
                <ol>
                    <li>このWebアプリには、<br>コンティンジェンシープランは存在しません。</li><br>
                    <li>ゲームが続けられないようなバグや、障害が発生した場合、幹事主催のじゃんけん大会となります。</li><br>
                    <li>30人の同時接続が非常に怖い。DBのトランザクションも切りまくってるのでレスポンスも心配。頼むぞ...</li><br>
                </ol>
                <center>
                    <form action="php/reload.php" method="post" class="reload">
                        <?php

                        if (isset($_SESSION["gameMaster"])) {
                            print "<input type=\"hidden\" name=\"formID\" value=\"admin\">";
                            print "<input type=\"submit\" class=\"submit\" value=\" 管理者メニューに戻る \">";
                            exit();
                        }
                        print "<input type=\"hidden\" name=\"formID\" value=\"question1\">";
                        print "<input type=\"submit\" class=\"submit\" value=\" ゲームを始める \">";
                        ?>
                    </form>
                </center>
            </div>
        </div>
    </div>
</body>

</html>