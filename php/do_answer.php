<?php
require('DBAdapter.php');
require('GameUser.php');
require('Game.php');
session_start();
if (!isset($_SESSION["gameUser"])) {
    header("Location: ../sessionError.php");
    exit();
}
if (!isset($_SESSION["game"])) {
    header("Location: ../sessionError.php");
    exit();
}
$gameUser = unserialize($_SESSION["gameUser"]);
$game =  unserialize($_SESSION["game"]);

$questionName = $_POST['questionName'];
$answerName = $_POST['answerName'];
$alredyAnswerName = 'already' . $answerName;
$answer = $_POST[$answerName];
$bill = $_POST['bill'];
$password = $_POST['pass'];


if ($game->IsAlredyGameMasterAnswer($answerName)) {
    $_SESSION['note'] = "既にゲームマスターは<br>回答を済ませています。<br>投票はできません。";
    $_SESSION[$alredyAnswerName] = true;
    header("Location: ../" . $answerName . ".php");
    exit();
}

if (!isset($_SESSION[$alredyAnswerName]) || $_SESSION[$alredyAnswerName] == false) {
    if (strcmp($answer, "x") == 0 && strcmp($password, "game") != 0) {
        $_SESSION['note'] = "ミニゲーム専用パスワードが違います。<br>再投票してください。";
        header("Location: ../" . $questionName . ".php");
        exit();
    }

    if (strcmp($answer, "x") == 0 && strcmp($password, "game") == 0) {
        if (!$gameUser->UpdateMiniGamerTransaction($answerName)) {
            $_SESSION['note'] = "ミニゲーム代表者の投票処理が失敗しました。<br>再投票してください。";
            header("Location: ../" . $questionName . ".php");
            exit();
        }
        $_SESSION[$alredyAnswerName] = true;
        header("Location: ../" . $answerName . ".php");
        exit();
    }

    if (!$gameUser->UpdateAnswerUserTransaction($answerName, $answer, $bill)) {
        $_SESSION['note'] = "投票処理が失敗しました。<br>再投票してください。";
        header("Location: ../" . $questionName . ".php");
        exit();
    }
    $_SESSION[$alredyAnswerName] = true;
    header("Location: ../" . $answerName . ".php");
    exit();
}

$_SESSION['note'] = 'すでに投票しています。';
header("Location: ../" . $answerName . ".php");
exit();
