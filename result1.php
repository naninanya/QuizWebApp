<?php
require('php/DBAdapter.php');
require('php/GameUser.php');
require('php/GameMaster.php');
require('php/Game.php');
require('php/GraphParameter.php');
session_start();
if (!isset($_SESSION["gameUser"]) && !isset($_SESSION["gameMaster"])) {
    header("Location: ./sessionError.php");
    exit();
}
if (!isset($_SESSION["game"])) {
    header("Location: ./sessionError.php");
    exit();
}
$game = unserialize($_SESSION["game"]);
?>
<html>

<head>
    <title>結果1回目</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/tab3.css">
    <link rel="stylesheet" type="text/css" href="css/radiobutton.css">
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <link rel="stylesheet" type="text/css" href="css/table.css">
    <link rel="stylesheet" type="text/css" href="css/select.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
    <script type="text/javascript">
        function submitChk() {
            return window.confirm('次の投票に移動します。よろしいですか？');
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
        if (isset($_SESSION['gameMaster']))
            print " <a href=\"./admin.php\"> 管理者メニューに戻る </a><br><br>";
        ?>
        <div class="tabs">
            <input id="programming" type="radio" name="tab_item" checked>
            <label class="tab_item" for="programming"><i class="fas fa-crown"></i> 順位</label>
            <input id="all" type="radio" name="tab_item">
            <label class="tab_item" for="all"><i class="fas fa-chart-line"></i> グラフ</label>
            <input id="design" type="radio" name="tab_item">
            <label class="tab_item" for="design"><i class="fas fa-forward"></i> 次へ</label>

            <div class="tab_content" id="programming_content">
                <div class="tab_content_description">
                    <form action="php/reload.php" method="post" class="reload">
                        <input type="hidden" name="formID" value="result1">
                        <input type="submit" class="submit" value=" 更新 ">
                    </form>
                    <?php
                    $answerName = 'answer1';
                    print "<table>";
                    print "<thead><tr>";
                    print "<td><i class=\"fas fa-crown\"></i><br>順位</td>";
                    print "<td><i class=\"fas fa-user\"></i><br>名前</td>";
                    print "<td><i class=\"far fa-calendar-check\"></i><br>得点</td>";
                    print "<td><i class=\"fas fa-chart-bar\"></i><br>変動</td>";
                    print "</tr></thead>";
                    print "<tbody>";
                    $rank = 1;
                    foreach ($game->GetGameDataForTableWithChange($answerName) as $row) {
                        if (strcmp($row["my_session_id"], session_id()) == 0)
                            print '<tr class="myRow">';
                        else
                            print '<tr>';
                        print "<th class='rank'>{$rank}位</th>";
                        print "<td class=\"txt\">{$row["nickname"]}</td>";
                        print "<td class=\"price\">{$row["point"]}</td>";
                        if ($row["change"] >= 0)
                            print "<td class=\"up\"><i class=\"fas fa-arrow-up\" style=\"color:#FF0000\";></i>{$row["change"]}</td>";
                        else
                            print "<td class=\"down\"><i class=\"fas fa-arrow-down\" style=\"color:#1E90FF\";></i>{$row["change"]}</td>";
                        print '</tr>';
                        $rank++;
                    }
                    print "</tbody></table>";
                    ?>
                </div>
            </div>
            <div class="tab_content" id="all_content">
                <div class="tab_content_description">
                    <?php
                    $graphParameter = new GraghParameter();
                    print "<canvas id=\"myLineChart\" width=\"400\" height=\"450\"></canvas>";
                    print "<script src=\"https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js\"></script>";
                    print "<script>";
                    print "var ctx = document.getElementById(\"myLineChart\");";
                    print "var myLineChart = new Chart(ctx, {";
                    print "type: 'line',";
                    print "data: {";
                    print "labels: ['', '1回目', '2回目', '3回目'],";
                    print "datasets: [";
                    if (isset($_SESSION["gameUser"])) {
                        $gameUser = unserialize($_SESSION["gameUser"]);
                        $userData = $gameUser->GetPointAtAnswer($answerName);
                        $graphParameter->MergeData($userData);
                        print "{";
                        print "label: 'あなたのポイント',";
                        print "fill: false,"; //面の表示
                        print "lineTension: 0,"; //線のカーブ
                        print "data: {$userData},";
                        print "backgroundColor: \"rgba(75,192,192,0.4)\",";
                        print "borderColor: \"rgba(75,192,192,1)\","; //枠線の色
                        print "pointBorderColor: \"rgba(75,192,192,1)\","; //結合点の枠線の色
                        print "pointBackgroundColor: \"#fff\","; //結合点の背景色
                        print "pointRadius: 5,"; //結合点のサイズ
                        print "pointHoverRadius: 8,"; //結合点のサイズ（ホバーしたとき）
                        print "pointHoverBackgroundColor: \"rgba(75,192,192,1)\","; //結合点の背景色（ホバーしたとき）
                        print "pointHoverBorderColor: \"rgba(220,220,220,1)\","; //結合点の枠線の色（ホバーしたとき）
                        print "pointHitRadius: 15,"; //結合点より外でマウスホバーを認識する範囲（ピクセル単位）
                        print "},";
                    }
                    $averageData = $game->GetAverageAtAnswer($answerName);
                    $graphParameter->MergeData($averageData);
                    print "{";
                    print "label: '平均ポイント',";
                    print "fill: false,"; //面の表示
                    print "lineTension: 0,"; //線のカーブ
                    print "data: {$averageData},";
                    print "backgroundColor: \"rgba(179,181,198,0.2)\","; //背景色
                    print "borderColor: \"rgba(179,181,198,1)\","; //枠線の色
                    print "pointBorderColor: \"rgba(179,181,198,1)\","; //結合点の枠線の色
                    print "pointBackgroundColor: \"#fff\","; //結合点の背景色
                    print "pointRadius: 5,"; //結合点のサイズ
                    print "pointHoverRadius: 8,"; //結合点のサイズ（ホバーしたとき）
                    print "pointHoverBackgroundColor: \"rgba(179,181,198,1)\","; //結合点の背景色（ホバーしたとき）
                    print "pointHoverBorderColor: \"rgba(220,220,220,1)\","; //結合点の枠線の色（ホバーしたとき）
                    print "pointHitRadius: 15,"; //結合点より外でマウスホバーを認識する範囲（ピクセル単位）
                    print "}";
                    print "],";
                    print "},";
                    print "options: {";
                    print "title: {";
                    print "display: true,";
                    print "},";
                    print "scales: {";
                    print "yAxes: [{";
                    print "ticks: {";
                    print "suggestedMax: {$graphParameter->GetGraphMax()},";
                    print "suggestedMin: 0,";
                    print "stepSize: 50,";
                    print "callback: function(value, index, values) {";
                    print "return value + '点'";
                    print "}";
                    print "}";
                    print "}]";
                    print "},";
                    print "}";
                    print "});";
                    print "</script>";
                    ?>
                </div>
            </div>
            <div class="tab_content" id="design_content">
                <div class="tab_content_description">
                    <br><br><br><br><br><br>
                    <form action="php/reload.php" method="post" class="reload" onsubmit="return submitChk()">
                        <input type="hidden" name="formID" value="question2">
                        <input type="submit" class="submit" value=" 次へ ">
                    </form>
                </div>
            </div>
        </div>
    </center>
</body>

</html>