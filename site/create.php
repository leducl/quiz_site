<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) header('Location: login.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = $_POST['quiz_json'];
    $data = json_decode($json, true);
    if (!isset($data['title'], $data['questions']) || !is_array($data['questions'])) {
        $error = 'Format JSON invalide.';
    } else {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('INSERT INTO quizzes(title,author_id) VALUES(?,?)');
        $stmt->execute([$data['title'], $_SESSION['user_id']]);
        $qid = $pdo->lastInsertId();
        foreach ($data['questions'] as $q) {
            $stmt = $pdo->prepare('INSERT INTO questions(quiz_id,text) VALUES(?,?)');
            $stmt->execute([$qid, $q['text']]);
            $qqid = $pdo->lastInsertId();
            foreach ($q['options'] as $opt) {
                $stmt = $pdo->prepare('INSERT INTO options(question_id,label,text,is_correct) VALUES(?,?,?,?)');
                $stmt->execute([$qqid, $opt['id'], $opt['text'], $opt['isCorrect'] ? 1 : 0]);
            }
        }
        $pdo->commit();
        header('Location: quizzes.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Créer un QCM</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="box">
    <h2>Créer un QCM (JSON)</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post">
        <textarea id="quizFormat" name="quiz_json" rows="15" cols="80" placeholder='{
  "title":"Titre",
  "questions":[
    {"text":"?","options":[{"id":"A","text":"...","isCorrect":false},...]},... ]
}'>
        </textarea>
        <!-- Bouton pour copier le format JSON -->
        <button type="button" id="copyFormatBtn">Copier le format JSON</button>
        <!-- Bouton de publication -->
        <button type="submit">Publier</button>
    </form>
    <p><a href="quizzes.php">Retour</a></p>
</div>

<!-- Script pour la copie dans le presse-papiers -->
<script>
document.getElementById('copyFormatBtn').addEventListener('click', function() {
    const template = `{
  "title":"Titre",
  "questions":[
    {"text":"?","options":[{"id":"A","text":"...","isCorrect":false},...]},... ]
}`;
    navigator.clipboard.writeText(template).then(function() {
        alert('Format JSON copié dans le presse-papiers');
    }).catch(function(err) {
        console.error('Erreur de copie : ', err);
    });
});
</script>
</body>
</html>
