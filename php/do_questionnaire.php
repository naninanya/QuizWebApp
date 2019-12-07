<?php
require('DBAdapter.php');
require('GameUser.php');

session_start();
if (!isset($_SESSION["gameUser"])) {
    header("Location: ../sessionError.php");
    exit();
}
$gameUser = unserialize($_SESSION["gameUser"]);
$contents = $_POST['contents'];


if (!isset($_SESSION['alreadyQuestionnaire']) || $_SESSION['alreadyQuestionnaire'] == false) {
    if (!$gameUser->InsertQuestionnaire($contents)) {
        $_SESSION['note'] = 'エラーが発生しました。<br>再投稿してください。';
        header("Location: ../result3.php");
        exit();
    }

    $_SESSION['alreadyQuestionnaire'] = true;
    $_SESSION['note'] = '投稿ありがとうございます！';
    header("Location: ../result3.php");
    exit();
}

$_SESSION['note'] = '既に投稿しています。';
header("Location: ../result3.php");
