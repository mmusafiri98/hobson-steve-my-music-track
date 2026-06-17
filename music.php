// ══════════════════════════════════════════════════════════
//  PATCH 1 — Remplace syncLyricsWithTimings (ligne ~680 dans music.php)
//  Problème résolu : quand les paroles ont des timestamps [mm:ss],
//  on ne dépend plus du tout de Whisper pour le timing.
// ══════════════════════════════════════════════════════════

function syncLyricsWithTimings(whisperSegs, rawLyrics, userOffset) {
  if (typeof userOffset !== 'number' || isNaN(userOffset)) userOffset = -0.3;

  function parseTimestamp(str) {
    const m = str.match(/^\[(\d+):(\d{2})(?::(\d{2}))?\]\s*/);
    if (!m) return null;
    const h = m[3] ? parseInt(m[1]) : 0,
          min = m[3] ? parseInt(m[2]) : parseInt(m[1]),
          sec = m[3] ? parseInt(m[3]) : parseInt(m[2]);
    return h * 3600 + min * 60 + sec;
  }

  const rawLines = rawLyrics.split('\n').map(l => l.trim()).filter(l => l.length > 0);
  if (!rawLines.length) return whisperSegs;

  const parsed = rawLines.map(line => {
    const ts = parseTimestamp(line);
    const text = ts !== null
      ? line.replace(/^\[\d+:\d{2}(?::\d{2})?\]\s*/, '').trim()
      : line;
    return { ts, text };
  }).filter(p => p.text.length > 0);

  const hasTimestamps = parsed.some(p => p.ts !== null);

  // ── CAS 1 : les paroles ont des timestamps [mm:ss] ──
  // On utilise UNIQUEMENT ces timestamps, Whisper n'est qu'une sécurité
  // pour les lignes sans timestamp intercalées.
  if (hasTimestamps) {
    const validSegs = (whisperSegs || []).filter(
      s => typeof s.start === 'number' && !isNaN(s.start) && s.end > s.start
    );
    const totalDur = validSegs.length ? validSegs[validSegs.length - 1].end : 300;

    // Interpoler les lignes sans timestamp
    const wa = parsed.map((p, i) => ({ text: p.text, start: p.ts, idx: i }));
    for (let i = 0; i < wa.length; i++) {
      if (wa[i].start !== null) continue;
      let prevTs = 0, prevIdx = -1;
      for (let j = i - 1; j >= 0; j--) {
        if (wa[j].start !== null) { prevTs = wa[j].start; prevIdx = j; break; }
      }
      let nextTs = totalDur, nextIdx = wa.length;
      for (let j = i + 1; j < wa.length; j++) {
        if (wa[j].start !== null) { nextTs = wa[j].start; nextIdx = j; break; }
      }
      const gc = nextIdx - prevIdx - 1, pg = i - prevIdx;
      wa[i].start = prevTs + (pg / gc) * (nextTs - prevTs);
    }

    const result = [];
    for (let i = 0; i < wa.length; i++) {
      const st = wa[i].start;
      const nd = i < wa.length - 1 ? wa[i + 1].start : totalDur;
      result.push({
        start: Math.max(0, st + userOffset),
        end:   Math.max(nd - 0.1, st + 1.2),
        text:  wa[i].text
      });
    }

    // Sécurité : éviter chevauchements
    for (let i = 1; i < result.length; i++) {
      if (result[i].start <= result[i - 1].start) {
        result[i].start = result[i - 1].start + 0.5;
        result[i].end = Math.max(result[i].end, result[i].start + 1.2);
      }
    }
    for (let i = 0; i < result.length - 1; i++) {
      if (result[i].end > result[i + 1].start) {
        result[i].end = Math.max(result[i].start + 0.4, result[i + 1].start - 0.05);
      }
    }
    return result;
  }

  // ── CAS 2 : paroles sans timestamps → on utilise Whisper pour les timings ──
  // (comportement original, inchangé)
  const validSegs = (whisperSegs || []).filter(
    s => typeof s.start === 'number' && typeof s.end === 'number'
      && !isNaN(s.start) && !isNaN(s.end) && s.end > s.start
  );
  const totalDur = validSegs.length ? validSegs[validSegs.length - 1].end : 180;
  const N = parsed.length, sd = totalDur / N;
  const result = [];
  for (let i = 0; i < N; i++) {
    const ts = i * sd, te = (i + 1) * sd, tc = (i + 0.5) * sd, mg = sd * 0.3;
    let bs = null, bd = Infinity;
    for (const s of validSegs) {
      if (s.start < ts - mg || s.start > te + mg) continue;
      const d = Math.abs(s.start - tc);
      if (d < bd) { bd = d; bs = s; }
    }
    const rs = bs ? bs.start : ts;
    result.push({
      start: Math.max(0, rs + userOffset),
      end:   Math.max(bs ? bs.end : te, rs + 1.2),
      text:  parsed[i].text
    });
  }
  for (let i = 1; i < result.length; i++) {
    if (result[i].start <= result[i - 1].start) {
      result[i].start = result[i - 1].start + 0.5;
      result[i].end = Math.max(result[i].end, result[i].start + 1.2);
    }
  }
  for (let i = 0; i < result.length - 1; i++) {
    if (result[i].end > result[i + 1].start) {
      result[i].end = Math.max(result[i].start + 0.4, result[i + 1].start - 0.05);
    }
  }
  return result;
}


// ══════════════════════════════════════════════════════════
//  PATCH 2 — Dans handleCreate, remplace le bloc Whisper
//  (cherche "if (useWhisper || useLyrics) {" vers ligne ~890)
//
//  Logique corrigée :
//  - Si les paroles ont des timestamps → pas besoin de Whisper du tout
//  - Si les paroles n'ont PAS de timestamps → Whisper est nécessaire
//  - Si pas de paroles → Whisper seul
// ══════════════════════════════════════════════════════════

// REMPLACE le bloc existant "if (useWhisper || useLyrics) { ... }" par :

  let segs = [];

  // Détecter si les paroles fournies ont des timestamps [mm:ss]
  const lyricsHaveTimestamps = useLyrics && rawLyrics &&
    /^\[\d+:\d{2}(?::\d{2})?\]/m.test(rawLyrics);

  dbg('INFO', `lyricsHaveTimestamps: ${lyricsHaveTimestamps}, useLyrics: ${useLyrics}, useWhisper: ${useWhisper}`);

  if (lyricsHaveTimestamps) {
    // ── Paroles avec timestamps : on n'a PAS besoin de Whisper ──
    dbg('OK', 'Paroles avec timestamps détectés → utilisation directe, Whisper ignoré');
    setStatus('Synchronisation des paroles…', 18, 'Timestamps [mm:ss] détectés');
    const off = document.getElementById('lyricsOffset');
    segs = syncLyricsWithTimings([], rawLyrics, off ? parseFloat(off.value) || 0 : -0.3);
    dbg('OK', `${segs.length} lignes synchronisées depuis les timestamps`);
    setStatus('Prêt!', 22, segs.length + ' lignes');
    await new Promise(r => setTimeout(r, 300));

  } else if (useWhisper || useLyrics) {
    // ── Whisper nécessaire (paroles sans timestamps OU sous-titres auto) ──
    setStatus('Chargement Whisper…', 5, 'Première fois: ~75 MB, puis en cache');
    try {
      const wSegs = await transcribeAll(audioFile, lang);
      if (useLyrics && rawLyrics) {
        setStatus('Synchronisation…', 18, '');
        const off = document.getElementById('lyricsOffset');
        segs = syncLyricsWithTimings(wSegs, rawLyrics, off ? parseFloat(off.value) || 0 : -0.3);
        dbg('OK', `${segs.length} lignes synchronisées avec Whisper (pas de timestamps)`);
      } else {
        segs = wSegs;
        dbg('OK', `${segs.length} segments Whisper (transcription auto)`);
      }
      setStatus('Prêt!', 22, segs.length + ' lignes');
      await new Promise(r => setTimeout(r, 400));
    } catch(e) {
      logT('⚠️ ' + e.message);
      dbg('WARN', 'Erreur Whisper (continuo sans sous-titres):', e.message);
      if (!confirm('Erreur Whisper:\n"' + e.message + '"\n\nContinuer sans sous-titres?')) {
        resetUI(); return;
      }
      segs = [];
    }
  }

  // À la fin du bloc, mettre à jour lyricsMode pour le payload DB :
  // (remplace aussi la ligne `const lyricsMode = ...` en haut de handleCreate)
  // const lyricsMode = lyricsHaveTimestamps ? 'manual' :
  //                    (useLyrics && rawLyrics ? 'manual' :
  //                    (useWhisper ? 'whisper' : 'none'));
