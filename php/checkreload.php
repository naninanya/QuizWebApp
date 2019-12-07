<?php
require('DBAdapter.php');
require('Game.php');
session_start();
if (!isset($_SESSION["gameUser"]) && !isset($_SESSION["gameMaster"])) {
    header("Location: ../sessionError.php");
    exit();
}
if (!isset($_SESSION["game"])) {
    header("Location: ./sessionError.php");
    exit();
}
$game = unserialize($_SESSION["game"]);

$formID = $_POST["formID"]; //≒answer_name
$nextFormID = $_POST["nextFormID"];

if ($game->IsAlredyGameMasterAnswer($formID)) {
    header("Location: ../" . $nextFormID . ".php");
    exit();
}

$_SESSION['note'] = 'ゲームマスターは<br>まだ回答を投稿していません。';
header("Location: ../" . $formID . ".php");
exit();
