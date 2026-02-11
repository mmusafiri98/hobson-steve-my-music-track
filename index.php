<?php
/* ================= CONFIG ================= */
$baseDir = __DIR__ . "/uploads";
$dirs = [
    "video" => "$baseDir/videos",
    "mp3"   => "$baseDir/mp3",
    "pdf"   => "$baseDir/pdf",
    "text"  => "$baseDir/transcriptions"
];

foreach ($dirs as $d) {
    if (!is_dir($d)) mkdir($d, 0777, true);
}

/* ================= SUPERDATA AI API FUNCTION ================= */
/* Qui puoi collegare una vera API esterna */
function superdata_ai_transcribe($filePath) {

    $apiKey = "sd_2e33ada3c7fabb785c23cc14fe8420a7";
    $endpoint = "https://api.superdata.ai/v1/transcribe";

    // ESEMPIO chiamata API reale (commentata)
    /*
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        "file" => new CURLFile($filePath)
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    return $data["text"];
    */

    // VERSIONE DEMO LOCALE (simulazione AI)
    return "Trascrizione AI simulata:\n\nQuesto è un esempio di testo generato automaticamente dall'intelligenza artificiale analizzando l'audio caricato.";
}

/* ================= UPLOAD ================= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["file"])) {

    $type = $_POST["type"];
    if (!isset($dirs[$type])) exit;

    $name = time() . "_" . basename($_FILES["file"]["name"]);
    $targetPath = $dirs[$type] . "/" . $name;

    move_uploaded_file($_FILES["file"]["tmp_name"], $targetPath);

    // Se è un MP3 → trascrizione automatica
    if ($type === "mp3") {
        $text = superdata_ai_transcribe($targetPath);
        file_put_contents($dirs["text"] . "/" . $name . ".txt", $text);
    }

    header("Location: index.php");
    exit;
}

/* ================= DELETE ================= */
if (isset($_GET["delete"])) {
    $file = realpath($_GET["delete"]);
    if ($file && str_starts_with($file, realpath($baseDir))) {
        unlink($file);
    }
    header("Location: index.php");
    exit;
}

/* ================= READ FILES ================= */
function listFiles($dir) {
    return array_values(array_filter(glob($dir . "/*"), "is_file"));
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>My Music Studio AI</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
*{box-sizing:border-box;font-family:Arial}
body{margin:0;background:#111;color:#fff}
header{padding:30px;text-align:center;background:linear-gradient(135deg,#d6004c,#7b1fa2)}
.container{max-width:1100px;margin:auto;padding:30px}
.upload{background:#1e1e1e;padding:20px;border-radius:12px;margin-bottom:30px}
input,select,button{width:100%;padding:12px;margin-bottom:10px;border:none;border-radius:8px}
button{background:#d6004c;color:#fff;cursor:pointer}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:15px}
.card{background:#1e1e1e;border-radius:12px;padding:15px}
a{color:#ff5c8a;text-decoration:none}
.transcription{background:#000;padding:10px;border-radius:8px;margin-top:10px;font-size:14px;white-space:pre-wrap}
</style>
</head>

<body>

<header>
<h1>🎶 My Music Studio + SuperData AI</h1>
<p>Upload + Trascrizione Automatica AI</p>
</header>

<div class="container">

<div class="upload">
<form method="post" enctype="multipart/form-data">
<select name="type">
<option value="video">🎬 Video</option>
<option value="mp3">🎵 MP3 (con trascrizione AI)</option>
<option value="pdf">📄 PDF</option>
</select>
<input type="file" name="file" required>
<button>Upload</button>
</form>
</div>

<!-- VIDEO -->
<h2>🎬 Video</h2>
<div class="grid">
<?php foreach(listFiles($dirs["video"]) as $f): ?>
<div class="card">
<video src="<?= "uploads/videos/".basename($f) ?>" controls width="100%"></video>
<a href="?delete=<?= $f ?>">🗑 Elimina</a>
</div>
<?php endforeach; ?>
</div>

<!-- MP3 + TRASCRIZIONE -->
<h2>🎵 MP3 + Trascrizione AI</h2>
<div class="grid">
<?php foreach(listFiles($dirs["mp3"]) as $f): ?>
<div class="card">
<p><b><?= basename($f) ?></b></p>
<audio src="<?= "uploads/mp3/".basename($f) ?>" controls></audio>

<?php
$txtFile = $dirs["text"] . "/" . basename($f) . ".txt";
if (file_exists($txtFile)):
?>
<div class="transcription">
<?= htmlspecialchars(file_get_contents($txtFile)) ?>
</div>
<?php endif; ?>

<a href="?delete=<?= $f ?>">🗑 Elimina</a>
</div>
<?php endforeach; ?>
</div>

<!-- PDF -->
<h2>📄 PDF</h2>
<div class="grid">
<?php foreach(listFiles($dirs["pdf"]) as $f): ?>
<div class="card">
<p><?= basename($f) ?></p>
<a href="<?= "uploads/pdf/".basename($f) ?>" target="_blank">Apri</a><br>
<a href="?delete=<?= $f ?>">🗑 Elimina</a>
</div>
<?php endforeach; ?>
</div>

</div>
</body>
</html>

