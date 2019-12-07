<?php
require('DBAdapter.php');
require('GameUser.php');
require('GameMaster.php');
require('Game.php');

session_start();

$nickName = $_POST["nickName"];
$password = $_POST["pass"];

if (strcmp($nickName, "admin") == 0 && strcmp($password, "admin") == 0) {
    if (isset($_SESSION["gameMaster"])) { //同セッションにおいて、二回目のログイン時
        header("Location: ../admin.php");
        exit();
    }
    if (strcmp($password, "admin") != 0) {
        header("Location: ../index.php");
        exit();
    }

    $gameMaster = new GameMaster($nickName, session_id());
    if ($gameMaster->ExistsGameMaster()) {
        //別セッションであるが、既にゲームマスターが存在する場合
        //既にゲームマスターが存在するならば、そのセッションIDでユーザーを管理する。
        $_SESSION['note'] = '既にゲームマスターは存在しています。';
        $game = new Game();
        $_SESSION["game"] = serialize($game);
        $_SESSION["gameMaster"] = serialize($gameMaster);
        header("Location: ../admin.php");
        exit();
    }

    if (!$gameMaster->CreateNewGameMasterTransaction()) {
        $_SESSION['note'] = 'ゲームマスターの作成に失敗しました。';
        header("Location: ../index.php");
        exit();
    }
    $game = new Game();
    $_SESSION["game"] = serialize($game);
    $_SESSION["gameMaster"] = serialize($gameMaster);
    header("Location: ../admin.php");
    exit();
}

if (strcmp($password, "iryo") == 0) {
    if (isset($_SESSION["gameUser"])) {
        $_SESSION['note'] = '既に同セッションで参加済みです。';
        header("Location: ../question1.php");
        exit();
    }

    $gameUser = new GameUser($nickName, session_id());
    if (!$gameUser->ExistsGameMaster()) {
        $_SESSION['note'] = "ゲームマスターがゲームを作成していません。";
        header("Location: ../index.php");
        exit();
    }

    $game = new Game();
    if ($game->IsAlredyGameMasterAnswer('answer1')) {
        $_SESSION['note'] = "既にゲームマスターは<br>第一問目の回答を済ませています。<br>今回のゲームには参加できません。";
        header("Location: ../index.php");
        exit();
    }

    if (!$gameUser->CreateNewGameUserTransaction()) {
        $_SESSION['note'] = 'ユーザーの作成に失敗しました。';
        header("Location: ../index.php");
        exit();
    }
    $_SESSION["game"] = serialize($game);
    $_SESSION["gameUser"] = serialize($gameUser);
    header("Location: ../information.php");
    exit();
}

$_SESSION['note'] = "パスワードが違います。";
header("Location: ../index.php");
