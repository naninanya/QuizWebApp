<?php
require('DBAdapter.php');
require('GameMaster.php');
require('Game.php');
session_start();
if (!isset($_SESSION["gameMaster"])) {
    header("Location: ../sessionError.php");
    exit();
}
if (!isset($_SESSION["game"])) {
    header("Location: ./sessionError.php");
    exit();
}
$gameMaster = unserialize($_SESSION['gameMaster']);
$game = unserialize($_SESSION['game']);

$sendName = $_POST['sendName'];
$answerName = $_POST['answerName'];
$answer = $_POST[$answerName];

if ($game->IsAlredyGameMasterAnswer($answerName)) {
    header("Location: ../" . $sendName . ".php");
    exit();
}

if (!$gameMaster->UpdateAnswerMasterTransaction($answerName, $answer)) {
    $_SESSION['note'] = "回答マスター更新処理が失敗しました。<br>再回答してください。";
    header("Location: ../" . $sendName . ".php");
    exit();
}

$_SESSION[$alredySendName] = true;
$_SESSION['note'] = "回答処理が終了しました。";
header("Location: ../" . $sendName . ".php");
exit();
