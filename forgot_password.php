<?php
// ============================================================
// forgot_password.php — Reset diretto password (NO EMAIL)
// 1. L'utente inserisce email
// 2. Se esiste, può creare nuova password
// 3. La nuova password viene salvata nel DB
// 4. Redirect automatico a index.php
// ============================================================

session_start();
require_once __DIR__ . '/db.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: music.php');
    exit;
}

$step  = 'email'; // email | reset | done
$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pdo = getDB();

    // STEP 1: verifica email
    if (isset($_POST['check_email'])) {

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Inserisci un'email valida.";
        } else {

            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND is_active = TRUE LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                $_SESSION['reset_user_id'] = $user['id'];
                $_SESSION['reset_email']   = $email;
                $step = 'reset';
            } else {
                $error = "Account non trovato.";
            }
        }
    }

    // STEP 2: salva nuova password
    if (isset($_POST['reset_password'])) {

        if (empty($_SESSION['reset_user_id'])) {
            header("Location: forgot_password.php");
            exit;
        }

        $password        = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (strlen($password) < 6) {
            $error = "La password deve contenere almeno 6 caratteri.";
            $step = 'reset';
        } elseif ($password !== $confirmPassword) {
            $error = "Le password non coincidono.";
            $step = 'reset';
        } else {

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

            $stmt = $pdo->prepare("UPDATE users SET password = :pass WHERE id = :id");
            $stmt->execute([
                ':pass' => $hashedPassword,
                ':id'   => $_SESSION['reset_user_id']
            ]);

            unset($_SESSION['reset_user_id'], $_SESSION['reset_email']);

            header("Location: index.php?reset=success");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>🎶 My Music Studio — Reset Password</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body{font-family:Arial;background:#0e0e0e;color:#fff;display:flex;justify-content:center;align-items:center;height:100vh}
.card{background:#1a1a1a;padding:40px;border-radius:15px;width:100%;max-width:400px}
input{width:100%;padding:12px;margin:10px 0;border-radius:8px;border:1px solid #333;background:#111;color:#fff}
button{width:100%;padding:14px;border:none;border-radius:30px;background:#d6004c;color:#fff;font-weight:bold;cursor:pointer}
button:hover{opacity:.8}
.error{background:#2a0000;padding:10px;border-radius:8px;color:#ff4d4d;margin-bottom:15px}
.success{background:#002a00;padding:10px;border-radius:8px;color:#4caf50;margin-bottom:15px}
</style>
</head>
<body>

<div class="card">

<?php if ($step === 'email'): ?>

<h2>🔑 Recupera Password</h2>

<?php if ($error): ?>
<div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST">
<input type="email" name="email" placeholder="Inserisci la tua email" required>
<button type="submit" name="check_email">CONTINUA</button>
</form>

<?php elseif ($step === 'reset'): ?>

<h2>🔒 Nuova Password</h2>
<p style="font-size:14px;color:#aaa;">Account: <?= htmlspecialchars($_SESSION['reset_email']) ?></p>

<?php if ($error): ?>
<div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST">
<input type="password" name="password" placeholder="Nuova password" required>
<input type="password" name="confirm_password" placeholder="Conferma password" required>
<button type="submit" name="reset_password">SALVA PASSWORD</button>
</form>

<?php endif; ?>

<p style="text-align:center;margin-top:20px;">
<a href="index.php" style="color:#d6004c;">Torna al login</a>
</p>

</div>

</body>
</html>
