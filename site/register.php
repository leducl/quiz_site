<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username']);
    $p = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('INSERT INTO users(username,password_hash) VALUES(?,?)');
    $stmt->execute([$u,$p]);
    header('Location: login.php'); exit;
}
?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="assets/style.css"></head><body>
<h2>Inscription</h2>
<form method="post">
  <input name="username" required placeholder="Nom d'utilisateur"><br>
  <input type="password" name="password" required placeholder="Mot de passe"><br>
  <button>Créer compte</button>
</form>
<p><a href="login.php">J'ai déjà un compte</a></p>
</body></html>