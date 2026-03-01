<?php
// ============================================================
// forgot_password.php — Reset diretto password (NO EMAIL)
// 1. L'utente inserisce email
// 2. Se esiste, può creare nuova password
// 3. Salva in USERS (password_hash) + PASSWORD_RESETS (log)
// 4. Redirect automatico a index.php
// ============================================================

session_start();
require_once __DIR__ . '/db.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: music.php');
    exit;
}

$step  = 'email';
$error = '';

if (!empty($_SESSION['reset_user_id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $step = 'reset';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDB();

        // ══ STEP 1 : verifica email ═══════════════════════
        if (isset($_POST['check_email'])) {
            $email = trim($_POST['email'] ?? '');
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Inserisci un'email valida.";
                $step  = 'email';
            } else {
                $stmt = $pdo->prepare(
                    "SELECT id, username FROM users WHERE email = :email AND is_active = TRUE LIMIT 1"
                );
                $stmt->execute([':email' => $email]);
                $user = $stmt->fetch();
                if ($user) {
                    $_SESSION['reset_user_id']  = $user['id'];
                    $_SESSION['reset_email']    = $email;
                    $_SESSION['reset_username'] = $user['username'];
                    $step = 'reset';
                } else {
                    $error = "Nessun account attivo trovato con questa email.";
                    $step  = 'email';
                }
            }
        }

        // ══ STEP 2 : salva nuova password ═════════════════
        if (isset($_POST['reset_password'])) {
            if (empty($_SESSION['reset_user_id'])) {
                header('Location: forgot_password.php');
                exit;
            }

            $userId   = (int) $_SESSION['reset_user_id'];
            $email    = $_SESSION['reset_email']    ?? '';
            $username = $_SESSION['reset_username'] ?? '';
            $password = $_POST['password']          ?? '';
            $confirm  = $_POST['confirm_password']  ?? '';
            $ip       = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';

            if (strlen($password) < 6) {
                $error = "La password deve contenere almeno 6 caratteri.";
                $step  = 'reset';
            } elseif ($password !== $confirm) {
                $error = "Le password non coincidono.";
                $step  = 'reset';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

                // ── BUG FIX: colonna corretta e' password_hash non password ──
                $stmtUser = $pdo->prepare(
                    "UPDATE users SET password_hash = :hash, updated_at = NOW() WHERE id = :id"
                );
                $stmtUser->execute([':hash' => $hashedPassword, ':id' => $userId]);

                // ── Registra il reset nella tabella password_resets ──
                $tokenRaw  = bin2hex(random_bytes(32));
                $tokenHash = password_hash($tokenRaw, PASSWORD_BCRYPT, ['cost' => 10]);

                $stmtInv = $pdo->prepare(
                    "UPDATE password_resets SET used_at = NOW() WHERE user_id = :uid AND used_at IS NULL"
                );
                $stmtInv->execute([':uid' => $userId]);

                $stmtLog = $pdo->prepare(
                    "INSERT INTO password_resets (user_id, token, token_hash, expires_at, used_at, requested_ip)
                     VALUES (:uid, :token, :hash, NOW() + INTERVAL '1 hour', NOW(), :ip)"
                );
                $stmtLog->execute([
                    ':uid'   => $userId,
                    ':token' => $tokenRaw,
                    ':hash'  => $tokenHash,
                    ':ip'    => substr($ip, 0, 45),
                ]);

                // ── Invalida sessioni attive ──
                $pdo->prepare("DELETE FROM user_sessions WHERE user_id = :uid")
                    ->execute([':uid' => $userId]);

                unset($_SESSION['reset_user_id'], $_SESSION['reset_email'], $_SESSION['reset_username']);
                header('Location: index.php?reset=success');
                exit;
            }
        }

    } catch (PDOException $e) {
        $error = "Errore database: " . $e->getMessage();
        $step  = isset($_POST['check_email']) ? 'email' : 'reset';
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
@import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;600&display=swap');
:root{--red:#d6004c;--purple:#7b1fa2;--bg:#0e0e0e;--card:#191919;--border:#2a2a2a;--green:#4caf50;}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:#fff;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:20px;}
.auth-card{background:var(--card);border:1px solid var(--border);border-radius:20px;padding:40px 36px;width:100%;max-width:420px;animation:fadeUp .3s ease;}
@keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}}
.steps{display:flex;margin-bottom:30px;}
.step{flex:1;text-align:center;position:relative;}
.step:not(:last-child)::after{content:'';position:absolute;top:13px;left:50%;width:100%;height:2px;background:var(--border);z-index:0;}
.step.done:not(:last-child)::after{background:var(--green);}
.step-dot{width:26px;height:26px;border-radius:50%;border:2px solid var(--border);background:var(--bg);display:inline-flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;color:#555;position:relative;z-index:1;transition:all .25s;}
.step-lbl{font-size:.64rem;color:#555;margin-top:5px;text-transform:uppercase;letter-spacing:.5px;}
.step.active .step-dot{border-color:var(--red);background:var(--red);color:#fff;}
.step.active .step-lbl{color:var(--red);}
.step.done .step-dot{border-color:var(--green);background:var(--green);color:#fff;}
.step.done .step-lbl{color:var(--green);}
h2{font-family:'Bebas Neue',sans-serif;font-size:1.8rem;letter-spacing:2px;margin-bottom:5px;}
.subtitle{font-size:.84rem;color:#555;margin-bottom:24px;line-height:1.5;}
.email-badge{display:inline-flex;align-items:center;gap:7px;background:#111;border:1px solid var(--border);border-radius:20px;padding:6px 14px;font-size:.83rem;color:#aaa;margin-bottom:20px;}
.email-badge strong{color:#fff;}
.field{display:flex;flex-direction:column;gap:6px;margin-bottom:14px;}
.field label{font-size:.72rem;letter-spacing:1px;text-transform:uppercase;color:#777;font-weight:600;}
.pw-wrap{position:relative;}
.pw-wrap input{padding-right:46px;}
input[type="email"],input[type="password"]{background:#111;border:1px solid var(--border);color:#fff;padding:13px 15px;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.95rem;outline:none;transition:border-color .2s;width:100%;}
input:focus{border-color:var(--red);}
input::placeholder{color:#3a3a3a;}
input.valid{border-color:var(--green);}
input.invalid{border-color:#f44336;}
.pw-toggle{position:absolute;right:13px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#555;font-size:1rem;}
.pw-toggle:hover{color:#aaa;}
.pw-bar{height:4px;border-radius:2px;background:#222;margin-top:6px;overflow:hidden;}
.pw-bar-fill{height:100%;width:0%;border-radius:2px;transition:width .3s,background .3s;}
.pw-hint{font-size:.71rem;color:#555;margin-top:4px;}
.btn{display:block;width:100%;margin-top:18px;background:linear-gradient(135deg,var(--red),var(--purple));color:#fff;border:none;padding:14px;border-radius:50px;font-family:'Bebas Neue',sans-serif;font-size:1.2rem;letter-spacing:2px;cursor:pointer;transition:opacity .2s,transform .15s;}
.btn:hover:not(:disabled){opacity:.88;transform:translateY(-2px);}
.btn:disabled{background:#252525;color:#555;cursor:not-allowed;transform:none;}
.alert{padding:11px 15px;border-radius:10px;font-size:.84rem;margin-bottom:18px;line-height:1.5;}
.alert-error{background:#1a0808;border:1px solid #5a1a1a;color:#f44336;}
.back{text-align:center;margin-top:22px;font-size:.82rem;color:#555;}
.back a{color:var(--red);text-decoration:none;font-weight:600;}
.back a:hover{opacity:.8;}
</style>
</head>
<body>
<div class="auth-card">

  <div class="steps">
    <div class="step <?= $step === 'email' ? 'active' : 'done' ?>">
      <div class="step-dot"><?= $step !== 'email' ? '✓' : '1' ?></div>
      <div class="step-lbl">Email</div>
    </div>
    <div class="step <?= $step === 'reset' ? 'active' : '' ?>">
      <div class="step-dot">2</div>
      <div class="step-lbl">Password</div>
    </div>
  </div>

  <?php if ($step === 'email'): ?>
    <h2>🔑 Recupera Password</h2>
    <p class="subtitle">Inserisci la tua email per reimpostare la password.</p>
    <?php if ($error): ?>
      <div class="alert alert-error">⛔ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="tua@email.com" required autofocus autocomplete="email">
      </div>
      <button class="btn" type="submit" name="check_email">CONTINUA →</button>
    </form>

  <?php elseif ($step === 'reset'): ?>
    <h2>🔒 Nuova Password</h2>
    <p class="subtitle">Scegli una nuova password per il tuo account.</p>
    <div class="email-badge">
      👤 <strong><?= htmlspecialchars($_SESSION['reset_username'] ?? '') ?></strong>
      · <?= htmlspecialchars($_SESSION['reset_email'] ?? '') ?>
    </div>
    <?php if ($error): ?>
      <div class="alert alert-error">⛔ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" id="resetForm">
      <div class="field">
        <label for="password">Nuova Password</label>
        <div class="pw-wrap">
          <input type="password" id="password" name="password" placeholder="Min. 6 caratteri" required autocomplete="new-password">
          <button type="button" class="pw-toggle" onclick="togglePw('password',this)">👁</button>
        </div>
        <div class="pw-bar"><div class="pw-bar-fill" id="pwFill"></div></div>
        <div class="pw-hint" id="pwHint">Inserisci la nuova password</div>
      </div>
      <div class="field">
        <label for="confirm_password">Conferma Password</label>
        <div class="pw-wrap">
          <input type="password" id="confirm_password" name="confirm_password" placeholder="Ripeti la password" required autocomplete="new-password">
          <button type="button" class="pw-toggle" onclick="togglePw('confirm_password',this)">👁</button>
        </div>
      </div>
      <button class="btn" type="submit" name="reset_password" id="submitBtn" disabled>
        💾 SALVA NUOVA PASSWORD
      </button>
    </form>
  <?php endif; ?>

  <div class="back"><a href="index.php">← Torna al login</a></div>
</div>

<script>
function togglePw(id, btn) {
  const el = document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
  btn.textContent = el.type === 'password' ? '👁' : '🙈';
}
const pwInput = document.getElementById('password');
const pw2Input = document.getElementById('confirm_password');
const pwFill = document.getElementById('pwFill');
const pwHint = document.getElementById('pwHint');
const submitBtn = document.getElementById('submitBtn');
if (pwInput) {
  function validate() {
    const v = pwInput.value, v2 = pw2Input ? pw2Input.value : '';
    let score = 0;
    if (v.length >= 6) score++;
    if (v.length >= 10) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;
    const levels = [
      {w:'20%',c:'#f44336',l:'Troppo debole'},
      {w:'40%',c:'#ff5722',l:'Debole'},
      {w:'60%',c:'#ff9800',l:'Discreta'},
      {w:'80%',c:'#ffb300',l:'Buona'},
      {w:'100%',c:'#4caf50',l:'Ottima ✅'},
    ];
    const lv = levels[Math.max(0, score - 1)];
    if (pwFill) { pwFill.style.width = v.length ? lv.w : '0%'; pwFill.style.background = lv.c; }
    if (pwHint) { pwHint.textContent = v.length ? lv.l : 'Inserisci la nuova password'; pwHint.style.color = v.length ? lv.c : '#555'; }
    if (pw2Input && pw2Input.value) {
      pw2Input.classList.toggle('valid', v === v2);
      pw2Input.classList.toggle('invalid', v !== v2);
    }
    if (submitBtn) submitBtn.disabled = !(v.length >= 6 && v === v2);
  }
  pwInput.addEventListener('input', validate);
  if (pw2Input) pw2Input.addEventListener('input', validate);
}
</script>
</body>
</html>
