<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>🎶 My Music Studio</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;600&display=swap');

  :root {
    --red: #d6004c;
    --purple: #7b1fa2;
    --bg: #0e0e0e;
    --card: #191919;
    --border: #2a2a2a;
  }

  * { box-sizing: border-box; }

  body {
    margin: 0;
    font-family: 'DM Sans', sans-serif;
    background: var(--bg);
    color: #fff;
  }

  header {
    background: linear-gradient(135deg, var(--red), var(--purple));
    text-align: center;
    padding: 48px 20px 40px;
    position: relative;
    overflow: hidden;
  }

  header::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at 60% 40%, rgba(255,255,255,0.08) 0%, transparent 70%);
  }

  header h1 {
    margin: 0;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 3.5rem;
    letter-spacing: 3px;
    position: relative;
  }

  header p {
    margin: 6px 0 0;
    font-size: 1rem;
    color: rgba(255,255,255,0.75);
    letter-spacing: 1px;
    position: relative;
  }

  .container {
    max-width: 960px;
    margin: auto;
    padding: 30px 20px;
  }

  .upload-box {
    background: var(--card);
    border: 1px solid var(--border);
    padding: 30px;
    border-radius: 16px;
    margin-bottom: 40px;
  }

  .row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
  }

  .field {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  label.field-label {
    font-size: 0.78rem;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #888;
    font-weight: 600;
  }

  input[type=text] {
    background: #111;
    border: 1px solid var(--border);
    color: #fff;
    padding: 12px 14px;
    border-radius: 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.95rem;
    outline: none;
    transition: border-color 0.2s;
    width: 100%;
  }

  input[type=text]:focus { border-color: var(--red); }

  .file-input-wrapper {
    position: relative;
    overflow: hidden;
  }

  .file-btn {
    background: #111;
    border: 1px dashed #444;
    color: #aaa;
    padding: 12px 14px;
    border-radius: 10px;
    cursor: pointer;
    font-size: 0.9rem;
    text-align: center;
    transition: border-color 0.2s, color 0.2s;
    display: block;
    width: 100%;
  }

  .file-btn:hover { border-color: var(--red); color: #fff; }

  input[type=file] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
  }

  .file-name {
    font-size: 0.78rem;
    color: #666;
    margin-top: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .create-btn {
    display: block;
    width: 100%;
    margin-top: 20px;
    background: linear-gradient(135deg, var(--red), var(--purple));
    color: #fff;
    border: none;
    padding: 15px;
    border-radius: 50px;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.3rem;
    letter-spacing: 2px;
    cursor: pointer;
    transition: opacity 0.2s, transform 0.15s;
  }

  .create-btn:hover { opacity: 0.9; transform: translateY(-1px); }
  .create-btn:disabled { background: #333; color: #666; cursor: not-allowed; transform: none; }

  .progress {
    display: none;
    margin-top: 20px;
  }

  .progress-label {
    font-size: 0.82rem;
    color: #888;
    margin-bottom: 8px;
    display: flex;
    justify-content: space-between;
  }

  .progress-bar {
    width: 100%;
    height: 8px;
    background: #222;
    border-radius: 4px;
    overflow: hidden;
  }

  .progress-fill {
    height: 100%;
    width: 0;
    background: linear-gradient(90deg, var(--red), var(--purple));
    border-radius: 4px;
    transition: width 0.3s ease;
  }

  .status-text {
    text-align: center;
    margin-top: 10px;
    font-size: 0.85rem;
    color: #aaa;
  }

  /* Gallery */
  .gallery { margin-top: 10px; }

  .gallery h2 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.8rem;
    letter-spacing: 2px;
    margin-bottom: 20px;
    color: #fff;
  }

  .video-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
  }

  .video-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    transition: transform 0.2s;
  }

  .video-card:hover { transform: translateY(-3px); }

  .video-card video {
    width: 100%;
    height: 175px;
    object-fit: cover;
    display: block;
  }

  .video-info {
    padding: 12px 14px 4px;
    font-size: 0.9rem;
    line-height: 1.5;
  }

  .video-info b { font-size: 1rem; }
  .video-info span { color: #888; font-size: 0.85rem; }

  .video-actions {
    display: flex;
    gap: 8px;
    padding: 10px 14px 14px;
  }

  .download-btn, .delete-btn {
    flex: 1;
    border-radius: 8px;
    text-align: center;
    padding: 9px;
    font-size: 0.83rem;
    cursor: pointer;
    font-family: 'DM Sans', sans-serif;
    font-weight: 600;
    transition: opacity 0.15s;
  }

  .download-btn {
    background: var(--red);
    color: #fff;
    text-decoration: none;
    display: flex;
    justify-content: center;
    align-items: center;
    border: none;
  }

  .delete-btn {
    background: #2a2a2a;
    color: #aaa;
    border: none;
  }

  .download-btn:hover, .delete-btn:hover { opacity: 0.8; }

  .empty {
    text-align: center;
    color: #444;
    padding: 70px 20px;
    font-size: 1.1rem;
  }

  .empty span { font-size: 2.5rem; display: block; margin-bottom: 10px; }

  footer {
    text-align: center;
    padding: 30px;
    color: #333;
    font-size: 0.82rem;
    border-top: 1px solid #1a1a1a;
    margin-top: 60px;
  }

  @media (max-width: 600px) {
    .row { grid-template-columns: 1fr; }
    header h1 { font-size: 2.5rem; }
  }
</style>
</head>
<body>

<header>
  <h1>🎶 My Music Studio</h1>
  <p>Crea video da cover + audio</p>
</header>

<div class="container">

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

    <button class="create-btn" id="createBtn">CREA VIDEO</button>

    <div class="progress" id="progressBox">
      <div class="progress-label">
        <span id="statusText">Elaborazione...</span>
        <span id="pctText">0%</span>
      </div>
      <div class="progress-bar">
        <div class="progress-fill" id="progressFill"></div>
      </div>
    </div>
  </div>

  <div class="gallery" id="gallery" style="display:none">
    <h2>🎞️ I tuoi video</h2>
    <div id="videoGrid" class="video-grid"></div>
  </div>

  <div class="empty" id="empty">
    <span>🎵</span>
    Nessun video ancora — crea il primo!
  </div>

</div>

<footer>© 2026 – My Music Studio</footer>

<script>
/* ── helpers UI ── */
function updateLabel(inputId, labelId, nameId) {
  const f = document.getElementById(inputId).files[0];
  if (f) {
    document.getElementById(labelId).textContent = '✅ ' + f.name;
    document.getElementById(nameId).textContent = f.name;
  }
}

/* ── IndexedDB ── */
let db;

async function openDB() {
  return new Promise((resolve, reject) => {
    const req = indexedDB.open("VideoDB", 1);
    req.onupgradeneeded = e => {
      const d = e.target.result;
      if (!d.objectStoreNames.contains("videos"))
        d.createObjectStore("videos", { keyPath: "id" });
    };
    req.onsuccess = e => { db = e.target.result; resolve(); };
    req.onerror  = e => reject(e.target.error);
  });
}

async function saveVideo(blob, title, artist) {
  return new Promise(resolve => {
    const tx = db.transaction("videos", "readwrite");
    tx.objectStore("videos").put({
      id: Date.now().toString(), blob, title, artist, date: Date.now()
    });
    tx.oncomplete = () => resolve();
  });
}

async function getVideos() {
  return new Promise(resolve => {
    const tx  = db.transaction("videos", "readonly");
    const req = tx.objectStore("videos").getAll();
    req.onsuccess = e => resolve(e.target.result || []);
  });
}

async function deleteVideo(id) {
  if (!confirm("Eliminare questo video?")) return;
  const tx = db.transaction("videos", "readwrite");
  tx.objectStore("videos").delete(id);
  tx.oncomplete = () => loadGallery();
}

async function loadGallery() {
  const vids    = await getVideos();
  const grid    = document.getElementById("videoGrid");
  const gallery = document.getElementById("gallery");
  const empty   = document.getElementById("empty");
  grid.innerHTML = "";
  if (!vids.length) {
    gallery.style.display = "none";
    empty.style.display   = "block";
    return;
  }
  empty.style.display   = "none";
  gallery.style.display = "block";

  vids.sort((a, b) => b.date - a.date).forEach(v => {
    const url  = URL.createObjectURL(v.blob);
    const card = document.createElement("div");
    card.className = "video-card";
    card.innerHTML = `
      <video src="${url}" controls></video>
      <div class="video-info"><b>${v.title}</b><br><span>${v.artist}</span></div>
      <div class="video-actions">
        <a class="download-btn" href="${url}" download="${v.title}.webm">⬇ Scarica</a>
        <button class="delete-btn" onclick="deleteVideo('${v.id}')">🗑 Elimina</button>
      </div>`;
    grid.appendChild(card);
  });
}

/* ══════════════════════════════════════
   CREAZIONE VIDEO — FIX principale
   Il problema originale: audio.onended
   NON scatta in modo affidabile quando
   l'audio è gestito da un AudioContext.
   Soluzione: polling su currentTime
   con un setInterval che controlla
   se la traccia è finita.
══════════════════════════════════════ */
document.getElementById("createBtn").addEventListener("click", async () => {
  const title     = document.getElementById("title").value.trim();
  const artist    = document.getElementById("artist").value.trim();
  const coverFile = document.getElementById("coverFile").files[0];
  const audioFile = document.getElementById("audioFile").files[0];

  if (!title || !artist || !coverFile || !audioFile) {
    alert("Compila tutti i campi e carica cover + audio.");
    return;
  }

  const btn         = document.getElementById("createBtn");
  const progressBox = document.getElementById("progressBox");
  const progressFill= document.getElementById("progressFill");
  const pctText     = document.getElementById("pctText");
  const statusText  = document.getElementById("statusText");

  btn.disabled             = true;
  progressBox.style.display = "block";
  statusText.textContent   = "Avvio...";

  try {
    /* Carica immagine */
    const img = new Image();
    img.src   = URL.createObjectURL(coverFile);
    await new Promise(res => img.onload = res);

    /* Canvas */
    const canvas = document.createElement("canvas");
    canvas.width  = 1280;
    canvas.height = 720;
    const ctx = canvas.getContext("2d");

    /* Audio — usiamo AudioContext solo per il routing verso MediaStream */
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

    /* Decodifica il file audio in ArrayBuffer */
    const arrayBuffer = await audioFile.arrayBuffer();
    const audioBuffer = await audioCtx.decodeAudioData(arrayBuffer);
    const duration    = audioBuffer.duration;

    /* Sorgente: BufferSource (non ha il bug di onended) */
    const source = audioCtx.createBufferSource();
    source.buffer = audioBuffer;

    /* Destination → traccia audio per il recorder */
    const dest = audioCtx.createMediaStreamDestination();
    source.connect(dest);

    /* Stream video dal canvas */
    const canvasStream = canvas.captureStream(30);

    /* Aggiunge la traccia audio allo stream video */
    const audioTrack = dest.stream.getAudioTracks()[0];
    canvasStream.addTrack(audioTrack);

    /* MediaRecorder */
    const mimeType = MediaRecorder.isTypeSupported("video/webm;codecs=vp9,opus")
      ? "video/webm;codecs=vp9,opus"
      : "video/webm";

    const recorder = new MediaRecorder(canvasStream, { mimeType });
    const chunks   = [];

    recorder.ondataavailable = e => { if (e.data.size > 0) chunks.push(e.data); };

    recorder.onstop = async () => {
      statusText.textContent = "Salvataggio...";
      const blob = new Blob(chunks, { type: "video/webm" });
      await saveVideo(blob, title, artist);

      /* Reset UI */
      btn.disabled              = false;
      progressBox.style.display = "none";
      progressFill.style.width  = "0%";
      pctText.textContent       = "0%";
      statusText.textContent    = "Elaborazione...";
      document.getElementById("title").value  = "";
      document.getElementById("artist").value = "";
      document.getElementById("coverFile").value = "";
      document.getElementById("audioFile").value = "";
      document.getElementById("coverLabel").textContent = "📷 Scegli immagine";
      document.getElementById("audioLabel").textContent = "🎵 Scegli audio";
      document.getElementById("coverName").textContent  = "Nessun file scelto";
      document.getElementById("audioName").textContent  = "Nessun file scelto";

      loadGallery();
    };

    /* Funzione draw */
    const drawFrame = () => {
      /* Sfondo scuro con leggero blur della cover */
      ctx.fillStyle = "#000";
      ctx.fillRect(0, 0, 1280, 720);

      /* Cover centrata */
      const scale = Math.min(1280 / img.width, 720 / img.height) * 0.72;
      const x = (1280 - img.width  * scale) / 2;
      const y = (720  - img.height * scale) / 2 - 45;
      ctx.drawImage(img, x, y, img.width * scale, img.height * scale);

      /* Testi */
      ctx.textAlign = "center";
      ctx.shadowColor = "rgba(0,0,0,0.7)";
      ctx.shadowBlur  = 10;
      ctx.fillStyle   = "#fff";
      ctx.font        = "bold 52px Arial";
      ctx.fillText(title, 640, 655);
      ctx.font        = "36px Arial";
      ctx.fillStyle   = "#ccc";
      ctx.fillText(artist, 640, 705);
      ctx.shadowBlur  = 0;
    };

    /* Avvia recording e sorgente audio */
    recorder.start(1000); // chunk ogni secondo per sicurezza
    source.start(0);
    statusText.textContent = "Registrazione in corso...";

    const startTime = audioCtx.currentTime;

    /* ── FIX PRINCIPALE: polling basato su AudioContext.currentTime ── */
    const drawAndProgress = setInterval(() => {
      drawFrame();

      const elapsed = audioCtx.currentTime - startTime;
      const pct     = Math.min((elapsed / duration) * 100, 100);
      progressFill.style.width = pct + "%";
      pctText.textContent      = Math.floor(pct) + "%";

      /* Quando l'audio è finito, stoppiamo */
      if (elapsed >= duration - 0.1) {
        clearInterval(drawAndProgress);
        statusText.textContent = "Finalizzazione...";
        recorder.requestData();
        setTimeout(() => {
          recorder.stop();
          audioCtx.close();
        }, 300);
      }
    }, 33); // ~30fps

    /* Fallback: onended di source (quando disponibile) */
    source.onended = () => {
      if (recorder.state === "recording") {
        clearInterval(drawAndProgress);
        statusText.textContent = "Finalizzazione...";
        recorder.requestData();
        setTimeout(() => {
          recorder.stop();
          audioCtx.close();
        }, 300);
      }
    };

  } catch (err) {
    console.error(err);
    alert("Errore: " + err.message);
    btn.disabled              = false;
    progressBox.style.display = "none";
  }
});

window.onload = async () => {
  await openDB();
  loadGallery();
};
</script>
</body>
</html>
