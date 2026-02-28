<?php
// ============================================================
//  db.php — Connessione Neon PostgreSQL (PDO)
//  Includi questo file in ogni pagina che usa il DB
// ============================================================

define('DB_DSN',      'pgsql:host=ep-lively-meadow-ai7bwkkn-pooler.c-4.us-east-1.aws.neon.tech;port=5432;dbname=neondb;sslmode=require');
define('DB_USER',     'neondb_owner');
define('DB_PASSWORD', 'npg_2fJsV5agylDW');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(DB_DSN, DB_USER, DB_PASSWORD, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode(['error' => 'Connessione DB fallita: ' . $e->getMessage()]));
        }
    }
    return $pdo;
}
