<?php
// ============================================================
//  register.php — Pagina di Registrazione
//  Salva il nuovo utente nella tabella users del DB Neon
// ============================================================
session_start();
require_once __DIR__ . '/db.php';

// Se già loggato → redirect
if (!empty($_SESSION['user_id'])) {
    header('Location: music.php');
    exit;
}

$error   = '';
$success = false;

// ── Gestione form POST ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $password  = trim($_POST['password']  ?? '');
    $password2 = trim($_POST['password2'] ?? '');

    // Validazioni
    if (empty($username) || empty($email) || empty($password) || empty($password2)) {
        $error = 'Compila tutti i campi obbligatori.';
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $error = 'Il nome utente deve essere tra 3 e 50 caratteri.';
    } elseif (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $username)) {
        $error = 'Il nome utente può contenere solo lettere, numeri, _, - e .';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Indirizzo email non valido.';
    } elseif (strlen($password) < 8) {
        $error = 'La password deve essere di almeno 8 caratteri.';
    } elseif ($password !== $password2) {
        $error = 'Le due password non coincidono.';
    } else {
        try {
            $pdo = getDB();

            // Controlla unicità email
            $chk = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
            $chk->execute([':email' => $email]);
            if ($chk->fetch()) {
                $error = 'Questa email è già registrata.';
            } else {
                // Controlla unicità username
                $chk2 = $pdo->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
                $chk2->execute([':username' => $username]);
                if ($chk2->fetch()) {
                    $error = 'Questo nome utente è già in uso.';
                } else {
                    // Recupera device info dall'header (inviato dal JS)
                    $device_id   = substr(trim($_POST['device_id']   ?? ''), 0, 120);
                    $device_name = substr(trim($_POST['device_name'] ?? ''), 0, 100);

                    // Hash della password
                    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

                    // Inserisce il nuovo utente
                    $ins = $pdo->prepare(
                        'INSERT INTO users
                           (username, email, password_hash, role, is_active, device_id, device_name)
                         VALUES
                           (:username, :email, :hash, :role, :active, :device_id, :device_name)
                         RETURNING id'
                    );
                    $ins->execute([
                        ':username'    => $username,
                        ':email'       => $email,
                        ':hash'        => $hash,
                        ':role'        => 'user',
                        ':active'      => true,
                        ':device_id'   => $device_id   ?: null,
                        ':device_name' => $device_name ?: null,
                    ]);

                    $newUser = $ins->fetch();
                    $newId   = $newUser['id'];

                    // Crea il progetto di default
                    $proj = $pdo->prepare(
                        'INSERT INTO projects (user_id, name, description)
                         VALUES (:uid, :name, :desc)'
                    );
                    $proj->execute([
                        ':uid'  => $newId,
                        ':name' => 'Progetto 1',
                        ':desc' => 'Il mio primo progetto musicale',
                    ]);

                    // Redirect al login con messaggio
                    header('Location: index.php?registered=1');
                    exit;
                }
            }
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
<title>🎶 My Music Studio — Registrazione</title>
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
.auth-card{background:var(--card);border:1px solid var(--border);border-radius:20px;padding:40px 36px;width:100%;max-width:460px;animation:fadeUp .3s ease;}
@keyframes fadeUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:none}}
.auth-card h2{font-family:'Bebas Neue',sans-serif;font-size:1.9rem;letter-spacing:2px;margin-bottom:6px;}
.auth-card p.subtitle{font-size:.84rem;color:#555;margin-bottom:28px;}

.field{display:flex;flex-direction:column;gap:7px;margin-bottom:16px;}
.field label{font-size:.74rem;letter-spacing:1.2px;text-transform:uppercase;color:#777;font-weight:600;}
.field input{background:#111;border:1px solid var(--border);color:#fff;padding:13px 15px;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.95rem;outline:none;transition:border-color .2s;width:100%;}
.field input:focus{border-color:var(--red);}
.field input::placeholder{color:#3a3a3a;}
.field input.valid{border-color:var(--green);}
.field input.invalid{border-color:#f44336;}

/* Forza password */
.pw-strength{height:4px;border-radius:2px;margin-top:6px;background:#222;overflow:hidden;}
.pw-strength-fill{height:100%;width:0%;border-radius:2px;transition:width .3s,background .3s;}
.pw-hint{font-size:.72rem;color:#555;margin-top:4px;}

.row-2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}

.btn-primary{display:block;width:100%;margin-top:22px;background:linear-gradient(135deg,var(--red),var(--purple));color:#fff;border:none;padding:15px;border-radius:50px;font-family:'Bebas Neue',sans-serif;font-size:1.25rem;letter-spacing:2px;cursor:pointer;transition:opacity .2s,transform .15s;}
.btn-primary:hover{opacity:.88;transform:translateY(-2px);}
.btn-primary:disabled{background:#2a2a2a;color:#555;cursor:not-allowed;transform:none;}

.alert{padding:12px 16px;border-radius:10px;font-size:.86rem;margin-bottom:20px;line-height:1.5;}
.alert-error{background:#1a0808;border:1px solid #5a1a1a;color:#f44336;}

.auth-footer{text-align:center;margin-top:24px;font-size:.84rem;color:#555;}
.auth-footer a{color:var(--red);text-decoration:none;font-weight:600;}
.auth-footer a:hover{opacity:.8;}

.terms{font-size:.76rem;color:#444;margin-top:12px;text-align:center;line-height:1.5;}

.pw-wrap{position:relative;}
.pw-wrap input{padding-right:46px;}
.pw-toggle{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#555;font-size:1rem;padding:2px;}
.pw-toggle:hover{color:#aaa;}

footer{text-align:center;padding:24px;color:#2a2a2a;font-size:.78rem;border-top:1px solid #161616;}
@media(max-width:480px){.row-2{grid-template-columns:1fr;}}
</style>
</head>
<body>

<header>
  <h1>🎶 My Music Studio</h1>
  <p>Crea il tuo account gratuito</p>
</header>

<div class="auth-wrap">
  <div class="auth-card">
    <h2>🎵 Registrati</h2>
    <p class="subtitle">Crea il tuo account per accedere allo studio</p>

    <?php if ($error): ?>
      <div class="alert alert-error">⛔ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php" id="regForm" novalidate>
      <!-- Campi nascosti device (compilati da JS) -->
      <input type="hidden" id="device_id"   name="device_id">
      <input type="hidden" id="device_name" name="device_name">

      <div class="field">
        <label for="username">Nome utente</label>
        <input type="text" id="username" name="username"
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
               placeholder="es: mario_rossi" required
               autocomplete="username" minlength="3" maxlength="50">
      </div>

      <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
               placeholder="tua@email.com" required autocomplete="email">
      </div>

      <div class="row-2">
        <div class="field">
          <label for="password">Password</label>
          <div class="pw-wrap">
            <input type="password" id="password" name="password"
                   placeholder="Min. 8 caratteri" required
                   autocomplete="new-password" minlength="8">
            <button type="button" class="pw-toggle" onclick="togglePw('password',this)">👁</button>
          </div>
          <div class="pw-strength"><div class="pw-strength-fill" id="pwFill"></div></div>
          <div class="pw-hint" id="pwHint">Almeno 8 caratteri</div>
        </div>
        <div class="field">
          <label for="password2">Conferma Password</label>
          <div class="pw-wrap">
            <input type="password" id="password2" name="password2"
                   placeholder="Ripeti la password" required
                   autocomplete="new-password">
            <button type="button" class="pw-toggle" onclick="togglePw('password2',this)">👁</button>
          </div>
        </div>
      </div>

      <button type="submit" class="btn-primary" id="submitBtn">🚀 CREA ACCOUNT</button>

      <div class="terms">
        Registrandoti accetti i Termini di Servizio e la Privacy Policy
      </div>
    </form>

    <div class="auth-footer">
      Hai già un account? <a href="index.php">Accedi</a>
    </div>
  </div>
</div>

<footer>© 2026 – My Music Studio · Whisper by OpenAI · Transformers.js · Picasso AI</footer>

<script>
// ── Device info (inviato al server) ──────────────────────
(function(){
  let did = localStorage.getItem('mms_device_id');
  if(!did){
    did = 'dev_' + Math.random().toString(36).slice(2,10) + '_' + Date.now().toString(36);
    localStorage.setItem('mms_device_id', did);
  }
  const ua = navigator.userAgent;
  let browser = 'Browser';
  if(ua.includes('Chrome') && !ua.includes('Edg')) browser = 'Chrome';
  else if(ua.includes('Firefox')) browser = 'Firefox';
  else if(ua.includes('Safari') && !ua.includes('Chrome')) browser = 'Safari';
  else if(ua.includes('Edg')) browser = 'Edge';
  let os = 'Unknown';
  if(ua.includes('Windows')) os = 'Windows';
  else if(ua.includes('Mac')) os = 'Mac';
  else if(ua.includes('Android')) os = 'Android';
  else if(ua.includes('iPhone')||ua.includes('iPad')) os = 'iOS';
  else if(ua.includes('Linux')) os = 'Linux';
  document.getElementById('device_id').value   = did;
  document.getElementById('device_name').value = browser + ' / ' + os;
})();

// ── Mostra/Nascondi password ─────────────────────────────
function togglePw(id, btn) {
  const input = document.getElementById(id);
  if (input.type === 'password') { input.type = 'text'; btn.textContent = '🙈'; }
  else { input.type = 'password'; btn.textContent = '👁'; }
}

// ── Forza password ───────────────────────────────────────
const pwInput = document.getElementById('password');
const pwFill  = document.getElementById('pwFill');
const pwHint  = document.getElementById('pwHint');

pwInput.addEventListener('input', function(){
  const val = this.value;
  let score = 0;
  if(val.length >= 8)  score++;
  if(val.length >= 12) score++;
  if(/[A-Z]/.test(val))    score++;
  if(/[0-9]/.test(val))    score++;
  if(/[^A-Za-z0-9]/.test(val)) score++;

  const levels = [
    { pct:'20%', color:'#f44336', label:'Troppo debole' },
    { pct:'40%', color:'#ff5722', label:'Debole' },
    { pct:'60%', color:'#ff9800', label:'Discreta' },
    { pct:'80%', color:'#ffb300', label:'Buona' },
    { pct:'100%',color:'#4caf50', label:'Ottima ✅' },
  ];
  const lv = levels[Math.max(0, score - 1)] || levels[0];
  pwFill.style.width      = val.length ? lv.pct : '0%';
  pwFill.style.background = lv.color;
  pwHint.textContent      = val.length ? lv.label : 'Almeno 8 caratteri';
  pwHint.style.color      = lv.color;
});

// ── Validazione conferma password ────────────────────────
const pw2Input = document.getElementById('password2');
pw2Input.addEventListener('input', function(){
  if(this.value && this.value !== pwInput.value){
    this.classList.add('invalid'); this.classList.remove('valid');
  } else if(this.value) {
    this.classList.add('valid'); this.classList.remove('invalid');
  }
});

// ── Blocca invio se password non coincidono ──────────────
document.getElementById('regForm').addEventListener('submit', function(e){
  if(pwInput.value !== pw2Input.value){
    e.preventDefault();
    pw2Input.classList.add('invalid');
    pw2Input.focus();
    alert('Le due password non coincidono!');
  }
});
</script>
</body>
</html>
