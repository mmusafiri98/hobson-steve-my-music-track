<?php
/* ================= CONFIG ================= */
$base = __DIR__."/uploads";
$dirs = ["covers","mp3","meta","subs","videos"];
foreach($dirs as $d){
    if(!is_dir("$base/$d")) mkdir("$base/$d",0777,true);
}

/* ================= AI TRASCRIZIONE SIMULATA ================= */
function ai_transcribe($audio){
    return "Questa è una trascrizione AI simulata del testo della canzone.";
}

/* ================= CREA SOTTOTITOLI ================= */
function make_vtt($text,$path){
    $vtt="WEBVTT\n\n";
    $t=0;
    foreach(explode(".", $text) as $line){
        if(trim($line)){
            $start=gmdate("H:i:s",$t).".000";
            $t+=4;
            $end=gmdate("H:i:s",$t).".000";
            $vtt.="$start --> $end\n$line\n\n";
        }
    }
    file_put_contents($path,$vtt);
}

/* ================= UPLOAD ================= */
if($_SERVER["REQUEST_METHOD"]==="POST"){
    $id=time();
    $title=$_POST["title"];
    $artist=$_POST["artist"];

    move_uploaded_file($_FILES["cover"]["tmp_name"],"$base/covers/$id.jpg");
    move_uploaded_file($_FILES["audio"]["tmp_name"],"$base/mp3/$id.mp3");

    $text=ai_transcribe("$base/mp3/$id.mp3");
    make_vtt($text,"$base/subs/$id.vtt");

    file_put_contents("$base/meta/$id.json",json_encode([
        "title"=>$title,
        "artist"=>$artist
    ]));

    header("Location: index.php");
    exit;
}

/* ================= LEGGI DATI ================= */
$items = glob("$base/meta/*.json");
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>My Music Studio AI</title>
<style>
body{background:#111;color:#fff;font-family:Arial;margin:0;padding:20px}
.card{background:#1e1e1e;padding:20px;border-radius:12px;margin-bottom:20px}
video,img,audio{width:100%;border-radius:10px;margin-bottom:10px}
button{padding:10px 15px;border:none;border-radius:8px;background:#d6004c;color:#fff;cursor:pointer;margin-bottom:10px}
</style>
</head>
<body>

<h1>🎶 My Music Studio AI</h1>

<form method="post" enctype="multipart/form-data" style="margin-bottom:30px">
<input name="title" placeholder="Titolo" required><br>
<input name="artist" placeholder="Artista" required><br>
<label>Cover</label>
<input type="file" name="cover" accept="image/*" required><br>
<label>Audio</label>
<input type="file" name="audio" accept="audio/*" required><br>
<button>Upload + Trascrizione AI</button>
</form>

<hr>

<?php foreach($items as $m):
$id=basename($m,".json");
$meta=json_decode(file_get_contents($m),true);
?>
<div class="card">
<h3><?=htmlspecialchars($meta["title"])?> — <?=htmlspecialchars($meta["artist"])?></h3>

<!-- VIDEO GENERATO IN BROWSER -->
<video id="video_<?=$id?>" controls>
<source src="<?= "uploads/videos/$id.webm" ?>">
<track src="<?= "uploads/subs/$id.vtt" ?>" kind="subtitles" srclang="it" label="Italiano" default>
</video>

<img src="<?= "uploads/covers/$id.jpg" ?>">

<audio src="<?= "uploads/mp3/$id.mp3" ?>" controls></audio>

<!-- BOTTONE GENERA VIDEO -->
<button onclick="generateVideo('<?=$id?>')">🎬 Generate Video</button>
</div>
<?php endforeach; ?>

<script>
async function generateVideo(id) {
try {
const canvas = document.createElement("canvas");
canvas.width = 960; canvas.height = 540;
const ctx = canvas.getContext("2d");

const img = new Image();
img.src = `uploads/covers/${id}.jpg`;
await img.decode();

const audio = new Audio(`uploads/mp3/${id}.mp3`);
await audio.play().catch(()=>{}); // necessità autoplay click

const stream = canvas.captureStream(30);

const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
const source = audioCtx.createMediaElementSource(audio);
const dest = audioCtx.createMediaStreamDestination();

source.connect(dest);
source.connect(audioCtx.destination);
stream.addTrack(dest.stream.getAudioTracks()[0]);

const recorder = new MediaRecorder(stream, { mimeType: "video/webm" });
let chunks = [];

recorder.ondataavailable = e => chunks.push(e.data);

recorder.onstop = () => {
const blob = new Blob(chunks,{type:"video/webm"});
const a = document.createElement("a");
a.href = URL.createObjectURL(blob);
a.download = `${id}-video.webm`;
a.click();
};

function draw(){
ctx.drawImage(img,0,0,canvas.width,canvas.height);
requestAnimationFrame(draw);
}

draw();
recorder.start();
audio.play();
audio.onended = () => recorder.stop();

} catch(err){
alert("Errore generazione video: "+err.message);
console.error(err);
}
}
</script>

</body>
</html>

