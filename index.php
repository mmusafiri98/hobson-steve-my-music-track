<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Video Generator</title>
<style>
body{background:#111;color:#fff;font-family:Arial;text-align:center;padding:40px}
input,button{margin:10px;padding:10px}
.progress{width:400px;margin:auto;background:#333;height:25px;border-radius:20px;overflow:hidden}
.bar{height:100%;width:0;background:#d6004c;text-align:center;line-height:25px}
</style>
</head>
<body>

<h2>🎬 Générateur vidéo</h2>

<input id="title" placeholder="Titre"><br>
<input id="artist" placeholder="Artiste"><br>
<input type="file" id="cover" accept="image/*"><br>
<input type="file" id="audio" accept="audio/*"><br>

<button onclick="createVideo()">Créer la vidéo</button>

<div class="progress" style="display:none" id="progressBox">
<div class="bar" id="bar">0%</div>
</div>

<script>

async function createVideo(){

const title=document.getElementById("title").value.trim()
const artist=document.getElementById("artist").value.trim()
const coverFile=document.getElementById("cover").files[0]
const audioFile=document.getElementById("audio").files[0]

if(!title||!artist||!coverFile||!audioFile){
alert("Remplis tout")
return
}

const progressBox=document.getElementById("progressBox")
const bar=document.getElementById("bar")
progressBox.style.display="block"

try{

/* CANVAS */
const canvas=document.createElement("canvas")
canvas.width=1280
canvas.height=720
const ctx=canvas.getContext("2d")

/* IMAGE LOAD PROPER */
const img=new Image()
img.src=URL.createObjectURL(coverFile)

await new Promise((resolve,reject)=>{
img.onload=resolve
img.onerror=reject
})

function draw(){
ctx.fillStyle="#000"
ctx.fillRect(0,0,1280,720)

const scale=Math.min(1280/img.width,720/img.height)*0.7
const x=(1280-img.width*scale)/2
const y=(720-img.height*scale)/2-40

ctx.drawImage(img,x,y,img.width*scale,img.height*scale)

ctx.fillStyle="#fff"
ctx.textAlign="center"
ctx.font="bold 48px Arial"
ctx.fillText(title,640,650)
ctx.font="32px Arial"
ctx.fillText(artist,640,700)
}

/* AUDIO */
const audio=new Audio(URL.createObjectURL(audioFile))

await new Promise((resolve,reject)=>{
audio.onloadedmetadata=resolve
audio.onerror=reject
})

const duration=audio.duration

/* STREAM */
const stream=canvas.captureStream(30)

const audioCtx=new AudioContext()
const source=audioCtx.createMediaElementSource(audio)
const dest=audioCtx.createMediaStreamDestination()

source.connect(dest)
source.connect(audioCtx.destination)

stream.addTrack(dest.stream.getAudioTracks()[0])

const recorder=new MediaRecorder(stream,{
mimeType:"video/webm"
})

let chunks=[]

recorder.ondataavailable=e=>{
if(e.data.size>0)chunks.push(e.data)
}

recorder.onstop=()=>{
const blob=new Blob(chunks,{type:"video/webm"})
const url=URL.createObjectURL(blob)

const a=document.createElement("a")
a.href=url
a.download=title+".webm"
a.click()

audioCtx.close()

bar.style.width="100%"
bar.innerText="100%"
}

/* START */
recorder.start()
audio.play()

const drawLoop=setInterval(draw,33)

const progLoop=setInterval(()=>{
let p=(audio.currentTime/duration)*100
if(p>100)p=100
bar.style.width=p+"%"
bar.innerText=Math.floor(p)+"%"
},300)

audio.onended=()=>{
clearInterval(drawLoop)
clearInterval(progLoop)

recorder.requestData()

setTimeout(()=>{
recorder.stop()
},200)
}

/* sécurité */
setTimeout(()=>{
if(recorder.state==="recording"){
recorder.stop()
}
},duration*1000+2000)

}catch(e){
alert("Erreur: "+e.message)
}

}

</script>
</body>
</html>


