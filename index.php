<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>🎶 My Music Studio</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;600&display=swap');
  :root { --red:#d6004c; --purple:#7b1fa2; --bg:#0e0e0e; --card:#191919; --border:#2a2a2a; }
  * { box-sizing:border-box; }
  body { margin:0; font-family:'DM Sans',sans-serif; background:var(--bg); color:#fff; }
  header { background:linear-gradient(135deg,var(--red),var(--purple)); text-align:center; padding:48px 20px 40px; position:relative; overflow:hidden; }
  header::before { content:''; position:absolute; inset:0; background:radial-gradient(ellipse at 60% 40%,rgba(255,255,255,.08) 0%,transparent 70%); }
  header h1 { margin:0; font-family:'Bebas Neue',sans-serif; font-size:3.5rem; letter-spacing:3px; position:relative; }
  header p  { margin:6px 0 0; font-size:1rem; color:rgba(255,255,255,.75); letter-spacing:1px; position:relative; }
  .container { max-width:960px; margin:auto; padding:30px 20px; }

  /* API KEY BANNER */
  .api-banner { background:#1a1a2e; border:1px solid #7b1fa2; border-radius:12px; padding:18px 22px; margin-bottom:24px; display:flex; align-items:center; gap:14px; flex-wrap:wrap; }
  .api-banner .api-icon { font-size:1.6rem; flex-shrink:0; }
  .api-banner .api-text { flex:1; min-width:180px; }
  .api-banner .api-text strong { display:block; font-size:.92rem; color:#e0b0ff; margin-bottom:3px; }
  .api-banner .api-text span { font-size:.78rem; color:#888; }
  .api-input-row { display:flex; gap:8px; align-items:center; flex:2; min-width:220px; }
  .api-input-row input { flex:1; background:#0e0e0e; border:1px solid var(--border); color:#fff; padding:10px 14px; border-radius:8px; font-family:'DM Sans',sans-serif; font-size:.88rem; outline:none; }
  .api-input-row input:focus { border-color:var(--purple); }
  .api-save-btn { background:var(--purple); color:#fff; border:none; padding:10px 18px; border-radius:8px; cursor:pointer; font-size:.85rem; font-weight:600; white-space:nowrap; }
  .api-save-btn:hover { opacity:.85; }
  .api-status { font-size:.78rem; margin-top:6px; }
  .api-status.ok  { color:#4caf50; }
  .api-status.err { color:#f44336; }

  .upload-box { background:var(--card); border:1px solid var(--border); padding:30px; border-radius:16px; margin-bottom:40px; }
  .row { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px; }
  .field { display:flex; flex-direction:column; gap:6px; }
  label.field-label { font-size:.78rem; letter-spacing:1px; text-transform:uppercase; color:#888; font-weight:600; }
  input[type=text] { background:#111; border:1px solid var(--border); color:#fff; padding:12px 14px; border-radius:10px; font-family:'DM Sans',sans-serif; font-size:.95rem; outline:none; transition:border-color .2s; width:100%; }
  input[type=text]:focus { border-color:var(--red); }
  .file-input-wrapper { position:relative; overflow:hidden; }
  .file-btn { background:#111; border:1px dashed #444; color:#aaa; padding:12px 14px; border-radius:10px; cursor:pointer; font-size:.9rem; text-align:center; transition:border-color .2s,color .2s; display:block; width:100%; }
  .file-btn:hover { border-color:var(--red); color:#fff; }
  input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
  .file-name { font-size:.78rem; color:#666; margin-top:4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

  .whisper-toggle { display:flex; align-items:center; gap:10px; margin:16px 0 4px; cursor:pointer; user-select:none; }
  .whisper-toggle input[type=checkbox] { width:18px; height:18px; accent-color:var(--purple); cursor:pointer; }
  .whisper-toggle label { font-size:.9rem; color:#ccc; cursor:pointer; }
  .whisper-toggle .badge { background:var(--purple); color:#fff; font-size:.68rem; padding:2px 7px; border-radius:20px; font-weight:700; letter-spacing:.5px; }

  .create-btn { display:block; width:100%; margin-top:20px; background:linear-gradient(135deg,var(--red),var(--purple)); color:#fff; border:none; padding:15px; border-radius:50px; font-family:'Bebas Neue',sans-serif; font-size:1.3rem; letter-spacing:2px; cursor:pointer; transition:opacity .2s,transform .15s; }
  .create-btn:hover { opacity:.9; transform:translateY(-1px); }
  .create-btn:disabled { background:#333; color:#666; cursor:not-allowed; transform:none; }

  .progress { display:none; margin-top:20px; }
  .progress-label { font-size:.82rem; color:#888; margin-bottom:8px; display:flex; justify-content:space-between; }
  .progress-bar { width:100%; height:8px; background:#222; border-radius:4px; overflow:hidden; }
  .progress-fill { height:100%; width:0; background:linear-gradient(90deg,var(--red),var(--purple)); border-radius:4px; transition:width .3s linear; }
  .status-sub { text-align:center; margin-top:10px; font-size:.82rem; color:#666; }

  .gallery { margin-top:10px; }
  .gallery h2 { font-family:'Bebas Neue',sans-serif; font-size:1.8rem; letter-spacing:2px; margin-bottom:20px; }
  .video-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:20px; }
  .video-card { background:var(--card); border:1px solid var(--border); border-radius:14px; overflow:hidden; transition:transform .2s; }
  .video-card:hover { transform:translateY(-3px); }
  .video-card video { width:100%; height:175px; object-fit:cover; display:block; }
  .video-info { padding:12px 14px 4px; font-size:.9rem; line-height:1.5; }
  .video-info b { font-size:1rem; }
  .video-info span { color:#888; font-size:.85rem; }
  .video-actions { display:flex; gap:8px; padding:10px 14px 14px; }
  .download-btn,.delete-btn { flex:1; border-radius:8px; text-align:center; padding:9px; font-size:.83rem; cursor:pointer; font-family:'DM Sans',sans-serif; font-weight:600; transition:opacity .15s; border:none; }
  .download-btn { background:var(--red); color:#fff; text-decoration:none; display:flex; justify-content:center; align-items:center; }
  .delete-btn   { background:#2a2a2a; color:#aaa; }
  .download-btn:hover,.delete-btn:hover { opacity:.8; }

  .empty { text-align:center; color:#444; padding:70px 20px; font-size:1.1rem; }
  .empty span { font-size:2.5rem; display:block; margin-bottom:10px; }
  footer { text-align:center; padding:30px; color:#333; font-size:.82rem; border-top:1px solid #1a1a1a; margin-top:60px; }
  @media(max-width:600px){ .row{grid-template-columns:1fr;} header h1{font-size:2.5rem;} }
</style>
</head>
<body>

<header>
  <h1>🎶 My Music Studio</h1>
  <p>Crea video da cover + audio con sottotitoli automatici</p>
</header>

<div class="container">

  <!-- API KEY BANNER -->
  <div class="api-banner">
    <div class="api-icon">🔑</div>
    <div class="api-text">
      <strong>OpenAI API Key (Whisper)</strong>
      <span>La chiave viene salvata solo in memoria — non è mai scritta nel codice</span>
    </div>
    <div class="api-input-row">
      <input type="password" id="apiKeyInput" placeholder="sk-... oppure NK0o4...">
      <button class="api-save-btn" onclick="saveApiKey()">Salva</button>
    </div>
    <div style="width:100%">
      <div class="api-status" id="apiStatus">⚪ Nessuna chiave inserita</div>
    </div>
  </div>

  <div class="upload-box">
    <div class="row">
      <div class="field">
        <label class="field-label">Titolo</label>
        <input type="text" id="title" placeholder="Es. Bohemian Rhapsody">
      </div>
      <div class="field">
        <label class="field-label">Artista</label>
        <input type="text" id="artist" placeholder="Es. Queen">
      </div>
    </div>
    <div class="row">
      <div class="field">
        <label class="field-label">Cover (immagine)</label>
        <div class="file-input-wrapper">
          <span class="file-btn" id="coverLabel">📷 Scegli immagine</span>
          <input type="file" id="coverFile" accept="image/*" onchange="updateLabel('coverFile','coverLabel','coverName')">
        </div>
        <div class="file-name" id="coverName">Nessun file scelto</div>
      </div>
      <div class="field">
        <label class="field-label">Audio (MP3, WAV)</label>
        <div class="file-input-wrapper">
          <span class="file-btn" id="audioLabel">🎵 Scegli audio</span>
          <input type="file" id="audioFile" accept="audio/*" onchange="updateLabel('audioFile','audioLabel','audioName')">
        </div>
        <div class="file-name" id="audioName">Nessun file scelto</div>
      </div>
    </div>

    <div class="whisper-toggle">
      <input type="checkbox" id="useWhisper" checked>
      <label for="useWhisper">Aggiungi sottotitoli automatici con Whisper</label>
      <span class="badge">AI</span>
    </div>

    <button class="create-btn" id="createBtn">CREA VIDEO</button>

    <div class="progress" id="progressBox">
      <div class="progress-label">
        <span id="statusText">Elaborazione...</span>
        <span id="pctText">0%</span>
      </div>
      <div class="progress-bar"><div class="progress-fill" id="progressFill"></div></div>
      <div class="status-sub" id="statusSub">Non chiudere questa pagina</div>
    </div>
  </div>

  <div class="gallery" id="gallery" style="display:none">
    <h2>🎞️ I tuoi video</h2>
    <div id="videoGrid" class="video-grid"></div>
  </div>
  <div class="empty" id="empty"><span>🎵</span>Nessun video ancora — crea il primo!</div>
</div>

<footer>© 2026 – My Music Studio</footer>

<script>
/* ════════════════════════
   API KEY — solo in memoria
════════════════════════ */
let _apiKey = '';

function saveApiKey() {
  const val = document.getElementById('apiKeyInput').value.trim();
  if (!val) { showApiStatus('err', '❌ Inserisci una chiave valida'); return; }
  _apiKey = val;
  document.getElementById('apiKeyInput').value = ''; // cancella il campo
  showApiStatus('ok', '✅ Chiave salvata in memoria (non persistente)');
}

function showApiStatus(type, msg) {
  const el = document.getElementById('apiStatus');
  el.textContent  = msg;
  el.className    = 'api-status ' + type;
}

/* ════════════════════════
   WHISPER — trascrizione
   Restituisce array di segmenti:
   [{ start, end, text }, ...]
════════════════════════ */
async function transcribeWithWhisper(audioFile) {
  if (!_apiKey) throw new Error('Inserisci la tua OpenAI API Key nel banner in alto.');

  const formData = new FormData();
  formData.append('file', audioFile, audioFile.name || 'audio.mp3');
  formData.append('model', 'whisper-1');
  formData.append('response_format', 'verbose_json'); // restituisce timestamps
  formData.append('timestamp_granularities[]', 'segment');

  const res = await fetch('https://api.openai.com/v1/audio/transcriptions', {
    method: 'POST',
    headers: { 'Authorization': 'Bearer ' + _apiKey },
    body: formData
  });

  if (!res.ok) {
    const err = await res.json().catch(() => ({}));
    throw new Error('Whisper API: ' + (err.error?.message || res.statusText));
  }

  const data = await res.json();
  // segments: [{ start, end, text }]
  return data.segments || [];
}

/* ════════════════════════
   Disegna sottotitolo sul canvas
   Restituisce il testo attivo
   al tempo "elapsed"
════════════════════════ */
function getActiveSubtitle(segments, elapsed) {
  if (!segments || !segments.length) return '';
  for (const seg of segments) {
    if (elapsed >= seg.start && elapsed < seg.end) {
      return seg.text.trim();
    }
  }
  return '';
}

function drawFrame(ctx, img, title, artist, subtitle) {
  // Sfondo
  ctx.fillStyle = '#000';
  ctx.fillRect(0, 0, 1280, 720);

  // Cover centrata
  const scale = Math.min(1280 / img.width, 720 / img.height) * 0.65;
  const x = (1280 - img.width  * scale) / 2;
  // Se ci sono sottotitoli alziamo un po' la cover
  const yOffset = subtitle ? -80 : -30;
  const y = (720  - img.height * scale) / 2 + yOffset;
  ctx.drawImage(img, x, y, img.width * scale, img.height * scale);

  // Titolo + artista
  ctx.textAlign   = 'center';
  ctx.shadowColor = 'rgba(0,0,0,.9)';
  ctx.shadowBlur  = 14;
  ctx.fillStyle   = '#fff';
  ctx.font        = 'bold 50px Arial';
  ctx.fillText(title, 640, 620);
  ctx.font      = '34px Arial';
  ctx.fillStyle = '#bbb';
  ctx.fillText(artist, 640, 665);

  // Sottotitolo (karaoke-style)
  if (subtitle) {
    // Sfondo semi-trasparente per il sottotitolo
    const padding = 20;
    ctx.font = 'bold 36px Arial';
    const textW = ctx.measureText(subtitle).width;
    const boxW  = Math.min(textW + padding * 2, 1200);
    const boxX  = (1280 - boxW) / 2;
    const boxY  = 695;
    const boxH  = 54;

    ctx.shadowBlur = 0;
    ctx.fillStyle  = 'rgba(0,0,0,0.65)';
    roundRect(ctx, boxX, boxY, boxW, boxH, 10);
    ctx.fill();

    // Testo sottotitolo con glow colorato
    ctx.shadowColor = '#d6004c';
    ctx.shadowBlur  = 18;
    ctx.fillStyle   = '#fff';
    ctx.font        = 'bold 34px Arial';
    // Tronca se troppo lungo
    let sub = subtitle;
    while (ctx.measureText(sub).width > boxW - 30 && sub.length > 5) {
      sub = sub.slice(0, -4) + '...';
    }
    ctx.fillText(sub, 1280 / 2, boxY + 37);
    ctx.shadowBlur = 0;
  }
}

function roundRect(ctx, x, y, w, h, r) {
  ctx.beginPath();
  ctx.moveTo(x + r, y);
  ctx.lineTo(x + w - r, y);
  ctx.quadraticCurveTo(x + w, y, x + w, y + r);
  ctx.lineTo(x + w, y + h - r);
  ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
  ctx.lineTo(x + r, y + h);
  ctx.quadraticCurveTo(x, y + h, x, y + h - r);
  ctx.lineTo(x, y + r);
  ctx.quadraticCurveTo(x, y, x + r, y);
  ctx.closePath();
}

/* ════════════════════════
   IndexedDB — lazy init
════════════════════════ */
let _db = null;
function getDB() {
  if (_db) return Promise.resolve(_db);
  return new Promise((resolve, reject) => {
    const req = indexedDB.open('MusicStudioDB', 2);
    req.onupgradeneeded = e => {
      const d = e.target.result;
      if (!d.objectStoreNames.contains('videos'))
        d.createObjectStore('videos', { keyPath: 'id' });
    };
    req.onsuccess = e => {
      _db = e.target.result;
      _db.onclose = () => { _db = null; };
      _db.onversionchange = () => { _db.close(); _db = null; };
      resolve(_db);
    };
    req.onerror   = e => reject(new Error('IndexedDB non disponibile: ' + e.target.error));
    req.onblocked = () => reject(new Error('IndexedDB bloccato — chiudi altre schede.'));
  });
}

async function saveVideo(blob, title, artist) {
  const db = await getDB();
  return new Promise((resolve, reject) => {
    const tx  = db.transaction('videos', 'readwrite');
    tx.objectStore('videos').put({ id: Date.now().toString(), blob, title, artist, date: Date.now() });
    tx.oncomplete = () => resolve();
    tx.onerror    = e => reject(new Error('Salvataggio fallito: ' + e.target.error));
  });
}

async function getVideos() {
  const db = await getDB();
  return new Promise((resolve, reject) => {
    const tx  = db.transaction('videos', 'readonly');
    const req = tx.objectStore('videos').getAll();
    req.onsuccess = e => resolve(e.target.result || []);
    req.onerror   = e => reject(e.target.error);
  });
}

async function deleteVideo(id) {
  if (!confirm('Eliminare questo video?')) return;
  const db = await getDB();
  const tx  = db.transaction('videos', 'readwrite');
  tx.objectStore('videos').delete(id);
  tx.oncomplete = () => loadGallery();
}

/* ════════════════════════
   Gallery
════════════════════════ */
async function loadGallery() {
  try {
    const vids = await getVideos();
    const grid    = document.getElementById('videoGrid');
    const gallery = document.getElementById('gallery');
    const empty   = document.getElementById('empty');
    grid.innerHTML = '';
    if (!vids.length) { gallery.style.display='none'; empty.style.display='block'; return; }
    empty.style.display='none'; gallery.style.display='block';
    vids.sort((a,b)=>b.date-a.date).forEach(v => {
      const url  = URL.createObjectURL(v.blob);
      const card = document.createElement('div');
      card.className = 'video-card';
      card.innerHTML = `
        <video src="${url}" controls></video>
        <div class="video-info"><b>${v.title}</b><br><span>${v.artist}</span></div>
        <div class="video-actions">
          <a class="download-btn" href="${url}" download="${v.title}.webm">⬇ Scarica</a>
          <button class="delete-btn" onclick="deleteVideo('${v.id}')">🗑 Elimina</button>
        </div>`;
      grid.appendChild(card);
    });
  } catch(e) { console.warn('loadGallery:', e); }
}

/* ════════════════════════
   UI helpers
════════════════════════ */
function updateLabel(inputId, labelId, nameId) {
  const f = document.getElementById(inputId).files[0];
  if (f) {
    document.getElementById(labelId).textContent = '✅ ' + f.name;
    document.getElementById(nameId).textContent  = f.name;
  }
}

function setStatus(main, pct, sub) {
  if (main != null) document.getElementById('statusText').textContent = main;
  if (pct  != null) {
    document.getElementById('progressFill').style.width = pct + '%';
    document.getElementById('pctText').textContent = Math.floor(pct) + '%';
  }
  if (sub != null) document.getElementById('statusSub').textContent = sub;
}

function resetUI() {
  document.getElementById('createBtn').disabled        = false;
  document.getElementById('progressBox').style.display = 'none';
  document.getElementById('progressFill').style.width  = '0%';
  document.getElementById('pctText').textContent       = '0%';
  ['title','artist'].forEach(id => document.getElementById(id).value = '');
  ['coverFile','audioFile'].forEach(id => document.getElementById(id).value = '');
  document.getElementById('coverLabel').textContent = '📷 Scegli immagine';
  document.getElementById('audioLabel').textContent = '🎵 Scegli audio';
  document.getElementById('coverName').textContent  = 'Nessun file scelto';
  document.getElementById('audioName').textContent  = 'Nessun file scelto';
}

function readAsDataURL(file) {
  return new Promise((res,rej) => {
    const r = new FileReader();
    r.onload = e => res(e.target.result);
    r.onerror = () => rej(new Error('Errore lettura immagine'));
    r.readAsDataURL(file);
  });
}
function readAsArrayBuffer(file) {
  return new Promise((res,rej) => {
    const r = new FileReader();
    r.onload = e => res(e.target.result);
    r.onerror = () => rej(new Error('Errore lettura audio'));
    r.readAsArrayBuffer(file);
  });
}

/* ════════════════════════
   CREAZIONE VIDEO
════════════════════════ */
document.getElementById('createBtn').addEventListener('click', async () => {
  const title     = document.getElementById('title').value.trim();
  const artist    = document.getElementById('artist').value.trim();
  const coverFile = document.getElementById('coverFile').files[0];
  const audioFile = document.getElementById('audioFile').files[0];
  const useWhisper= document.getElementById('useWhisper').checked;

  if (!title || !artist || !coverFile || !audioFile) {
    alert('Compila tutti i campi e carica cover + audio.');
    return;
  }
  if (useWhisper && !_apiKey) {
    alert('Per usare i sottotitoli inserisci prima la tua OpenAI API Key nel banner in alto.\nOppure deseleziona "Aggiungi sottotitoli automatici".');
    return;
  }

  document.getElementById('createBtn').disabled        = true;
  document.getElementById('progressBox').style.display = 'block';
  setStatus('Inizializzazione...', 2, 'Non chiudere questa pagina');

  let drawInterval = null;
  let audioCtx     = null;
  let stopped      = false;

  function doStop(recorder) {
    if (stopped) return;
    stopped = true;
    if (drawInterval) { clearInterval(drawInterval); drawInterval = null; }
    setStatus('Finalizzazione...', 99, '');
    try {
      if (recorder.state === 'recording') {
        recorder.requestData();
        setTimeout(() => {
          try { recorder.stop(); } catch(e) {}
          if (audioCtx) audioCtx.close().catch(() => {});
        }, 600);
      }
    } catch(e) {}
  }

  try {
    // 1 — DB check
    setStatus('Apertura database...', 3);
    await getDB();

    // 2 — Whisper (se abilitato) — PRIMA della registrazione
    let segments = [];
    if (useWhisper) {
      setStatus('Trascrizione con Whisper AI...', 8, 'Invio audio a OpenAI...');
      try {
        segments = await transcribeWithWhisper(audioFile);
        setStatus('Trascrizione completata!', 18, segments.length + ' segmenti trovati');
        await new Promise(r => setTimeout(r, 600)); // pausa visiva
      } catch(whisperErr) {
        // Whisper fallisce → continua senza sottotitoli
        console.warn('Whisper error:', whisperErr);
        const go = confirm('Whisper ha restituito un errore:\n"' + whisperErr.message + '"\n\nVuoi creare il video senza sottotitoli?');
        if (!go) { resetUI(); return; }
        segments = [];
      }
    }

    // 3 — Immagine base64
    setStatus('Caricamento immagine...', 20);
    const imgDataURL = await readAsDataURL(coverFile);
    const img = new Image();
    await new Promise((res, rej) => {
      img.onload  = res;
      img.onerror = () => rej(new Error('Impossibile caricare immagine'));
      img.src = imgDataURL;
    });

    // 4 — Audio decode
    setStatus('Decodifica audio...', 26);
    const audioBuf = await readAsArrayBuffer(audioFile);
    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    const audioBuffer = await audioCtx.decodeAudioData(audioBuf);
    const duration    = audioBuffer.duration;

    // 5 — Canvas
    const canvas = document.createElement('canvas');
    canvas.width = 1280; canvas.height = 720;
    const ctx2d = canvas.getContext('2d');
    drawFrame(ctx2d, img, title, artist, '');

    // 6 — Stream
    const canvasStream = canvas.captureStream(30);
    if (!canvasStream || canvasStream.getVideoTracks().length === 0)
      throw new Error('captureStream() non supportato. Usa Chrome o Edge.');

    // 7 — Audio routing
    const bufSrc = audioCtx.createBufferSource();
    bufSrc.buffer = audioBuffer;
    const dest = audioCtx.createMediaStreamDestination();
    bufSrc.connect(dest);
    const audioTrack = dest.stream.getAudioTracks()[0];
    if (audioTrack) canvasStream.addTrack(audioTrack);

    // 8 — MediaRecorder
    const mimeType = ['video/webm;codecs=vp9,opus','video/webm;codecs=vp8,opus','video/webm']
      .find(m => MediaRecorder.isTypeSupported(m)) || 'video/webm';
    const recorder = new MediaRecorder(canvasStream, { mimeType });
    const chunks   = [];

    recorder.ondataavailable = e => { if (e.data && e.data.size > 0) chunks.push(e.data); };

    recorder.onstop = async () => {
      setStatus('Salvataggio nel database...', 99, '');
      try {
        if (chunks.length === 0)
          throw new Error('Nessun dato registrato. Usa Chrome o Edge e riprova.');
        const blob = new Blob(chunks, { type: 'video/webm' });
        await saveVideo(blob, title, artist);
        setStatus('✅ Video salvato!', 100, '');
        setTimeout(() => { resetUI(); loadGallery(); }, 900);
      } catch(err) {
        alert('Errore: ' + err.message);
        resetUI();
      }
    };

    // 9 — Avvio
    setStatus('Registrazione...', 30, 'Non chiudere questa pagina');
    recorder.start(1000);
    const t0 = audioCtx.currentTime;
    bufSrc.start(0);

    // 10 — Loop draw + sottotitoli
    drawInterval = setInterval(() => {
      if (stopped) return;
      const elapsed = audioCtx.currentTime - t0;
      const pct = Math.min(30 + (elapsed / duration) * 68, 98);
      setStatus('Registrazione...', pct, Math.floor(elapsed) + 's / ' + Math.floor(duration) + 's');

      const sub = getActiveSubtitle(segments, elapsed);
      drawFrame(ctx2d, img, title, artist, sub);

      if (elapsed >= duration) doStop(recorder);
    }, 50);

    bufSrc.onended = () => doStop(recorder);

  } catch(err) {
    if (drawInterval) clearInterval(drawInterval);
    if (audioCtx)     audioCtx.close().catch(() => {});
    console.error(err);
    alert('Errore: ' + err.message);
    resetUI();
  }
});

/* Avvio */
window.addEventListener('load', () => {
  getDB()
    .then(() => loadGallery())
    .catch(err => {
      console.error('DB init error:', err);
      document.getElementById('empty').innerHTML =
        '<span>⚠️</span>IndexedDB non disponibile.<br>Apri con Chrome/Edge oppure usa un server locale.';
    });
});
</script>
</body>
</html>
