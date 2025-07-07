<?php
require 'config.php';
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD']!=='POST') exit;
$quiz_id = (int)$_POST['quiz_id'];
$score = (int)$_POST['score'];
$stmt = $pdo->prepare('INSERT INTO scores(user_id,quiz_id,score) VALUES(?,?,?)');
$stmt->execute([$_SESSION['user_id'],$quiz_id,$score]);
echo json_encode(['status'=>'ok']);