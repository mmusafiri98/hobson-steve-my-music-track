<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>🎶 My Music Studio</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
    margin:0;
    font-family: 'Arial', sans-serif;
    background:#111;
    color:#fff;
}
header {
    background: linear-gradient(135deg,#d6004c,#7b1fa2);
    text-align:center;
    padding:40px 20px;
}
header h1 {
    margin:0;
    font-size:2.5rem;
}
header p {
    margin:5px 0 0;
    font-size:1.2rem;
    color:#eee;
}
.container {
    max-width:1000px;
    margin:auto;
    padding:20px;
}
.tabs {
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-bottom:30px;
}
.tab {
    flex:1;
    min-width:150px;
    padding:15px;
    background:#1e1e1e;
    border:none;
    border-radius:12px;
    cursor:pointer;
    color:#fff;
    font-weight:bold;
    transition:0.3s;
}
.tab.active {
    background: linear-gradient(135deg,#d6004c,#7b1fa2);
}
.section {
    display:none;
    animation: fadeIn 0.5s;
}
.section.active { display:block; }
@keyframes fadeIn { from{opacity:0} to{opacity:1} }

.upload-box {
    background:#1e1e1e;
    padding:25px;
    border-radius:12px;
    margin-bottom:40px;
}
input[type=text], input[type=file] {
    width:100%;
    padding:12px;
    margin-bottom:15px;
    border-radius:8px;
    border:none;
}
button {
    background:#d6004c;
    color:#fff;
    border:none;
    padding:12px 24px;
    border-radius:25px;
    cursor:pointer;
    transition:0.2s;
}
button:hover:not(:disabled){opacity:0.9;}
button:disabled{
    background:#555;
    cursor:not-allowed;
}

.progress { display:none; margin-top:15px; }
.progress-bar { width:100%; height:25px; background:#333; border-radius:15px; overflow:hidden; }
.progress-fill { height:100%; width:0; background:linear-gradient(90deg,#d6004c,#7b1fa2); text-align:center; line-height:25px; }

.gallery { margin-top:40px; }
.video-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:20px; }
.video-card { background:#1e1e1e; border-radius:12px; overflow:hidden; transition:0.2s; }
.video-card:hover { transform:scale(1.02); }
.video-card video { width:100%; height:180px; object-fit:cover; }
.video-info { padding:10px; font-size:0.95rem; }
.video-actions { display:flex; gap:8px; padding:0 10px 10px; }
.download-btn, .delete-btn { flex:1; border-radius:8px; text-align:center; padding:8px; font-size:0.85rem; cursor:pointer; }
.download-btn { background:#d6004c; color:#fff; text-decoration:none; display:flex; justify-content:center; align-items:center; }
.delete-btn { background:#444; color:#fff; border:none; }
.empty { text-align:center; color:#777; padding:60px; font-size:1.2rem; }
footer { text-align:center; padding:20px; color:#777; }
</style>
</head>
<body>

<header>
<h1>🎶 My Music Studio</h1>
<p>Crea e gestisci video localmente</p>
</header>

<div class="container">

<div class="tabs">
<button class="tab active" onclick="switchTab('create')">🎬 Crea Video</button>
</div>

<!-- CREA VIDEO -->
<div id="createSection" class="section active">
<div class="upload-box">
<input id="title" placeholder="Titolo">
<input id="artist" placeholder="Artista">
<label>Cover (immagine)</label>
<input type="file" id="coverFile" accept="image/*">
<label>Audio (MP3, WAV)</label>
<input type="file" id="audioFile" accept="audio/*">
<button id="createBtn" onclick="createVideo()">Crea Video</button>

<div class="progress" id="progressBox">
<div class="progress-bar"><div class="progress-fill" id="progressFill">0%</div></div>
<div id="status"></div>
</div>
</div>
</div>

<!-- GALLERIA -->
<div class="gallery" id="gallery" style="display:none">
<h2>🎞️ Video</h2>
<div id="videoGrid" class="video-grid"></div>
</div>

<div class="empty" id="empty">Nessun video</div>

</div>

<footer>© 2026 – My Music Studio</footer>

<script>
/* ------------------- DATABASE ------------------- */
let db;
function openDB(){
return new Promise((res,rej)=>{
const request=indexedDB.open("VideoDB",1);
request.onupgradeneeded=e=>{
db=e.target.result;
if(!db.objectStoreNames.contains("videos")){
db.createObjectStore("videos",{keyPath:"id"});
}
}
request.onsuccess=e=>{
db=e.target.result;
res();
};
request.onerror=e=>rej(e.target.error);
});
}
async function saveVideo(blob,title,artist){
return new Promise(resolve=>{
const tx=db.transaction("videos","readwrite");
tx.objectStore("videos").put({id:Date.now().toString(),blob,title,artist,date:Date.now()});
tx.oncomplete=()=>resolve();
});
}
async function getVideos(){
return new Promise(resolve=>{
const tx=db.transaction("videos","readonly");
const req=tx.objectStore("videos").getAll();
req.onsuccess=e=>resolve(e.target.result||[]);
});
}
async function deleteVideo(id){
if(!confirm("Eliminare questo video?"))return;
const tx=db.transaction("videos","readwrite");
tx.objectStore("videos").delete(id);
tx.oncomplete=()=>loadGallery();
}

/* ------------------- UI ------------------- */
function switchTab(tab){
document.querySelectorAll('.tab').forEach(b=>b.classList.remove('active'));
document.querySelectorAll('.section').forEach(s=>s.classList.remove('active'));
if(tab==='create'){
document.querySelectorAll('.tab')[0].classList.add('active');
document.getElementById('createSection').classList.add('active');
}
}

/* ------------------- CREATE VIDEO ------------------- */
async function createVideo(){
const title=document.getElementById("title").value.trim();
const artist=document.getElementById("artist").value.trim();
const coverFile=document.getElementById("coverFile").files[0];
const audioFile=document.getElementById("audioFile").files[0];
if(!title||!artist||!coverFile||!audioFile){alert("Compila tutti i campi"); return;}
const btn=document.getElementById("createBtn");
const progressBox=document.getElementById("progressBox");
const progressFill=document.getElementById("progressFill");
const status=document.getElementById("status");
btn.disabled=true;
progressBox.style.display="block";
status.textContent="Preparazione...";

try{
const canvas=document.createElement("canvas");
canvas.width=1280; canvas.height=720;
const ctx=canvas.getContext("2d");
const img=new Image();
img.src=URL.createObjectURL(coverFile);
await new Promise((res,rej)=>{img.onload=res; img.onerror=rej;});
function draw(){
ctx.fillStyle="#000"; ctx.fillRect(0,0,1280,720);
const scale=Math.min(1280/img.width,720/img.height)*0.7;
const x=(1280-img.width*scale)/2;
const y=(720-img.height*scale)/2-40;
ctx.drawImage(img,x,y,img.width*scale,img.height*scale);
ctx.fillStyle="#fff"; ctx.textAlign="center";
ctx.font="bold 48px Arial"; ctx.fillText(title,640,650);
ctx.font="32px Arial"; ctx.fillText(artist,640,700);
}

const audio=new Audio(URL.createObjectURL(audioFile));
await new Promise((res,rej)=>{audio.onloadedmetadata=res; audio.onerror=rej;});
const duration=audio.duration;
const stream=canvas.captureStream(30);
const audioCtx=new AudioContext();
const source=audioCtx.createMediaElementSource(audio);
const dest=audioCtx.createMediaStreamDestination();
source.connect(dest); source.connect(audioCtx.destination);
stream.addTrack(dest.stream.getAudioTracks()[0]);

const recorder=new MediaRecorder(stream,{mimeType:"video/webm"});
let chunks=[];
recorder.ondataavailable=e=>{if(e.data.size>0)chunks.push(e.data);}
recorder.onstop=async()=>{
const blob=new Blob(chunks,{type:"video/webm"});
await saveVideo(blob,title,artist);
bar.style.width="100%"; bar.innerText="100%";
status.textContent="✅ Video creato";
audioCtx.close();
btn.disabled=false;
document.getElementById("title").value="";
document.getElementById("artist").value="";
document.getElementById("coverFile").value="";
document.getElementById("audioFile").value="";
loadGallery();
};

recorder.start(); audio.play();
const drawLoop=setInterval(draw,33);
const bar=progressFill;
const progLoop=setInterval(()=>{
let p=(audio.currentTime/duration)*100; if(p>100)p=100;
bar.style.width=p+"%"; bar.innerText=Math.floor(p)+"%";
},300);

audio.onended=()=>{
clearInterval(drawLoop); clearInterval(progLoop);
recorder.requestData();
setTimeout(()=>{recorder.stop();},200);
};

setTimeout(()=>{if(recorder.state==="recording") recorder.stop();},duration*1000+2000);

}catch(e){alert("Errore: "+e.message); btn.disabled=false; progressBox.style.display="none";}
}

/* ------------------- GALLERY ------------------- */
async function loadGallery(){
const vids=await getVideos();
const grid=document.getElementById("videoGrid");
const gallery=document.getElementById("gallery");
const empty=document.getElementById("empty");
grid.innerHTML="";
if(!vids.length){gallery.style.display="none"; empty.style.display="block"; return;}
empty.style.display="none"; gallery.style.display="block";
vids.sort((a,b)=>b.date-a.date).forEach(v=>{
const url=URL.createObjectURL(v.blob);
const card=document.createElement("div");
card.className="video-card";
card.innerHTML=`
<video src="${url}" controls></video>
<div class="video-info"><b>${v.title}</b><br>${v.artist}</div>
<div class="video-actions">
<a class="download-btn" href="${url}" download="${v.title}.webm">Scarica</a>
<button class="delete-btn" onclick="deleteVideo('${v.id}')">Elimina</button>
</div>`;
grid.appendChild(card);
});
}

/* ------------------- INIT ------------------- */
window.onload=async()=>{await openDB(); loadGallery();}
</script>

</body>
</html>



