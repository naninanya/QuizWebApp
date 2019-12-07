<?php
require('php/DBAdapter.php');
require('php/GameMaster.php');
require('php/Game.php');
session_start();
if (!isset($_SESSION["gameMaster"])) {
    header("Location: ./sessionError.php");
    exit();
}
if (!isset($_SESSION["game"])) {
    header("Location: ./sessionError.php");
    exit();
}
$gameMaster = unserialize($_SESSION['gameMaster']);
$game = unserialize($_SESSION["game"]);
?>
<html>

<head>
    <title>回答入力3回目</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/tab3.css">
    <link rel="stylesheet" type="text/css" href="css/radiobutton.css">
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <link rel="stylesheet" type="text/css" href="css/table.css">
    <link rel="stylesheet" type="text/css" href="css/chart.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
    <script type="text/javascript">
        function submitChk() {
            return window.confirm('3問目の回答です。回答してよろしいですか？回答を間違えると修正する術がありません。注意してください。');
        }
    </script>
</head>

<body>
    <center>
        <?php
        $answer_name = 'answer3';
        if (isset($_SESSION['note'])) {
            $note = $_SESSION["note"];
            print "<h4>${note}</h4>";
            unset($_SESSION['note']);
        }
        if ($game->IsAlredyGameMasterAnswer($answer_name))
            print "<h4>既に回答しています。</h4>";
        else
            print "<h4>まだ回答していません。</h4>";
        ?>
        <a href="./admin.php"> 管理者メニューに戻る </a><br><br>
        <div class="tabs">
            <input id="programming" type="radio" name="tab_item" checked>
            <label class="tab_item" for="programming"><i class="fas fa-chart-pie"></i>投票結果</label>
            <input id="all" type="radio" name="tab_item">
            <label class="tab_item" for="all"><i class="fas fa-crown"></i>順位</label>
            <input id="design" type="radio" name="tab_item">
            <label class="tab_item" for="design"><i class="fas fa-vote-yea"></i>回答入力</label>

            <div class="tab_content" id="programming_content">
                <div class="tab_content_description">
                    <form action="php/reload.php" method="post" class="reload">
                        <input type="hidden" name="formID" value="send3">
                        <input type="submit" class="submit" value=" 更新 ">
                    </form>
                    <?php
                    $game->GetCountGroupByAnswer($answer_name);
                    print "<div class=\"chart\">";
                    print "<canvas id=\"myChart\" class=\"paichart\" width=\"400\" height=\"450\"></canvas>";
                    print "<p>投票率<br>{$game->GetVotingRate()}%</p>";
                    print "<script src=\"https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js\"></script>";
                    print "</div>";
                    print "<script>";
                    print "new Chart(document.getElementById(\"myChart\"), {";
                    print "type: \"doughnut\",";
                    print "data: {labels: [\"A\", \"B\", \"C\", \"D\", \"ミニゲーム代表者\",\"未回答\"],";
                    print "datasets: [{" . $game->GetChartData() . ",";
                    print "backgroundColor: [\"rgb(251, 90, 72)\",\"rgb(83, 185, 235)\",\"rgb(102, 204, 51)\",\"rgb(255, 182, 43)\",\"rgb(224, 64, 251)\",\"rgb(134, 145, 152)\"]}]";
                    print "}";
                    print "});";
                    print "</script>";
                    ?>
                </div>
            </div>
            <div class="tab_content" id="all_content">
                <div class="tab_content_description">
                    <?php
                    $answer_name = 'answer3';
                    print "<table>";
                    print "<thead><tr>";
                    print "<td><i class=\"fas fa-user\"></i><br>名前</td>";
                    print "<td><i class=\"fas fa-vote-yea\"><br>投票</td>";
                    print "<td><i class=\"fas fa-coins\"></i><br>ベッド</td>";
                    print "</tr></thead>";
                    print "<tbody>";
                    foreach ($game->GetAnswerDataForTable($answer_name) as $row) {
                        if (strcmp($row["my_session_id"], session_id()) == 0)
                            print '<tr class="myRow">';
                        else if (strcmp($row["answer"], "*") == 0)
                            print '<tr class="miniGamerRow">';
                        else
                            print '<tr>';
                        print "<td class=\"txt\">{$row["nickname"]}</td>";
                        if (strcmp($row["answer"], "*") == 0)
                            print "<td class=\"txt\">代表者</td>";
                        else
                            print "<td class=\"txt\">{$row["answer"]}</td>";
                        print "<td class=\"price\">{$row["bill"]}</td>";
                        print '</tr>';
                    }
                    print "</tbody></table>";
                    ?>
                    <br>
                    <?php
                    print "<table>";
                    print "<thead><tr>";
                    print "<td><i class=\"fas fa-crown\"></i><br>順位</td>";
                    print "<td><i class=\"fas fa-user\"></i><br>名前</td>";
                    print "<td><i class=\"far fa-calendar-check\"></i><br>得点</td>";
                    print "</tr></thead>";
                    print "<tbody>";
                    $rank = 1;
                    foreach ($game->GetGameDataForTable() as $row) {
                        if (strcmp($row["my_session_id"], session_id()) == 0)
                            print '<tr class="myRow">';
                        else
                            print '<tr>';
                        print "<th class='rank'>{$rank}位</th>";
                        print "<td class=\"txt\">{$row["nickname"]}</td>";
                        print "<td class=\"price\">{$row["point"]}</td>";
                        print '</tr>';
                        $rank++;
                    }
                    print "</tbody></table>";
                    ?>
                </div>
            </div>
            <div class="tab_content" id="design_content">
                <div class="tab_content_description">
                    <h4>回答入力1回目</h4>
                    <h4>以下の項目から選択して、<br>回答を入力してください。</h4>
                    <h3>
                        <font color="red">!回答は一回きりです注意してください!</font>
                    </h3>
                    <form action="php/do_send_answer.php" method="post" class="answer3" onsubmit="return submitChk()">
                        <div class="radio">
                            <input type="radio" name="answer3" id="A" value="A" checked>
                            <label for="A" class="A">A</label>
                        </div>
                        <div class="radio">
                            <input type="radio" name="answer3" id="B" value="B">
                            <label for="B" class="B">B</label>
                        </div>
                        <div class="radio">
                            <input type="radio" name="answer3" id="C" value="C">
                            <label for="C" class="C">C</label>
                        </div>
                        <div class="radio">
                            <input type="radio" name="answer3" id="D" value="D">
                            <label for="D" class="D">D</label>
                        </div><br>
                        <input type="hidden" name="sendName" value="send3">
                        <input type="hidden" name="answerName" value="answer3">
                        <input type="submit" class="submit" value=" 回答入力する ">
                    </form>
                </div>
            </div>
        </div>
    </center>
</body>

</html>