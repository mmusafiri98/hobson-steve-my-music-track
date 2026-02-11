<?php
/* ══════════════════════════════════════════════════
   BACKEND PHP — SUPERDATA TRANSCRIPTION
══════════════════════════════════════════════════ */

if (isset($_GET['action']) && $_GET['action'] === 'transcribe') {

    header('Content-Type: application/json');

    $apiKey = "sd_2e33ada3c7fabb785c23cc14fe8420a7";

    if (!isset($_FILES['file'])) {
        echo json_encode(["error" => "No file uploaded"]);
        exit;
    }

    $tmpFile = $_FILES['file']['tmp_name'];

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => "https://api.superdata.ai/v1/audio/transcriptions",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $apiKey"
        ],
        CURLOPT_POSTFIELDS => [
            "file" => new CURLFile($tmpFile),
            "model" => "superdata-transcribe"
        ]
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Music Studio — Superdata Edition</title>

<style>
body{
background:#0d0d14;
color:#fff;
font-family:Arial;
margin:0;
padding:0;
}

header{
padding:20px;
background:#111;
text-align:center;
font-size:22px;
letter-spacing:2px;
}

.container{
padding:30px;
max-width:900px;
margin:auto;
}

.card{
background:#16161f;
padding:20px;
border-radius:10px;
margin-bottom:20px;
}

input[type=text]{
width:100%;
padding:10px;
margin-bottom:10px;
background:#1e1e2a;
border:1px solid #333;
color:white;
border-radius:6px;
}

.dropzone{
border:2px dashed #d6004c;
padding:30px;
text-align:center;
cursor:pointer;
border-radius:10px;
}

button{
background:#d6004c;
color:white;
border:none;
padding:12px 20px;
border-radius:6px;
cursor:pointer;
}

button:hover{
opacity:0.8;
}

.transcript{
margin-top:15px;
background:#1e1e2a;
padding:15px;
border-radius:8px;
display:none;
white-space:pre-wrap;
}
</style>
</head>
<body>

<header>🎶 MUSIC STUDIO — SUPERDATA AI</header>

<div class="container">

<div class="card">
<h3>🎵 Crea Video con Trascrizione Automatica</h3>

<input type="text" id="title" placeholder="Titolo">
<input type="text" id="artist" placeholder="Artista">

<div class="dropzone" id="dz_audio">
🎧 Clicca o trascina audio
</div>

<input type="file" id="audioFile" accept="audio/*" hidden>

<br>
<button onclick="createVideo()">🎬 Genera Video</button>

<div class="transcript" id="transcriptBox"></div>

</div>

</div>

<script>
let selectedAudio = null;

const dz = document.getElementById("dz_audio");
const input = document.getElementById("audioFile");

dz.onclick = () => input.click();

input.onchange = e => {
    selectedAudio = e.target.files[0];
    dz.innerHTML = "✅ " + selectedAudio.name;
};

dz.ondragover = e => {
    e.preventDefault();
};

dz.ondrop = e => {
    e.preventDefault();
    selectedAudio = e.dataTransfer.files[0];
    dz.innerHTML = "✅ " + selectedAudio.name;
};

async function transcribeAudio(file){
    const fd = new FormData();
    fd.append("file", file);

    const res = await fetch("?action=transcribe",{
        method:"POST",
        body:fd
    });

    const data = await res.json();
    return data.text || "Trascrizione non disponibile";
}

async function createVideo(){

    if(!selectedAudio){
        alert("Seleziona un audio");
        return;
    }

    document.getElementById("transcriptBox").style.display="block";
    document.getElementById("transcriptBox").innerHTML="🎙️ Trascrizione in corso...";

    const text = await transcribeAudio(selectedAudio);

    document.getElementById("transcriptBox").innerHTML=text;

    alert("Video generato (demo) con testo trascritto!");
}
</script>

</body>
</html>

