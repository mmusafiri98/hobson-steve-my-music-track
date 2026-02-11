<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Music Studio</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
  --bg: #0d0d14;
  --surface: #16161f;
  --surface2: #1e1e2a;
  --accent: #d6004c;
  --accent2: #7b1fa2;
  --green: #22c55e;
  --blue: #3b82f6;
  --text: #f0f0f8;
  --muted: #7777aa;
  --border: rgba(255,255,255,0.07);
  --radius: 14px;
}
* { margin:0; padding:0; box-sizing:border-box; }
body {
  background: var(--bg);
  color: var(--text);
  font-family: 'Inter', sans-serif;
  min-height: 100vh;
}

/* HEADER */
header {
  background: linear-gradient(135deg, rgba(214,0,76,0.15), rgba(123,31,162,0.1));
  border-bottom: 1px solid var(--border);
  padding: 24px 32px;
  display: flex;
  align-items: center;
  gap: 16px;
}
.logo { font-family: 'Bebas Neue'; font-size: 2rem; letter-spacing: 3px; background: linear-gradient(90deg,#d6004c,#7b1fa2); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
.logo-sub { font-size: 0.8rem; color: var(--muted); }

/* LAYOUT */
.layout { display: flex; min-height: calc(100vh - 82px); }

/* SIDEBAR */
.sidebar {
  width: 200px;
  background: var(--surface);
  border-right: 1px solid var(--border);
  padding: 20px 12px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  flex-shrink: 0;
}
.nav-item {
  display: flex; align-items: center; gap: 10px;
  padding: 11px 14px;
  border-radius: 10px;
  border: none;
  background: transparent;
  color: var(--muted);
  font-family: 'Inter', sans-serif;
  font-size: 0.87rem;
  font-weight: 500;
  cursor: pointer;
  text-align: left;
  width: 100%;
  transition: all 0.18s;
}
.nav-item:hover { background: var(--surface2); color: var(--text); }
.nav-item.active { background: linear-gradient(135deg,rgba(214,0,76,0.2),rgba(123,31,162,0.15)); color: var(--text); border: 1px solid rgba(214,0,76,0.25); }

.sidebar-bottom {
  margin-top: auto;
  padding-top: 16px;
  border-top: 1px solid var(--border);
}
.api-label { font-size: 0.73rem; color: var(--muted); margin-bottom: 6px; display: block; font-weight: 600; letter-spacing: 0.5px; }
#openaiKey {
  width: 100%; padding: 9px 10px;
  background: var(--surface2); border: 1px solid var(--border);
  border-radius: 8px; color: var(--text);
  font-size: 0.78rem; font-family: 'Inter', sans-serif;
}
#openaiKey:focus { outline: none; border-color: var(--accent); }
#keyStatus { margin-top: 5px; font-size: 0.72rem; }
.key-ok { color: var(--green); }
.key-no { color: #fbbf24; }

/* MAIN */
.main { flex: 1; padding: 28px 32px; overflow-y: auto; }

/* TABS */
.page { display: none; }
.page.active { display: block; }

/* PAGE TITLE */
.page-title { font-family: 'Bebas Neue'; font-size: 1.6rem; letter-spacing: 2px; margin-bottom: 22px; color: var(--text); }

/* CARDS */
.card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 24px;
  margin-bottom: 20px;
}
.card-label {
  font-size: 0.72rem; font-weight: 700;
  letter-spacing: 1.5px; text-transform: uppercase;
  color: var(--muted); margin-bottom: 16px;
}

/* INPUTS */
.input-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px; }
.field { display: flex; flex-direction: column; gap: 7px; }
.field label { font-size: 0.8rem; color: var(--muted); font-weight: 500; }
.field input[type=text] {
  padding: 11px 13px;
  background: var(--surface2); border: 1px solid var(--border);
  border-radius: 9px; color: var(--text);
  font-family: 'Inter', sans-serif; font-size: 0.9rem;
  transition: border-color 0.18s;
}
.field input[type=text]:focus { outline: none; border-color: var(--accent); }

/* UPLOAD ZONE */
.dropzone {
  border: 2px dashed rgba(214,0,76,0.3);
  border-radius: 12px; padding: 28px 20px;
  text-align: center; cursor: pointer;
  transition: all 0.2s; background: rgba(214,0,76,0.02);
}
.dropzone:hover, .dropzone.over { border-color: var(--accent); background: rgba(214,0,76,0.07); }
.dropzone.filled { border-color: var(--green); background: rgba(34,197,94,0.05); }
.dz-icon { font-size: 2.2rem; margin-bottom: 8px; }
.dz-text { font-size: 0.88rem; color: var(--muted); }
.dz-sub { font-size: 0.75rem; color: #444; margin-top: 4px; }

/* BUTTONS */
.btn {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 12px 24px; border-radius: 9px; border: none;
  font-family: 'Inter', sans-serif; font-size: 0.88rem; font-weight: 600;
  cursor: pointer; transition: all 0.18s; text-decoration: none;
}
.btn-primary { background: linear-gradient(135deg,var(--accent),var(--accent2)); color: #fff; }
.btn-primary:hover:not(:disabled) { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(214,0,76,0.3); }
.btn-primary:disabled { opacity: 0.4; cursor: not-allowed; transform: none; box-shadow: none; }
.btn-green { background: rgba(34,197,94,0.15); color: var(--green); border: 1px solid rgba(34,197,94,0.3); }
.btn-blue { background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); }
.btn-purple { background: rgba(123,31,162,0.2); color: #c084fc; border: 1px solid rgba(123,31,162,0.3); }
.btn-red { background: rgba(239,68,68,0.12); color: #f87171; border: 1px solid rgba(239,68,68,0.25); }
.btn-sm { padding: 7px 14px; font-size: 0.8rem; border-radius: 7px; }

/* PROGRESS */
.progress-wrap { display: none; margin-top: 18px; }
.progress-label { font-size: 0.82rem; color: var(--muted); margin-bottom: 6px; display: flex; justify-content: space-between; }
.progress-track { height: 8px; background: var(--surface2); border-radius: 99px; overflow: hidden; }
.progress-bar { height: 100%; width: 0%; background: linear-gradient(90deg,var(--accent),var(--accent2)); border-radius: 99px; transition: width 0.3s; }
.progress-info { font-size: 0.8rem; color: var(--muted); margin-top: 6px; }

/* AUDIO PREVIEW */
audio { width: 100%; margin-top: 10px; accent-color: var(--accent); height: 36px; }

/* TRANSCRIPT BOX */
.transcript-box {
  display: none; margin-top: 14px;
  background: var(--surface2); border: 1px solid var(--border);
  border-radius: 10px; padding: 14px;
}
.transcript-box.show { display: block; }
.transcript-title { font-size: 0.72rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: var(--accent); margin-bottom: 8px; }
.transcript-text { font-size: 0.88rem; line-height: 1.75; color: var(--text); white-space: pre-wrap; max-height: 200px; overflow-y: auto; }

/* GALLERY */
.gallery-section { margin-bottom: 32px; }
.gallery-heading { font-family: 'Bebas Neue'; font-size: 1.1rem; letter-spacing: 2px; color: var(--muted); margin-bottom: 14px; }
.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(270px, 1fr)); gap: 16px; }

/* MEDIA CARD */
.mcard {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
  transition: border-color 0.2s, transform 0.2s;
}
.mcard:hover { border-color: rgba(214,0,76,0.35); transform: translateY(-2px); }
.mcard video { width: 100%; height: 170px; object-fit: cover; display: block; background: #000; }
.mcard-thumb { height: 120px; display: flex; align-items: center; justify-content: center; font-size: 3.2rem; background: var(--surface2); }
.mcard-body { padding: 12px 14px 8px; }
.mcard-title { font-weight: 600; font-size: 0.92rem; }
.mcard-sub { font-size: 0.78rem; color: var(--muted); margin-top: 2px; }
.mcard-lyrics { font-size: 0.75rem; color: var(--muted); margin-top: 8px; line-height: 1.55; max-height: 56px; overflow: hidden; }
.mcard-actions { display: flex; gap: 7px; padding: 8px 12px 13px; flex-wrap: wrap; }

/* EMPTY */
.empty { text-align: center; padding: 60px 20px; color: var(--muted); }
.empty-icon { font-size: 3rem; margin-bottom: 12px; }

/* TOAST */
.toast {
  position: fixed; bottom: 24px; right: 24px;
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 12px; padding: 13px 18px;
  font-size: 0.86rem; z-index: 9999;
  transform: translateY(70px); opacity: 0;
  transition: all 0.28s; max-width: 300px;
}
.toast.show { transform: translateY(0); opacity: 1; }
.toast.ok { border-color: var(--green); color: var(--green); }
.toast.err { border-color: #f87171; color: #f87171; }
.toast.info { border-color: var(--accent); }

@media(max-width: 768px) {
  .layout { flex-direction: column; }
  .sidebar { width: 100%; flex-direction: row; flex-wrap: wrap; padding: 10px; gap: 6px; }
  .nav-item { flex: 1; min-width: 80px; justify-content: center; font-size: 0.78rem; padding: 9px 8px; }
  .sidebar-bottom { display: none; }
  .main { padding: 16px; }
  .input-row { grid-template-columns: 1fr; }
  header { padding: 16px; }
}
</style>
</head>
<body>

<header>
  <div>
    <div class="logo">🎶 MUSIC STUDIO</div>
    <div class="logo-sub">Crea video · Trascrivi con Whisper · Libreria locale</div>
  </div>
</header>

<div class="layout">

  <!-- SIDEBAR -->
  <nav class="sidebar">
    <button class="nav-item active" id="nav-create" onclick="goTo('create')">🎬 Crea Video</button>
    <button class="nav-item" id="nav-import" onclick="goTo('import')">📥 Importa Video</button>
    <button class="nav-item" id="nav-mp3"    onclick="goTo('mp3')">🎵 Importa MP3</button>
    <button class="nav-item" id="nav-pdf"    onclick="goTo('pdf')">📄 Importa PDF</button>
    <button class="nav-item" id="nav-lib"    onclick="goTo('lib')">🗂️ Libreria</button>

    <div class="sidebar-bottom">
      <span class="api-label">🔑 OpenAI API Key</span>
      <input type="password" id="openaiKey" placeholder="sk-..." oninput="onKeyInput()">
      <div id="keyStatus"></div>
    </div>
  </nav>

  <!-- MAIN -->
  <main class="main">

    <!-- ═══════════════ CREA VIDEO ═══════════════ -->
    <div id="page-create" class="page active">
      <div class="page-title">🎬 CREA VIDEO MUSICALE</div>

      <div class="card">
        <div class="card-label">Informazioni brano</div>
        <div class="input-row">
          <div class="field"><label>Titolo *</label><input type="text" id="v_title" placeholder="Es. My Song"></div>
          <div class="field"><label>Artista *</label><input type="text" id="v_artist" placeholder="Es. Mario Rossi"></div>
        </div>
      </div>

      <div class="card">
        <div class="card-label">Immagine di copertina</div>
        <div class="dropzone" id="dz_cover">
          <div class="dz-icon">🖼️</div>
          <div class="dz-text">Clicca o trascina la cover</div>
          <div class="dz-sub">JPG · PNG · WEBP</div>
        </div>
        <input type="file" id="inp_cover" accept="image/*" hidden>
      </div>

      <div class="card">
        <div class="card-label">File audio
          <span id="whisper_badge" style="font-size:0.7rem;margin-left:8px;padding:2px 8px;border-radius:20px;background:rgba(251,191,36,0.15);color:#fbbf24">⚠️ Inserisci API Key per Whisper</span>
        </div>
        <div class="dropzone" id="dz_audio">
          <div class="dz-icon">🎧</div>
          <div class="dz-text">Clicca o trascina l'audio</div>
          <div class="dz-sub">MP3 · WAV · M4A · OGG</div>
        </div>
        <input type="file" id="inp_audio" accept="audio/*" hidden>

        <!-- TRASCRIZIONE AUTOMATICA -->
        <div class="transcript-box" id="trans_box">
          <div class="transcript-title">📝 Testo trascritto — Whisper AI</div>
          <div class="transcript-text" id="trans_text"></div>
        </div>
      </div>

      <button class="btn btn-primary" id="btn_create" onclick="doCreateVideo()">
        🎬 Genera Video
      </button>

      <div class="progress-wrap" id="pw">
        <div class="progress-label">
          <span id="p_label">Preparazione...</span>
          <span id="p_pct">0%</span>
        </div>
        <div class="progress-track"><div class="progress-bar" id="p_bar"></div></div>
        <div class="progress-info" id="p_info"></div>
      </div>
    </div>

    <!-- ═══════════════ IMPORTA VIDEO ═══════════════ -->
    <div id="page-import" class="page">
      <div class="page-title">📥 IMPORTA VIDEO</div>
      <div class="card">
        <div class="card-label">Dati video</div>
        <div class="input-row">
          <div class="field"><label>Titolo</label><input type="text" id="iv_title" placeholder="Titolo"></div>
          <div class="field"><label>Artista</label><input type="text" id="iv_artist" placeholder="Artista"></div>
        </div>
        <div class="dropzone" id="dz_vid"><div class="dz-icon">🎬</div><div class="dz-text">Clicca o trascina il video</div><div class="dz-sub">MP4 · WEBM · MOV</div></div>
        <input type="file" id="inp_vid" accept="video/*" hidden>
        <br>
        <button class="btn btn-primary" id="btn_iv" disabled onclick="doImportVideo()">📥 Aggiungi Video</button>
      </div>
    </div>

    <!-- ═══════════════ IMPORTA MP3 ═══════════════ -->
    <div id="page-mp3" class="page">
      <div class="page-title">🎵 IMPORTA MP3</div>
      <div class="card">
        <div class="card-label">Dati brano</div>
        <div class="input-row">
          <div class="field"><label>Titolo</label><input type="text" id="im_title" placeholder="Titolo"></div>
          <div class="field"><label>Artista</label><input type="text" id="im_artist" placeholder="Artista"></div>
        </div>
        <div class="dropzone" id="dz_mp3"><div class="dz-icon">🎵</div><div class="dz-text">Clicca o trascina il file audio</div><div class="dz-sub">MP3 · WAV · M4A</div></div>
        <input type="file" id="inp_mp3" accept="audio/*" hidden>
        <br>
        <button class="btn btn-primary" id="btn_im" disabled onclick="doImportMP3()">🎵 Aggiungi MP3</button>
      </div>
    </div>

    <!-- ═══════════════ IMPORTA PDF ═══════════════ -->
    <div id="page-pdf" class="page">
      <div class="page-title">📄 IMPORTA PDF</div>
      <div class="card">
        <div class="card-label">Dati documento</div>
        <div class="input-row">
          <div class="field"><label>Titolo</label><input type="text" id="ip_title" placeholder="Titolo"></div>
          <div class="field"><label>Descrizione</label><input type="text" id="ip_desc" placeholder="Opzionale"></div>
        </div>
        <div class="dropzone" id="dz_pdf"><div class="dz-icon">📄</div><div class="dz-text">Clicca o trascina il PDF</div></div>
        <input type="file" id="inp_pdf" accept="application/pdf" hidden>
        <br>
        <button class="btn btn-primary" id="btn_ip" disabled onclick="doImportPDF()">📄 Aggiungi PDF</button>
      </div>
    </div>

    <!-- ═══════════════ LIBRERIA ═══════════════ -->
    <div id="page-lib" class="page">
      <div class="page-title">🗂️ LIBRERIA</div>
      <div id="lib_content"><div class="empty"><div class="empty-icon">📂</div><p>Libreria vuota — crea o importa contenuti</p></div></div>
    </div>

  </main>
</div>

<div class="toast" id="toast"></div>

<script>
/* ══════════════════════════════════════════════════
   DATABASE — IndexedDB
══════════════════════════════════════════════════ */
let _db = null;
function openDB() {
  return new Promise((res, rej) => {
    if (_db) return res(_db);
    const r = indexedDB.open('MusicStudioV5', 1);
    r.onupgradeneeded = e => {
      const d = e.target.result;
      ['videos','mp3s','pdfs'].forEach(s => {
        if (!d.objectStoreNames.contains(s)) d.createObjectStore(s, { keyPath: 'id' });
      });
    };
    r.onsuccess = e => { _db = e.target.result; res(_db); };
    r.onerror   = e => rej(e.target.error);
  });
}
async function dbPut(store, obj)  { const d = await openDB(); return new Promise((res,rej) => { const tx = d.transaction(store,'readwrite'); tx.objectStore(store).put(obj); tx.oncomplete = res; tx.onerror = () => rej(tx.error); }); }
async function dbAll(store)       { const d = await openDB(); return new Promise((res,rej) => { try { const tx = d.transaction(store,'readonly'); const r = tx.objectStore(store).getAll(); r.onsuccess = e => res(e.target.result||[]); r.onerror = () => res([]); } catch { res([]); } }); }
async function dbDel(store, id)   { const d = await openDB(); return new Promise((res,rej) => { const tx = d.transaction(store,'readwrite'); tx.objectStore(store).delete(id); tx.oncomplete = res; tx.onerror = () => rej(tx.error); }); }

/* ══════════════════════════════════════════════════
   TOAST & NAVIGATION
══════════════════════════════════════════════════ */
function toast(msg, type='info') {
  const el = document.getElementById('toast');
  el.textContent = msg; el.className = 'toast ' + type + ' show';
  setTimeout(() => el.classList.remove('show'), 3200);
}
function goTo(tab) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(b => b.classList.remove('active'));
  document.getElementById('page-' + tab).classList.add('active');
  document.getElementById('nav-' + tab).classList.add('active');
  if (tab === 'lib') loadLib();
}

/* ══════════════════════════════════════════════════
   API KEY WHISPER
══════════════════════════════════════════════════ */
function onKeyInput() {
  const k = document.getElementById('openaiKey').value.trim();
  const st = document.getElementById('keyStatus');
  const badge = document.getElementById('whisper_badge');
  if (k.startsWith('sk-') && k.length > 20) {
    st.innerHTML = '<span class="key-ok">✅ Whisper attivo</span>';
    badge.style.background = 'rgba(34,197,94,0.15)';
    badge.style.color = '#22c55e';
    badge.textContent = '✅ Whisper AI attivo';
  } else {
    st.innerHTML = '<span class="key-no">⚠️ Chiave non valida</span>';
    badge.style.background = 'rgba(251,191,36,0.15)';
    badge.style.color = '#fbbf24';
    badge.textContent = '⚠️ Senza API key = nessuna trascrizione';
  }
}

/* ══════════════════════════════════════════════════
   WHISPER TRANSCRIPTION
══════════════════════════════════════════════════ */
async function whisperTranscribe(audioFile) {
  const key = document.getElementById('openaiKey').value.trim();
  if (!key || !key.startsWith('sk-')) return null;
  try {
    const fd = new FormData();
    fd.append('file', audioFile, audioFile.name || 'audio.mp3');
    fd.append('model', 'whisper-1');
    const r = await fetch('https://api.openai.com/v1/audio/transcriptions', {
      method: 'POST',
      headers: { 'Authorization': 'Bearer ' + key },
      body: fd
    });
    if (!r.ok) { console.error('Whisper HTTP', r.status); return null; }
    const j = await r.json();
    return j.text || null;
  } catch(e) { console.error('Whisper error', e); return null; }
}

/* ══════════════════════════════════════════════════
   DROPZONE HELPER
══════════════════════════════════════════════════ */
function makeZone(zoneId, inputId, validateFn, onFile) {
  const zone  = document.getElementById(zoneId);
  const input = document.getElementById(inputId);
  zone.addEventListener('click', () => input.click());
  input.addEventListener('change', e => { if (e.target.files[0]) trigger(e.target.files[0]); });
  zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('over'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('over'));
  zone.addEventListener('drop', e => { e.preventDefault(); zone.classList.remove('over'); if (e.dataTransfer.files[0]) trigger(e.dataTransfer.files[0]); });
  function trigger(f) {
    if (!validateFn(f)) return;
    zone.classList.add('filled');
    zone.innerHTML = `<div class="dz-icon">✅</div><div class="dz-text" style="color:#22c55e">${f.name}</div>`;
    onFile(f);
  }
}

/* ══════════════════════════════════════════════════
   STATO FILES
══════════════════════════════════════════════════ */
let F = { cover: null, audio: null, vid: null, mp3: null, pdf: null };

// Cover zone
makeZone('dz_cover', 'inp_cover',
  f => { if (!f.type.startsWith('image/')) { toast('Seleziona un\'immagine', 'err'); return false; } return true; },
  f => { F.cover = f; }
);

// Audio zone — trascrizione automatica al caricamento
makeZone('dz_audio', 'inp_audio',
  f => { if (!f.type.startsWith('audio/')) { toast('Seleziona un file audio', 'err'); return false; } return true; },
  async f => {
    F.audio = f;
    const key = document.getElementById('openaiKey').value.trim();
    if (key && key.startsWith('sk-')) {
      toast('🎙️ Trascrizione in corso...', 'info');
      const txt = await whisperTranscribe(f);
      if (txt) {
        document.getElementById('trans_text').textContent = txt;
        document.getElementById('trans_box').classList.add('show');
        toast('✅ Testo trascritto!', 'ok');
      } else {
        toast('⚠️ Trascrizione fallita', 'err');
      }
    }
  }
);

// Import video zone
makeZone('dz_vid', 'inp_vid',
  f => { if (!f.type.startsWith('video/')) { toast('Seleziona un video', 'err'); return false; } return true; },
  f => { F.vid = f; document.getElementById('btn_iv').disabled = false; }
);

// Import mp3 zone
makeZone('dz_mp3', 'inp_mp3',
  f => { if (!f.type.startsWith('audio/')) { toast('Seleziona un audio', 'err'); return false; } return true; },
  f => { F.mp3 = f; document.getElementById('btn_im').disabled = false; }
);

// Import pdf zone
makeZone('dz_pdf', 'inp_pdf',
  f => { if (f.type !== 'application/pdf') { toast('Seleziona un PDF', 'err'); return false; } return true; },
  f => { F.pdf = f; document.getElementById('btn_ip').disabled = false; }
);

/* ══════════════════════════════════════════════════
   PROGRESS HELPERS
══════════════════════════════════════════════════ */
function showProgress() { document.getElementById('pw').style.display = 'block'; }
function hideProgress() { setTimeout(() => { document.getElementById('pw').style.display = 'none'; setP(0,'',''); }, 2500); }
function setP(pct, label, info) {
  document.getElementById('p_bar').style.width = pct + '%';
  document.getElementById('p_pct').textContent = Math.round(pct) + '%';
  document.getElementById('p_label').textContent = label;
  document.getElementById('p_info').textContent = info;
}

/* ══════════════════════════════════════════════════
   CREA VIDEO  ← FUNZIONE PRINCIPALE CORRETTA
══════════════════════════════════════════════════ */
async function doCreateVideo() {
  const title  = document.getElementById('v_title').value.trim();
  const artist = document.getElementById('v_artist').value.trim();

  if (!title)   { toast('Inserisci il titolo', 'err'); return; }
  if (!artist)  { toast('Inserisci l\'artista', 'err'); return; }
  if (!F.cover) { toast('Seleziona la cover', 'err'); return; }
  if (!F.audio) { toast('Seleziona l\'audio', 'err'); return; }

  const btn = document.getElementById('btn_create');
  btn.disabled = true;
  showProgress();
  setP(5, '🖼️ Caricamento cover...', '');

  try {
    /* ── 1. Carica immagine ── */
    const imgEl = new Image();
    const imgURL = URL.createObjectURL(F.cover);
    imgEl.src = imgURL;
    await new Promise((res, rej) => { imgEl.onload = res; imgEl.onerror = rej; });

    setP(12, '🎵 Caricamento audio...', '');

    /* ── 2. Carica audio ── */
    const audioURL = URL.createObjectURL(F.audio);
    const audioEl  = document.createElement('audio');
    audioEl.src    = audioURL;
    audioEl.crossOrigin = 'anonymous';

    await new Promise((res, rej) => {
      audioEl.addEventListener('loadedmetadata', res, { once: true });
      audioEl.addEventListener('error', rej, { once: true });
      setTimeout(rej, 10000); // timeout 10s
    });

    const duration = audioEl.duration;
    if (!duration || !isFinite(duration)) throw new Error('Durata audio non rilevabile — prova con un altro file');

    setP(20, '🎨 Preparazione canvas...', `Durata: ${Math.floor(duration)}s`);

    /* ── 3. Canvas 1280×720 ── */
    const canvas = document.createElement('canvas');
    canvas.width  = 1280;
    canvas.height = 720;
    const ctx = canvas.getContext('2d');

    /* ── 4. AudioContext + Analyser ── */
    const ac      = new (window.AudioContext || window.webkitAudioContext)();
    const src     = ac.createMediaElementSource(audioEl);
    const analyser = ac.createAnalyser();
    analyser.fftSize = 256;
    const dest    = ac.createMediaStreamDestination();
    src.connect(analyser);
    src.connect(dest);
    src.connect(ac.destination);

    const freqArr = new Uint8Array(analyser.frequencyBinCount);
    const bars    = new Array(64).fill(0);

    /* ── 5. Funzione disegno frame ── */
    function drawFrame(progress) {
      // Sfondo
      ctx.fillStyle = '#0d0d14';
      ctx.fillRect(0, 0, 1280, 720);

      // Sfondo gradiente
      const bg = ctx.createRadialGradient(640, 360, 100, 640, 360, 700);
      bg.addColorStop(0, 'rgba(123,31,162,0.12)');
      bg.addColorStop(1, 'rgba(214,0,76,0.04)');
      ctx.fillStyle = bg;
      ctx.fillRect(0, 0, 1280, 720);

      // Barre frequenza (visualizzatore)
      analyser.getByteFrequencyData(freqArr);
      for (let i = 0; i < 64; i++) {
        bars[i] = bars[i] * 0.82 + (freqArr[i * 2] / 255) * 0.18;
        const bh = 10 + bars[i] * 140;
        const bx = 100 + i * 17;
        const alpha = 0.08 + bars[i] * 0.22;
        const grad = ctx.createLinearGradient(bx, 720 - bh - 30, bx, 720 - 30);
        grad.addColorStop(0, `rgba(214,0,76,${alpha * 1.5})`);
        grad.addColorStop(1, `rgba(123,31,162,${alpha})`);
        ctx.fillStyle = grad;
        ctx.beginPath();
        ctx.roundRect(bx, 720 - bh - 30, 13, bh, [3, 3, 0, 0]);
        ctx.fill();
      }

      // Cover artwork — ombra
      const cover_w = 380, cover_h = 380;
      const scale = Math.min(cover_w / imgEl.naturalWidth, cover_h / imgEl.naturalHeight);
      const dw = imgEl.naturalWidth * scale;
      const dh = imgEl.naturalHeight * scale;
      const ix = 110, iy = (720 - dh) / 2;

      ctx.shadowColor = 'rgba(214,0,76,0.5)';
      ctx.shadowBlur  = 50;
      ctx.drawImage(imgEl, ix, iy, dw, dh);
      ctx.shadowBlur  = 0;

      // Linea decorativa
      ctx.strokeStyle = 'rgba(214,0,76,0.5)';
      ctx.lineWidth   = 2;
      ctx.beginPath();
      ctx.moveTo(560, 190); ctx.lineTo(560, 530);
      ctx.stroke();

      // Titolo
      ctx.font      = 'bold 60px Arial';
      ctx.fillStyle = '#ffffff';
      ctx.textAlign = 'left';
      ctx.shadowColor = 'rgba(0,0,0,0.95)';
      ctx.shadowBlur  = 20;
      ctx.fillText(title.length > 20 ? title.substring(0,20)+'...' : title, 600, 310);

      // Artista
      ctx.font      = '34px Arial';
      ctx.fillStyle = 'rgba(255,255,255,0.65)';
      ctx.shadowBlur = 12;
      ctx.fillText(artist, 600, 360);
      ctx.shadowBlur = 0;

      // Barra di progresso
      const barX = 600, barY = 460, barW = 560, barH = 6;
      ctx.fillStyle = 'rgba(255,255,255,0.1)';
      ctx.beginPath(); ctx.roundRect(barX, barY, barW, barH, 3); ctx.fill();
      ctx.fillStyle = '#d6004c';
      ctx.beginPath(); ctx.roundRect(barX, barY, barW * progress, barH, 3); ctx.fill();
      // Pallino
      ctx.beginPath();
      ctx.arc(barX + barW * progress, barY + barH / 2, 8, 0, Math.PI * 2);
      ctx.fillStyle = '#fff';
      ctx.fill();

      // Timestamp
      const cur = Math.floor(duration * progress);
      const tot = Math.floor(duration);
      const fmt = t => `${Math.floor(t/60)}:${String(t%60).padStart(2,'0')}`;
      ctx.font      = '22px Arial';
      ctx.fillStyle = 'rgba(255,255,255,0.4)';
      ctx.fillText(fmt(cur) + ' / ' + fmt(tot), barX, barY + 36);

      // Testo trascritto (preview primissime parole)
      const lyr = document.getElementById('trans_text').textContent;
      if (lyr) {
        const words = lyr.split(' ').slice(0, 12).join(' ');
        ctx.font      = 'italic 20px Arial';
        ctx.fillStyle = 'rgba(255,255,255,0.28)';
        ctx.fillText('"' + words + '..."', barX, barY + 70);
      }
    }

    /* ── 6. MediaRecorder ── */
    setP(25, '🎙️ Avvio registrazione...', '');

    const mimeType = ['video/webm;codecs=vp9,opus','video/webm;codecs=vp8,opus','video/webm']
      .find(t => MediaRecorder.isTypeSupported(t)) || 'video/webm';

    const videoStream = canvas.captureStream(30);
    const audioTrack  = dest.stream.getAudioTracks()[0];
    if (audioTrack) videoStream.addTrack(audioTrack);

    const recorder = new MediaRecorder(videoStream, { mimeType, videoBitsPerSecond: 4000000 });
    const chunks   = [];
    recorder.ondataavailable = e => { if (e.data.size > 0) chunks.push(e.data); };

    /* ── 7. Fine registrazione ── */
    recorder.onstop = async () => {
      setP(92, '💾 Salvataggio nel database...', '');
      const blob     = new Blob(chunks, { type: 'video/webm' });
      const transcript = document.getElementById('trans_text').textContent || '';
      await dbPut('videos', {
        id: Date.now().toString(), blob,
        title, artist, transcript, date: Date.now()
      });
      setP(100, '✅ Video creato con successo!', `${(blob.size/1024/1024).toFixed(1)} MB salvati`);
      toast('🎬 Video creato!', 'ok');
      hideProgress();
      btn.disabled = false;
      resetCreateForm();
      URL.revokeObjectURL(audioURL);
      URL.revokeObjectURL(imgURL);
      ac.close();
    };

    /* ── 8. RENDER LOOP ── */
    let rafID;
    const startTime = performance.now();

    function renderLoop() {
      const elapsed   = (performance.now() - startTime) / 1000;
      const progress  = Math.min(elapsed / duration, 1);
      drawFrame(progress);  // ← disegna frame sul canvas
      setP(
        25 + progress * 65,
        '🎬 Generazione video...',
        `${Math.floor(elapsed)}s / ${Math.floor(duration)}s  (${Math.floor(progress*100)}%)`
      );
      if (progress < 1) {
        rafID = requestAnimationFrame(renderLoop);
      }
    }

    /* ── 9. AVVIO ── */
    recorder.start(500);
    audioEl.play();
    renderLoop();

    audioEl.addEventListener('ended', () => {
      cancelAnimationFrame(rafID);
      recorder.stop();
    }, { once: true });

  } catch(err) {
    console.error('Errore creazione video:', err);
    toast('❌ ' + err.message, 'err');
    btn.disabled = false;
    hideProgress();
  }
}

function resetCreateForm() {
  document.getElementById('v_title').value  = '';
  document.getElementById('v_artist').value = '';
  F.cover = null; F.audio = null;
  ['dz_cover','dz_audio'].forEach(id => {
    const z = document.getElementById(id);
    z.classList.remove('filled');
  });
  document.getElementById('dz_cover').innerHTML = '<div class="dz-icon">🖼️</div><div class="dz-text">Clicca o trascina la cover</div><div class="dz-sub">JPG · PNG · WEBP</div>';
  document.getElementById('dz_audio').innerHTML = '<div class="dz-icon">🎧</div><div class="dz-text">Clicca o trascina l\'audio</div><div class="dz-sub">MP3 · WAV · M4A · OGG</div>';
  document.getElementById('trans_box').classList.remove('show');
  document.getElementById('trans_text').textContent = '';
}

/* ══════════════════════════════════════════════════
   IMPORTA VIDEO / MP3 / PDF
══════════════════════════════════════════════════ */
async function doImportVideo() {
  if (!F.vid) return;
  const btn = document.getElementById('btn_iv');
  btn.disabled = true;
  try {
    const blob = new Blob([await F.vid.arrayBuffer()], { type: F.vid.type });
    await dbPut('videos', { id: Date.now().toString(), blob, title: document.getElementById('iv_title').value.trim()||'Video', artist: document.getElementById('iv_artist').value.trim()||'Sconosciuto', date: Date.now() });
    toast('✅ Video importato!', 'ok');
    document.getElementById('iv_title').value = ''; document.getElementById('iv_artist').value = '';
    document.getElementById('dz_vid').innerHTML = '<div class="dz-icon">🎬</div><div class="dz-text">Clicca o trascina il video</div>';
    document.getElementById('dz_vid').classList.remove('filled');
    F.vid = null;
  } catch(e) { toast('❌ ' + e.message, 'err'); }
  btn.disabled = true;
}

async function doImportMP3() {
  if (!F.mp3) return;
  const btn = document.getElementById('btn_im');
  btn.disabled = true;
  try {
    const blob = new Blob([await F.mp3.arrayBuffer()], { type: F.mp3.type || 'audio/mpeg' });
    await dbPut('mp3s', { id: Date.now().toString(), blob, title: document.getElementById('im_title').value.trim()||F.mp3.name, artist: document.getElementById('im_artist').value.trim()||'Artista', date: Date.now() });
    toast('✅ MP3 importato!', 'ok');
    document.getElementById('im_title').value = ''; document.getElementById('im_artist').value = '';
    document.getElementById('dz_mp3').innerHTML = '<div class="dz-icon">🎵</div><div class="dz-text">Clicca o trascina il file audio</div>';
    document.getElementById('dz_mp3').classList.remove('filled');
    F.mp3 = null;
  } catch(e) { toast('❌ ' + e.message, 'err'); }
  btn.disabled = true;
}

async function doImportPDF() {
  if (!F.pdf) return;
  const btn = document.getElementById('btn_ip');
  btn.disabled = true;
  try {
    const blob = new Blob([await F.pdf.arrayBuffer()], { type: 'application/pdf' });
    await dbPut('pdfs', { id: Date.now().toString(), blob, title: document.getElementById('ip_title').value.trim()||F.pdf.name, desc: document.getElementById('ip_desc').value.trim()||'', date: Date.now() });
    toast('✅ PDF importato!', 'ok');
    document.getElementById('ip_title').value = ''; document.getElementById('ip_desc').value = '';
    document.getElementById('dz_pdf').innerHTML = '<div class="dz-icon">📄</div><div class="dz-text">Clicca o trascina il PDF</div>';
    document.getElementById('dz_pdf').classList.remove('filled');
    F.pdf = null;
  } catch(e) { toast('❌ ' + e.message, 'err'); }
  btn.disabled = true;
}

/* ══════════════════════════════════════════════════
   LIBRERIA
══════════════════════════════════════════════════ */
let playingAudio = null;

async function loadLib() {
  const [vids, mp3s, pdfs] = await Promise.all([dbAll('videos'), dbAll('mp3s'), dbAll('pdfs')]);
  const cont = document.getElementById('lib_content');
  if (!vids.length && !mp3s.length && !pdfs.length) {
    cont.innerHTML = '<div class="empty"><div class="empty-icon">📂</div><p>Libreria vuota</p></div>';
    return;
  }
  let html = '';

  if (vids.length) {
    html += '<div class="gallery-section"><div class="gallery-heading">🎬 VIDEO</div><div class="grid" id="vgrid"></div></div>';
  }
  if (mp3s.length) {
    html += '<div class="gallery-section"><div class="gallery-heading">🎵 BRANI AUDIO</div><div class="grid" id="mgrid"></div></div>';
  }
  if (pdfs.length) {
    html += '<div class="gallery-section"><div class="gallery-heading">📄 DOCUMENTI PDF</div><div class="grid" id="pgrid"></div></div>';
  }

  cont.innerHTML = html;

  // Render videos
  if (vids.length) {
    const vg = document.getElementById('vgrid');
    vids.sort((a,b) => b.date - a.date).forEach(v => {
      const url = URL.createObjectURL(v.blob);
      const c = document.createElement('div');
      c.className = 'mcard';
      c.innerHTML = `
        <video src="${url}" controls preload="metadata"></video>
        <div class="mcard-body">
          <div class="mcard-title">${v.title||'Video'}</div>
          <div class="mcard-sub">${v.artist||''}</div>
          ${v.transcript ? `<div class="mcard-lyrics">"${v.transcript.substring(0,100)}..."</div>` : ''}
        </div>
        <div class="mcard-actions">
          <a class="btn btn-blue btn-sm" href="${url}" download="${v.title||'video'}.webm">💾 Scarica</a>
          <button class="btn btn-red btn-sm" onclick="delItem('videos','${v.id}')">🗑️</button>
        </div>`;
      vg.appendChild(c);
    });
  }

  // Render mp3s
  if (mp3s.length) {
    const mg = document.getElementById('mgrid');
    mp3s.sort((a,b) => b.date - a.date).forEach(m => {
      const url = URL.createObjectURL(m.blob);
      const c = document.createElement('div');
      c.className = 'mcard';
      c.innerHTML = `
        <div class="mcard-thumb">🎵</div>
        <div class="mcard-body">
          <div class="mcard-title">${m.title||'Audio'}</div>
          <div class="mcard-sub">${m.artist||''}</div>
        </div>
        <div class="mcard-actions">
          <button class="btn btn-green btn-sm" onclick="playAudio('${m.id}')">▶️ Ascolta</button>
          <a class="btn btn-blue btn-sm" href="${url}" download="${m.title||'audio'}.mp3">💾</a>
          <button class="btn btn-red btn-sm" onclick="delItem('mp3s','${m.id}')">🗑️</button>
        </div>`;
      mg.appendChild(c);
    });
  }

  // Render pdfs
  if (pdfs.length) {
    const pg = document.getElementById('pgrid');
    pdfs.sort((a,b) => b.date - a.date).forEach(p => {
      const url = URL.createObjectURL(p.blob);
      const c = document.createElement('div');
      c.className = 'mcard';
      c.innerHTML = `
        <div class="mcard-thumb">📄</div>
        <div class="mcard-body">
          <div class="mcard-title">${p.title||'PDF'}</div>
          <div class="mcard-sub">${p.desc||''}</div>
        </div>
        <div class="mcard-actions">
          <button class="btn btn-purple btn-sm" onclick="window.open('${url}','_blank')">👁️ Apri</button>
          <a class="btn btn-blue btn-sm" href="${url}" download="${p.title||'doc'}.pdf">💾</a>
          <button class="btn btn-red btn-sm" onclick="delItem('pdfs','${p.id}')">🗑️</button>
        </div>`;
      pg.appendChild(c);
    });
  }
}

async function playAudio(id) {
  if (playingAudio) { playingAudio.pause(); playingAudio = null; }
  const all = await dbAll('mp3s');
  const m = all.find(x => x.id === id);
  if (m) {
    playingAudio = new Audio(URL.createObjectURL(m.blob));
    playingAudio.play();
    toast('▶️ ' + m.title, 'info');
  }
}

async function delItem(store, id) {
  if (!confirm('Eliminare questo elemento?')) return;
  await dbDel(store, id);
  loadLib();
  toast('🗑️ Eliminato', 'info');
}

/* INIT */
openDB().catch(console.error);
</script>
</body>
</html>
