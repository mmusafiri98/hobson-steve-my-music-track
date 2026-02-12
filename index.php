<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>🎶 My Music Studio</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body { margin:0; font-family:Arial,sans-serif; background:#111; color:#fff; }
header { background: linear-gradient(135deg,#d6004c,#7b1fa2); text-align:center; padding:40px 20px; }
header h1 { margin:0; font-size:2.5rem; }
header p { margin:5px 0 0; font-size:1.2rem; color:#eee; }
.container { max-width:1000px; margin:auto; padding:20px; }
.upload-box { background:#1e1e1e; padding:25px; border-radius:12px; margin-bottom:40px; }
input[type=text], input[type=file] { width:100%; padding:12px; margin-bottom:15px; border-radius:8px; border:none; }
button { background:#d6004c; color:#fff; border:none; padding:12px 24px; border-radius:25px; cursor:pointer; }
button:disabled { background:#555; cursor:not-allowed; }
.progress { display:none; margin-top:15px; }
.progress-bar { width:100%; height:25px; background:#333; border-radius:15px; overflow:hidden; }
.progress-fill { height:100%; width:0; background:linear-gradient(90deg,#d6004c,#7b1fa2); text-align:center; line-height:25px; }
.gallery { margin-top:40px; }
.video-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:20px; }
.video-card { background:#1e1e1e; border-radius:12px; overflow:hidden; }
.video-card video { width:100%; height:180px; object-fit:cover; }
.video-info { padding:10px; font-size:0.95rem; }
.video-actions { display:flex; gap:8px; padding:0 10px 10px; }
.download-btn, .delete-btn { flex:1; border-radius:8px; text-align:center; padding:8px; font-size:0.85rem; cursor:pointer; }
.download-btn { background:#d6004c; color:#fff; text-decoration:none; display:flex; justify-content:center; align-items:center; }
.delete-btn { background:#444; color:#fff; border:none; }
.empty { text-align:center; color:#777; padding:60px; font-size:1.2rem; }
</style>
</head>
<body>

<header>
<h1>🎶 My Music Studio</h1>
<p>Crea video da cover + audio</p>
</header>

<div class="container">

<div class="upload-box">
<input id="title" placeholder="Titolo">
<input id="artist" placeholder="Artista">
<label>Cover</label>
<input type="file" id="coverFile" accept="image/*">
<label>Audio (MP3, WAV)</label>
<input type="file" id="audioFile" accept="audio/*">
<button id="createBtn">Crea Video</button>

<div class="progress" id="progressBox">
<div class="progress-bar"><div class="progress-fill" id="progressFill">0%</div></div>
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
let db;

async function openDB(){
    return new Promise((resolve,reject)=>{
        const request=indexedDB.open("VideoDB",1);
        request.onupgradeneeded=e=>{
            db=e.target.result;
            if(!db.objectStoreNames.contains("videos")){
                db.createObjectStore("videos",{keyPath:"id"});
            }
        };
        request.onsuccess=e=>{db=e.target.result; resolve();};
        request.onerror=e=>reject(e.target.error);
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
    if(!confirm("Eliminare questo video?")) return;
    const tx=db.transaction("videos","readwrite");
    tx.objectStore("videos").delete(id);
    tx.oncomplete=()=>loadGallery();
}

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

/* Creazione video */
document.getElementById("createBtn").addEventListener("click", async ()=>{
    const title=document.getElementById("title").value.trim();
    const artist=document.getElementById("artist").value.trim();
    const coverFile=document.getElementById("coverFile").files[0];
    const audioFile=document.getElementById("audioFile").files[0];
    if(!title||!artist||!coverFile||!audioFile){alert("Compila tutti i campi"); return;}
    const btn=document.getElementById("createBtn");
    const progressBox=document.getElementById("progressBox");
    const progressFill=document.getElementById("progressFill");
    btn.disabled=true;
    progressBox.style.display="block";

    try{
        const canvas=document.createElement("canvas");
        canvas.width=1280; canvas.height=720;
        const ctx=canvas.getContext("2d");
        const img=new Image();
        img.src=URL.createObjectURL(coverFile);
        await new Promise(res=>img.onload=res);

        const audio=new Audio(URL.createObjectURL(audioFile));
        await new Promise(res=>audio.onloadedmetadata=res);
        const duration=audio.duration;

        const stream=canvas.captureStream(30);
        const audioCtx=new AudioContext();
        const source=audioCtx.createMediaElementSource(audio);
        const dest=audioCtx.createMediaStreamDestination();
        source.connect(dest); source.connect(audioCtx.destination);
        stream.addTrack(dest.stream.getAudioTracks()[0]);

        const recorder=new MediaRecorder(stream,{mimeType:"video/webm"});
        let chunks=[];
        recorder.ondataavailable=e=>{if(e.data.size>0) chunks.push(e.data);}
        recorder.onstop=async()=>{
            const blob=new Blob(chunks,{type:"video/webm"});
            await saveVideo(blob,title,artist);
            btn.disabled=false;
            progressBox.style.display="none";
            progressFill.style.width="0%";
            progressFill.innerText="0%";
            document.getElementById("title").value="";
            document.getElementById("artist").value="";
            document.getElementById("coverFile").value="";
            document.getElementById("audioFile").value="";
            loadGallery();
        };

        recorder.start();
        audio.play();

        const drawLoop=setInterval(()=>{
            ctx.fillStyle="#000"; ctx.fillRect(0,0,1280,720);
            const scale=Math.min(1280/img.width,720/img.height)*0.7;
            const x=(1280-img.width*scale)/2;
            const y=(720-img.height*scale)/2-40;
            ctx.drawImage(img,x,y,img.width*scale,img.height*scale);
            ctx.fillStyle="#fff"; ctx.textAlign="center";
            ctx.font="bold 48px Arial"; ctx.fillText(title,640,650);
            ctx.font="32px Arial"; ctx.fillText(artist,640,700);
        },33);

        const progLoop=setInterval(()=>{
            let p=(audio.currentTime/duration)*100; if(p>100)p=100;
            progressFill.style.width=p+"%"; progressFill.innerText=Math.floor(p)+"%";
        },200);

        audio.onended=()=>{
            clearInterval(drawLoop);
            clearInterval(progLoop);
            recorder.requestData();
            setTimeout(()=>recorder.stop(),200);
            audioCtx.close();
        };

    }catch(e){alert("Errore: "+e.message); btn.disabled=false; progressBox.style.display="none";}
});

window.onload=async()=>{
    await openDB();
    loadGallery();
};
</script>

</body>
</html>

