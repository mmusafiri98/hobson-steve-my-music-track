<?php
// ============================================================
//  index.php — Pagina di Login
//  Salva last_login_at e last_login_ip nella tabella users
// ============================================================
session_start();
require_once __DIR__ . '/db.php';

// Se già loggato → redirect diretto a music.php
if (!empty($_SESSION['user_id'])) {
    header('Location: music.php');
    exit;
}

$error   = '';
$success = '';

// ── Gestione form POST ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Compila tutti i campi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email non valida.';
    } else {
        try {
            $pdo  = getDB();
            $stmt = $pdo->prepare(
                'SELECT id, username, email, password_hash, role, is_active
                   FROM users
                  WHERE email = :email
                  LIMIT 1'
            );
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if (!$user) {
                $error = 'Credenziali non valide.';
            } elseif (!$user['is_active']) {
                $error = 'Account disabilitato. Contatta l\'amministratore.';
            } elseif (!password_verify($password, $user['password_hash'])) {
                $error = 'Credenziali non valide.';
            } else {
                // ── Login riuscito ──────────────────────────
                // Aggiorna last_login
                $ip   = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                $upd  = $pdo->prepare(
                    'UPDATE users
                        SET last_login_at = NOW(),
                            last_login_ip = :ip
                      WHERE id = :id'
                );
                $upd->execute([':ip' => substr($ip, 0, 45), ':id' => $user['id']]);

                // Regenera session ID (sicurezza)
                session_regenerate_id(true);

                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email']    = $user['email'];
                $_SESSION['role']     = $user['role'];

                header('Location: music.php');
                exit;
            }
        } catch (PDOException $e) {
            $error = 'Errore database: ' . $e->getMessage();
        }
    }
}

// Messaggio di successo se arrivato dalla registrazione
if (isset($_GET['registered'])) {
    $success = 'Registrazione completata! Ora puoi accedere.';
}
if (isset($_GET['logout'])) {
    $success = 'Logout effettuato con successo.';
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>🎶 My Music Studio — Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;600&display=swap');
:root{--red:#d6004c;--purple:#7b1fa2;--bg:#0e0e0e;--card:#191919;--border:#2a2a2a;--green:#4caf50;}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:#fff;min-height:100vh;display:flex;flex-direction:column;}

/* Header */
header{background:linear-gradient(135deg,var(--red),var(--purple));text-align:center;padding:48px 20px 40px;position:relative;overflow:hidden;}
header::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 60% 40%,rgba(255,255,255,.12) 0%,transparent 65%);}
header h1{font-family:'Bebas Neue',sans-serif;font-size:3.4rem;letter-spacing:4px;position:relative;}
header p{margin-top:8px;font-size:.95rem;color:rgba(255,255,255,.72);letter-spacing:1px;position:relative;}

/* Card */
.auth-wrap{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 20px;}
.auth-card{background:var(--card);border:1px solid var(--border);border-radius:20px;padding:40px 36px;width:100%;max-width:420px;animation:fadeUp .3s ease;}
@keyframes fadeUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:none}}
.auth-card h2{font-family:'Bebas Neue',sans-serif;font-size:1.9rem;letter-spacing:2px;margin-bottom:6px;}
.auth-card p.subtitle{font-size:.84rem;color:#555;margin-bottom:28px;}

/* Fields */
.field{display:flex;flex-direction:column;gap:7px;margin-bottom:16px;}
.field label{font-size:.74rem;letter-spacing:1.2px;text-transform:uppercase;color:#777;font-weight:600;}
.field input{background:#111;border:1px solid var(--border);color:#fff;padding:13px 15px;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.95rem;outline:none;transition:border-color .2s;width:100%;}
.field input:focus{border-color:var(--red);}
.field input::placeholder{color:#3a3a3a;}

/* Buttons */
.btn-primary{display:block;width:100%;margin-top:22px;background:linear-gradient(135deg,var(--red),var(--purple));color:#fff;border:none;padding:15px;border-radius:50px;font-family:'Bebas Neue',sans-serif;font-size:1.25rem;letter-spacing:2px;cursor:pointer;transition:opacity .2s,transform .15s;}
.btn-primary:hover{opacity:.88;transform:translateY(-2px);}

/* Alerts */
.alert{padding:12px 16px;border-radius:10px;font-size:.86rem;margin-bottom:20px;line-height:1.5;}
.alert-error{background:#1a0808;border:1px solid #5a1a1a;color:#f44336;}
.alert-success{background:#0d1a0d;border:1px solid #1e4a1e;color:var(--green);}

/* Footer link */
.auth-footer{text-align:center;margin-top:24px;font-size:.84rem;color:#555;}
.auth-footer a{color:var(--red);text-decoration:none;font-weight:600;}
.auth-footer a:hover{opacity:.8;}

/* Password toggle */
.pw-wrap{position:relative;}
.pw-wrap input{padding-right:46px;}
.pw-toggle{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#555;font-size:1rem;padding:2px;}
.pw-toggle:hover{color:#aaa;}

footer{text-align:center;padding:24px;color:#2a2a2a;font-size:.78rem;border-top:1px solid #161616;}
</style>
</head>
<body>

<header>
  <h1>🎶 My Music Studio</h1>
  <p>Accedi al tuo studio musicale personale</p>
</header>

<div class="auth-wrap">
  <div class="auth-card">
    <h2>🔐 Accedi</h2>
    <p class="subtitle">Inserisci le tue credenziali per continuare</p>

    <?php if ($error): ?>
      <div class="alert alert-error">⛔ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php" novalidate>
      <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
               placeholder="tua@email.com" required autocomplete="email">
      </div>

      <div class="field">
        <label for="password">Password</label>
        <div class="pw-wrap">
          <input type="password" id="password" name="password"
                 placeholder="••••••••" required autocomplete="current-password">
          <button type="button" class="pw-toggle" onclick="togglePw('password',this)" title="Mostra/Nascondi">👁</button>
        </div>
      </div>

      <div style="text-align:right;margin-top:-8px;margin-bottom:4px;">
        <a href="forgot_password.php" style="font-size:.8rem;color:#555;text-decoration:none;">
          Password dimenticata?
        </a>
      </div>

      <button type="submit" class="btn-primary">🚀 ENTRA</button>
    </form>

    <div class="auth-footer">
      Non hai un account? <a href="register.php">Registrati</a>
    </div>
  </div>
</div>

<footer>© 2026 – My Music Studio · Whisper by OpenAI · Transformers.js · Picasso AI</footer>

<script>
function togglePw(id, btn) {
  const input = document.getElementById(id);
  if (input.type === 'password') { input.type = 'text'; btn.textContent = '🙈'; }
  else { input.type = 'password'; btn.textContent = '👁'; }
}
</script>
</body>
</html>
