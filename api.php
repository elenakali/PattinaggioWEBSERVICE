<?php
header("Content-Type: application/json; charset=UTF-8");

$h = 'localhost';
$db = 'db_pattinaggio';
$u = 'root';
$p = '';

try {
    $pdo = new PDO("mysql:host=$h;dbname=$db;charset=utf8", $u, $p);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database irraggiungibile"]);
    exit;
}

$azione = isset($_GET['azione']) ? $_GET['azione'] : '';

if ($azione == 'lista_corsi') {
    
    $q = $pdo->query("SELECT * FROM corsi_pattini WHERE posti_liberi > 0");
    $corsi = $q->fetchAll(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode(["status" => "ok", "payload" => $corsi]);

} elseif ($azione == 'iscriviti') {
    
    $dati = json_decode(file_get_contents("php://input"));
    
    if (!empty($dati->id_corso) && !empty($dati->email)) {
        
        $chk = $pdo->prepare("SELECT posti_liberi FROM corsi_pattini WHERE id = ?");
        $chk->execute([$dati->id_corso]);
        $corso = $chk->fetch(PDO::FETCH_ASSOC);
        
        if ($corso && $corso['posti_liberi'] > 0) {
            
            $pdo->beginTransaction();
            
            try {
                $ins = $pdo->prepare("INSERT INTO iscrizioni (id_corso, email) VALUES (?, ?)");
                $ins->execute([$dati->id_corso, $dati->email]);
                
                $upd = $pdo->prepare("UPDATE corsi_pattini SET posti_liberi = posti_liberi - 1 WHERE id = ?");
                $upd->execute([$dati->id_corso]);
                
                $pdo->commit();
                
                http_response_code(201);
                echo json_encode(["status" => "ok", "message" => "Iscrizione confermata"]);
                
            } catch (Exception $e) {
                $pdo->rollBack();
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Errore di sistema"]);
            }
            
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Pista al completo"]);
        }
        
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Dati obbligatori mancanti"]);
    }

} else {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Endpoint non valido"]);
}
?>
