<?php
// ═══════════════════════════════════════════════════════
//  index.php — My Music Studio
//  Whisper tourne 100% dans le navigateur (Transformers.js)
//  Aucune clé API, aucun crédit, aucun serveur requis
// ═══════════════════════════════════════════════════════
// Pas de PHP côté serveur nécessaire pour Whisper ici.
// Ce fichier peut aussi être ouvert comme index.html simple.
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>🎶 My Music Studio</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
/* ════════════════════════════
   VARIABLES & RESET
════════════════════════════ */
@import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;600&display=swap');

:root {
  --red:    #d6004c;
  --purple: #7b1fa2;
  --bg:     #0e0e0e;
  --card:   #191919;
  --border: #2a2a2a;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: #fff; min-height: 100vh; }

/* ════════════════════════════
   HEADER
════════════════════════════ */
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

/* ════════════════════════════
   LAYOUT
════════════════════════════ */
.container { max-width: 960px; margin: auto; padding: 34px 20px 60px; }

/* ════════════════════════════
   BANDEAU WHISPER LOCAL
════════════════════════════ */
.whisper-banner {
  background: linear-gradient(135deg, #0d1b0d, #0a1a2e);
  border: 1px solid #2a4a2a;
  border-radius: 14px;
  padding: 18px 22px;
  margin-bottom: 26px;
  display: flex;
  align-items: flex-start;
  gap: 14px;
}
.whisper-banner .wb-icon { font-size: 2rem; flex-shrink: 0; margin-top: 2px; }
.whisper-banner .wb-body { flex: 1; }
.whisper-banner .wb-title {
  font-family: 'Bebas Neue', sans-serif;
  font-size: 1.1rem;
  letter-spacing: 1px;
  color: #4caf50;
  margin-bottom: 4px;
}
.whisper-banner .wb-desc { font-size: .82rem; color: #888; line-height: 1.5; }
.whisper-banner .wb-desc b { color: #aaa; }
.model-status {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  margin-top: 10px;
  font-size: .8rem;
  padding: 5px 12px;
  border-radius: 20px;
  background: #111;
  border: 1px solid var(--border);
  color: #888;
}
.model-status .dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: #555;
  flex-shrink: 0;
}
.model-status.loading .dot { background: #ffb300; animation: pulse 1s infinite; }
.model-status.ready   .dot { background: #4caf50; }
.model-status.ready       { color: #4caf50; border-color: #2a4a2a; }
.model-status.error   .dot { background: #f44336; }
.model-status.error       { color: #f44336; }

@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.3} }

/* ════════════════════════════
   UPLOAD BOX
════════════════════════════ */
.upload-box {
  background: var(--card);
  border: 1px solid var(--border);
  padding: 32px;
  border-radius: 18px;
  margin-bottom: 44px;
}
.row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
.field { display: flex; flex-direction: column; gap: 7px; }
.field-label { font-size: .74rem; letter-spacing: 1.2px; text-transform: uppercase; color: #777; font-weight: 600; }

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
input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
.file-name { font-size: .76rem; color: #555; margin-top: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* Toggle sous-titres */
.toggle-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 20px 0 4px;
  background: #111;
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 13px 16px;
  cursor: pointer;
}
.toggle-row input[type="checkbox"] { width: 18px; height: 18px; accent-color: #4caf50; cursor: pointer; flex-shrink: 0; }
.toggle-row label { font-size: .92rem; color: #ccc; cursor: pointer; flex: 1; }
.badge-free {
  background: #1a3a1a;
  color: #4caf50;
  border: 1px solid #2a5a2a;
  font-size: .66rem;
  padding: 3px 9px;
  border-radius: 20px;
  font-weight: 700;
  letter-spacing: .5px;
}

/* Bouton créer */
.create-btn {
  display: block; width: 100%; margin-top: 22px;
  background: linear-gradient(135deg, var(--red), var(--purple));
  color: #fff; border: none; padding: 16px;
  border-radius: 50px;
  font-family: 'Bebas Neue', sans-serif;
  font-size: 1.35rem; letter-spacing: 2px;
  cursor: pointer;
  transition: opacity .2s, transform .15s;
}
.create-btn:hover:not(:disabled) { opacity: .88; transform: translateY(-2px); }
.create-btn:disabled { background: #2a2a2a; color: #555; cursor: not-allowed; }

/* ════════════════════════════
   PROGRESSION
════════════════════════════ */
.progress { display: none; margin-top: 22px; }
.progress-header { display: flex; justify-content: space-between; font-size: .82rem; color: #888; margin-bottom: 9px; }
.progress-track { width: 100%; height: 9px; background: #222; border-radius: 5px; overflow: hidden; }
.progress-fill { height: 100%; width: 0%; background: linear-gradient(90deg, var(--red), var(--purple)); border-radius: 5px; transition: width .35s ease; }
.progress-sub { text-align: center; margin-top: 10px; font-size: .8rem; color: #555; }

/* Log Whisper en temps réel */
.whisper-log {
  display: none;
  margin-top: 14px;
  background: #0d0d0d;
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 12px 16px;
  max-height: 120px;
  overflow-y: auto;
  font-size: .78rem;
  color: #4caf50;
  font-family: monospace;
  line-height: 1.6;
}

/* ════════════════════════════
   GALERIE
════════════════════════════ */
.gallery { margin-top: 6px; }
.gallery-title { font-family: 'Bebas Neue', sans-serif; font-size: 1.9rem; letter-spacing: 2px; margin-bottom: 22px; }
.video-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(278px, 1fr)); gap: 22px; }
.video-card {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 14px;
  overflow: hidden;
  transition: transform .2s, box-shadow .2s;
}
.video-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(214,0,76,.15); }
.video-card video { width: 100%; height: 176px; object-fit: cover; display: block; }
.video-meta { padding: 13px 15px 6px; line-height: 1.5; }
.video-meta strong { font-size: .97rem; display: block; }
.video-meta span   { font-size: .83rem; color: #666; }
.video-actions { display: flex; gap: 9px; padding: 10px 15px 15px; }
.btn-dl, .btn-del {
  flex: 1; border-radius: 9px; text-align: center; padding: 9px 6px;
  font-size: .82rem; font-family: 'DM Sans', sans-serif; font-weight: 600;
  cursor: pointer; transition: opacity .15s; border: none;
  display: flex; align-items: center; justify-content: center; gap: 5px;
  text-decoration: none;
}
.btn-dl  { background: var(--red);  color: #fff; }
.btn-del { background: #252525; color: #888; }
.btn-dl:hover, .btn-del:hover { opacity: .78; }

.empty { text-align: center; color: #3a3a3a; padding: 80px 20px; font-size: 1.05rem; }
.empty-icon { font-size: 2.8rem; display: block; margin-bottom: 12px; }

footer { text-align: center; padding: 28px; color: #2a2a2a; font-size: .8rem; border-top: 1px solid #161616; }

@media (max-width: 580px) {
  .row { grid-template-columns: 1fr; }
  header h1 { font-size: 2.6rem; }
  .upload-box { padding: 22px 18px; }
}
</style>
</head>
<body>

<header>
  <h1>🎶 My Music Studio</h1>
  <p>Sous-titres automatiques Whisper — 100% gratuit, 100% dans le navigateur</p>
</header>

<div class="container">

  <!-- Bandeau info Whisper local -->
  <div class="whisper-banner">
    <div class="wb-icon">🤖</div>
    <div class="wb-body">
      <div class="wb-title">✅ Whisper tourne dans ton navigateur — Zéro clé API / Zéro crédit</div>
      <div class="wb-desc">
        Modèle utilisé : <b>Whisper Small</b> via Transformers.js (Hugging Face) &nbsp;·&nbsp;
        Première utilisation : téléchargement du modèle ~75 MB (mis en cache ensuite) &nbsp;·&nbsp;
        <b>Fonctionne offline après le premier chargement</b>
      </div>
      <div class="model-status" id="modelStatus">
        <span class="dot"></span>
        <span id="modelStatusText">En attente — le modèle se charge au premier clic</span>
      </div>
    </div>
  </div>

  <div class="upload-box">

    <!-- Titre + Artiste -->
    <div class="row">
      <div class="field">
        <span class="field-label">Titre</span>
        <input type="text" id="title" placeholder="Ex : Bohemian Rhapsody">
      </div>
      <div class="field">
        <span class="field-label">Artiste</span>
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
        <span class="field-label">Audio (MP3, WAV, MP4…)</span>
        <div class="file-input-wrapper">
          <span class="file-btn" id="audioLabel">🎵 Choisir l'audio ou la vidéo</span>
          <input type="file" id="audioFile" accept="audio/*,video/*"
                 onchange="updateLabel('audioFile','audioLabel','audioName')">
        </div>
        <div class="file-name" id="audioName">Aucun fichier choisi</div>
      </div>
    </div>

    <!-- Toggle sous-titres -->
    <div class="toggle-row">
      <input type="checkbox" id="useWhisper" checked>
      <label for="useWhisper">Ajouter les sous-titres automatiques (Whisper local)</label>
      <span class="badge-free">GRATUIT</span>
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

    <!-- Log Whisper en temps réel -->
    <div class="whisper-log" id="whisperLog"></div>

  </div>

  <!-- Galerie -->
  <div class="gallery" id="gallery" style="display:none">
    <div class="gallery-title">🎞️ Mes vidéos</div>
    <div id="videoGrid" class="video-grid"></div>
  </div>
  <div class="empty" id="empty">
    <span class="empty-icon">🎵</span>
    Aucune vidéo — créez la première !
  </div>

</div>

<footer>© 2026 – My Music Studio &nbsp;·&nbsp; Whisper by OpenAI · Transformers.js by Hugging Face</footer>

<script type="module">
// ════════════════════════════════════════════════════════
//  Transformers.js — Whisper tourne 100% dans le navigateur
//  Pas de clé API, pas de serveur, pas de crédit
// ════════════════════════════════════════════════════════
import {
  pipeline,
  env
} from 'https://cdn.jsdelivr.net/npm/@huggingface/transformers@3.1.0/dist/transformers.min.js';

// Utilise le cache du navigateur pour le modèle (évite re-téléchargement)
env.allowLocalModels  = false;
env.useBrowserCache   = true;

// ── Variables globales ──────────────────────────────────
let whisperPipeline = null;   // pipeline Whisper chargé
let modelLoading    = false;

// ── Références DOM ──────────────────────────────────────
const modelStatusEl   = document.getElementById('modelStatus');
const modelStatusText = document.getElementById('modelStatusText');
const whisperLog      = document.getElementById('whisperLog');

/* ════════════════════════════════════════════════════════
   CHARGEMENT DU MODÈLE WHISPER
   Modèle : openai/whisper-small (75 MB, mis en cache)
   Pour plus de précision : whisper-medium (300 MB)
   Pour plus de vitesse  : whisper-tiny (40 MB)
════════════════════════════════════════════════════════ */
function setModelStatus(state, text) {
  modelStatusEl.className = 'model-status ' + state;
  modelStatusText.textContent = text;
}

function logWhisper(msg) {
  whisperLog.style.display = 'block';
  whisperLog.innerHTML += msg + '<br>';
  whisperLog.scrollTop = whisperLog.scrollHeight;
}

async function loadWhisperModel() {
  if (whisperPipeline) return whisperPipeline;
  if (modelLoading)    return null;
  modelLoading = true;

  setModelStatus('loading', 'Chargement du modèle Whisper… (premier usage ~75 MB)');
  logWhisper('⏳ Téléchargement du modèle Whisper Small…');

  try {
    whisperPipeline = await pipeline(
      'automatic-speech-recognition',
      'onnx-community/whisper-small',   // modèle quantisé ONNX, léger et rapide
      {
        dtype: {
          encoder_model:    'fp32',
          decoder_model_merged: 'q4',   // quantisation 4bit pour la vitesse
        },
        progress_callback: (p) => {
          if (p.status === 'downloading') {
            const pct = p.progress ? Math.floor(p.progress) : '?';
            setModelStatus('loading', `Téléchargement : ${pct}% — ${p.file || ''}`);
          }
          if (p.status === 'done') {
            logWhisper('✅ Fichier chargé : ' + (p.file || ''));
          }
        }
      }
    );
    setModelStatus('ready', '✅ Modèle Whisper prêt — fonctionne offline');
    logWhisper('🎉 Whisper Small chargé et prêt !');
    modelLoading = false;
    return whisperPipeline;
  } catch(err) {
    setModelStatus('error', '❌ Erreur chargement : ' + err.message);
    logWhisper('❌ Erreur : ' + err.message);
    modelLoading = false;
    throw err;
  }
}

/* ════════════════════════════════════════════════════════
   TRANSCRIPTION avec Whisper local
   Retourne segments : [{ start, end, text }, ...]
════════════════════════════════════════════════════════ */
async function transcribeLocal(audioFile) {
  const pipe = await loadWhisperModel();
  if (!pipe) throw new Error('Modèle non chargé');

  logWhisper('🎵 Lecture du fichier audio…');

  // Convertit le fichier en AudioBuffer via Web Audio API
  const arrayBuf  = await audioFile.arrayBuffer();
  const audioCtxT = new (window.AudioContext || window.webkitAudioContext)({ sampleRate: 16000 });
  const decoded   = await audioCtxT.decodeAudioData(arrayBuf);
  audioCtxT.close();

  // Extrait le canal mono en Float32Array (Whisper attend 16kHz mono)
  const channelData = decoded.getChannelData(0);

  logWhisper('⚙️ Transcription en cours… (selon la durée : 10–120 sec)');
  setModelStatus('loading', '⚙️ Transcription en cours…');

  const result = await pipe(channelData, {
    language:            'french',     // change en 'english', 'italian', etc. si besoin
    task:                'transcribe',
    return_timestamps:   true,         // timestamps pour les sous-titres
    chunk_length_s:      30,           // découpe en chunks de 30s
    stride_length_s:     5,
    callback_function: (beams) => {
      // Affiche les mots au fur et à mesure
      const text = beams[0]?.output_token_ids ? '' : (beams[0]?.text || '');
      if (text) logWhisper('📝 ' + text);
    }
  });

  setModelStatus('ready', '✅ Modèle Whisper prêt');

  // Normalise les segments retournés
  // result.chunks = [{ timestamp: [start, end], text }]
  const segments = (result.chunks || []).map(c => ({
    start: c.timestamp[0] ?? 0,
    end:   c.timestamp[1] ?? (c.timestamp[0] + 3),
    text:  c.text.trim()
  })).filter(s => s.text.length > 0);

  logWhisper('✅ ' + segments.length + ' segments transcrits');
  return segments;
}

/* ════════════════════════════════════════════════════════
   CANVAS — rendu des frames
════════════════════════════════════════════════════════ */
function getSubtitle(segments, elapsed) {
  for (const s of segments) {
    if (elapsed >= s.start && elapsed < s.end) return s.text;
  }
  return '';
}

function roundRect(ctx, x, y, w, h, r) {
  ctx.beginPath();
  ctx.moveTo(x+r,y); ctx.lineTo(x+w-r,y);
  ctx.quadraticCurveTo(x+w,y,x+w,y+r);
  ctx.lineTo(x+w,y+h-r);
  ctx.quadraticCurveTo(x+w,y+h,x+w-r,y+h);
  ctx.lineTo(x+r,y+h);
  ctx.quadraticCurveTo(x,y+h,x,y+h-r);
  ctx.lineTo(x,y+r);
  ctx.quadraticCurveTo(x,y,x+r,y);
  ctx.closePath();
}

function clipText(ctx, text, maxW) {
  if (ctx.measureText(text).width <= maxW) return text;
  while (text.length > 3 && ctx.measureText(text+'…').width > maxW)
    text = text.slice(0,-1);
  return text + '…';
}

function drawFrame(ctx, img, title, artist, subtitle) {
  const W = 1280, H = 720;
  ctx.fillStyle = '#000';
  ctx.fillRect(0, 0, W, H);

  // Pochette
  const scale = Math.min(W/img.width, H/img.height) * 0.65;
  const iw = img.width * scale, ih = img.height * scale;
  const ix = (W-iw)/2, iy = (H-ih)/2 + (subtitle ? -65 : -18);
  ctx.drawImage(img, ix, iy, iw, ih);

  // Titre
  ctx.textAlign = 'center';
  ctx.shadowColor = 'rgba(0,0,0,.95)'; ctx.shadowBlur = 16;
  ctx.fillStyle = '#fff'; ctx.font = 'bold 52px Arial';
  ctx.fillText(clipText(ctx, title, 1180), W/2, 618);

  // Artiste
  ctx.font = '36px Arial'; ctx.fillStyle = '#bbb';
  ctx.fillText(clipText(ctx, artist, 1180), W/2, 664);
  ctx.shadowBlur = 0;

  // Sous-titre karaoké
  if (subtitle) {
    ctx.font = 'bold 37px Arial';
    const sub = clipText(ctx, subtitle, 1160);
    const tw  = ctx.measureText(sub).width;
    const bw  = tw + 44, bh = 54;
    const bx  = (W-bw)/2, by = H-65;
    ctx.fillStyle = 'rgba(0,0,0,.72)';
    roundRect(ctx, bx, by, bw, bh, 11); ctx.fill();
    ctx.shadowColor = '#d6004c'; ctx.shadowBlur = 22;
    ctx.fillStyle = '#fff';
    ctx.fillText(sub, W/2, by+38);
    ctx.shadowBlur = 0;
  }
}

/* ════════════════════════════════════════════════════════
   IndexedDB — stockage local lazy
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
      _db.onclose = () => { _db = null; };
      _db.onversionchange = () => { _db.close(); _db = null; };
      resolve(_db);
    };
    req.onerror   = e => reject(new Error('IndexedDB : ' + e.target.error));
    req.onblocked = () => reject(new Error('IndexedDB bloqué'));
  });
}
async function dbSave(blob, title, artist) {
  const db = await getDB();
  return new Promise((res, rej) => {
    const tx = db.transaction('videos','readwrite');
    tx.objectStore('videos').put({ id: Date.now().toString(), blob, title, artist, date: Date.now() });
    tx.oncomplete = () => res();
    tx.onerror    = e => rej(new Error('Sauvegarde : ' + e.target.error));
  });
}
async function dbGetAll() {
  const db = await getDB();
  return new Promise((res,rej) => {
    const req = db.transaction('videos','readonly').objectStore('videos').getAll();
    req.onsuccess = e => res(e.target.result||[]);
    req.onerror   = e => rej(e.target.error);
  });
}
async function dbDelete(id) {
  if (!confirm('Supprimer cette vidéo ?')) return;
  const db = await getDB();
  const tx  = db.transaction('videos','readwrite');
  tx.objectStore('videos').delete(id);
  tx.oncomplete = () => loadGallery();
}
// Expose dbDelete au HTML (onclick)
window.dbDelete = dbDelete;

/* ════════════════════════════════════════════════════════
   GALERIE
════════════════════════════════════════════════════════ */
function esc(s) {
  return String(s)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
async function loadGallery() {
  try {
    const videos  = await dbGetAll();
    const grid    = document.getElementById('videoGrid');
    const gallery = document.getElementById('gallery');
    const empty   = document.getElementById('empty');
    grid.innerHTML = '';
    if (!videos.length) { gallery.style.display='none'; empty.style.display='block'; return; }
    empty.style.display='none'; gallery.style.display='block';
    videos.sort((a,b)=>b.date-a.date).forEach(v => {
      const url  = URL.createObjectURL(v.blob);
      const card = document.createElement('div');
      card.className = 'video-card';
      card.innerHTML = `
        <video src="${url}" controls preload="metadata"></video>
        <div class="video-meta"><strong>${esc(v.title)}</strong><span>${esc(v.artist)}</span></div>
        <div class="video-actions">
          <a class="btn-dl" href="${url}" download="${esc(v.title)}.webm">⬇ Télécharger</a>
          <button class="btn-del" onclick="dbDelete('${v.id}')">🗑 Supprimer</button>
        </div>`;
      grid.appendChild(card);
    });
  } catch(e) { console.warn('gallery:', e); }
}

/* ════════════════════════════════════════════════════════
   UI HELPERS
════════════════════════════════════════════════════════ */
function updateLabel(inputId, labelId, nameId) {
  const f = document.getElementById(inputId).files[0];
  if (!f) return;
  document.getElementById(labelId).textContent = '✅ ' + f.name;
  document.getElementById(nameId).textContent  = f.name;
}
window.updateLabel = updateLabel;

function setStatus(text, pct, sub) {
  if (text!=null) document.getElementById('statusText').textContent = text;
  if (pct!=null)  {
    document.getElementById('progressFill').style.width = pct+'%';
    document.getElementById('pctText').textContent = Math.floor(pct)+'%';
  }
  if (sub!=null) document.getElementById('statusSub').textContent = sub;
}

function resetUI() {
  document.getElementById('createBtn').disabled = false;
  document.getElementById('progressBox').style.display = 'none';
  document.getElementById('progressFill').style.width  = '0%';
  document.getElementById('pctText').textContent = '0%';
  document.getElementById('whisperLog').style.display = 'none';
  document.getElementById('whisperLog').innerHTML = '';
  ['title','artist'].forEach(id => document.getElementById(id).value='');
  ['coverFile','audioFile'].forEach(id => document.getElementById(id).value='');
  document.getElementById('coverLabel').textContent = '📷 Choisir une image';
  document.getElementById('audioLabel').textContent = "🎵 Choisir l'audio ou la vidéo";
  document.getElementById('coverName').textContent  = 'Aucun fichier choisi';
  document.getElementById('audioName').textContent  = 'Aucun fichier choisi';
}

function readAs(file, mode) {
  return new Promise((res,rej) => {
    const r = new FileReader();
    r.onload  = e => res(e.target.result);
    r.onerror = () => rej(new Error('Erreur lecture'));
    mode==='buffer' ? r.readAsArrayBuffer(file) : r.readAsDataURL(file);
  });
}

/* ════════════════════════════════════════════════════════
   CRÉATION VIDÉO
════════════════════════════════════════════════════════ */
document.getElementById('createBtn').addEventListener('click', async () => {

  const title      = document.getElementById('title').value.trim();
  const artist     = document.getElementById('artist').value.trim();
  const coverFile  = document.getElementById('coverFile').files[0];
  const audioFile  = document.getElementById('audioFile').files[0];
  const useWhisper = document.getElementById('useWhisper').checked;

  if (!title||!artist||!coverFile||!audioFile) {
    alert("Remplissez tous les champs et chargez la pochette + l'audio.");
    return;
  }

  document.getElementById('createBtn').disabled = true;
  document.getElementById('progressBox').style.display = 'block';
  setStatus('Initialisation…', 2, 'Ne fermez pas cette page');

  let drawLoop = null, audioCtx = null, stopped = false;

  function doStop(recorder) {
    if (stopped) return;
    stopped = true;
    if (drawLoop) { clearInterval(drawLoop); drawLoop = null; }
    setStatus('Finalisation…', 99, '');
    if (recorder.state === 'recording') {
      recorder.requestData();
      setTimeout(() => {
        try { recorder.stop(); } catch(e) {}
        if (audioCtx) audioCtx.close().catch(()=>{});
      }, 700);
    }
  }

  try {
    // 1 — DB
    setStatus('Base de données…', 3);
    await getDB();

    // 2 — Whisper local (dans le navigateur)
    let segments = [];
    if (useWhisper) {
      setStatus('Chargement Whisper…', 5, 'Téléchargement modèle si première fois…');
      try {
        segments = await transcribeLocal(audioFile);
        setStatus('Transcription OK !', 20, segments.length + ' segments');
        await new Promise(r => setTimeout(r, 400));
      } catch(wErr) {
        console.warn('Whisper error:', wErr);
        logWhisper('⚠️ ' + wErr.message);
        const go = confirm('Erreur Whisper :\n"'+wErr.message+'"\n\nCréer la vidéo sans sous-titres ?');
        if (!go) { resetUI(); return; }
        segments = [];
      }
    }

    // 3 — Image base64
    setStatus('Chargement image…', 22);
    const imgB64 = await readAs(coverFile, 'dataurl');
    const img = new Image();
    await new Promise((res,rej) => { img.onload=res; img.onerror=()=>rej(new Error("Image invalide")); img.src=imgB64; });

    // 4 — Audio decode
    setStatus('Décodage audio…', 28);
    const audioBuf = await readAs(audioFile, 'buffer');
    audioCtx = new (window.AudioContext||window.webkitAudioContext)();
    const decoded  = await audioCtx.decodeAudioData(audioBuf);
    const duration = decoded.duration;

    // 5 — Canvas
    const canvas = document.createElement('canvas');
    canvas.width=1280; canvas.height=720;
    const ctx2d = canvas.getContext('2d');
    drawFrame(ctx2d, img, title, artist, '');

    // 6 — captureStream
    const stream = canvas.captureStream(30);
    if (!stream||stream.getVideoTracks().length===0)
      throw new Error('captureStream() non supporté. Utilisez Chrome ou Edge.');

    // 7 — Audio routing
    const bufSrc = audioCtx.createBufferSource();
    bufSrc.buffer = decoded;
    const dest = audioCtx.createMediaStreamDestination();
    bufSrc.connect(dest);
    const aTrack = dest.stream.getAudioTracks()[0];
    if (aTrack) stream.addTrack(aTrack);

    // 8 — Recorder
    const mimeType = ['video/webm;codecs=vp9,opus','video/webm;codecs=vp8,opus','video/webm']
      .find(m => MediaRecorder.isTypeSupported(m)) || 'video/webm';
    const recorder = new MediaRecorder(stream, { mimeType });
    const chunks = [];
    recorder.ondataavailable = e => { if (e.data?.size>0) chunks.push(e.data); };

    recorder.onstop = async () => {
      setStatus('Sauvegarde…', 99, '');
      try {
        if (!chunks.length) throw new Error('Aucune donnée. Utilisez Chrome ou Edge.');
        const blob = new Blob(chunks, { type:'video/webm' });
        await dbSave(blob, title, artist);
        setStatus('✅ Vidéo sauvegardée !', 100, '');
        setTimeout(() => { resetUI(); loadGallery(); }, 900);
      } catch(err) {
        alert('Erreur sauvegarde : ' + err.message);
        resetUI();
      }
    };

    // 9 — Start
    setStatus('Enregistrement…', 30, 'Ne fermez pas cette page');
    recorder.start(1000);
    const t0 = audioCtx.currentTime;
    bufSrc.start(0);

    // 10 — Loop
    drawLoop = setInterval(() => {
      if (stopped) return;
      const elapsed = audioCtx.currentTime - t0;
      const pct = Math.min(30+(elapsed/duration)*68, 98);
      setStatus('Enregistrement…', pct, Math.floor(elapsed)+'s / '+Math.floor(duration)+'s');
      drawFrame(ctx2d, img, title, artist, getSubtitle(segments, elapsed));
      if (elapsed >= duration) doStop(recorder);
    }, 50);

    bufSrc.onended = () => doStop(recorder);

  } catch(err) {
    if (drawLoop) clearInterval(drawLoop);
    if (audioCtx) audioCtx.close().catch(()=>{});
    console.error(err);
    alert('Erreur : ' + err.message);
    resetUI();
  }
});

/* ════════════════════════════════════════════════════════
   DÉMARRAGE
════════════════════════════════════════════════════════ */
getDB()
  .then(() => loadGallery())
  .catch(err => {
    console.error(err);
    document.getElementById('empty').innerHTML =
      '<span class="empty-icon">⚠️</span>IndexedDB indisponible.<br>Utilisez Chrome ou Edge.';
  });

</script>
</body>
</html>
