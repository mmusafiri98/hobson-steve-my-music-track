<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ══════════════════════════════════════════════════
   BACKEND PHP — SUPERDATA TRANSCRIPTION
══════════════════════════════════════════════════ */

if (isset($_GET['action']) && $_GET['action'] === 'transcribe') {

    header('Content-Type: application/json');

    $apiKey = "sd_2e33ada3c7fabb785c23cc14fe8420a7";

    if (!isset($_FILES['file'])) {
        echo json_encode(["error" => "Nessun file ricevuto"]);
        exit;
    }

    $tmpFile = $_FILES['file']['tmp_name'];

    if (!file_exists($tmpFile)) {
        echo json_encode(["error" => "File temporaneo non trovato"]);
        exit;
    }

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => "https://api.superdata.ai/v1/audio/transcriptions",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_TIMEOUT => 120,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $apiKey"
        ],
        CURLOPT_POSTFIELDS => [
            "file" => new CURLFile($tmpFile),
            "model" => "superdata-transcribe"
        ]
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response === false) {
        echo json_encode([
            "error" => "Errore cURL: " . curl_error($ch)
        ]);
        curl_close($ch);
        exit;
    }

    curl_close($ch);

    if ($httpCode !== 200) {
        echo json_encode([
            "error" => "Errore API HTTP $httpCode",
            "response" => $response
        ]);
        exit;
    }

    $decoded = json_decode($response, true);

    if (!isset($decoded['text'])) {
        echo json_encode([
            "error" => "Risposta API non valida",
            "response" => $decoded
        ]);
        exit;
    }

    echo json_encode([
        "text" => $decoded['text']
    ]);

    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Music Studio</title>

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
margin-top:15px;
}

.transcript{
margin-top:15px;
background:#1e1e2a;
padding:15px;
border-radius:8px;
white-space:pre-wrap;
display:none;
}
</style>
</head>
<body>

<header>🎶 MUSIC STUDIO</header>

<div class="container">

<div class="card">

<h3>🎧 Carica Audio</h3>

<div class="dropzone" id="dz">
Clicca o trascina audio
</div>

<input type="file" id="audioFile" accept="audio/*" hidden>

<button onclick="startTranscription()">🎙️ Trascrivi</button>

<div class="transcript" id="transcriptBox"></div>

</div>

</div>

<script>
let selectedAudio = null;

const dz = document.getElementById("dz");
const input = document.getElementById("audioFile");
const box = document.getElementById("transcriptBox");

dz.onclick = () => input.click();

input.onchange = e => {
    selectedAudio = e.target.files[0];
    dz.innerHTML = "✅ " + selectedAudio.name;
};

dz.ondragover = e => e.preventDefault();

dz.ondrop = e => {
    e.preventDefault();
    selectedAudio = e.dataTransfer.files[0];
    dz.innerHTML = "✅ " + selectedAudio.name;
};

async function startTranscription(){

    if(!selectedAudio){
        alert("Seleziona un audio");
        return;
    }

    box.style.display="block";
    box.innerHTML="🎙️ Trascrizione in corso...";

    try{
        const fd = new FormData();
        fd.append("file", selectedAudio);

        const res = await fetch("?action=transcribe",{
            method:"POST",
            body:fd
        });

        const data = await res.json();

        if(data.error){
            box.innerHTML = "❌ ERRORE:\n" + data.error + "\n\n" + (data.response ? JSON.stringify(data.response, null, 2) : "");
            return;
        }

        box.innerHTML = data.text;

    } catch(err){
        box.innerHTML = "❌ Errore JavaScript: " + err.message;
    }
}
</script>

</body>
</html>

