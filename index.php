<?php
/* ================= CONFIG ================= */
$baseDir = __DIR__ . "/uploads";
$dirs = [
    "video" => "$baseDir/videos",
    "mp3"   => "$baseDir/mp3",
    "pdf"   => "$baseDir/pdf"
];

foreach ($dirs as $d) {
    if (!is_dir($d)) mkdir($d, 0777, true);
}

/* ================= UPLOAD ================= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["file"])) {
    $type = $_POST["type"];
    if (!isset($dirs[$type])) exit;

    $name = time() . "_" . basename($_FILES["file"]["name"]);
    move_uploaded_file($_FILES["file"]["tmp_name"], $dirs[$type] . "/" . $name);
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
<title>My Music Studio</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
*{box-sizing:border-box;font-family:Arial}
body{margin:0;background:#111;color:#fff}
header{padding:30px;text-align:center;background:linear-gradient(135deg,#d6004c,#7b1fa2)}
.container{max-width:1000px;margin:auto;padding:30px}
.upload{background:#1e1e1e;padding:20px;border-radius:12px;margin-bottom:30px}
input,button{width:100%;padding:12px;margin-bottom:10px;border:none;border-radius:8px}
button{background:#d6004c;color:#fff;cursor:pointer}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:15px}
.card{background:#1e1e1e;border-radius:12px;padding:15px}
a{color:#ff5c8a;text-decoration:none}
.delete{color:#999;font-size:14px}
</style>
</head>

<body>

<header>
<h1>🎶 My Music Studio</h1>
<p>Gestione file reale con PHP</p>
</header>

<div class="container">

<!-- UPLOAD -->
<div class="upload">
<form method="post" enctype="multipart/form-data">
<select name="type">
<option value="video">🎬 Video</option>
<option value="mp3">🎵 MP3</option>
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
<a class="delete" href="?delete=<?= $f ?>">🗑 Elimina</a>
</div>
<?php endforeach; ?>
</div>

<!-- MP3 -->
<h2>🎵 MP3</h2>
<div class="grid">
<?php foreach(listFiles($dirs["mp3"]) as $f): ?>
<div class="card">
<p><?= basename($f) ?></p>
<audio src="<?= "uploads/mp3/".basename($f) ?>" controls></audio>
<a class="delete" href="?delete=<?= $f ?>">🗑 Elimina</a>
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
<a class="delete" href="?delete=<?= $f ?>">🗑 Elimina</a>
</div>
<?php endforeach; ?>
</div>

</div>
</body>
</html>

