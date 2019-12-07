<?php
require('php/DBAdapter.php');
require('php/GameUser.php');
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
$gameUser = unserialize($_SESSION["gameUser"]);
$game = unserialize($_SESSION["game"]);
?>
<html>

<head>
    <title>投票1回目</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/tab.css">
    <link rel="stylesheet" type="text/css" href="css/radiobutton.css">
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <link rel="stylesheet" type="text/css" href="css/table.css">
    <link rel="stylesheet" type="text/css" href="css/select.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
    <script type="text/javascript">
        function submitChk() {
            var str = document.getElementById("bill").value;
            return window.confirm(str + 'ポイントをベッドしています。\r\n投票してよろしいですか？');
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
        print "<p>ようこそ！{$gameUser->GetNickName()}さん</p>";
        ?>
        <div class="tabs">
            <input id="programming" type="radio" name="tab_item" checked>
            <label class="tab_item" for="programming"><i class="fas fa-crown"></i> 順位</label>
            <input id="all" type="radio" name="tab_item">
            <label class="tab_item" for="all"><i class="fas fa-vote-yea"></i> 投票</label>
            <div class="tab_content" id="programming_content">
                <div class="tab_content_description">
                    <form action="php/reload.php" method="post" class="reload">
                        <input type="hidden" name="formID" value="question1">
                        <input type="submit" class="submit" value=" 更新 ">
                    </form>
                    <?php
                    print "<table>";
                    print "<thead><tr>";
                    print "<td><i class=\"fas fa-crown\"></i><br>順位</td>";
                    print "<td><i class=\"fas fa-user\"></i><br>名前</td>";
                    print "<td><i class=\"far fa-calendar-check\"></i><br>得点</td>";
                    print "<td><i class=\"fas fa-chart-bar\"></i><br>変動</td>";
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
                        print "<td class=\"price\">-</td>";
                        print '</tr>';
                        $rank++;
                    }
                    print "</tbody></table>";
                    ?>
                </div>
            </div>
            <div class="tab_content" id="all_content">
                <div class="tab_content_description">
                    <p><b>投票1回目</b></p>
                    <p>以下の項目から選択して、<br>投票を行ってください。</p>
                    <form action="php/do_answer.php" method="post" class="answer1" onsubmit="return submitChk()">
                        <div class="radio">
                            <input type="radio" name="answer1" id="A" value="A" checked>
                            <label for="A" class="A">A</label>
                        </div>
                        <div class="radio">
                            <input type="radio" name="answer1" id="B" value="B">
                            <label for="B" class="B">B</label>
                        </div>
                        <div class="radio">
                            <input type="radio" name="answer1" id="C" value="C">
                            <label for="C" class="C">C</label>
                        </div>
                        <div class="radio">
                            <input type="radio" name="answer1" id="D" value="D">
                            <label for="D" class="D">D</label>
                        </div>
                        <div class="radio">
                            <input type="radio" name="answer1" id="x" value="x">
                            <label for="x" class="x">ミニゲーム代表者</label>
                        </div>
                        <br>
                        <p>ベッドするポイントを<br>設定してください。</p>
                        <?php
                        $point = $gameUser->GetPoint();
                        print "<p>現在のポイントは{$point}です。</p>";
                        print "<div class=\"cp_ipselect cp_sl04\">";
                        print "<select style=\"font-size:12px\" align=\"center\" name=\"bill\" id=\"bill\">";
                        for ($i = 5; $i <= $point; $i += 5) {
                            print "<option value=\"{$i}\">{$i}ポイント</option>";
                        }
                        print "</select>";
                        print "</div>";
                        ?>
                        <p>ミニゲーム代表者は<br>幹事より専用パスワードを<br>聞いてください。</p>
                        <input class="pass" type="password" placeholder="&#xf084; 専用パスワード" name="pass"><br>
                        <input type="hidden" name="answerName" value="answer1">
                        <input type="hidden" name="questionName" value="question1">
                        <?php
                        if ($point > 0)
                            print "<input type=\"submit\" class=\"submit\" value=\" 投票する \">";
                        else
                            print "<p>ゲームオーバー<br>ポイントが0です。</p>";
                        ?>
                    </form>
                </div>
            </div>
        </div>
    </center>
</body>

</html>