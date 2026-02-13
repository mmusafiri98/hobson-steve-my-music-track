<?php
// ═══════════════════════════════════════════════════════════
//  index.php — My Music Studio
//  PHP gère l'appel Whisper côté serveur (clé API cachée)
//  HTML + CSS + JS dans le même fichier
// ═══════════════════════════════════════════════════════════

// ⚠️  METS TA NOUVELLE CLÉ ICI (l'ancienne est compromise)
define('OPENAI_API_KEY', 'METS_TA_CLE_ICI');

// ── Requête AJAX : transcription Whisper ──────────────────
// Le JS envoie POST avec ?action=whisper
// PHP appelle OpenAI et renvoie le JSON des segments
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'whisper') {

    header('Content-Type: application/json');

    // Vérifie fichier reçu
    if (empty($_FILES['audio']) || $_FILES['audio']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['error' => 'Aucun fichier audio reçu']);
        exit;
    }

    $audioFile = $_FILES['audio']['tmp_name'];
    $audioName = $_FILES['audio']['name'] ?? 'audio.mp3';
    $audioMime = $_FILES['audio']['type'] ?? 'audio/mpeg';
    $maxSize   = 25 * 1024 * 1024; // 25 MB limite Whisper

    if ($_FILES['audio']['size'] > $maxSize) {
        http_response_code(413);
        echo json_encode(['error' => 'Fichier trop grand (max 25 MB)']);
        exit;
    }

    // Appel Whisper via cURL
    $postFields = [
        'model'                      => 'whisper-1',
        'response_format'            => 'verbose_json',
        'timestamp_granularities[]'  => 'segment',
        'file'                       => new CURLFile($audioFile, $audioMime, $audioName),
    ];

    $ch = curl_init('https://api.openai.com/v1/audio/transcriptions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $postFields,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . OPENAI_API_KEY],
        CURLOPT_TIMEOUT        => 120,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response  = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        http_response_code(502);
        echo json_encode(['error' => 'Erreur réseau : ' . $curlError]);
        exit;
    }

    http_response_code($httpCode);
    echo $response;
    exit;
}
// ── Fin bloc PHP ─────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>🎶 My Music Studio</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
/* ════════════════════════════════
   RESET & VARIABLES
════════════════════════════════ */
@import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;600&display=swap');

:root {
  --red:    #d6004c;
  --purple: #7b1fa2;
  --bg:     #0e0e0e;
  --card:   #191919;
  --border: #2a2a2a;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: 'DM Sans', sans-serif;
  background: var(--bg);
  color: #fff;
  min-height: 100vh;
}

/* ════════════════════════════════
   HEADER
════════════════════════════════ */
header {
  background: linear-gradient(135deg, var(--red), var(--purple));
  text-align: center;
  padding: 52px 20px 44px;
  position: relative;
  overflow: hidden;
}
header::before {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(ellipse at 60% 40%, rgba(255,255,255,.1) 0%, transparent 65%);
}
header h1 {
  font-family: 'Bebas Neue', sans-serif;
  font-size: 3.8rem;
  letter-spacing: 4px;
  position: relative;
  text-shadow: 0 2px 20px rgba(0,0,0,.3);
}
header p {
  margin-top: 8px;
  font-size: .98rem;
  color: rgba(255,255,255,.75);
  letter-spacing: 1px;
  position: relative;
}

/* ════════════════════════════════
   LAYOUT
════════════════════════════════ */
.container {
  max-width: 960px;
  margin: auto;
  padding: 34px 20px 60px;
}

/* ════════════════════════════════
   UPLOAD BOX
════════════════════════════════ */
.upload-box {
  background: var(--card);
  border: 1px solid var(--border);
  padding: 32px;
  border-radius: 18px;
  margin-bottom: 44px;
}

.row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 16px;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 7px;
}

.field-label {
  font-size: .74rem;
  letter-spacing: 1.2px;
  text-transform: uppercase;
  color: #777;
  font-weight: 600;
}

input[type="text"] {
  background: #111;
  border: 1px solid var(--border);
  color: #fff;
  padding: 13px 15px;
  border-radius: 10px;
  font-family: 'DM Sans', sans-serif;
  font-size: .95rem;
  outline: none;
  transition: border-color .2s;
  width: 100%;
}
input[type="text"]:focus { border-color: var(--red); }

/* ── File inputs ── */
.file-input-wrapper { position: relative; overflow: hidden; }

.file-btn {
  background: #111;
  border: 1px dashed #3a3a3a;
  color: #888;
  padding: 13px 15px;
  border-radius: 10px;
  cursor: pointer;
  font-size: .9rem;
  text-align: center;
  display: block;
  width: 100%;
  transition: border-color .2s, color .2s;
}
.file-btn:hover { border-color: var(--red); color: #fff; }

input[type="file"] {
  position: absolute;
  inset: 0;
  opacity: 0;
  cursor: pointer;
  width: 100%;
  height: 100%;
}

.file-name {
  font-size: .76rem;
  color: #555;
  margin-top: 5px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* ── Whisper toggle ── */
.whisper-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 20px 0 4px;
  background: #111;
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 13px 16px;
}
.whisper-row input[type="checkbox"] {
  width: 18px;
  height: 18px;
  accent-color: var(--purple);
  cursor: pointer;
  flex-shrink: 0;
}
.whisper-row label {
  font-size: .92rem;
  color: #ccc;
  cursor: pointer;
  flex: 1;
}
.badge {
  background: var(--purple);
  color: #fff;
  font-size: .66rem;
  padding: 3px 9px;
  border-radius: 20px;
  font-weight: 700;
  letter-spacing: .5px;
  flex-shrink: 0;
}

.whisper-info {
  font-size: .78rem;
  color: #555;
  padding: 4px 2px 0;
  min-height: 20px;
  transition: color .2s;
}
.whisper-info.ok      { color: #4caf50; }
.whisper-info.err     { color: #f44336; }
.whisper-info.loading { color: #ffb300; }

/* ── Bouton créer ── */
.create-btn {
  display: block;
  width: 100%;
  margin-top: 22px;
  background: linear-gradient(135deg, var(--red), var(--purple));
  color: #fff;
  border: none;
  padding: 16px;
  border-radius: 50px;
  font-family: 'Bebas Neue', sans-serif;
  font-size: 1.35rem;
  letter-spacing: 2px;
  cursor: pointer;
  transition: opacity .2s, transform .15s;
}
.create-btn:hover:not(:disabled) { opacity: .88; transform: translateY(-2px); }
.create-btn:disabled { background: #2a2a2a; color: #555; cursor: not-allowed; }

/* ════════════════════════════════
   BARRE DE PROGRESSION
════════════════════════════════ */
.progress { display: none; margin-top: 22px; }

.progress-header {
  display: flex;
  justify-content: space-between;
  font-size: .82rem;
  color: #888;
  margin-bottom: 9px;
}

.progress-track {
  width: 100%;
  height: 9px;
  background: #222;
  border-radius: 5px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  width: 0%;
  background: linear-gradient(90deg, var(--red), var(--purple));
  border-radius: 5px;
  transition: width .35s ease;
}

.progress-sub {
  text-align: center;
  margin-top: 10px;
  font-size: .8rem;
  color: #555;
}

/* ════════════════════════════════
   GALERIE
════════════════════════════════ */
.gallery { margin-top: 6px; }

.gallery-title {
  font-family: 'Bebas Neue', sans-serif;
  font-size: 1.9rem;
  letter-spacing: 2px;
  margin-bottom: 22px;
  color: #fff;
}

.video-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(278px, 1fr));
  gap: 22px;
}

.video-card {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 14px;
  overflow: hidden;
  transition: transform .2s, box-shadow .2s;
}
.video-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 40px rgba(214,0,76,.15);
}

.video-card video {
  width: 100%;
  height: 176px;
  object-fit: cover;
  display: block;
}

.video-meta {
  padding: 13px 15px 6px;
  line-height: 1.5;
}
.video-meta strong { font-size: .97rem; display: block; }
.video-meta span   { font-size: .83rem; color: #666; }

.video-actions {
  display: flex;
  gap: 9px;
  padding: 10px 15px 15px;
}

.btn-dl, .btn-del {
  flex: 1;
  border-radius: 9px;
  text-align: center;
  padding: 9px 6px;
  font-size: .82rem;
  font-family: 'DM Sans', sans-serif;
  font-weight: 600;
  cursor: pointer;
  transition: opacity .15s;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  text-decoration: none;
}
.btn-dl  { background: var(--red);  color: #fff; }
.btn-del { background: #252525; color: #888; }
.btn-dl:hover, .btn-del:hover { opacity: .78; }

/* ── Vide ── */
.empty {
  text-align: center;
  color: #3a3a3a;
  padding: 80px 20px;
  font-size: 1.05rem;
}
.empty .empty-icon { font-size: 2.8rem; display: block; margin-bottom: 12px; }

/* ════════════════════════════════
   FOOTER
════════════════════════════════ */
footer {
  text-align: center;
  padding: 28px;
  color: #2a2a2a;
  font-size: .8rem;
  border-top: 1px solid #161616;
}

/* ════════════════════════════════
   RESPONSIVE
════════════════════════════════ */
@media (max-width: 580px) {
  .row { grid-template-columns: 1fr; }
  header h1 { font-size: 2.6rem; }
  .upload-box { padding: 22px 18px; }
}
</style>
</head>
<body>

<!-- ══ HEADER ══════════════════════════════════════════ -->
<header>
  <h1>🎶 My Music Studio</h1>
  <p>Crée des vidéos avec sous-titres automatiques Whisper AI</p>
</header>

<!-- ══ MAIN ════════════════════════════════════════════ -->
<div class="container">

  <div class="upload-box">

    <!-- Titre + Artiste -->
    <div class="row">
      <div class="field">
        <label class="field-label" for="title">Titre</label>
        <input type="text" id="title" placeholder="Ex : Bohemian Rhapsody">
      </div>
      <div class="field">
        <label class="field-label" for="artist">Artiste</label>
        <input type="text" id="artist" placeholder="Ex : Queen">
      </div>
    </div>

    <!-- Pochette + Audio -->
    <div class="row">
      <div class="field">
        <span class="field-label">Pochette (image)</span>
        <div class="file-input-wrapper">
          <span class="file-btn" id="coverLabel">📷 Choisir une image</span>
          <input type="file" id="coverFile" accept="image/*"
                 onchange="updateLabel('coverFile','coverLabel','coverName')">
        </div>
        <div class="file-name" id="coverName">Aucun fichier choisi</div>
      </div>
      <div class="field">
        <span class="field-label">Audio (MP3, WAV…)</span>
        <div class="file-input-wrapper">
          <span class="file-btn" id="audioLabel">🎵 Choisir l'audio</span>
          <input type="file" id="audioFile" accept="audio/*"
                 onchange="updateLabel('audioFile','audioLabel','audioName')">
        </div>
        <div class="file-name" id="audioName">Aucun fichier choisi</div>
      </div>
    </div>

    <!-- Toggle Whisper -->
    <div class="whisper-row">
      <input type="checkbox" id="useWhisper" checked>
      <label for="useWhisper">Ajouter les sous-titres automatiques avec Whisper</label>
      <span class="badge">AI</span>
    </div>
    <div class="whisper-info" id="whisperInfo">
      🔒 Clé API gérée côté serveur PHP — invisible dans le navigateur
    </div>

    <!-- Bouton -->
    <button class="create-btn" id="createBtn">🎬 CRÉER LA VIDÉO</button>

    <!-- Progression -->
    <div class="progress" id="progressBox">
      <div class="progress-header">
        <span id="statusText">Traitement…</span>
        <span id="pctText">0%</span>
      </div>
      <div class="progress-track">
        <div class="progress-fill" id="progressFill"></div>
      </div>
      <div class="progress-sub" id="statusSub">Ne fermez pas cette page</div>
    </div>

  </div><!-- /upload-box -->

  <!-- Galerie -->
  <div class="gallery" id="gallery" style="display:none">
    <div class="gallery-title">🎞️ Mes vidéos</div>
    <div id="videoGrid" class="video-grid"></div>
  </div>

  <div class="empty" id="empty">
    <span class="empty-icon">🎵</span>
    Aucune vidéo — créez la première !
  </div>

</div><!-- /container -->

<footer>© 2026 – My Music Studio &nbsp;·&nbsp; Powered by Whisper AI</footer>

<!-- ══ JAVASCRIPT ═══════════════════════════════════════ -->
<script>
'use strict';

/* ════════════════════════════════════════════════════════
   1. WHISPER — appel PHP proxy (clé API cachée côté serveur)
   On envoie le fichier audio en POST à index.php?action=whisper
   PHP appelle OpenAI et renvoie les segments JSON
════════════════════════════════════════════════════════ */
async function transcribeAudio(audioFile) {
  const form = new FormData();
  form.append('audio', audioFile, audioFile.name || 'audio.mp3');

  const res  = await fetch('?action=whisper', { method: 'POST', body: form });
  const data = await res.json();

  if (!res.ok) {
    throw new Error(data.error || 'Erreur Whisper (' + res.status + ')');
  }

  // verbose_json => data.segments = [{ start, end, text }, ...]
  return Array.isArray(data.segments) ? data.segments : [];
}

/* ════════════════════════════════════════════════════════
   2. CANVAS — rendu des frames vidéo
════════════════════════════════════════════════════════ */

// Retourne le sous-titre actif à l'instant "elapsed"
function getSubtitle(segments, elapsed) {
  for (const s of segments) {
    if (elapsed >= s.start && elapsed < s.end) return s.text.trim();
  }
  return '';
}

// Rectangle arrondi (pour le fond du sous-titre)
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

// Coupe le texte s'il est trop large
function clipText(ctx, text, maxW) {
  if (ctx.measureText(text).width <= maxW) return text;
  while (text.length > 3 && ctx.measureText(text + '…').width > maxW)
    text = text.slice(0, -1);
  return text + '…';
}

// Dessine un frame complet sur le canvas 1280×720
function drawFrame(ctx, img, title, artist, subtitle) {
  const W = 1280, H = 720;

  // ── Fond noir ──
  ctx.fillStyle = '#000';
  ctx.fillRect(0, 0, W, H);

  // ── Pochette centrée (remontée si sous-titre présent) ──
  const scale = Math.min(W / img.width, H / img.height) * 0.65;
  const iw = img.width  * scale;
  const ih = img.height * scale;
  const ix = (W - iw) / 2;
  const iy = (H - ih) / 2 + (subtitle ? -65 : -18);
  ctx.drawImage(img, ix, iy, iw, ih);

  // ── Titre ──
  ctx.textAlign   = 'center';
  ctx.shadowColor = 'rgba(0,0,0,.95)';
  ctx.shadowBlur  = 16;
  ctx.fillStyle   = '#ffffff';
  ctx.font        = 'bold 52px Arial';
  ctx.fillText(clipText(ctx, title,  1180), W / 2, 618);

  // ── Artiste ──
  ctx.font      = '36px Arial';
  ctx.fillStyle = '#bbbbbb';
  ctx.fillText(clipText(ctx, artist, 1180), W / 2, 664);
  ctx.shadowBlur = 0;

  // ── Sous-titre karaoké ──
  if (subtitle) {
    ctx.font       = 'bold 37px Arial';
    const sub      = clipText(ctx, subtitle, 1160);
    const tw       = ctx.measureText(sub).width;
    const bw       = tw + 44;
    const bh       = 54;
    const bx       = (W - bw) / 2;
    const by       = H - 65;

    // Fond semi-transparent
    ctx.fillStyle = 'rgba(0,0,0,.72)';
    roundRect(ctx, bx, by, bw, bh, 11);
    ctx.fill();

    // Texte avec glow
    ctx.shadowColor = '#d6004c';
    ctx.shadowBlur  = 22;
    ctx.fillStyle   = '#ffffff';
    ctx.fillText(sub, W / 2, by + 38);
    ctx.shadowBlur  = 0;
  }
}

/* ════════════════════════════════════════════════════════
   3. IndexedDB — stockage local des vidéos
   Initialisation lazy : getDB() ouvre la connexion
   seulement quand on en a besoin
════════════════════════════════════════════════════════ */
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
      _db.onclose        = () => { _db = null; };
      _db.onversionchange = () => { _db.close(); _db = null; };
      resolve(_db);
    };
    req.onerror   = e => reject(new Error('IndexedDB indisponible : ' + e.target.error));
    req.onblocked = () => reject(new Error('IndexedDB bloqué — fermez les autres onglets.'));
  });
}

async function dbSaveVideo(blob, title, artist) {
  const db = await getDB();
  return new Promise((resolve, reject) => {
    const tx = db.transaction('videos', 'readwrite');
    tx.objectStore('videos').put({
      id: Date.now().toString(), blob, title, artist, date: Date.now()
    });
    tx.oncomplete = () => resolve();
    tx.onerror    = e => reject(new Error('Sauvegarde échouée : ' + e.target.error));
  });
}

async function dbGetVideos() {
  const db = await getDB();
  return new Promise((resolve, reject) => {
    const req = db.transaction('videos', 'readonly').objectStore('videos').getAll();
    req.onsuccess = e => resolve(e.target.result || []);
    req.onerror   = e => reject(e.target.error);
  });
}

async function dbDeleteVideo(id) {
  if (!confirm('Supprimer cette vidéo ?')) return;
  const db = await getDB();
  const tx  = db.transaction('videos', 'readwrite');
  tx.objectStore('videos').delete(id);
  tx.oncomplete = () => loadGallery();
}

/* ════════════════════════════════════════════════════════
   4. GALERIE
════════════════════════════════════════════════════════ */
function esc(s) {
  return String(s)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

async function loadGallery() {
  try {
    const videos  = await dbGetVideos();
    const grid    = document.getElementById('videoGrid');
    const gallery = document.getElementById('gallery');
    const empty   = document.getElementById('empty');
    grid.innerHTML = '';

    if (!videos.length) {
      gallery.style.display = 'none';
      empty.style.display   = 'block';
      return;
    }
    empty.style.display   = 'none';
    gallery.style.display = 'block';

    videos.sort((a, b) => b.date - a.date).forEach(v => {
      const url  = URL.createObjectURL(v.blob);
      const card = document.createElement('div');
      card.className = 'video-card';
      card.innerHTML = `
        <video src="${url}" controls preload="metadata"></video>
        <div class="video-meta">
          <strong>${esc(v.title)}</strong>
          <span>${esc(v.artist)}</span>
        </div>
        <div class="video-actions">
          <a class="btn-dl" href="${url}" download="${esc(v.title)}.webm">⬇ Télécharger</a>
          <button class="btn-del" onclick="dbDeleteVideo('${v.id}')">🗑 Supprimer</button>
        </div>`;
      grid.appendChild(card);
    });
  } catch(e) {
    console.warn('loadGallery:', e);
  }
}

/* ════════════════════════════════════════════════════════
   5. HELPERS UI
════════════════════════════════════════════════════════ */
function updateLabel(inputId, labelId, nameId) {
  const f = document.getElementById(inputId).files[0];
  if (!f) return;
  document.getElementById(labelId).textContent = '✅ ' + f.name;
  document.getElementById(nameId).textContent  = f.name;
}

function setStatus(text, pct, sub) {
  if (text != null) document.getElementById('statusText').textContent = text;
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
  ['title', 'artist'].forEach(id => { document.getElementById(id).value = ''; });
  ['coverFile', 'audioFile'].forEach(id => { document.getElementById(id).value = ''; });
  document.getElementById('coverLabel').textContent = '📷 Choisir une image';
  document.getElementById('audioLabel').textContent = '🎵 Choisir l\'audio';
  document.getElementById('coverName').textContent  = 'Aucun fichier choisi';
  document.getElementById('audioName').textContent  = 'Aucun fichier choisi';
}

function readFileAs(file, mode) {
  return new Promise((res, rej) => {
    const r = new FileReader();
    r.onload  = e => res(e.target.result);
    r.onerror = () => rej(new Error('Erreur lecture fichier'));
    mode === 'buffer' ? r.readAsArrayBuffer(file) : r.readAsDataURL(file);
  });
}

/* ════════════════════════════════════════════════════════
   6. CRÉATION VIDÉO — pipeline principal
════════════════════════════════════════════════════════ */
document.getElementById('createBtn').addEventListener('click', async () => {

  const title      = document.getElementById('title').value.trim();
  const artist     = document.getElementById('artist').value.trim();
  const coverFile  = document.getElementById('coverFile').files[0];
  const audioFile  = document.getElementById('audioFile').files[0];
  const useWhisper = document.getElementById('useWhisper').checked;

  if (!title || !artist || !coverFile || !audioFile) {
    alert('Remplissez tous les champs et chargez la pochette + l\'audio.');
    return;
  }

  // ── Désactive le bouton + affiche la progression ──
  document.getElementById('createBtn').disabled        = true;
  document.getElementById('progressBox').style.display = 'block';
  setStatus('Initialisation…', 2, 'Ne fermez pas cette page');

  const wInfo  = document.getElementById('whisperInfo');
  let drawLoop = null;
  let audioCtx = null;
  let stopped  = false;

  // Fonction stop unique (évite double appel)
  function doStop(recorder) {
    if (stopped) return;
    stopped = true;
    if (drawLoop) { clearInterval(drawLoop); drawLoop = null; }
    setStatus('Finalisation…', 99, '');
    if (recorder.state === 'recording') {
      recorder.requestData();
      setTimeout(() => {
        try { recorder.stop(); } catch(e) {}
        if (audioCtx) audioCtx.close().catch(() => {});
      }, 700);
    }
  }

  try {

    // ── ÉTAPE 1 : vérification IndexedDB ──────────────
    setStatus('Vérification base de données…', 3);
    await getDB();

    // ── ÉTAPE 2 : transcription Whisper ───────────────
    let segments = [];
    if (useWhisper) {
      wInfo.className   = 'whisper-info loading';
      wInfo.textContent = '⏳ Transcription en cours… (30–60 secondes)';
      setStatus('Transcription Whisper AI…', 8, 'Envoi audio au serveur PHP…');
      try {
        segments = await transcribeAudio(audioFile);
        wInfo.className   = 'whisper-info ok';
        wInfo.textContent = '✅ ' + segments.length + ' segments transcrits';
        setStatus('Transcription OK !', 20, segments.length + ' segments');
        await new Promise(r => setTimeout(r, 500));
      } catch (wErr) {
        wInfo.className   = 'whisper-info err';
        wInfo.textContent = '❌ ' + wErr.message;
        const continuer = confirm(
          'Whisper a retourné une erreur :\n"' + wErr.message + '"\n\n' +
          'Créer la vidéo sans sous-titres ?'
        );
        if (!continuer) { resetUI(); return; }
        segments = [];
      }
    }

    // ── ÉTAPE 3 : chargement image en base64 ──────────
    // (évite que le canvas soit marqué "tainted" par le browser)
    setStatus('Chargement image…', 22);
    const imgB64 = await readFileAs(coverFile, 'dataurl');
    const img    = new Image();
    await new Promise((res, rej) => {
      img.onload  = res;
      img.onerror = () => rej(new Error('Impossible de charger l\'image'));
      img.src = imgB64;
    });

    // ── ÉTAPE 4 : décodage audio ──────────────────────
    setStatus('Décodage audio…', 28);
    const audioBuf = await readFileAs(audioFile, 'buffer');
    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    const decoded  = await audioCtx.decodeAudioData(audioBuf);
    const duration = decoded.duration;

    // ── ÉTAPE 5 : canvas 1280 × 720 ──────────────────
    const canvas = document.createElement('canvas');
    canvas.width  = 1280;
    canvas.height = 720;
    const ctx2d = canvas.getContext('2d');
    drawFrame(ctx2d, img, title, artist, ''); // frame initial

    // ── ÉTAPE 6 : captureStream ───────────────────────
    const stream = canvas.captureStream(30);
    if (!stream || stream.getVideoTracks().length === 0)
      throw new Error('captureStream() non supporté. Utilisez Chrome ou Edge.');

    // ── ÉTAPE 7 : routing audio → stream ─────────────
    const bufSrc = audioCtx.createBufferSource();
    bufSrc.buffer = decoded;
    const dest    = audioCtx.createMediaStreamDestination();
    bufSrc.connect(dest);
    const audioTrack = dest.stream.getAudioTracks()[0];
    if (audioTrack) stream.addTrack(audioTrack);

    // ── ÉTAPE 8 : MediaRecorder ───────────────────────
    const mimeType = [
      'video/webm;codecs=vp9,opus',
      'video/webm;codecs=vp8,opus',
      'video/webm'
    ].find(m => MediaRecorder.isTypeSupported(m)) || 'video/webm';

    const recorder = new MediaRecorder(stream, { mimeType });
    const chunks   = [];
    recorder.ondataavailable = e => { if (e.data?.size > 0) chunks.push(e.data); };

    recorder.onstop = async () => {
      setStatus('Sauvegarde…', 99, '');
      try {
        if (!chunks.length)
          throw new Error('Aucune donnée enregistrée. Utilisez Chrome ou Edge.');
        const blob = new Blob(chunks, { type: 'video/webm' });
        await dbSaveVideo(blob, title, artist);
        setStatus('✅ Vidéo sauvegardée !', 100, '');
        setTimeout(() => { resetUI(); loadGallery(); }, 900);
      } catch(err) {
        alert('Erreur sauvegarde : ' + err.message);
        resetUI();
      }
    };

    // ── ÉTAPE 9 : démarrage ───────────────────────────
    setStatus('Enregistrement…', 30, 'Ne fermez pas cette page');
    recorder.start(1000); // chunk toutes les 1s
    const t0 = audioCtx.currentTime;
    bufSrc.start(0);

    // ── ÉTAPE 10 : boucle draw 20fps + progression ────
    drawLoop = setInterval(() => {
      if (stopped) return;
      const elapsed = audioCtx.currentTime - t0;
      const pct     = Math.min(30 + (elapsed / duration) * 68, 98);
      setStatus(
        'Enregistrement…', pct,
        Math.floor(elapsed) + 's / ' + Math.floor(duration) + 's'
      );
      drawFrame(ctx2d, img, title, artist, getSubtitle(segments, elapsed));
      if (elapsed >= duration) doStop(recorder);
    }, 50);

    // Fallback : onended du BufferSource
    bufSrc.onended = () => doStop(recorder);

  } catch(err) {
    if (drawLoop) clearInterval(drawLoop);
    if (audioCtx) audioCtx.close().catch(() => {});
    console.error(err);
    alert('Erreur : ' + err.message);
    resetUI();
  }

}); // fin createBtn click

/* ════════════════════════════════════════════════════════
   7. DÉMARRAGE DE L'APP
════════════════════════════════════════════════════════ */
window.addEventListener('load', () => {
  getDB()
    .then(() => loadGallery())
    .catch(err => {
      console.error('DB init :', err);
      document.getElementById('empty').innerHTML =
        '<span class="empty-icon">⚠️</span>' +
        'IndexedDB indisponible.<br>Utilisez Chrome ou Edge avec un serveur PHP.';
    });
});
</script>
</body>
</html>
