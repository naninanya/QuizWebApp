<?php
require('php/DBAdapter.php');
require('php/Game.php');
session_start();
if (!isset($_SESSION["gameUser"])) {
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
    <title>第2問－回答</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/tab.css">
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <link rel="stylesheet" type="text/css" href="css/table.css">
    <link rel="stylesheet" type="text/css" href="css/chart.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
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
        <div class="tabs">
            <input id="programming" type="radio" name="tab_item" checked>
            <label class="tab_item" for="programming"><i class="fas fa-chart-pie"></i> 投票結果</label>
            <input id="all" type="radio" name="tab_item">
            <label class="tab_item" for="all"><i class="fas fa-list"></i> 投票一覧</label>
            <div class="tab_content" id="programming_content">
                <div class="tab_content_description">
                    <form action="php/checkreload.php" method="post" class="reload">
                        <input type="hidden" name="formID" value="answer2">
                        <input type="hidden" name="nextFormID" value="result2">
                        <input type="submit" class="submit" value=" 更新 ">
                    </form>
                    <?php
                    $answer_name = 'answer2';
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
                </div>
            </div>
        </div>
    </center>
</body>

</html>