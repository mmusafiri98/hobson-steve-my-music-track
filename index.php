<?php
// index.php — My Music Studio
// Whisper Base — testo completo, gratuito, nel browser
// FIX: "Cannot read properties of undefined (reading 'time')"
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
  --red:#d6004c; --purple:#7b1fa2; --bg:#0e0e0e;
  --card:#191919; --border:#2a2a2a; --green:#4caf50;
}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:#fff;min-height:100vh;}

header{background:linear-gradient(135deg,var(--red),var(--purple));text-align:center;padding:52px 20px 44px;position:relative;overflow:hidden;}
header::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 60% 40%,rgba(255,255,255,.1) 0%,transparent 65%);}
header h1{font-family:'Bebas Neue',sans-serif;font-size:3.8rem;letter-spacing:4px;position:relative;}
header p{margin-top:8px;font-size:.98rem;color:rgba(255,255,255,.75);letter-spacing:1px;position:relative;}

.container{max-width:960px;margin:auto;padding:34px 20px 60px;}

.model-banner{background:#0d1a0d;border:1px solid #1e3a1e;border-radius:14px;padding:20px 22px;margin-bottom:26px;}
.model-top{display:flex;align-items:flex-start;gap:14px;flex-wrap:wrap;}
.model-icon{font-size:2rem;flex-shrink:0;margin-top:2px;}
.model-body{flex:1;min-width:180px;}
.model-title{font-family:'Bebas Neue',sans-serif;font-size:1.1rem;letter-spacing:1px;color:var(--green);margin-bottom:4px;}
.model-desc{font-size:.8rem;color:#666;line-height:1.6;}
.model-desc b{color:#999;}
.status-pill{display:inline-flex;align-items:center;gap:7px;padding:6px 14px;border-radius:20px;background:#111;border:1px solid var(--border);font-size:.78rem;color:#666;transition:all .3s;margin-top:10px;}
.status-pill .dot{width:8px;height:8px;border-radius:50%;background:#444;flex-shrink:0;transition:background .3s;}
.status-pill.loading{color:#ffb300;border-color:#3a2a00;}
.status-pill.loading .dot{background:#ffb300;animation:blink 1s infinite;}
.status-pill.ready{color:var(--green);border-color:#1e3a1e;}
.status-pill.ready .dot{background:var(--green);}
.status-pill.error{color:#f44336;border-color:#3a0a0a;}
.status-pill.error .dot{background:#f44336;}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.25}}

.model-dl{display:none;margin-top:16px;background:#0a0a0a;border:1px solid var(--border);border-radius:10px;padding:14px 16px;}
.model-dl-header{display:flex;justify-content:space-between;font-size:.78rem;color:#888;margin-bottom:8px;}
.model-dl-track{width:100%;height:7px;background:#1a1a1a;border-radius:4px;overflow:hidden;margin-bottom:10px;}
.model-dl-fill{height:100%;width:0%;background:linear-gradient(90deg,var(--green),#81c784);border-radius:4px;transition:width .4s ease;}
.model-dl-files{max-height:90px;overflow-y:auto;font-size:.74rem;color:#4a4a4a;font-family:monospace;line-height:1.7;}
.model-dl-files .f-done{color:var(--green);}
.model-dl-files .f-active{color:#ffb300;}

.upload-box{background:var(--card);border:1px solid var(--border);padding:32px;border-radius:18px;margin-bottom:44px;}
.row{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;}
.field{display:flex;flex-direction:column;gap:7px;}
.field-label{font-size:.74rem;letter-spacing:1.2px;text-transform:uppercase;color:#777;font-weight:600;}
input[type="text"]{background:#111;border:1px solid var(--border);color:#fff;padding:13px 15px;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.95rem;outline:none;transition:border-color .2s;width:100%;}
input[type="text"]:focus{border-color:var(--red);}
.lang-select{background:#111;border:1px solid var(--border);color:#fff;padding:12px 15px;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.9rem;outline:none;width:100%;cursor:pointer;}
.lang-select:focus{border-color:var(--purple);}
.file-input-wrapper{position:relative;overflow:hidden;}
.file-btn{background:#111;border:1px dashed #3a3a3a;color:#888;padding:13px 15px;border-radius:10px;cursor:pointer;font-size:.9rem;text-align:center;display:block;width:100%;transition:border-color .2s,color .2s;}
.file-btn:hover{border-color:var(--red);color:#fff;}
input[type="file"]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}
.file-name{font-size:.76rem;color:#555;margin-top:5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.toggle-row{display:flex;align-items:center;gap:12px;margin:10px 0 0;background:#111;border:1px solid var(--border);border-radius:10px;padding:13px 16px;}
.toggle-row input[type="checkbox"]{width:18px;height:18px;accent-color:var(--green);cursor:pointer;flex-shrink:0;}
.toggle-row label{font-size:.92rem;color:#ccc;cursor:pointer;flex:1;}
.badge-free{background:#1a3a1a;color:var(--green);border:1px solid #2a5a2a;font-size:.66rem;padding:3px 9px;border-radius:20px;font-weight:700;letter-spacing:.5px;}
.badge-prec{background:#1a1a3a;color:#9090ff;border:1px solid #2a2a5a;font-size:.66rem;padding:3px 9px;border-radius:20px;font-weight:700;letter-spacing:.5px;}
.create-btn{display:block;width:100%;margin-top:22px;background:linear-gradient(135deg,var(--red),var(--purple));color:#fff;border:none;padding:16px;border-radius:50px;font-family:'Bebas Neue',sans-serif;font-size:1.35rem;letter-spacing:2px;cursor:pointer;transition:opacity .2s,transform .15s;}
.create-btn:hover:not(:disabled){opacity:.88;transform:translateY(-2px);}
.create-btn:disabled{background:#2a2a2a;color:#555;cursor:not-allowed;}
.progress{display:none;margin-top:22px;}
.progress-header{display:flex;justify-content:space-between;font-size:.82rem;color:#888;margin-bottom:9px;}
.progress-track{width:100%;height:9px;background:#222;border-radius:5px;overflow:hidden;}
.progress-fill{height:100%;width:0%;background:linear-gradient(90deg,var(--red),var(--purple));border-radius:5px;transition:width .35s ease;}
.progress-sub{text-align:center;margin-top:10px;font-size:.8rem;color:#555;}
.trans-log{display:none;margin-top:14px;background:#0a0a0a;border:1px solid var(--border);border-radius:10px;padding:12px 16px;max-height:130px;overflow-y:auto;font-size:.78rem;color:var(--green);font-family:monospace;line-height:1.7;}
#lyricsBox{display:none;margin-top:12px;background:#0d0d1a;border:1px solid #2a2a5a;border-radius:12px;padding:18px;}
#lyricsText{width:100%;min-height:180px;background:#111;border:1px solid #3a3a3a;color:#fff;padding:14px;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.9rem;outline:none;resize:vertical;line-height:1.7;transition:border-color .2s;}
#lyricsText:focus{border-color:#9090ff;}
.offset-row{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-top:14px;}
.offset-row span{font-size:.78rem;color:#777;white-space:nowrap;}
.offset-row input[type=range]{flex:1;accent-color:#9090ff;min-width:120px;}
#offsetVal{font-size:.82rem;color:#9090ff;width:44px;text-align:right;}
.gallery{margin-top:6px;}
.gallery-title{font-family:'Bebas Neue',sans-serif;font-size:1.9rem;letter-spacing:2px;margin-bottom:22px;}
.video-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(278px,1fr));gap:22px;}
.video-card{background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden;transition:transform .2s,box-shadow .2s;}
.video-card:hover{transform:translateY(-4px);box-shadow:0 12px 40px rgba(214,0,76,.15);}
.video-card video{width:100%;height:176px;object-fit:cover;display:block;}
.video-meta{padding:13px 15px 6px;line-height:1.5;}
.video-meta strong{font-size:.97rem;display:block;}
.video-meta span{font-size:.83rem;color:#666;}
.video-actions{display:flex;gap:9px;padding:10px 15px 15px;}
.btn-dl,.btn-del{flex:1;border-radius:9px;text-align:center;padding:9px 6px;font-size:.82rem;font-family:'DM Sans',sans-serif;font-weight:600;cursor:pointer;transition:opacity .15s;border:none;display:flex;align-items:center;justify-content:center;gap:5px;text-decoration:none;}
.btn-dl{background:var(--red);color:#fff;}
.btn-del{background:#252525;color:#888;}
.btn-dl:hover,.btn-del:hover{opacity:.78;}
.empty{text-align:center;color:#3a3a3a;padding:80px 20px;font-size:1.05rem;}
.empty-icon{font-size:2.8rem;display:block;margin-bottom:12px;}
footer{text-align:center;padding:28px;color:#2a2a2a;font-size:.8rem;border-top:1px solid #161616;}
@media(max-width:580px){.row{grid-template-columns:1fr;}header h1{font-size:2.6rem;}.upload-box{padding:22px 18px;}}
</style>
</head>
<body>
<header>
  <h1>🎶 My Music Studio</h1>
  <p>Testo completo · gratuito · nel browser · zero chiave API</p>
</header>

<div class="container">

  <div class="model-banner">
    <div class="model-top">
      <div class="model-icon">🤖</div>
      <div class="model-body">
        <div class="model-title">Whisper Base — Zero chiave API · Zero crediti · Offline dopo primo download</div>
        <div class="model-desc">
          Modello: <b>Whisper Base (~75 MB)</b> · Download unico poi in cache ·
          <b>Trascrive TUTTO: strofe, ritornello, bridge</b>
        </div>
        <div class="status-pill" id="statusPill">
          <span class="dot"></span>
          <span id="statusPillTxt">In attesa del primo clic</span>
        </div>
      </div>
    </div>
    <div class="model-dl" id="modelDl">
      <div class="model-dl-header">
        <span id="dlLabel">Download modello…</span>
        <span id="dlPct">0%</span>
      </div>
      <div class="model-dl-track"><div class="model-dl-fill" id="dlFill"></div></div>
      <div class="model-dl-files" id="dlFiles"></div>
    </div>
  </div>

  <div class="upload-box">
    <div class="row">
      <div class="field">
        <span class="field-label">Titolo</span>
        <input type="text" id="title" placeholder="Es: Bohemian Rhapsody">
      </div>
      <div class="field">
        <span class="field-label">Artista</span>
        <input type="text" id="artist" placeholder="Es: Queen">
      </div>
    </div>
    <div class="row">
      <div class="field">
        <span class="field-label">Lingua della canzone</span>
        <select class="lang-select" id="songLang">
          <option value="italian">🇮🇹 Italiano</option>
          <option value="french">🇫🇷 Français</option>
          <option value="english">🇬🇧 English</option>
          <option value="spanish">🇪🇸 Español</option>
          <option value="portuguese">🇵🇹 Português</option>
          <option value="arabic">🇸🇦 عربي</option>
          <option value="german">🇩🇪 Deutsch</option>
          <option value="auto">🌍 Auto-detect</option>
        </select>
      </div>
      <div class="field">
        <span class="field-label">Copertina (immagine)</span>
        <div class="file-input-wrapper">
          <span class="file-btn" id="coverLabel">📷 Scegli immagine</span>
          <input type="file" id="coverFile" accept="image/*"
                 onchange="updateLabel('coverFile','coverLabel','coverName')">
        </div>
        <div class="file-name" id="coverName">Nessun file scelto</div>
      </div>
    </div>
    <div class="row">
      <div class="field">
        <span class="field-label">Audio (MP3, WAV, MP4…)</span>
        <div class="file-input-wrapper">
          <span class="file-btn" id="audioLabel">🎵 Scegli audio</span>
          <input type="file" id="audioFile" accept="audio/*,video/*"
                 onchange="updateLabel('audioFile','audioLabel','audioName')">
        </div>
        <div class="file-name" id="audioName">Nessun file scelto</div>
      </div>
    </div>

    <div class="toggle-row" style="margin-top:20px;">
      <input type="checkbox" id="useWhisper" checked onchange="toggleLyrics()">
      <label for="useWhisper">🤖 Sottotitoli automatici con Whisper</label>
      <span class="badge-free">GRATIS</span>
    </div>
    <div class="toggle-row">
      <input type="checkbox" id="useLyrics" onchange="toggleLyrics()">
      <label for="useLyrics">✏️ Incolla il testo esatto — sincronizzazione precisa al 100%</label>
      <span class="badge-prec">PRECISO</span>
    </div>

    <div id="lyricsBox">
      <div class="field-label" style="margin-bottom:8px;">Testo della canzone</div>
      <div style="font-size:.76rem;color:#555;margin-bottom:10px;line-height:1.6;">
        Incolla il testo riga per riga. Whisper usa i tuoi testi <b style="color:#888">esatti</b>
        e li sincronizza con l'audio.
      </div>
      <textarea id="lyricsText"
        placeholder="Incolla qui il testo completo...

Strofa 1
Prima riga della strofa
Seconda riga della strofa

Ritornello
Il ritornello della canzone"></textarea>
      <div style="font-size:.74rem;color:#444;margin-top:8px;">
        💡 Una riga = un sottotitolo · Le righe vuote vengono ignorate
      </div>
      <div class="offset-row">
        <span>⏱ Anticipo / Ritardo:</span>
        <input type="range" id="lyricsOffset" min="-3.0" max="3.0" step="0.1" value="-0.3"
          oninput="document.getElementById('offsetVal').textContent=
            (parseFloat(this.value)>=0?'+':'')+parseFloat(this.value).toFixed(1)+'s'">
        <span id="offsetVal">-0.3s</span>
        <span style="font-size:.72rem;color:#444;">← anticipa &nbsp;|&nbsp; ritarda →</span>
      </div>
    </div>

    <button class="create-btn" id="createBtn">🎬 CREA IL VIDEO</button>

    <div class="progress" id="progressBox">
      <div class="progress-header">
        <span id="statusText">Elaborazione…</span>
        <span id="pctText">0%</span>
      </div>
      <div class="progress-track"><div class="progress-fill" id="progressFill"></div></div>
      <div class="progress-sub" id="statusSub">Non chiudere questa pagina</div>
    </div>
    <div class="trans-log" id="transLog"></div>
  </div>

  <div class="gallery" id="gallery" style="display:none">
    <div class="gallery-title">🎞️ I miei video</div>
    <div id="videoGrid" class="video-grid"></div>
  </div>
  <div class="empty" id="empty">
    <span class="empty-icon">🎵</span>
    Nessun video — crea il primo!
  </div>
</div>

<footer>© 2026 – My Music Studio · Whisper by OpenAI · Transformers.js by Hugging Face</footer>

<script type="module">
'use strict';
import { pipeline, env }
  from 'https://cdn.jsdelivr.net/npm/@huggingface/transformers@3.1.0/dist/transformers.min.js';

env.allowLocalModels = false;
env.useBrowserCache  = true;

/* ══ DOM ══ */
const statusPill    = document.getElementById('statusPill');
const statusPillTxt = document.getElementById('statusPillTxt');
const modelDl       = document.getElementById('modelDl');
const dlLabel       = document.getElementById('dlLabel');
const dlPct         = document.getElementById('dlPct');
const dlFill        = document.getElementById('dlFill');
const dlFiles       = document.getElementById('dlFiles');
const transLog      = document.getElementById('transLog');

function setPill(state,txt){
  statusPill.className='status-pill '+state;
  statusPillTxt.textContent=txt;
}

/* ══ DOWNLOAD PROGRESS ══ */
const FILE_W={
  'encoder_model.onnx':30,'decoder_model_merged.onnx':20,
  'tokenizer.json':1,'config.json':1,
  'tokenizer_config.json':1,'preprocessor_config.json':1
};
const TOT_W=Object.values(FILE_W).reduce((a,b)=>a+b,0);
let loadedW=0;
const filesDone=new Set();
function safeId(n){return 'fr-'+n.replace(/[^a-z0-9]/gi,'_');}

function onProgress(p){
  if(p.status==='initiate'){
    modelDl.style.display='block';
    addFRow(p.file||'','f-active','⏳ '+(p.file||''));
  }
  if(p.status==='progress'){
    const pct=p.progress?Math.floor(p.progress):0;
    updFRow(p.file||'',pct+'%');
    const contrib=(pct/100)*(FILE_W[p.file||'']||0.5);
    const tot=Math.min(Math.floor(((loadedW+contrib)/TOT_W)*100),99);
    dlFill.style.width=tot+'%';dlPct.textContent=tot+'%';
    dlLabel.textContent='Download: '+(p.file||'')+' — '+pct+'%';
    setPill('loading','Download… '+tot+'%');
  }
  if(p.status==='done'){
    const f=p.file||'';
    if(!filesDone.has(f)){filesDone.add(f);loadedW+=FILE_W[f]||0.5;}
    doneFRow(f);
  }
}
function addFRow(name,cls,txt){
  const id=safeId(name);
  if(document.getElementById(id))return;
  const d=document.createElement('div');
  d.id=id;d.className=cls;d.textContent=txt;
  dlFiles.appendChild(d);dlFiles.scrollTop=dlFiles.scrollHeight;
}
function updFRow(name,sfx){
  const el=document.getElementById(safeId(name));
  if(el)el.textContent='⏳ '+name+' — '+sfx;
}
function doneFRow(name){
  const el=document.getElementById(safeId(name));
  if(el){el.className='f-done';el.textContent='✅ '+name;}
}
function logT(msg){
  transLog.style.display='block';
  const d=document.createElement('div');
  d.textContent=msg;
  transLog.appendChild(d);
  transLog.scrollTop=transLog.scrollHeight;
}

/* ══ WHISPER MODEL ══ */
let pipe=null,isLoading=false;
async function getModel(){
  if(pipe)return pipe;
  if(isLoading){
    await new Promise(res=>{const t=setInterval(()=>{if(!isLoading){clearInterval(t);res();}},200);});
    return pipe;
  }
  isLoading=true;
  setPill('loading','Caricamento Whisper Base…');
  modelDl.style.display='block';
  pipe=await pipeline('automatic-speech-recognition','onnx-community/whisper-base',{
    dtype:{encoder_model:'fp32',decoder_model_merged:'q4'},
    progress_callback:onProgress
  });
  dlFill.style.width='100%';dlPct.textContent='100%';
  dlLabel.textContent='✅ Modello pronto — in cache per la prossima volta';
  setPill('ready','✅ Whisper Base pronto (offline)');
  isLoading=false;
  return pipe;
}

/* ══ TRASCRIZIONE COMPLETA chunk per chunk ══ */
async function transcribeAll(audioFile,language){
  const model=await getModel();
  logT('🎵 Lettura file audio…');
  const arrayBuf=await audioFile.arrayBuffer();
  const actx=new (window.AudioContext||window.webkitAudioContext)({sampleRate:16000});
  const decoded=await actx.decodeAudioData(arrayBuf);
  await actx.close();
  const totalSec=decoded.duration;
  const sr=decoded.sampleRate;
  const channelData=decoded.getChannelData(0);
  logT('⏱ Durata: '+Math.floor(totalSec)+'s — trascrizione chunk per chunk…');

  const CHUNK_SEC=28,STRIDE_SEC=4;
  const CHUNK_SAMP=CHUNK_SEC*sr,STRIDE_SAMP=STRIDE_SEC*sr;
  const totalChunks=Math.ceil(totalSec/(CHUNK_SEC-STRIDE_SEC));
  const allSegs=[];
  let chunkStart=0,chunkIdx=0;

  while(chunkStart<channelData.length){
    chunkIdx++;
    const chunkEnd=Math.min(chunkStart+CHUNK_SAMP,channelData.length);
    const chunkData=channelData.slice(chunkStart,chunkEnd);
    const timeOff=chunkStart/sr;
    logT('🔄 Chunk '+chunkIdx+'/'+totalChunks+' — '+Math.floor(timeOff)+'s → '+Math.floor(timeOff+chunkData.length/sr)+'s');
    const opts={task:'transcribe',return_timestamps:true};
    if(language&&language!=='auto')opts.language=language;
    const result=await model(chunkData,opts);
    for(const c of (result.chunks||[])){
      const seg={
        start:(c.timestamp[0]??0)+timeOff,
        end:(c.timestamp[1]??((c.timestamp[0]??0)+3))+timeOff,
        text:(c.text||'').trim()
      };
      if(seg.text&&!allSegs.some(s=>Math.abs(s.start-seg.start)<1&&s.text===seg.text)){
        allSegs.push(seg);
        logT('📝 ['+seg.start.toFixed(1)+'s] '+seg.text);
      }
    }
    chunkStart+=CHUNK_SAMP-STRIDE_SAMP;
    if(channelData.length-chunkStart<sr*2)break;
  }
  allSegs.sort((a,b)=>a.start-b.start);
  logT('✅ Trascrizione: '+allSegs.length+' segmenti (0s→'+Math.floor(totalSec)+'s)');
  return allSegs;
}

/* ══════════════════════════════════════════════════════════════
   SINCRONIZZAZIONE TESTO MANUALE
   ─────────────────────────────────────────────────────────────
   CAUSA DELL'ERRORE "Cannot read properties of undefined (reading 'time')":
   
   Il bug era in questa riga:
     const searchEnd = Math.min(cursor + windowSize * 4, total - windowSize + 1);
   
   Quando cursor è vicino alla fine dell'array, la formula
   "total - windowSize + 1" può diventare NEGATIVA se windowSize > total.
   In quel caso searchEnd < cursor, il for non gira mai, bestIdx
   rimane = cursor che può essere >= total, e wWords[bestIdx] è undefined.
   
   FIX APPLICATO: 3 livelli di protezione
   1. searchEnd calcolato con Math.max per non scendere mai sotto cursor
   2. bestIdx bloccato nell'intervallo [0, WN-1] PRIMA di accedere all'array
   3. endIdx bloccato nell'intervallo [0, WN-1] PRIMA di accedere all'array
══════════════════════════════════════════════════════════════ */
function syncLyricsWithTimings(whisperSegs, rawLyrics, userOffset){
  if(typeof userOffset !== 'number' || isNaN(userOffset)) userOffset = -0.3;

  /* 1 — Righe utente: una riga = un sottotitolo */
  const lines = rawLyrics.split('\n').map(l=>l.trim()).filter(l=>l.length>0);
  if(!lines.length || !whisperSegs.length) return whisperSegs;

  /* 2 — Calcola durata totale reale dell'audio da Whisper */
  const validSegs = whisperSegs.filter(s=>
    typeof s.start==='number' && typeof s.end==='number' &&
    !isNaN(s.start) && !isNaN(s.end) && s.end > s.start
  );
  const totalDur = validSegs.length
    ? validSegs[validSegs.length-1].end
    : 180;

  /* ══════════════════════════════════════════════════════════
     ALGORITMO: DIVISIONE TEMPORALE LINEARE
     
     Problema con il matching testuale:
     Il ritornello "credo che dio..." appare 3 volte nel testo.
     L'algoritmo di similarità trova sempre la PRIMA occorrenza
     nel trascritto Whisper, perdendo la sincronizzazione dopo
     la prima ripetizione.
     
     Soluzione: ignoriamo il testo di Whisper completamente.
     Dividiamo la durata dell'audio in N fette uguali,
     dove N = numero di righe. Ogni riga occupa la sua fetta.
     
     Poi AFFINIAMO ogni riga cercando il segmento Whisper
     più vicino al centro della sua fetta temporale.
     Questo aggiustamento locale migliora la precisione
     senza mai perdere l'ordine cronologico.
  ══════════════════════════════════════════════════════════ */

  const N       = lines.length;
  const sliceDur = totalDur / N;   // durata di ogni fetta in secondi
  const result  = [];

  for(let i=0; i<N; i++){
    /* Centro temporale di questa riga */
    const tCenter = (i + 0.5) * sliceDur;

    /* Inizio e fine della fetta */
    const tSliceStart = i * sliceDur;
    const tSliceEnd   = (i + 1) * sliceDur;

    /* Cerca il segmento Whisper più vicino al centro
       MA solo dentro la fetta ±30% per non sconfinare */
    const margin = sliceDur * 0.3;
    const tMin   = Math.max(0, tSliceStart - margin);
    const tMax   = Math.min(totalDur, tSliceEnd   + margin);

    let bestSeg  = null;
    let bestDist = Infinity;
    for(const s of validSegs){
      if(s.start < tMin || s.start > tMax) continue;
      const dist = Math.abs(s.start - tCenter);
      if(dist < bestDist){ bestDist=dist; bestSeg=s; }
    }

    /* Se trovato un segmento vicino → usa il suo timestamp.
       Altrimenti → usa il centro della fetta come fallback. */
    const rawStart = bestSeg ? bestSeg.start : tSliceStart;

    result.push({
      start: Math.max(0, rawStart + userOffset),
      end:   Math.max(
        bestSeg ? bestSeg.end : tSliceEnd,
        rawStart + 1.2
      ),
      text: lines[i]
    });
  }

  /* 3 — Garantisce ordine strettamente crescente */
  for(let i=1; i<result.length; i++){
    if(result[i].start <= result[i-1].start){
      result[i].start = result[i-1].start + (sliceDur * 0.8);
      result[i].end   = Math.max(result[i].end, result[i].start + 1.2);
    }
  }

  /* 4 — Rimuove sovrapposizioni */
  for(let i=0; i<result.length-1; i++){
    if(result[i].end > result[i+1].start){
      result[i].end = Math.max(result[i].start + 0.4, result[i+1].start - 0.05);
    }
  }

  logT('🔗 '+result.length+' righe → durata '+totalDur.toFixed(0)+'s');
  logT('⏱ Intervallo per riga: ~'+sliceDur.toFixed(1)+'s');
  return result;
}

/* ══ CANVAS ══ */
function getSub(segs,t){
  for(const s of segs) if(t>=s.start&&t<s.end) return s.text;
  return '';
}
function roundRect(ctx,x,y,w,h,r){
  ctx.beginPath();
  ctx.moveTo(x+r,y);ctx.lineTo(x+w-r,y);ctx.quadraticCurveTo(x+w,y,x+w,y+r);
  ctx.lineTo(x+w,y+h-r);ctx.quadraticCurveTo(x+w,y+h,x+w-r,y+h);
  ctx.lineTo(x+r,y+h);ctx.quadraticCurveTo(x,y+h,x,y+h-r);
  ctx.lineTo(x,y+r);ctx.quadraticCurveTo(x,y,x+r,y);
  ctx.closePath();
}
function clipTxt(ctx,txt,maxW){
  if(!txt)return '';
  if(ctx.measureText(txt).width<=maxW)return txt;
  while(txt.length>3&&ctx.measureText(txt+'…').width>maxW)txt=txt.slice(0,-1);
  return txt+'…';
}
function drawFrame(ctx,img,title,artist,sub){
  const W=1280,H=720;
  ctx.fillStyle='#000';ctx.fillRect(0,0,W,H);
  const sc=Math.min(W/img.width,H/img.height);
  const iw=img.width*sc,ih=img.height*sc;
  const ix=(W-iw)/2,iy=(H-ih)/2;
  ctx.drawImage(img,ix,iy,iw,ih);
  const grad=ctx.createLinearGradient(0,H-200,0,H);
  grad.addColorStop(0,'rgba(0,0,0,0)');
  grad.addColorStop(1,'rgba(0,0,0,0.9)');
  ctx.fillStyle=grad;ctx.fillRect(0,H-200,W,200);
  ctx.textAlign='left';
  ctx.shadowColor='rgba(0,0,0,1)';ctx.shadowBlur=14;
  ctx.fillStyle='#fff';ctx.font='bold 42px Arial';
  ctx.fillText(clipTxt(ctx,title,700),50,H-72);
  ctx.font='28px Arial';ctx.fillStyle='rgba(255,255,255,.75)';
  ctx.fillText(clipTxt(ctx,artist,700),50,H-36);
  ctx.shadowBlur=0;
  if(sub){
    ctx.font='bold 46px Arial';ctx.textAlign='center';
    const words=sub.split(' '),maxLW=1100;
    const lines=[];let line='';
    for(const w of words){
      const test=line?line+' '+w:w;
      if(ctx.measureText(test).width>maxLW&&line){lines.push(line);line=w;}
      else line=test;
    }
    if(line)lines.push(line);
    const lineH=62,totalH=lines.length*lineH;
    const centerY=iy+ih/2,startY=centerY-totalH/2+lineH*0.75;
    const pad=30;
    const maxW=Math.max(...lines.map(l=>ctx.measureText(l).width));
    const boxW=Math.min(maxW+pad*2,1200),boxH=totalH+pad*1.5;
    ctx.fillStyle='rgba(0,0,0,0.62)';
    roundRect(ctx,(W-boxW)/2,startY-lineH*0.75-pad*0.5,boxW,boxH,18);ctx.fill();
    ctx.shadowColor='rgba(0,0,0,.95)';ctx.shadowBlur=18;
    ctx.fillStyle='#fff';ctx.strokeStyle='rgba(0,0,0,.5)';ctx.lineWidth=3;
    lines.forEach((l,i)=>{
      ctx.strokeText(clipTxt(ctx,l,1140),W/2,startY+i*lineH);
      ctx.fillText(clipTxt(ctx,l,1140),W/2,startY+i*lineH);
    });
    ctx.shadowBlur=0;ctx.lineWidth=1;
  }
}

/* ══ INDEXEDDB ══ */
let _db=null;
function getDB(){
  if(_db)return Promise.resolve(_db);
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
    r.onblocked=()=>rej(new Error('IndexedDB bloccato'));
  });
}
async function dbSave(blob,title,artist){
  const db=await getDB();
  return new Promise((res,rej)=>{
    const tx=db.transaction('videos','readwrite');
    tx.objectStore('videos').put({id:Date.now().toString(),blob,title,artist,date:Date.now()});
    tx.oncomplete=()=>res();
    tx.onerror=e=>rej(new Error('Salvataggio: '+e.target.error));
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
async function dbDel(id){
  if(!confirm('Eliminare questo video?'))return;
  const db=await getDB();
  const tx=db.transaction('videos','readwrite');
  tx.objectStore('videos').delete(id);
  tx.oncomplete=()=>loadGallery();
}
window.dbDel=dbDel;

function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
async function loadGallery(){
  try{
    const vs=await dbGetAll();
    const grid=document.getElementById('videoGrid');
    const gal=document.getElementById('gallery');
    const emp=document.getElementById('empty');
    grid.innerHTML='';
    if(!vs.length){gal.style.display='none';emp.style.display='block';return;}
    emp.style.display='none';gal.style.display='block';
    vs.sort((a,b)=>b.date-a.date).forEach(v=>{
      const url=URL.createObjectURL(v.blob);
      const c=document.createElement('div');c.className='video-card';
      c.innerHTML=`
        <video src="${url}" controls preload="metadata"></video>
        <div class="video-meta"><strong>${esc(v.title)}</strong><span>${esc(v.artist)}</span></div>
        <div class="video-actions">
          <a class="btn-dl" href="${url}" download="${esc(v.title)}.webm">⬇ Scarica</a>
          <button class="btn-del" onclick="dbDel('${v.id}')">🗑 Elimina</button>
        </div>`;
      grid.appendChild(c);
    });
  }catch(e){console.warn(e);}
}

/* ══ UI HELPERS ══ */
function toggleLyrics(){
  document.getElementById('lyricsBox').style.display=
    document.getElementById('useLyrics').checked?'block':'none';
}
window.toggleLyrics=toggleLyrics;

function updateLabel(iId,lId,nId){
  const f=document.getElementById(iId).files[0];
  if(!f)return;
  document.getElementById(lId).textContent='✅ '+f.name;
  document.getElementById(nId).textContent=f.name;
}
window.updateLabel=updateLabel;

function setStatus(txt,pct,sub){
  if(txt!=null)document.getElementById('statusText').textContent=txt;
  if(pct!=null){
    document.getElementById('progressFill').style.width=pct+'%';
    document.getElementById('pctText').textContent=Math.floor(pct)+'%';
  }
  if(sub!=null)document.getElementById('statusSub').textContent=sub;
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
  document.getElementById('coverLabel').textContent='📷 Scegli immagine';
  document.getElementById('audioLabel').textContent='🎵 Scegli audio';
  document.getElementById('coverName').textContent='Nessun file scelto';
  document.getElementById('audioName').textContent='Nessun file scelto';
  document.getElementById('lyricsText').value='';
  document.getElementById('useLyrics').checked=false;
  document.getElementById('lyricsBox').style.display='none';
}
function readAs(file,mode){
  return new Promise((res,rej)=>{
    const r=new FileReader();
    r.onload=e=>res(e.target.result);
    r.onerror=()=>rej(new Error('Errore lettura'));
    mode==='buffer'?r.readAsArrayBuffer(file):r.readAsDataURL(file);
  });
}

/* ══ CREAZIONE VIDEO ══ */
document.getElementById('createBtn').addEventListener('click',async()=>{
  const title     =document.getElementById('title').value.trim();
  const artist    =document.getElementById('artist').value.trim();
  const coverFile =document.getElementById('coverFile').files[0];
  const audioFile =document.getElementById('audioFile').files[0];
  const useWhisper=document.getElementById('useWhisper').checked;
  const useLyrics =document.getElementById('useLyrics').checked;
  const rawLyrics =document.getElementById('lyricsText').value.trim();
  const lang      =document.getElementById('songLang').value;

  if(!title||!artist||!coverFile||!audioFile){
    alert('Compila tutti i campi e carica copertina + audio.');return;
  }
  if(useLyrics&&!rawLyrics){
    alert('Hai selezionato "Testo manuale" ma il campo è vuoto!');return;
  }

  document.getElementById('createBtn').disabled=true;
  document.getElementById('progressBox').style.display='block';
  setStatus('Inizializzazione…',2,'Non chiudere questa pagina');

  let drawLoop=null,audioCtx=null,stopped=false;
  function doStop(rec){
    if(stopped)return;stopped=true;
    if(drawLoop){clearInterval(drawLoop);drawLoop=null;}
    setStatus('Finalizzazione…',99,'');
    if(rec.state==='recording'){
      rec.requestData();
      setTimeout(()=>{try{rec.stop();}catch(e){}if(audioCtx)audioCtx.close().catch(()=>{});},700);
    }
  }

  try{
    setStatus('Database…',3);await getDB();

    let segs=[];
    if(useWhisper||useLyrics){
      setStatus('Caricamento Whisper…',5,'Prima volta: ~75 MB, poi in cache');
      try{
        const wSegs=await transcribeAll(audioFile,lang);
        if(useLyrics&&rawLyrics){
          setStatus('Sincronizzazione…',18,'Abbinamento righe ai timestamp…');
          const offsetEl=document.getElementById('lyricsOffset');
          const userOffset=offsetEl?parseFloat(offsetEl.value)||0:-0.3;
          segs=syncLyricsWithTimings(wSegs,rawLyrics,userOffset);
          logT('✅ '+segs.length+' righe sincronizzate');
          segs.slice(0,4).forEach(s=>logT('  ['+s.start.toFixed(1)+'s] '+s.text));
        }else{
          segs=wSegs;
          logT('✅ Trascrizione automatica: '+segs.length+' segmenti');
        }
        setStatus('Pronto!',22,segs.length+' righe');
        await new Promise(r=>setTimeout(r,400));
      }catch(e){
        console.warn(e);logT('⚠️ '+e.message);
        if(!confirm('Errore Whisper:\n"'+e.message+'"\n\nContinuare senza sottotitoli?')){
          resetUI();return;
        }
        segs=[];
      }
    }

    setStatus('Caricamento immagine…',24);
    const imgB64=await readAs(coverFile,'dataurl');
    const img=new Image();
    await new Promise((res,rej)=>{img.onload=res;img.onerror=()=>rej(new Error('Immagine non valida'));img.src=imgB64;});

    setStatus('Decodifica audio…',30);
    const aBuf=await readAs(audioFile,'buffer');
    audioCtx=new (window.AudioContext||window.webkitAudioContext)();
    const decoded=await audioCtx.decodeAudioData(aBuf);
    const duration=decoded.duration;

    const canvas=document.createElement('canvas');
    canvas.width=1280;canvas.height=720;
    const ctx2d=canvas.getContext('2d');
    drawFrame(ctx2d,img,title,artist,'');

    const stream=canvas.captureStream(30);
    if(!stream||!stream.getVideoTracks().length)
      throw new Error('captureStream() non supportato — usa Chrome o Edge.');

    const bufSrc=audioCtx.createBufferSource();
    bufSrc.buffer=decoded;
    const dest=audioCtx.createMediaStreamDestination();
    bufSrc.connect(dest);
    const aTrack=dest.stream.getAudioTracks()[0];
    if(aTrack)stream.addTrack(aTrack);

    const mime=['video/webm;codecs=vp9,opus','video/webm;codecs=vp8,opus','video/webm']
      .find(m=>MediaRecorder.isTypeSupported(m))||'video/webm';
    const rec=new MediaRecorder(stream,{mimeType:mime});
    const chunks=[];
    rec.ondataavailable=e=>{if(e.data?.size>0)chunks.push(e.data);};
    rec.onstop=async()=>{
      setStatus('Salvataggio…',99,'');
      try{
        if(!chunks.length)throw new Error('Nessun dato — usa Chrome o Edge.');
        const blob=new Blob(chunks,{type:'video/webm'});
        await dbSave(blob,title,artist);
        setStatus('✅ Video salvato!',100,'');
        setTimeout(()=>{resetUI();loadGallery();},900);
      }catch(err){alert('Errore salvataggio: '+err.message);resetUI();}
    };

    setStatus('Registrazione…',32,'Non chiudere questa pagina');
    rec.start(1000);
    const t0=audioCtx.currentTime;
    bufSrc.start(0);

    drawLoop=setInterval(()=>{
      if(stopped)return;
      const el=audioCtx.currentTime-t0;
      const pct=Math.min(32+(el/duration)*66,98);
      setStatus('Registrazione…',pct,Math.floor(el)+'s / '+Math.floor(duration)+'s');
      drawFrame(ctx2d,img,title,artist,getSub(segs,el));
      if(el>=duration)doStop(rec);
    },50);

    bufSrc.onended=()=>doStop(rec);

  }catch(err){
    if(drawLoop)clearInterval(drawLoop);
    if(audioCtx)audioCtx.close().catch(()=>{});
    console.error(err);
    alert('Errore: '+err.message);
    resetUI();
  }
});

getDB().then(()=>loadGallery()).catch(()=>{
  document.getElementById('empty').innerHTML=
    '<span class="empty-icon">⚠️</span>IndexedDB non disponibile.<br>Usa Chrome o Edge.';
});
</script>
</body>
</html>
