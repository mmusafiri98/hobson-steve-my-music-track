<?php
// index.php — My Music Studio
// Whisper Tiny — 40 MB seulement, rapide, gratuit, 100% dans le navigateur
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>🎶 My Music Studio</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;600&display=swap');

:root {
  --red:    #d6004c;
  --purple: #7b1fa2;
  --bg:     #0e0e0e;
  --card:   #191919;
  --border: #2a2a2a;
  --green:  #4caf50;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: #fff; min-height: 100vh; }

/* ── HEADER ── */
header {
  background: linear-gradient(135deg, var(--red), var(--purple));
  text-align: center; padding: 52px 20px 44px;
  position: relative; overflow: hidden;
}
header::before {
  content: ''; position: absolute; inset: 0;
  background: radial-gradient(ellipse at 60% 40%, rgba(255,255,255,.1) 0%, transparent 65%);
}
header h1 {
  font-family: 'Bebas Neue', sans-serif; font-size: 3.8rem;
  letter-spacing: 4px; position: relative;
}
header p { margin-top: 8px; font-size: .98rem; color: rgba(255,255,255,.75); letter-spacing: 1px; position: relative; }

.container { max-width: 960px; margin: auto; padding: 34px 20px 60px; }

/* ── BANDEAU MODÈLE ── */
.model-banner {
  background: #0d1a0d;
  border: 1px solid #1e3a1e;
  border-radius: 14px;
  padding: 20px 22px;
  margin-bottom: 26px;
}
.model-banner-top {
  display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
}
.model-icon { font-size: 2rem; flex-shrink: 0; }
.model-info { flex: 1; min-width: 180px; }
.model-info .model-title {
  font-family: 'Bebas Neue', sans-serif;
  font-size: 1.1rem; letter-spacing: 1px;
  color: var(--green); margin-bottom: 3px;
}
.model-info .model-desc { font-size: .8rem; color: #666; line-height: 1.5; }
.model-info .model-desc b { color: #999; }

/* Pill statut */
.status-pill {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 6px 14px; border-radius: 20px;
  background: #111; border: 1px solid var(--border);
  font-size: .78rem; color: #666;
  transition: all .3s;
}
.status-pill .dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: #444; flex-shrink: 0;
  transition: background .3s;
}
.status-pill.loading { color: #ffb300; border-color: #3a2a00; }
.status-pill.loading .dot { background: #ffb300; animation: blink 1s infinite; }
.status-pill.ready   { color: var(--green); border-color: #1e3a1e; }
.status-pill.ready   .dot { background: var(--green); }
.status-pill.error   { color: #f44336; border-color: #3a0a0a; }
.status-pill.error   .dot { background: #f44336; }

@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.25} }

/* Barre téléchargement modèle */
.model-dl-area {
  display: none;
  margin-top: 16px;
  background: #0a0a0a;
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 14px 16px;
}
.model-dl-header {
  display: flex; justify-content: space-between;
  font-size: .78rem; color: #888; margin-bottom: 8px;
}
.model-dl-track {
  width: 100%; height: 7px;
  background: #1a1a1a; border-radius: 4px; overflow: hidden;
  margin-bottom: 10px;
}
.model-dl-fill {
  height: 100%; width: 0%;
  background: linear-gradient(90deg, var(--green), #81c784);
  border-radius: 4px; transition: width .4s ease;
}
.model-dl-files {
  max-height: 90px; overflow-y: auto;
  font-size: .74rem; color: #4a4a4a;
  font-family: monospace; line-height: 1.7;
}
.model-dl-files .done  { color: var(--green); }
.model-dl-files .active { color: #ffb300; }

/* ── UPLOAD BOX ── */
.upload-box {
  background: var(--card); border: 1px solid var(--border);
  padding: 32px; border-radius: 18px; margin-bottom: 44px;
}
.row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
.field { display: flex; flex-direction: column; gap: 7px; }
.field-label { font-size: .74rem; letter-spacing: 1.2px; text-transform: uppercase; color: #777; font-weight: 600; }

input[type="text"] {
  background: #111; border: 1px solid var(--border); color: #fff;
  padding: 13px 15px; border-radius: 10px;
  font-family: 'DM Sans', sans-serif; font-size: .95rem;
  outline: none; transition: border-color .2s; width: 100%;
}
input[type="text"]:focus { border-color: var(--red); }

.file-input-wrapper { position: relative; overflow: hidden; }
.file-btn {
  background: #111; border: 1px dashed #3a3a3a; color: #888;
  padding: 13px 15px; border-radius: 10px; cursor: pointer;
  font-size: .9rem; text-align: center; display: block; width: 100%;
  transition: border-color .2s, color .2s;
}
.file-btn:hover { border-color: var(--red); color: #fff; }
input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
.file-name { font-size: .76rem; color: #555; margin-top: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.toggle-row {
  display: flex; align-items: center; gap: 12px;
  margin: 20px 0 4px; background: #111;
  border: 1px solid var(--border); border-radius: 10px; padding: 13px 16px;
}
.toggle-row input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--green); cursor: pointer; flex-shrink: 0; }
.toggle-row label { font-size: .92rem; color: #ccc; cursor: pointer; flex: 1; }
.badge-free {
  background: #1a3a1a; color: var(--green);
  border: 1px solid #2a5a2a;
  font-size: .66rem; padding: 3px 9px; border-radius: 20px;
  font-weight: 700; letter-spacing: .5px;
}

/* ── BOUTON ── */
.create-btn {
  display: block; width: 100%; margin-top: 22px;
  background: linear-gradient(135deg, var(--red), var(--purple));
  color: #fff; border: none; padding: 16px; border-radius: 50px;
  font-family: 'Bebas Neue', sans-serif; font-size: 1.35rem; letter-spacing: 2px;
  cursor: pointer; transition: opacity .2s, transform .15s;
}
.create-btn:hover:not(:disabled) { opacity: .88; transform: translateY(-2px); }
.create-btn:disabled { background: #2a2a2a; color: #555; cursor: not-allowed; }

/* ── PROGRESSION VIDÉO ── */
.progress { display: none; margin-top: 22px; }
.progress-header { display: flex; justify-content: space-between; font-size: .82rem; color: #888; margin-bottom: 9px; }
.progress-track { width: 100%; height: 9px; background: #222; border-radius: 5px; overflow: hidden; }
.progress-fill {
  height: 100%; width: 0%;
  background: linear-gradient(90deg, var(--red), var(--purple));
  border-radius: 5px; transition: width .35s ease;
}
.progress-sub { text-align: center; margin-top: 10px; font-size: .8rem; color: #555; }

/* ── LOG TRANSCRIPTION ── */
.transcription-log {
  display: none; margin-top: 14px;
  background: #0a0a0a; border: 1px solid var(--border);
  border-radius: 10px; padding: 12px 16px;
  max-height: 110px; overflow-y: auto;
  font-size: .78rem; color: var(--green);
  font-family: monospace; line-height: 1.7;
}

/* ── GALERIE ── */
.gallery { margin-top: 6px; }
.gallery-title { font-family: 'Bebas Neue', sans-serif; font-size: 1.9rem; letter-spacing: 2px; margin-bottom: 22px; }
.video-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(278px,1fr)); gap: 22px; }
.video-card {
  background: var(--card); border: 1px solid var(--border);
  border-radius: 14px; overflow: hidden; transition: transform .2s, box-shadow .2s;
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
.btn-dl  { background: var(--red); color: #fff; }
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
  <p>Sous-titres automatiques — 100% gratuit · 100% dans le navigateur</p>
</header>

<div class="container">

  <!-- Bandeau modèle -->
  <div class="model-banner">
    <div class="model-banner-top">
      <div class="model-icon">🤖</div>
      <div class="model-info">
        <div class="model-title">Whisper Tiny — Zéro clé API · Zéro crédit · Zéro serveur</div>
        <div class="model-desc">
          Modèle : <b>Whisper Tiny (~40 MB)</b> · Téléchargé une seule fois puis mis en cache ·
          <b>Fonctionne offline après le premier chargement</b>
        </div>
        <div style="margin-top:10px">
          <span class="status-pill" id="statusPill">
            <span class="dot"></span>
            <span id="statusPillText">En attente du premier clic</span>
          </span>
        </div>
      </div>
    </div>

    <!-- Zone progression téléchargement modèle -->
    <div class="model-dl-area" id="modelDlArea">
      <div class="model-dl-header">
        <span id="dlLabel">Téléchargement du modèle…</span>
        <span id="dlPct">0%</span>
      </div>
      <div class="model-dl-track">
        <div class="model-dl-fill" id="dlFill"></div>
      </div>
      <div class="model-dl-files" id="dlFiles"></div>
    </div>
  </div>

  <!-- Upload box -->
  <div class="upload-box">
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
          <span class="file-btn" id="audioLabel">🎵 Choisir l'audio</span>
          <input type="file" id="audioFile" accept="audio/*,video/*"
                 onchange="updateLabel('audioFile','audioLabel','audioName')">
        </div>
        <div class="file-name" id="audioName">Aucun fichier choisi</div>
      </div>
    </div>

    <div class="toggle-row">
      <input type="checkbox" id="useWhisper" checked>
      <label for="useWhisper">Ajouter les sous-titres automatiques (Whisper Tiny — local)</label>
      <span class="badge-free">GRATUIT</span>
    </div>

    <button class="create-btn" id="createBtn">🎬 CRÉER LA VIDÉO</button>

    <!-- Progression création vidéo -->
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

    <!-- Log transcription -->
    <div class="transcription-log" id="transLog"></div>

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

<footer>© 2026 – My Music Studio · Whisper by OpenAI · Transformers.js by Hugging Face</footer>

<script type="module">
'use strict';
import { pipeline, env }
  from 'https://cdn.jsdelivr.net/npm/@huggingface/transformers@3.1.0/dist/transformers.min.js';

env.allowLocalModels = false;
env.useBrowserCache  = true;   // mise en cache — le modèle ne se télécharge qu'une fois

/* ═══════════════════════════════════════════
   RÉFÉRENCES DOM
═══════════════════════════════════════════ */
const statusPill     = document.getElementById('statusPill');
const statusPillText = document.getElementById('statusPillText');
const modelDlArea    = document.getElementById('modelDlArea');
const dlLabel        = document.getElementById('dlLabel');
const dlPct          = document.getElementById('dlPct');
const dlFill         = document.getElementById('dlFill');
const dlFiles        = document.getElementById('dlFiles');
const transLog       = document.getElementById('transLog');

/* ═══════════════════════════════════════════
   HELPERS AFFICHAGE
═══════════════════════════════════════════ */
function setPill(state, text) {
  statusPill.className = 'status-pill ' + state;
  statusPillText.textContent = text;
}

// Fichiers déjà comptabilisés pour la progression
const FILE_WEIGHTS = {
  'encoder_model.onnx':         22,
  'decoder_model_merged.onnx':  14,
  'tokenizer.json':              1,
  'config.json':                 1,
  'tokenizer_config.json':       1,
  'preprocessor_config.json':    1,
};
const TOTAL_WEIGHT = Object.values(FILE_WEIGHTS).reduce((a,b)=>a+b, 0);
let loadedWeight = 0;
const filesDone  = new Set();

function onDownloadProgress(p) {
  if (p.status === 'initiate') {
    modelDlArea.style.display = 'block';
    const name = p.file || '';
    addFileRow(name, 'active', '⏳ ' + name);
  }
  if (p.status === 'progress') {
    const name    = p.file || '';
    const pct     = p.progress ? Math.floor(p.progress) : 0;
    updateFileRow(name, pct + '%');
    // Mise à jour progression globale
    const w = FILE_WEIGHTS[name] || 0.5;
    const contrib = (pct / 100) * w;
    const totalDone = loadedWeight + contrib;
    const globalPct = Math.min(Math.floor((totalDone / TOTAL_WEIGHT) * 100), 99);
    dlFill.style.width = globalPct + '%';
    dlPct.textContent  = globalPct + '%';
    dlLabel.textContent = 'Téléchargement : ' + name;
    setPill('loading', 'Téléchargement… ' + globalPct + '%');
  }
  if (p.status === 'done') {
    const name = p.file || '';
    if (!filesDone.has(name)) {
      filesDone.add(name);
      loadedWeight += FILE_WEIGHTS[name] || 0.5;
    }
    markFileDone(name);
  }
}

function addFileRow(name, cls, text) {
  // Évite les doublons
  if (document.getElementById('file-' + CSS.escape(name))) return;
  const div = document.createElement('div');
  div.id        = 'file-' + name;
  div.className = cls;
  div.textContent = text;
  dlFiles.appendChild(div);
  dlFiles.scrollTop = dlFiles.scrollHeight;
}
function updateFileRow(name, suffix) {
  const el = document.getElementById('file-' + name);
  if (el) el.textContent = '⏳ ' + name + ' — ' + suffix;
}
function markFileDone(name) {
  const el = document.getElementById('file-' + name);
  if (el) { el.className = 'done'; el.textContent = '✅ ' + name; }
}

function logTrans(msg) {
  transLog.style.display = 'block';
  const line = document.createElement('div');
  line.textContent = msg;
  transLog.appendChild(line);
  transLog.scrollTop = transLog.scrollHeight;
}

/* ═══════════════════════════════════════════
   CHARGEMENT WHISPER TINY
═══════════════════════════════════════════ */
let whisperPipe = null;
let isLoading   = false;

async function getWhisper() {
  if (whisperPipe) return whisperPipe;
  if (isLoading)   return null;
  isLoading = true;

  setPill('loading', 'Chargement du modèle Whisper Tiny…');
  modelDlArea.style.display = 'block';
  dlLabel.textContent = 'Initialisation…';

  whisperPipe = await pipeline(
    'automatic-speech-recognition',
    'onnx-community/whisper-tiny',        // ← Tiny : ~40 MB seulement
    {
      dtype: {
        encoder_model:        'fp32',
        decoder_model_merged: 'q4',       // quantisation 4-bit → encore plus léger
      },
      progress_callback: onDownloadProgress
    }
  );

  // Téléchargement terminé
  dlFill.style.width = '100%';
  dlPct.textContent  = '100%';
  dlLabel.textContent = '✅ Modèle prêt — mis en cache pour la prochaine fois';
  setPill('ready', '✅ Whisper Tiny prêt (offline)');
  isLoading = false;
  return whisperPipe;
}

/* ═══════════════════════════════════════════
   TRANSCRIPTION LOCALE
═══════════════════════════════════════════ */
async function transcribeLocal(audioFile) {
  const pipe = await getWhisper();
  if (!pipe) throw new Error('Modèle non chargé');

  logTrans('🎵 Lecture du fichier audio…');

  // Convertit en Float32Array 16kHz mono (format attendu par Whisper)
  const arrayBuf   = await audioFile.arrayBuffer();
  const audioCtxT  = new (window.AudioContext || window.webkitAudioContext)({ sampleRate: 16000 });
  const decoded    = await audioCtxT.decodeAudioData(arrayBuf);
  await audioCtxT.close();
  const channelData = decoded.getChannelData(0);

  logTrans('⚙️ Transcription… (' + Math.floor(decoded.duration) + 's audio)');
  setPill('loading', '⚙️ Transcription en cours…');

  const result = await pipe(channelData, {
    task:              'transcribe',
    return_timestamps:  true,
    chunk_length_s:     30,
    stride_length_s:     5,
  });

  setPill('ready', '✅ Whisper Tiny prêt');

  const segments = (result.chunks || [])
    .map(c => ({
      start: c.timestamp[0] ?? 0,
      end:   c.timestamp[1] ?? ((c.timestamp[0] ?? 0) + 3),
      text:  c.text.trim()
    }))
    .filter(s => s.text.length > 0);

  logTrans('✅ ' + segments.length + ' segments transcrits');
  segments.slice(0, 3).forEach(s =>
    logTrans('  [' + s.start.toFixed(1) + 's] ' + s.text)
  );
  if (segments.length > 3) logTrans('  …');
  return segments;
}

/* ═══════════════════════════════════════════
   CANVAS
═══════════════════════════════════════════ */
function getSubtitle(segs, t) {
  for (const s of segs)
    if (t >= s.start && t < s.end) return s.text;
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

function clip(ctx, txt, maxW) {
  if (ctx.measureText(txt).width <= maxW) return txt;
  while (txt.length > 3 && ctx.measureText(txt+'…').width > maxW) txt = txt.slice(0,-1);
  return txt + '…';
}

function drawFrame(ctx, img, title, artist, sub) {
  const W=1280, H=720;
  ctx.fillStyle='#000'; ctx.fillRect(0,0,W,H);

  const sc = Math.min(W/img.width, H/img.height) * 0.65;
  const iw = img.width*sc, ih = img.height*sc;
  ctx.drawImage(img, (W-iw)/2, (H-ih)/2+(sub?-65:-18), iw, ih);

  ctx.textAlign='center';
  ctx.shadowColor='rgba(0,0,0,.95)'; ctx.shadowBlur=16;
  ctx.fillStyle='#fff'; ctx.font='bold 52px Arial';
  ctx.fillText(clip(ctx,title,1180), W/2, 618);
  ctx.font='36px Arial'; ctx.fillStyle='#bbb';
  ctx.fillText(clip(ctx,artist,1180), W/2, 664);
  ctx.shadowBlur=0;

  if (sub) {
    ctx.font='bold 37px Arial';
    const s=clip(ctx,sub,1160), tw=ctx.measureText(s).width;
    const bw=tw+44, bh=54, bx=(W-bw)/2, by=H-65;
    ctx.fillStyle='rgba(0,0,0,.72)';
    roundRect(ctx,bx,by,bw,bh,11); ctx.fill();
    ctx.shadowColor='#d6004c'; ctx.shadowBlur=22;
    ctx.fillStyle='#fff'; ctx.fillText(s,W/2,by+38);
    ctx.shadowBlur=0;
  }
}

/* ═══════════════════════════════════════════
   INDEXEDDB
═══════════════════════════════════════════ */
let _db=null;
function getDB(){
  if(_db) return Promise.resolve(_db);
  return new Promise((res,rej)=>{
    const r=indexedDB.open('MusicStudioDB',2);
    r.onupgradeneeded=e=>{
      const d=e.target.result;
      if(!d.objectStoreNames.contains('videos'))
        d.createObjectStore('videos',{keyPath:'id'});
    };
    r.onsuccess=e=>{
      _db=e.target.result;
      _db.onclose=()=>{_db=null;};
      _db.onversionchange=()=>{_db.close();_db=null;};
      res(_db);
    };
    r.onerror=e=>rej(new Error('IndexedDB: '+e.target.error));
    r.onblocked=()=>rej(new Error('IndexedDB bloqué'));
  });
}
async function dbSave(blob,title,artist){
  const db=await getDB();
  return new Promise((res,rej)=>{
    const tx=db.transaction('videos','readwrite');
    tx.objectStore('videos').put({id:Date.now().toString(),blob,title,artist,date:Date.now()});
    tx.oncomplete=()=>res();
    tx.onerror=e=>rej(new Error('Sauvegarde: '+e.target.error));
  });
}
async function dbGetAll(){
  const db=await getDB();
  return new Promise((res,rej)=>{
    const req=db.transaction('videos','readonly').objectStore('videos').getAll();
    req.onsuccess=e=>res(e.target.result||[]);
    req.onerror=e=>rej(e.target.error);
  });
}
async function dbDelete(id){
  if(!confirm('Supprimer cette vidéo ?')) return;
  const db=await getDB();
  const tx=db.transaction('videos','readwrite');
  tx.objectStore('videos').delete(id);
  tx.oncomplete=()=>loadGallery();
}
window.dbDelete=dbDelete;

/* ═══════════════════════════════════════════
   GALERIE
═══════════════════════════════════════════ */
function esc(s){
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;')
    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
async function loadGallery(){
  try{
    const vs=await dbGetAll();
    const grid=document.getElementById('videoGrid');
    const gal=document.getElementById('gallery');
    const emp=document.getElementById('empty');
    grid.innerHTML='';
    if(!vs.length){gal.style.display='none';emp.style.display='block';return;}
    emp.style.display='none'; gal.style.display='block';
    vs.sort((a,b)=>b.date-a.date).forEach(v=>{
      const url=URL.createObjectURL(v.blob);
      const c=document.createElement('div');
      c.className='video-card';
      c.innerHTML=`
        <video src="${url}" controls preload="metadata"></video>
        <div class="video-meta"><strong>${esc(v.title)}</strong><span>${esc(v.artist)}</span></div>
        <div class="video-actions">
          <a class="btn-dl" href="${url}" download="${esc(v.title)}.webm">⬇ Télécharger</a>
          <button class="btn-del" onclick="dbDelete('${v.id}')">🗑 Supprimer</button>
        </div>`;
      grid.appendChild(c);
    });
  }catch(e){console.warn('gallery:',e);}
}

/* ═══════════════════════════════════════════
   UI HELPERS
═══════════════════════════════════════════ */
function updateLabel(iId,lId,nId){
  const f=document.getElementById(iId).files[0];
  if(!f) return;
  document.getElementById(lId).textContent='✅ '+f.name;
  document.getElementById(nId).textContent=f.name;
}
window.updateLabel=updateLabel;

function setStatus(txt,pct,sub){
  if(txt!=null) document.getElementById('statusText').textContent=txt;
  if(pct!=null){
    document.getElementById('progressFill').style.width=pct+'%';
    document.getElementById('pctText').textContent=Math.floor(pct)+'%';
  }
  if(sub!=null) document.getElementById('statusSub').textContent=sub;
}

function resetUI(){
  document.getElementById('createBtn').disabled=false;
  document.getElementById('progressBox').style.display='none';
  document.getElementById('progressFill').style.width='0%';
  document.getElementById('pctText').textContent='0%';
  document.getElementById('transLog').style.display='none';
  document.getElementById('transLog').innerHTML='';
  ['title','artist'].forEach(id=>document.getElementById(id).value='');
  ['coverFile','audioFile'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('coverLabel').textContent='📷 Choisir une image';
  document.getElementById('audioLabel').textContent="🎵 Choisir l'audio";
  document.getElementById('coverName').textContent='Aucun fichier choisi';
  document.getElementById('audioName').textContent='Aucun fichier choisi';
}

function readAs(file,mode){
  return new Promise((res,rej)=>{
    const r=new FileReader();
    r.onload=e=>res(e.target.result);
    r.onerror=()=>rej(new Error('Erreur lecture'));
    mode==='buffer'?r.readAsArrayBuffer(file):r.readAsDataURL(file);
  });
}

/* ═══════════════════════════════════════════
   CRÉATION VIDÉO
═══════════════════════════════════════════ */
document.getElementById('createBtn').addEventListener('click', async()=>{
  const title     =document.getElementById('title').value.trim();
  const artist    =document.getElementById('artist').value.trim();
  const coverFile =document.getElementById('coverFile').files[0];
  const audioFile =document.getElementById('audioFile').files[0];
  const useWhisper=document.getElementById('useWhisper').checked;

  if(!title||!artist||!coverFile||!audioFile){
    alert("Remplissez tous les champs et chargez la pochette + l'audio.");
    return;
  }

  document.getElementById('createBtn').disabled=true;
  document.getElementById('progressBox').style.display='block';
  setStatus('Initialisation…',2,'Ne fermez pas cette page');

  let drawLoop=null, audioCtx=null, stopped=false;

  function doStop(rec){
    if(stopped) return; stopped=true;
    if(drawLoop){clearInterval(drawLoop);drawLoop=null;}
    setStatus('Finalisation…',99,'');
    if(rec.state==='recording'){
      rec.requestData();
      setTimeout(()=>{
        try{rec.stop();}catch(e){}
        if(audioCtx) audioCtx.close().catch(()=>{});
      },700);
    }
  }

  try{
    // 1 — DB
    setStatus('Base de données…',3); await getDB();

    // 2 — Whisper local
    let segs=[];
    if(useWhisper){
      setStatus('Chargement Whisper Tiny…',5,'Première fois : ~40 MB à télécharger');
      try{
        segs=await transcribeLocal(audioFile);
        setStatus('Transcription OK !',20,segs.length+' segments');
        await new Promise(r=>setTimeout(r,400));
      }catch(e){
        console.warn('Whisper:',e); logTrans('⚠️ '+e.message);
        if(!confirm('Erreur Whisper :\n"'+e.message+'"\n\nContinuer sans sous-titres ?')){
          resetUI(); return;
        }
        segs=[];
      }
    }

    // 3 — Image
    setStatus('Chargement image…',22);
    const imgB64=await readAs(coverFile,'dataurl');
    const img=new Image();
    await new Promise((res,rej)=>{
      img.onload=res;
      img.onerror=()=>rej(new Error('Image invalide'));
      img.src=imgB64;
    });

    // 4 — Audio
    setStatus('Décodage audio…',28);
    const aBuf=await readAs(audioFile,'buffer');
    audioCtx=new (window.AudioContext||window.webkitAudioContext)();
    const decoded=await audioCtx.decodeAudioData(aBuf);
    const duration=decoded.duration;

    // 5 — Canvas
    const canvas=document.createElement('canvas');
    canvas.width=1280; canvas.height=720;
    const ctx2d=canvas.getContext('2d');
    drawFrame(ctx2d,img,title,artist,'');

    // 6 — Stream
    const stream=canvas.captureStream(30);
    if(!stream||!stream.getVideoTracks().length)
      throw new Error('captureStream() non supporté — utilisez Chrome ou Edge.');

    // 7 — Audio routing
    const bufSrc=audioCtx.createBufferSource();
    bufSrc.buffer=decoded;
    const dest=audioCtx.createMediaStreamDestination();
    bufSrc.connect(dest);
    const aTrack=dest.stream.getAudioTracks()[0];
    if(aTrack) stream.addTrack(aTrack);

    // 8 — Recorder
    const mime=['video/webm;codecs=vp9,opus','video/webm;codecs=vp8,opus','video/webm']
      .find(m=>MediaRecorder.isTypeSupported(m))||'video/webm';
    const rec=new MediaRecorder(stream,{mimeType:mime});
    const chunks=[];
    rec.ondataavailable=e=>{if(e.data?.size>0)chunks.push(e.data);};

    rec.onstop=async()=>{
      setStatus('Sauvegarde…',99,'');
      try{
        if(!chunks.length) throw new Error('Aucune donnée — utilisez Chrome ou Edge.');
        const blob=new Blob(chunks,{type:'video/webm'});
        await dbSave(blob,title,artist);
        setStatus('✅ Vidéo sauvegardée !',100,'');
        setTimeout(()=>{resetUI();loadGallery();},900);
      }catch(err){
        alert('Erreur sauvegarde : '+err.message);
        resetUI();
      }
    };

    // 9 — Start
    setStatus('Enregistrement…',30,'Ne fermez pas cette page');
    rec.start(1000);
    const t0=audioCtx.currentTime;
    bufSrc.start(0);

    // 10 — Loop
    drawLoop=setInterval(()=>{
      if(stopped) return;
      const el=audioCtx.currentTime-t0;
      const pct=Math.min(30+(el/duration)*68,98);
      setStatus('Enregistrement…',pct,Math.floor(el)+'s / '+Math.floor(duration)+'s');
      drawFrame(ctx2d,img,title,artist,getSubtitle(segs,el));
      if(el>=duration) doStop(rec);
    },50);

    bufSrc.onended=()=>doStop(rec);

  }catch(err){
    if(drawLoop) clearInterval(drawLoop);
    if(audioCtx) audioCtx.close().catch(()=>{});
    console.error(err);
    alert('Erreur : '+err.message);
    resetUI();
  }
});

/* ═══════════════════════════════════════════
   DÉMARRAGE
═══════════════════════════════════════════ */
getDB()
  .then(()=>loadGallery())
  .catch(err=>{
    console.error(err);
    document.getElementById('empty').innerHTML=
      '<span class="empty-icon">⚠️</span>IndexedDB indisponible.<br>Utilisez Chrome ou Edge.';
  });
</script>
</body>
</html>
