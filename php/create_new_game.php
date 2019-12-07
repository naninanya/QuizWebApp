<?php
require('GameMaster.php');
require('DBAdapter.php');
session_start();

if (!isset($_SESSION["gameMaster"])) {
    header("Location: ../sessionError.php");
    exit();
}
$gameMaster = unserialize($_SESSION["gameMaster"]);

session_regenerate_id();
//session_id()は即座に変更されるが、cookieについては遷移する際に自動で変更される。

if (!$gameMaster->CreateNewGameTransaction(session_id())) {
    $_SESSION['note'] = '新規ゲームの作成に失敗しました。';
    header("Location: ../admin.php");
    exit();
}
$_SESSION["gameMaster"] = serialize($gameMaster);
$_SESSION['note'] = '新規ゲームを作成しました。';
header("Location: ../admin.php");
