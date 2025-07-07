<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) header('Location: login.php');

$sql = "
SELECT q.id, q.title, u.username AS author,
  (SELECT COUNT(*) FROM questions WHERE quiz_id=q.id) AS total_questions,
  (SELECT score FROM scores
     WHERE user_id=? AND quiz_id=q.id
     ORDER BY taken_at DESC LIMIT 1) AS last_score
FROM quizzes q
JOIN users u ON q.author_id=u.id
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$quizzes = $stmt->fetchAll();
?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="assets/style.css"></head><body>
<h2>Liste des QCM</h2>
<p><a href="create.php">Créer un QCM</a> | <a href="logout.php">Déconnexion</a></p>
<table>
  <tr>
    <th>Titre</th><th>Auteur</th><th>Nb Q</th><th>Dernière note</th><th>Action</th>
  </tr>
  <?php foreach($quizzes as $q): ?>
  <tr>
    <td><?=htmlspecialchars($q['title'])?></td>
    <td><?=htmlspecialchars($q['author'])?></td>
    <td><?= $q['total_questions'] ?></td>
    <td><?= $q['last_score'] ?? '-' ?></td>
    <td><a href="quiz.php?id=<?=$q['id']?>">Passer</a></td>
  </tr>
  <?php endforeach; ?>
</table>
</body></html>