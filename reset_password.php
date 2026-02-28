<?php
// ============================================================
//  reset_password.php — Reimpostazione Password
//  L'utente arriva qui cliccando il link ricevuto via email.
//  Valida il token e permette di impostare una nuova password.
// ============================================================
session_start();
require_once __DIR__ . '/db.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: music.php');
    exit;
}

$tokenRaw = trim($_GET['token'] ?? '');
$step     = 'invalid'; // 'invalid' | 'form' | 'success'
$error    = '';
$userData = null;
$resetId  = null;

// ── Valida il token ─────────────────────────────────────────
if ($tokenRaw !== '') {
    try {
        $pdo  = getDB();
        $rows = $pdo->prepare(
            'SELECT pr.id, pr.user_id, pr.token, pr.token_hash, pr.expires_at, pr.used_at,
                    u.username, u.email
               FROM password_resets pr
               JOIN users u ON u.id = pr.user_id
              WHERE pr.expires_at > NOW()
                AND pr.used_at IS NULL
              ORDER BY pr.created_at DESC'
        );
        $rows->execute();
        $allTokens = $rows->fetchAll();

        // Confronto bcrypt su tutti i token validi (sicurezza timing-safe)
        foreach ($allTokens as $row) {
            if (password_verify($tokenRaw, $row['token_hash'])) {
                $userData = $row;
                $resetId  = $row['id'];
                $step     = 'form';
                break;
            }
        }

    } catch (PDOException $e) {
        $error = 'Errore database: ' . $e->getMessage();
    }
}

// ── Gestione form nuova password ────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'form') {
    $newPw  = $_POST['password']  ?? '';
    $newPw2 = $_POST['password2'] ?? '';

    if (strlen($newPw) < 8) {
        $error = 'La password deve essere di almeno 8 caratteri.';
    } elseif ($newPw !== $newPw2) {
        $error = 'Le due password non coincidono.';
    } else {
        try {
            $pdo  = getDB();
            $hash = password_hash($newPw, PASSWORD_BCRYPT, ['cost' => 12]);

            // Aggiorna password
            $upd = $pdo->prepare(
                'UPDATE users SET password_hash = :hash, updated_at = NOW() WHERE id = :id'
            );
            $upd->execute([':hash' => $hash, ':id' => $userData['user_id']]);

            // Marca token come usato
            $mark = $pdo->prepare('UPDATE password_resets SET used_at = NOW() WHERE id = :id');
            $mark->execute([':id' => $resetId]);

            // Invalida tutte le sessioni attive dell'utente
            $del = $pdo->prepare('DELETE FROM user_sessions WHERE user_id = :uid');
            $del->execute([':uid' => $userData['user_id']]);

            $step = 'success';

        } catch (PDOException $e) {
            $error = 'Errore database: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>🎶 My Music Studio — Nuova Password</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;600&display=swap');
:root{--red:#d6004c;--purple:#7b1fa2;--bg:#0e0e0e;--card:#191919;--border:#2a2a2a;--green:#4caf50;}
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

/* Steps */
.steps{display:flex;gap:0;margin-bottom:28px;}
.step{flex:1;text-align:center;position:relative;}
.step:not(:last-child)::after{content:'';position:absolute;top:14px;left:50%;width:100%;height:2px;background:var(--border);z-index:0;}
.step.done:not(:last-child)::after{background:var(--green);}
.step-dot{width:28px;height:28px;border-radius:50%;border:2px solid var(--border);background:var(--bg);display:inline-flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:700;color:#555;position:relative;z-index:1;transition:all .3s;}
.step-label{font-size:.66rem;color:#555;margin-top:6px;text-transform:uppercase;letter-spacing:.6px;}
.step.active .step-dot{border-color:var(--red);background:var(--red);color:#fff;}
.step.active .step-label{color:var(--red);}
.step.done .step-dot{border-color:var(--green);background:var(--green);color:#fff;}
.step.done .step-label{color:var(--green);}

/* Fields */
.field{display:flex;flex-direction:column;gap:7px;margin-bottom:16px;}
.field label{font-size:.74rem;letter-spacing:1.2px;text-transform:uppercase;color:#777;font-weight:600;}
.field input{background:#111;border:1px solid var(--border);color:#fff;padding:13px 15px;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.95rem;outline:none;transition:border-color .2s;width:100%;}
.field input:focus{border-color:var(--red);}
.field input::placeholder{color:#3a3a3a;}
.field input.valid{border-color:var(--green);}
.field input.invalid{border-color:#f44336;}

/* Forza password */
.pw-wrap{position:relative;}
.pw-wrap input{padding-right:46px;}
.pw-toggle{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#555;font-size:1rem;padding:2px;}
.pw-toggle:hover{color:#aaa;}
.pw-strength{height:5px;border-radius:3px;margin-top:7px;background:#222;overflow:hidden;}
.pw-strength-fill{height:100%;width:0%;border-radius:3px;transition:width .3s,background .3s;}
.pw-hint{font-size:.72rem;color:#555;margin-top:5px;}

/* Requisiti */
.pw-reqs{background:#111;border:1px solid var(--border);border-radius:10px;padding:12px 14px;margin-bottom:16px;}
.pw-reqs-title{font-size:.72rem;color:#555;text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px;}
.req{display:flex;align-items:center;gap:8px;font-size:.8rem;color:#444;margin-bottom:5px;transition:color .2s;}
.req:last-child{margin-bottom:0;}
.req-dot{width:16px;height:16px;border-radius:50%;border:1.5px solid #333;display:inline-flex;align-items:center;justify-content:center;font-size:.6rem;flex-shrink:0;transition:all .2s;}
.req.ok{color:#aaa;}
.req.ok .req-dot{border-color:var(--green);background:var(--green);color:#fff;}

/* Buttons */
.btn-primary{display:block;width:100%;margin-top:10px;background:linear-gradient(135deg,var(--red),var(--purple));color:#fff;border:none;padding:15px;border-radius:50px;font-family:'Bebas Neue',sans-serif;font-size:1.2rem;letter-spacing:2px;cursor:pointer;transition:opacity .2s,transform .15s;}
.btn-primary:hover:not(:disabled){opacity:.88;transform:translateY(-2px);}
.btn-primary:disabled{background:#2a2a2a;color:#555;cursor:not-allowed;transform:none;}
.btn-login{display:block;width:100%;margin-top:16px;background:linear-gradient(135deg,var(--green),#388e3c);color:#fff;border:none;padding:15px;border-radius:50px;font-family:'Bebas Neue',sans-serif;font-size:1.2rem;letter-spacing:2px;cursor:pointer;transition:opacity .2s,transform .15s;text-decoration:none;text-align:center;}
.btn-login:hover{opacity:.88;transform:translateY(-2px);}

/* Alerts */
.alert{padding:13px 16px;border-radius:10px;font-size:.86rem;margin-bottom:20px;line-height:1.6;}
.alert-error{background:#1a0808;border:1px solid #5a1a1a;color:#f44336;}

/* Invalid / Success / states */
.big-icon{font-size:3.5rem;display:block;text-align:center;margin-bottom:16px;animation:pop .5s ease;}
@keyframes pop{from{transform:scale(.4) rotate(-10deg)}80%{transform:scale(1.15) rotate(2deg)}to{transform:scale(1) rotate(0)}}
.state-title{font-family:'Bebas Neue',sans-serif;font-size:1.7rem;letter-spacing:2px;text-align:center;margin-bottom:8px;}
.state-msg{font-size:.87rem;color:#666;text-align:center;line-height:1.7;margin-bottom:24px;}

/* User badge */
.user-badge{display:inline-flex;align-items:center;gap:8px;background:#111;border:1px solid var(--border);border-radius:20px;padding:7px 14px;font-size:.84rem;color:#aaa;margin-bottom:20px;}
.user-badge strong{color:#fff;}

.auth-footer{text-align:center;margin-top:22px;font-size:.84rem;color:#555;}
.auth-footer a{color:var(--red);text-decoration:none;font-weight:600;}
.auth-footer a:hover{opacity:.8;}

footer{text-align:center;padding:24px;color:#2a2a2a;font-size:.78rem;border-top:1px solid #161616;}
</style>
</head>
<body>

<header>
  <h1>🎶 My Music Studio</h1>
  <p>Reimpostazione della tua password</p>
</header>

<div class="auth-wrap">
  <div class="auth-card">

    <!-- Steps indicator -->
    <div class="steps">
      <div class="step done">
        <div class="step-dot">✓</div>
        <div class="step-label">Email</div>
      </div>
      <div class="step done">
        <div class="step-dot">✓</div>
        <div class="step-label">Link</div>
      </div>
      <div class="step <?= $step === 'success' ? 'done' : ($step === 'form' ? 'active' : '') ?>">
        <div class="step-dot"><?= $step === 'success' ? '✓' : '3' ?></div>
        <div class="step-label">Reset</div>
      </div>
    </div>

    <?php if ($step === 'invalid'): ?>
      <!-- ── TOKEN NON VALIDO ── -->
      <span class="big-icon">⛔</span>
      <div class="state-title" style="color:#f44336;">Link non valido</div>
      <p class="state-msg">
        Il link di reset è <strong style="color:#f44336">scaduto</strong> oppure è già stato utilizzato.<br><br>
        I link sono validi solo per <strong style="color:#ccc">1 ora</strong> dal momento della richiesta.
      </p>
      <a href="forgot_password.php" class="btn-primary" style="display:block;text-align:center;text-decoration:none;">
        🔄 RICHIEDI NUOVO LINK
      </a>

    <?php elseif ($step === 'form'): ?>
      <!-- ── FORM NUOVA PASSWORD ── -->
      <h2>🔐 Nuova Password</h2>
      <p class="subtitle">Scegli una password sicura per il tuo account.</p>

      <!-- Badge utente -->
      <div>
        <span class="user-badge">
          👤 <strong><?= htmlspecialchars($userData['username']) ?></strong>
          · <?= htmlspecialchars($userData['email']) ?>
        </span>
      </div>

      <?php if ($error): ?>
        <div class="alert alert-error">⛔ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <!-- Requisiti password -->
      <div class="pw-reqs">
        <div class="pw-reqs-title">Requisiti password</div>
        <div class="req" id="req-len"><span class="req-dot">✓</span> Almeno 8 caratteri</div>
        <div class="req" id="req-upper"><span class="req-dot">✓</span> Una lettera maiuscola</div>
        <div class="req" id="req-num"><span class="req-dot">✓</span> Un numero</div>
        <div class="req" id="req-special"><span class="req-dot">✓</span> Un carattere speciale (!@#$…)</div>
        <div class="req" id="req-match"><span class="req-dot">✓</span> Le due password coincidono</div>
      </div>

      <form method="POST" action="reset_password.php?token=<?= urlencode($tokenRaw) ?>" id="resetForm" novalidate>

        <div class="field">
          <label for="password">Nuova Password</label>
          <div class="pw-wrap">
            <input type="password" id="password" name="password"
                   placeholder="Minimo 8 caratteri" required
                   autocomplete="new-password" autofocus>
            <button type="button" class="pw-toggle" onclick="togglePw('password',this)">👁</button>
          </div>
          <div class="pw-strength"><div class="pw-strength-fill" id="pwFill"></div></div>
          <div class="pw-hint" id="pwHint">Inserisci la nuova password</div>
        </div>

        <div class="field">
          <label for="password2">Conferma Nuova Password</label>
          <div class="pw-wrap">
            <input type="password" id="password2" name="password2"
                   placeholder="Ripeti la password" required
                   autocomplete="new-password">
            <button type="button" class="pw-toggle" onclick="togglePw('password2',this)">👁</button>
          </div>
        </div>

        <button type="submit" class="btn-primary" id="submitBtn" disabled>
          🔒 SALVA NUOVA PASSWORD
        </button>
      </form>

    <?php elseif ($step === 'success'): ?>
      <!-- ── SUCCESSO ── -->
      <span class="big-icon">🎉</span>
      <div class="state-title" style="color:var(--green);">Password aggiornata!</div>
      <p class="state-msg">
        La tua password è stata reimpostata con successo.<br>
        Tutte le sessioni precedenti sono state <strong style="color:#ccc">disconnesse</strong>.<br><br>
        Ora puoi accedere con la nuova password.
      </p>
      <a href="index.php" class="btn-login">🚀 VAI AL LOGIN</a>

    <?php endif; ?>

    <?php if ($step !== 'success'): ?>
      <div class="auth-footer">
        Ricordi la password? <a href="index.php">Torna al login</a>
      </div>
    <?php endif; ?>

  </div>
</div>

<footer>© 2026 – My Music Studio · Whisper by OpenAI · Transformers.js · Picasso AI</footer>

<script>
// ── Mostra/Nascondi password ─────────────────────────────
function togglePw(id, btn) {
  const input = document.getElementById(id);
  if (input.type === 'password') { input.type = 'text'; btn.textContent = '🙈'; }
  else { input.type = 'password'; btn.textContent = '👁'; }
}

// ── Elementi ─────────────────────────────────────────────
const pwInput   = document.getElementById('password');
const pw2Input  = document.getElementById('password2');
const pwFill    = document.getElementById('pwFill');
const pwHint    = document.getElementById('pwHint');
const submitBtn = document.getElementById('submitBtn');

if (pwInput) {
  // ── Validazione requisiti in tempo reale ────────────────
  function checkReq(id, ok) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.toggle('ok', ok);
  }

  function validate() {
    const val  = pwInput.value;
    const val2 = pw2Input ? pw2Input.value : '';

    const okLen     = val.length >= 8;
    const okUpper   = /[A-Z]/.test(val);
    const okNum     = /[0-9]/.test(val);
    const okSpecial = /[^A-Za-z0-9]/.test(val);
    const okMatch   = val.length > 0 && val === val2;

    checkReq('req-len',     okLen);
    checkReq('req-upper',   okUpper);
    checkReq('req-num',     okNum);
    checkReq('req-special', okSpecial);
    checkReq('req-match',   okMatch);

    // Forza password
    let score = [okLen, okUpper, okNum, okSpecial, val.length >= 12].filter(Boolean).length;
    const levels = [
      { pct: '20%', color: '#f44336', label: 'Troppo debole' },
      { pct: '40%', color: '#ff5722', label: 'Debole'        },
      { pct: '60%', color: '#ff9800', label: 'Discreta'      },
      { pct: '80%', color: '#ffb300', label: 'Buona'         },
      { pct: '100%',color: '#4caf50', label: 'Ottima ✅'    },
    ];
    const lv = levels[Math.max(0, score - 1)];
    if (pwFill) {
      pwFill.style.width      = val.length ? lv.pct : '0%';
      pwFill.style.background = lv.color;
    }
    if (pwHint) {
      pwHint.textContent = val.length ? lv.label : 'Inserisci la nuova password';
      pwHint.style.color = val.length ? lv.color : '#555';
    }

    // Abilita submit solo se tutti i requisiti ok
    if (submitBtn) {
      submitBtn.disabled = !(okLen && okUpper && okNum && okSpecial && okMatch);
    }

    // Feedback conferma password
    if (pw2Input && pw2Input.value) {
      pw2Input.classList.toggle('valid',   okMatch);
      pw2Input.classList.toggle('invalid', !okMatch);
    }
  }

  pwInput.addEventListener('input',  validate);
  if (pw2Input) pw2Input.addEventListener('input', validate);

  // ── Blocca invio se non valido ──────────────────────────
  const form = document.getElementById('resetForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      if (pwInput.value !== pw2Input.value) {
        e.preventDefault();
        pw2Input.classList.add('invalid');
        pw2Input.focus();
      }
    });
  }
}

// ── Redirect automatico dopo successo ────────────────────
<?php if ($step === 'success'): ?>
let countdown = 5;
const loginBtn = document.querySelector('.btn-login');
const origText = loginBtn ? loginBtn.textContent : '';
const timer = setInterval(() => {
  countdown--;
  if (loginBtn) loginBtn.textContent = `🚀 VAI AL LOGIN (${countdown}s)`;
  if (countdown <= 0) {
    clearInterval(timer);
    window.location.href = 'index.php';
  }
}, 1000);
<?php endif; ?>
</script>
</body>
</html>
