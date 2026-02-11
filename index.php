<?php
/* ================= CONFIG ================= */
$dataFile = "data.json";
$uploadDir = "uploads";
$videoDir = "$uploadDir/videos";
$coverDir = "$uploadDir/covers";
$textDir  = "$uploadDir/transcriptions";

foreach([$uploadDir,$videoDir,$coverDir,$textDir] as $d)
    if(!is_dir($d)) mkdir($d,0777,true);

$data = file_exists($dataFile) ? json_decode(file_get_contents($dataFile),true) : [];

/* ================= UPLOAD VIDEO ================= */
if($_SERVER["REQUEST_METHOD"]==="POST" && isset($_FILES["video"])){

    $id = time();
    $title = $_POST["title"];
    $artist = $_POST["artist"];

    $videoName = "$id.webm";
    $coverName = "$id.jpg";

    move_uploaded_file($_FILES["video"]["tmp_name"], "$videoDir/$videoName");
    move_uploaded_file($_FILES["cover"]["tmp_name"], "$coverDir/$coverName");

    $data[$id] = [
        "title"=>$title,
        "artist"=>$artist,
        "video"=>$videoName,
        "cover"=>$coverName
    ];

    file_put_contents($dataFile, json_encode($data,JSON_PRETTY_PRINT));
    exit("OK");
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Music Video AI Studio</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<style>
body{background:#111;color:#fff;font-family:Arial;margin:0}
header{padding:20px;text-align:center;background:#d6004c}
.container{max-width:900px;margin:auto;padding:20px}
input,button{width:100%;padding:12px;margin-bottom:10px}
button{background:#7b1fa2;color:#fff;border:none}
.card{background:#1e1e1e;padding:15px;border-radius:10px;margin-bottom:20px}
video{width:100%;border-radius:10px}
</style>
</head>
<body>

<header>
<h1>🎬 Music Video AI Studio</h1>
<p>Cover + Audio → Video WEBM</p>
</header>

<div class="container">

<div class="card">
<input id="title" placeholder="Titolo canzone">
<input id="artist" placeholder="Artista">
<label>Cover</label>
<input type="file" id="cover" accept="image/*">
<label>Audio</label>
<input type="file" id="audio" accept="audio/*">
<button onclick="createVideo()">🎬 Genera Video</button>
</div>

<?php foreach($data as $v): ?>
<div class="card">
<h3><?= htmlspecialchars($v["title"]) ?></h3>
<p><?= htmlspecialchars($v["artist"]) ?></p>
<video controls>
<source src="uploads/videos/<?= $v["video"] ?>" type="video/webm">
</video>
</div>
<?php endforeach; ?>

</div>

<script>
async function createVideo(){

const title=titleInput=title.value
const artistVal=artist.value
const coverFile=cover.files[0]
const audioFile=audio.files[0]

if(!title||!artistVal||!coverFile||!audioFile)
return alert("Completa tutti i campi")

const canvas=document.createElement("canvas")
canvas.width=1280
canvas.height=720
const ctx=canvas.getContext("2d")

const img=new Image()
img.src=URL.createObjectURL(coverFile)
await img.decode()

const audioEl=new Audio(URL.createObjectURL(audioFile))
const stream=canvas.captureStream(30)

const ac=new AudioContext()
const src=ac.createMediaElementSource(audioEl)
const dest=ac.createMediaStreamDestination()
src.connect(dest)
src.connect(ac.destination)
stream.addTrack(dest.stream.getAudioTracks()[0])

const rec=new MediaRecorder(stream,{mimeType:"video/webm"})
let chunks=[]

rec.ondataavailable=e=>chunks.push(e.data)
rec.onstop=async()=>{
const videoBlob=new Blob(chunks,{type:"video/webm"})
const fd=new FormData()
fd.append("video",videoBlob)
fd.append("cover",coverFile)
fd.append("title",titleInput)
fd.append("artist",artistVal)

await fetch("",{method:"POST",body:fd})
location.reload()
}

function draw(){
ctx.drawImage(img,0,0,canvas.width,canvas.height)
ctx.fillStyle="rgba(0,0,0,0.5)"
ctx.fillRect(0,560,1280,160)
ctx.fillStyle="#fff"
ctx.font="48px Arial"
ctx.textAlign="center"
ctx.fillText(titleInput,640,620)
ctx.font="32px Arial"
ctx.fillText(artistVal,640,670)
}

const loop=setInterval(draw,1000/30)
rec.start()
audioEl.play()
audioEl.onended=()=>{
clearInterval(loop)
rec.stop()
}
}
</script>

</body>
</html>

