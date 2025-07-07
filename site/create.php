<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) header('Location: login.php');
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $json = $_POST['quiz_json'];
    $data = json_decode($json, true);
    // Valider format minimal
    if (!isset($data['title'],$data['questions'])||!is_array($data['questions']))
        $error='Format JSON invalide.';
    else {
      $pdo->beginTransaction();
      $stmt = $pdo->prepare('INSERT INTO quizzes(title,author_id) VALUES(?,?)');
      $stmt->execute([$data['title'],$_SESSION['user_id']]);
      $qid = $pdo->lastInsertId();
      foreach ($data['questions'] as $q) {
        $stmt = $pdo->prepare('INSERT INTO questions(quiz_id,text) VALUES(?,?)');
        $stmt->execute([$qid,$q['text']]);
        $qqid = $pdo->lastInsertId();
        foreach ($q['options'] as $opt) {
          $stmt = $pdo->prepare(
            'INSERT INTO options(question_id,label,text,is_correct) VALUES(?,?,?,?)'
          );
          $stmt->execute([$qqid,$opt['id'],$opt['text'],$opt['isCorrect']?1:0]);
        }
      }
      $pdo->commit();
      header('Location: quizzes.php'); exit;
    }
}
?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="assets/style.css"></head>
<body>
<h2>Créer un QCM (JSON)</h2>
<?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
<form method="post">
  <textarea name="quiz_json" rows="15" cols="80" placeholder='{
  "title":"Titre",
  "questions":[
    {"text":"?","options":[{"id":"A","text":"...","isCorrect":false},...]},... ]
}'></textarea><br>
  <button>Publier</button>
</form>
<p><a href="quizzes.php">Retour</a></p>
</body></html>