<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>My Music Studio – Cover Video</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
*{box-sizing:border-box;font-family:Arial,sans-serif}
body{margin:0;background:#111;color:#fff}
header{padding:30px;text-align:center;background:linear-gradient(135deg,#d6004c,#7b1fa2)}
.container{max-width:1000px;margin:auto;padding:40px 20px}
.tabs{display:flex;gap:10px;margin-bottom:30px;flex-wrap:wrap}
.tab{flex:1;min-width:150px;padding:15px;background:#1e1e1e;border:none;color:#fff;cursor:pointer;border-radius:12px}
.tab.active{background:linear-gradient(135deg,#d6004c,#7b1fa2)}
.section{display:none}
.section.active{display:block}
.upload-box{background:#1e1e1e;padding:25px;border-radius:12px;margin-bottom:40px}
input{width:100%;padding:12px;margin-bottom:15px;border-radius:8px;border:none}
input[type=file]{background:#fff;color:#000}
button{background:#d6004c;color:#fff;border:none;padding:12px 24px;border-radius:25px;cursor:pointer}
button:disabled{background:#555}
.upload-area{border:3px dashed #d6004c;border-radius:12px;padding:40px;text-align:center;cursor:pointer}
.progress{display:none;margin-top:15px}
.progress-bar{height:25px;background:#333;border-radius:15px;overflow:hidden}
.progress-fill{height:100%;width:0;background:linear-gradient(90deg,#d6004c,#7b1fa2);text-align:center;line-height:25px}
.gallery{margin-top:40px}
.video-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px}
.video-card{background:#1e1e1e;border-radius:12px;overflow:hidden}
.video-card video{width:100%;height:200px;object-fit:cover}
.video-info{padding:15px}
.video-actions{display:flex;gap:10px;padding:0 15px 15px}
.download-btn{flex:1;background:#d6004c;color:#fff;text-decoration:none;padding:8px;border-radius:8px;text-align:center}
.delete-btn{flex:1;background:#444;color:#fff;border:none;border-radius:8px;cursor:pointer;padding:8px}
.empty{text-align:center;color:#777;padding:60px}
footer{text-align:center;padding:20px;color:#777}
</style>
</head>

<body>

<header>
<h1>🎶 My Music Studio</h1>
<p>Crea e gestisci video localmente</p>
</header>

<div class="container">

<div class="upload-box">
<input id="title" placeholder="Titolo">
<input id="artist" placeholder="Artista">
<input type="file" id="coverFile" accept="image/*">
<input type="file" id="audioFile" accept="audio/*">
<button id="createBtn" onclick="createVideo()">Crea Video</button>

<div class="progress" id="progressBox">
<div class="progress-bar">
<div class="progress-fill" id="progressFill">0%</div>
</div>
<div id="status"></div>
</div>
</div>

<div class="gallery" id="gallery" style="display:none">
<h2>🎞️ Video</h2>
<div id="videoGrid" class="video-grid"></div>
</div>

<div class="empty" id="empty">Nessun video</div>

</div>

<footer>© 2026 – My Music Studio</footer>

<script>

/* ================= DATABASE ================= */

let db;

function openDB(){
return new Promise((res,rej)=>{
const request=indexedDB.open("VideoDB",1);

request.onupgradeneeded=e=>{
const db=e.target.result;
db.createObjectStore("videos",{keyPath:"id"});
};

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
tx.objectStore("videos").put({
id:Date.now().toString(),
blob,
title,
artist,
date:Date.now()
});
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

/* ================= CREATE VIDEO ================= */

async function createVideo(){

const title=document.getElementById("title").value.trim();
const artist=document.getElementById("artist").value.trim();
const cover=document.getElementById("coverFile").files[0];
const audioFile=document.getElementById("audioFile").files[0];

if(!title||!artist||!cover||!audioFile){
alert("Compila tutti i campi");
return;
}

const btn=document.getElementById("createBtn");
const progressBox=document.getElementById("progressBox");
const progressFill=document.getElementById("progressFill");
const status=document.getElementById("status");

btn.disabled=true;
progressBox.style.display="block";
status.textContent="Preparazione...";

try{

/* CANVAS */

const canvas=document.createElement("canvas");
canvas.width=1280;
canvas.height=720;
const ctx=canvas.getContext("2d");

const img=new Image();
img.src=URL.createObjectURL(cover);
await img.decode();

function draw(){
ctx.fillStyle="#000";
ctx.fillRect(0,0,1280,720);

const scale=Math.min(1280/img.width,720/img.height)*0.7;
const x=(1280-img.width*scale)/2;
const y=(720-img.height*scale)/2-40;

ctx.drawImage(img,x,y,img.width*scale,img.height*scale);

ctx.fillStyle="#fff";
ctx.textAlign="center";
ctx.font="bold 48px Arial";
ctx.fillText(title,640,650);
ctx.font="32px Arial";
ctx.fillText(artist,640,700);
}

/* AUDIO */

const audio=new Audio(URL.createObjectURL(audioFile));
await new Promise(res=>audio.onloadedmetadata=res);
const duration=audio.duration;

/* STREAM */

const stream=canvas.captureStream(30);

const audioCtx=new AudioContext();
const source=audioCtx.createMediaElementSource(audio);
const dest=audioCtx.createMediaStreamDestination();
source.connect(dest);
source.connect(audioCtx.destination);
stream.addTrack(dest.stream.getAudioTracks()[0]);

const recorder=new MediaRecorder(stream,{
mimeType:"video/webm;codecs=vp9,opus",
videoBitsPerSecond:2500000
});

let chunks=[];

recorder.ondataavailable=e=>{
if(e.data.size>0)chunks.push(e.data);
};

recorder.onstop=async()=>{
const blob=new Blob(chunks,{type:"video/webm"});
await saveVideo(blob,title,artist);

progressFill.style.width="100%";
progressFill.textContent="100%";
status.textContent="✅ Video creato";

audioCtx.close();

setTimeout(()=>{
progressBox.style.display="none";
btn.disabled=false;
loadGallery();
},1500);
};

recorder.start();
audio.play();

const drawLoop=setInterval(draw,33);

const progLoop=setInterval(()=>{
let p=(audio.currentTime/duration)*100;
if(p>100)p=100;
progressFill.style.width=p+"%";
progressFill.textContent=Math.floor(p)+"%";
},300);

audio.onended=()=>{
clearInterval(drawLoop);
clearInterval(progLoop);

progressFill.style.width="100%";
progressFill.textContent="100%";

recorder.requestData();

setTimeout(()=>{
recorder.stop();
},200);
};

/* sécurité */
setTimeout(()=>{
if(recorder.state==="recording"){
recorder.stop();
}
},duration*1000+1500);

}catch(e){
alert("Errore: "+e.message);
btn.disabled=false;
progressBox.style.display="none";
}
}

/* ================= GALLERY ================= */

async function loadGallery(){

const vids=await getVideos();
const grid=document.getElementById("videoGrid");
const gallery=document.getElementById("gallery");
const empty=document.getElementById("empty");

grid.innerHTML="";

if(!vids.length){
gallery.style.display="none";
empty.style.display="block";
return;
}

empty.style.display="none";
gallery.style.display="block";

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
</div>
`;
grid.appendChild(card);
});
}

async function deleteVideo(id){
if(!confirm("Eliminare?"))return;
const tx=db.transaction("videos","readwrite");
tx.objectStore("videos").delete(id);
tx.oncomplete=()=>loadGallery();
}

/* INIT */

window.onload=async()=>{
await openDB();
loadGallery();
};

</script>

</body>
</html>

