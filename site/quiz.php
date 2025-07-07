<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT title FROM quizzes WHERE id = ?');
$stmt->execute([$id]);
$quiz = $stmt->fetch();
if (!$quiz) {
    exit('Quiz introuvable');
}

$sql = "
SELECT q.id AS qid, q.text, o.id AS oid,
       o.label, o.text AS otext, o.is_correct
FROM questions q
JOIN options o ON o.question_id = q.id
WHERE q.quiz_id = ?
ORDER BY q.id, o.label
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$data = $stmt->fetchAll();

$questions = [];
foreach ($data as $r) {
    $qid = $r['qid'];
    if (!isset($questions[$qid])) {
        $questions[$qid] = [
            'text'    => $r['text'],
            'options' => []
        ];
    }
    $questions[$qid]['options'][] = [
        'oid'        => $r['oid'],
        'label'      => $r['label'],
        'text'       => $r['otext'],
        'is_correct' => (bool)$r['is_correct']
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($quiz['title']) ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="box">
    <h2><?= htmlspecialchars($quiz['title']) ?></h2>
    <div id="quiz-header"></div>
    <div id="quiz-container"></div>
</div>
<script src="assets/app.js"></script>
<script>
    const questions = <?= json_encode(array_values($questions), JSON_UNESCAPED_UNICODE) ?>;
    const quizId    = <?= $id ?>;
    initQuiz(questions, quizId);
</script>
</body>
</html>
