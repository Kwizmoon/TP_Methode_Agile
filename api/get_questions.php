<?php 

session_start();
require __DIR__ . '/../config/Database.php';

header("Content-Type: application/json; charset=utf-8");

$quiz_id = $_GET['quiz_id'] ?? ($_SESSION['quiz_id'] ?? null);

if(!$quiz_id){
    echo json_encode(["success" => false, "message" => "Quiz ID manquant"]);
    exit;
}

try{
    // Récupérer les questions liées à ce quiz
    $stmt = $pdo->prepare("SELECT id, text AS question FROM questions WHERE quiz_id = ?");
    $stmt->execute([$quiz_id]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Compter les réponses pour chaque question
    foreach($questions as $q){
        $stmtRep = $pdo->prepare("SELECT COUNT(*) FROM reponses WHERE question_id = ?");
        $stmtRep->execute([$q['id']]);
        $q['nb_reponses'] = $stmtRep->fetchColumn();
    }

    echo json_encode([
        "success" => true,
        "questions" => $questions
    ]);

} catch(Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

?>