<?php
/* =========================================================
   SECTION PHP — API WHISPER PROXY
   ========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'whisper') {

    header('Content-Type: application/json');

    if (!isset($_FILES['audio'])) {
        echo json_encode(['error' => 'Aucun fichier audio reçu']);
        exit;
    }

    $apiKey = getenv("OPENAI_API_KEY"); 
    // ⚠️ Mets ta clé dans variable d'environnement serveur
    // OU remplace par :
    // $apiKey = "NK0o4vnKFgYq8hqKIu3SU6BbTM2N5uKO_4xKV931qgk";

    if (!$apiKey) {
        echo json_encode(['error' => 'Clé API non configurée côté serveur']);
        exit;
    }

    $audioPath = $_FILES['audio']['tmp_name'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/audio/transcriptions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        "file" => new CURLFile($audioPath),
        "model" => "whisper-1",
        "response_format" => "verbose_json"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>My Music Studio</title>
<style>
body{background:#111;color:#fff;font-family:Arial;margin:0;padding:40px}
button{padding:10px 20px;background:#d6004c;color:#fff;border:none;border-radius:8px}
input{margin:10px 0;padding:8px}
video{width:100%;max-width:400px;margin-top:10px}
</style>
</head>
<body>

<h1>🎵 My Music Studio</h1>

<input type="text" id="title" placeholder="Titolo"><br>
<input type="text" id="artist" placeholder="Artista"><br>
<input type="file" id="coverFile" accept="image/*"><br>
<input type="file" id="audioFile" accept="audio/*"><br>

<button onclick="createVideo()">Crea Video</button>

<h2>Gallery</h2>
<div id="gallery"></div>

<script>
let db;

function openDB(){
  return new Promise((resolve,reject)=>{
    const req=indexedDB.open("MusicDB",1);
    req.onupgradeneeded=e=>{
      db=e.target.result;
      db.createObjectStore("videos",{keyPath:"id"});
    };
    req.onsuccess=e=>{db=e.target.result;resolve();}
  });
}

async function saveVideo(blob,title,artist){
  return new Promise(res=>{
    const tx=db.transaction("videos","readwrite");
    tx.objectStore("videos").put({id:Date.now().toString(),blob,title,artist});
    tx.oncomplete=res;
  });
}

async function loadGallery(){
  const tx=db.transaction("videos","readonly");
  const req=tx.objectStore("videos").getAll();
  req.onsuccess=e=>{
    const g=document.getElementById("gallery");
    g.innerHTML="";
    e.target.result.forEach(v=>{
      const url=URL.createObjectURL(v.blob);
      g.innerHTML+=`<div>
        <b>${v.title}</b><br>
        <video src="${url}" controls></video>
      </div>`;
    });
  };
}

async function transcribePHP(audioFile){
  const formData=new FormData();
  formData.append("audio",audioFile);
  const res=await fetch("index.php?action=whisper",{
    method:"POST",
    body:formData
  });
  return await res.json();
}

async function createVideo(){
  const title=document.getElementById("title").value;
  const artist=document.getElementById("artist").value;
  const cover=document.getElementById("coverFile").files[0];
  const audio=document.getElementById("audioFile").files[0];

  if(!title||!artist||!cover||!audio){
    alert("Compila tutto");
    return;
  }

  // Whisper via PHP
  const whisperData=await transcribePHP(audio);
  console.log("Segments:",whisperData.segments);

  const canvas=document.createElement("canvas");
  canvas.width=1280; canvas.height=720;
  const ctx=canvas.getContext("2d");

  const img=new Image();
  img.src=URL.createObjectURL(cover);
  await new Promise(res=>img.onload=res);

  const stream=canvas.captureStream(30);
  const audioCtx=new AudioContext();
  const source=audioCtx.createMediaElementSource(new Audio(URL.createObjectURL(audio)));
  const dest=audioCtx.createMediaStreamDestination();
  source.connect(dest);
  stream.addTrack(dest.stream.getAudioTracks()[0]);

  const recorder=new MediaRecorder(stream);
  let chunks=[];
  recorder.ondataavailable=e=>chunks.push(e.data);

  recorder.onstop=async()=>{
    const blob=new Blob(chunks,{type:"video/webm"});
    await saveVideo(blob,title,artist);
    loadGallery();
  };

  recorder.start();

  let start=audioCtx.currentTime;
  let interval=setInterval(()=>{
    ctx.fillStyle="#000";
    ctx.fillRect(0,0,1280,720);
    ctx.drawImage(img,240,100,800,400);
    ctx.fillStyle="#fff";
    ctx.font="40px Arial";
    ctx.fillText(title,640,600);
    ctx.fillText(artist,640,650);
  },33);

  setTimeout(()=>{
    clearInterval(interval);
    recorder.stop();
  },5000); // test 5 sec
}

window.onload=async()=>{
  await openDB();
  loadGallery();
};
</script>

</body>
</html>
