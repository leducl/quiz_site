<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username']);
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$u]);
    $user = $stmt->fetch();
    if ($user && password_verify($_POST['password'], $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: quizzes.php'); exit;
    }
    $error = 'Identifiants invalides.';
}
?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="assets/style.css"></head><body>
<h2>Connexion</h2>
<?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
<form method="post">
  <input name="username" required placeholder="Nom d'utilisateur"><br>
  <input type="password" name="password" required placeholder="Mot de passe"><br>
  <button>Se connecter</button>
</form>
<p><a href="register.php">Créer un compte</a></p>
</body></html>