<?php
// ============================================================
//  forgot_password.php — Recupero Password
//  1. L'utente inserisce la sua email
//  2. Viene generato un token sicuro e salvato in DB
//  3. Il link di reset viene mostrato a schermo (o inviato via email)
// ============================================================
session_start();
require_once __DIR__ . '/db.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: music.php');
    exit;
}

$step    = 'form';   // 'form' | 'sent' | 'error'
$error   = '';
$email   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Inserisci un indirizzo email valido.';
    } else {
        try {
            $pdo = getDB();

            // Cerca utente attivo
            $stmt = $pdo->prepare(
                'SELECT id, username FROM users
                  WHERE email = :email AND is_active = TRUE LIMIT 1'
            );
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            // Risposta identica anche se l'email non esiste (anti user-enumeration)
            if ($user) {
                // Genera token sicuro
                $tokenRaw  = bin2hex(random_bytes(32));          // 64 char hex
                $tokenHash = password_hash($tokenRaw, PASSWORD_BCRYPT, ['cost' => 12]);

                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';

                // Invalida token precedenti non usati
                $inv = $pdo->prepare(
                    'UPDATE password_resets
                        SET used_at = NOW()
                      WHERE user_id = :uid AND used_at IS NULL AND expires_at > NOW()'
                );
                $inv->execute([':uid' => $user['id']]);

                // Salva il nuovo token (valido 1 ora)
                $ins = $pdo->prepare(
                    'INSERT INTO password_resets
                       (user_id, token, token_hash, expires_at, requested_ip)
                     VALUES
                       (:uid, :token, :hash, NOW() + INTERVAL \'1 hour\', :ip)'
                );
                $ins->execute([
                    ':uid'   => $user['id'],
                    ':token' => $tokenRaw,
                    ':hash'  => $tokenHash,
                    ':ip'    => substr($ip, 0, 45),
                ]);

                // Link di reset (in produzione → inviare via email)
                $resetLink = (isset($_SERVER['HTTPS']) ? 'https' : 'http')
                           . '://' . $_SERVER['HTTP_HOST']
                           . dirname($_SERVER['REQUEST_URI'])
                           . '/reset_password.php?token=' . urlencode($tokenRaw);

                // Salva link in sessione temporanea per mostrarlo a schermo
                // In produzione: invia via email e NON mostrare il link
                $_SESSION['_reset_link']     = $resetLink;
                $_SESSION['_reset_username'] = $user['username'];
            }

            $step = 'sent'; // Mostra sempre "email inviata" (sicurezza)

        } catch (PDOException $e) {
            $error = 'Errore database: ' . $e->getMessage();
        }
    }
}

// Recupera link dalla sessione (solo per demo — in produzione rimuovere)
$resetLink    = $_SESSION['_reset_link']     ?? null;
$resetUsername = $_SESSION['_reset_username'] ?? null;
unset($_SESSION['_reset_link'], $_SESSION['_reset_username']);
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>🎶 My Music Studio — Password Dimenticata</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;600&display=swap');
:root{--red:#d6004c;--purple:#7b1fa2;--bg:#0e0e0e;--card:#191919;--border:#2a2a2a;--green:#4caf50;--gold:#f0a500;}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:#fff;min-height:100vh;display:flex;flex-direction:column;}

header{background:linear-gradient(135deg,var(--red),var(--purple));text-align:center;padding:48px 20px 40px;position:relative;overflow:hidden;}
header::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 60% 40%,rgba(255,255,255,.12) 0%,transparent 65%);}
header h1{font-family:'Bebas Neue',sans-serif;font-size:3.4rem;letter-spacing:4px;position:relative;}
header p{margin-top:8px;font-size:.95rem;color:rgba(255,255,255,.72);letter-spacing:1px;position:relative;}

.auth-wrap{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 20px;}
.auth-card{background:var(--card);border:1px solid var(--border);border-radius:20px;padding:40px 36px;width:100%;max-width:440px;animation:fadeUp .3s ease;}
@keyframes fadeUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:none}}

.auth-card h2{font-family:'Bebas Neue',sans-serif;font-size:1.85rem;letter-spacing:2px;margin-bottom:6px;}
.auth-card .subtitle{font-size:.84rem;color:#555;margin-bottom:28px;line-height:1.6;}

/* Steps indicator */
.steps{display:flex;gap:0;margin-bottom:28px;}
.step{flex:1;text-align:center;position:relative;}
.step:not(:last-child)::after{content:'';position:absolute;top:14px;left:50%;width:100%;height:2px;background:var(--border);z-index:0;}
.step-dot{width:28px;height:28px;border-radius:50%;border:2px solid var(--border);background:var(--bg);display:inline-flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:700;color:#555;position:relative;z-index:1;transition:all .3s;}
.step-label{font-size:.66rem;color:#555;margin-top:6px;text-transform:uppercase;letter-spacing:.6px;}
.step.active .step-dot{border-color:var(--red);background:var(--red);color:#fff;}
.step.active .step-label{color:var(--red);}
.step.done .step-dot{border-color:var(--green);background:var(--green);color:#fff;}
.step.done .step-label{color:var(--green);}
.step.done:not(:last-child)::after{background:var(--green);}

/* Fields */
.field{display:flex;flex-direction:column;gap:7px;margin-bottom:18px;}
.field label{font-size:.74rem;letter-spacing:1.2px;text-transform:uppercase;color:#777;font-weight:600;}
.field input{background:#111;border:1px solid var(--border);color:#fff;padding:13px 15px;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.95rem;outline:none;transition:border-color .2s;width:100%;}
.field input:focus{border-color:var(--red);}
.field input::placeholder{color:#3a3a3a;}

/* Buttons */
.btn-primary{display:block;width:100%;margin-top:8px;background:linear-gradient(135deg,var(--red),var(--purple));color:#fff;border:none;padding:15px;border-radius:50px;font-family:'Bebas Neue',sans-serif;font-size:1.2rem;letter-spacing:2px;cursor:pointer;transition:opacity .2s,transform .15s;}
.btn-primary:hover{opacity:.88;transform:translateY(-2px);}

/* Alerts */
.alert{padding:13px 16px;border-radius:10px;font-size:.86rem;margin-bottom:22px;line-height:1.6;}
.alert-error{background:#1a0808;border:1px solid #5a1a1a;color:#f44336;}
.alert-success{background:#0d1a0d;border:1px solid #1e4a1e;color:var(--green);}
.alert-info{background:#0d0d1a;border:1px solid #2a2a5a;color:#9090ff;}

/* Success state */
.success-icon{font-size:3.5rem;display:block;text-align:center;margin-bottom:16px;animation:bounce .6s ease;}
@keyframes bounce{0%{transform:scale(.5)}70%{transform:scale(1.15)}100%{transform:scale(1)}}
.success-title{font-family:'Bebas Neue',sans-serif;font-size:1.7rem;letter-spacing:2px;text-align:center;color:var(--green);margin-bottom:8px;}
.success-msg{font-size:.88rem;color:#666;text-align:center;line-height:1.7;margin-bottom:24px;}

/* Reset link box (solo demo) */
.demo-link-box{background:#0a0a14;border:1px solid #2a2a5a;border-radius:12px;padding:16px;margin-bottom:20px;}
.demo-link-label{font-size:.7rem;color:#555;text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;}
.demo-link-badge{display:inline-block;background:#1a1a3a;border:1px solid #3a3a6a;color:#f0a500;font-size:.68rem;padding:2px 8px;border-radius:10px;margin-bottom:10px;font-weight:700;}
.demo-link-url{word-break:break-all;font-family:monospace;font-size:.76rem;color:#7070ff;background:#111;border:1px solid #2a2a4a;border-radius:8px;padding:10px 12px;display:block;margin-bottom:10px;line-height:1.5;}
.btn-copy{background:#1a1a3a;border:1px solid #3a3a6a;color:#9090ff;padding:8px 16px;border-radius:8px;font-size:.78rem;font-weight:600;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:6px;}
.btn-copy:hover{background:#2a2a5a;}
.btn-copy.copied{background:#0d1a0d;border-color:#1e4a1e;color:var(--green);}

/* Timer */
.token-timer{display:flex;align-items:center;gap:8px;font-size:.76rem;color:#555;margin-top:12px;}
.timer-dot{width:7px;height:7px;border-radius:50%;background:#f0a500;flex-shrink:0;animation:blink 1.5s infinite;}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}

/* Footer links */
.auth-footer{text-align:center;margin-top:24px;font-size:.84rem;color:#555;}
.auth-footer a{color:var(--red);text-decoration:none;font-weight:600;}
.auth-footer a:hover{opacity:.8;}

footer{text-align:center;padding:24px;color:#2a2a2a;font-size:.78rem;border-top:1px solid #161616;}
</style>
</head>
<body>

<header>
  <h1>🎶 My Music Studio</h1>
  <p>Recupero password del tuo account</p>
</header>

<div class="auth-wrap">
  <div class="auth-card">

    <!-- Steps indicator -->
    <div class="steps">
      <div class="step <?= $step === 'form' ? 'active' : 'done' ?>">
        <div class="step-dot"><?= $step !== 'form' ? '✓' : '1' ?></div>
        <div class="step-label">Email</div>
      </div>
      <div class="step <?= $step === 'sent' ? 'active' : '' ?>">
        <div class="step-dot">2</div>
        <div class="step-label">Link</div>
      </div>
      <div class="step">
        <div class="step-dot">3</div>
        <div class="step-label">Reset</div>
      </div>
    </div>

    <?php if ($step === 'form'): ?>
      <!-- ── STEP 1: Form email ── -->
      <h2>🔑 Password Dimenticata</h2>
      <p class="subtitle">Inserisci la tua email. Riceverai un link per reimpostare la password (valido 1 ora).</p>

      <?php if ($error): ?>
        <div class="alert alert-error">⛔ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="forgot_password.php" novalidate>
        <div class="field">
          <label for="email">Indirizzo Email</label>
          <input type="email" id="email" name="email"
                 value="<?= htmlspecialchars($email) ?>"
                 placeholder="tua@email.com" required
                 autocomplete="email" autofocus>
        </div>

        <button type="submit" class="btn-primary">📧 INVIA LINK DI RESET</button>
      </form>

    <?php elseif ($step === 'sent'): ?>
      <!-- ── STEP 2: Conferma invio ── -->
      <span class="success-icon">📬</span>
      <div class="success-title">Controlla la tua email!</div>
      <p class="success-msg">
        Se l'indirizzo <strong style="color:#ccc"><?= htmlspecialchars($email) ?></strong>
        è associato a un account, riceverai a breve il link per reimpostare la password.<br><br>
        Controlla anche la cartella <strong style="color:#999">Spam / Posta indesiderata</strong>.
      </p>

      <?php if ($resetLink): ?>
        <!-- ⚠️ SOLO DEMO — In produzione rimuovere questo blocco e inviare via email -->
        <div class="demo-link-box">
          <div class="demo-link-label">Link di Reset</div>
          <span class="demo-link-badge">⚠️ DEMO — In produzione inviare via email</span>
          <span class="demo-link-url" id="resetLinkText"><?= htmlspecialchars($resetLink) ?></span>
          <button class="btn-copy" id="copyBtn" onclick="copyLink()">
            📋 Copia link
          </button>
          <div class="token-timer">
            <span class="timer-dot"></span>
            <span>Link valido per <strong id="timerDisplay">60:00</strong> — scade alle <?= date('H:i', strtotime('+1 hour')) ?></span>
          </div>
        </div>
      <?php endif; ?>

      <div class="alert alert-info" style="font-size:.82rem;">
        💡 Non hai ricevuto nulla? Attendi qualche minuto, poi
        <a href="forgot_password.php" style="color:#9090ff;font-weight:600">riprova</a>.
      </div>

    <?php endif; ?>

    <div class="auth-footer">
      Ricordi la password? <a href="index.php">Torna al login</a>
    </div>
  </div>
</div>

<footer>© 2026 – My Music Studio · Whisper by OpenAI · Transformers.js · Picasso AI</footer>

<script>
// ── Copia link negli appunti ─────────────────────────────
function copyLink() {
  const url = document.getElementById('resetLinkText')?.textContent;
  if (!url) return;
  navigator.clipboard.writeText(url).then(() => {
    const btn = document.getElementById('copyBtn');
    btn.textContent = '✅ Copiato!';
    btn.classList.add('copied');
    setTimeout(() => { btn.textContent = '📋 Copia link'; btn.classList.remove('copied'); }, 2500);
  });
}

// ── Countdown 60 minuti ─────────────────────────────────
const timerEl = document.getElementById('timerDisplay');
if (timerEl) {
  let seconds = 3600;
  const tick = setInterval(() => {
    seconds--;
    if (seconds <= 0) { clearInterval(tick); timerEl.textContent = 'SCADUTO'; timerEl.style.color = '#f44336'; return; }
    const m = String(Math.floor(seconds / 60)).padStart(2, '0');
    const s = String(seconds % 60).padStart(2, '0');
    timerEl.textContent = m + ':' + s;
    if (seconds < 300) timerEl.style.color = '#f0a500';
    if (seconds < 60)  timerEl.style.color = '#f44336';
  }, 1000);
}
</script>
</body>
</html>
