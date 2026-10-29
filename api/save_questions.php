<?php

session_start();
require __DIR__ .   '/../config/Database.php';

header("Content-Type: application/json; charset=utf-8");

// Verifie la methode HTTP, accepte juste les requetes POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["message" => "Méthode non autorisée"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$question = trim($data['question'] ?? "");
$reponses = $data['reponses'] ?? [];
$radioChoisie = $data['radioChoisie'] ?? null;

$erreurs = [];
$champValides = true;

// Validation de la question
if($question === ""){
    $erreurs["question"] = "La question est obligatoire.";
    $champValides = false;
}

// Validation des réponses

$nombreBonnesReponses = 0;

if(!is_array($reponses) ||count($reponses) < 2){ 
    $erreurs["reponses"] = "Minimum de 2 réponses.";
    $champValides = false;

} else {
    // Parcourir chaque réponse pour vérifier son contenu et son statut true/false
    foreach ($reponses as $rep) {
        $texte = trim($rep['texte'] ?? '');
        
        // On récupère le true ou false envoyé par le JS
        $estCorrect = filter_var($rep['correct'] ?? false, FILTER_VALIDATE_BOOLEAN);

        // Vérification : est-ce que le texte de la réponse est vide ?
        if ($texte === "") {
            $erreurs["reponse_text"] = "Une réponse ne peut pas être vide.";
        }

        // Si cette réponse a 'correct' à true, on incrémente notre compteur
        if ($estCorrect === true) {
            $nombreBonnesReponses++;
        }
    }

    // Vérification finale des boutons radio (il en faut exactement UN à true)
    if ($nombreBonnesReponses === 0) {
        $erreurs["correct"] = "Vous devez sélectionner une bonne réponse en cochant un bouton radio.";
        $champValides = false;
    } 
}



if(empty($erreurs)){

    // Insertion de la question et des réponses dans la BD
    $insertionQuestion = $pdo->prepare(
        "INSERT IGNORE INTO questions (text, quiz_id)
        VALUES(:text, :quiz_id)"
    );

    $insertionQuestion->execute([
        'text' => $question,
        'quiz_id' => $_SESSION['quiz_id']
    ]);

    // Récupérer le ID de la question
    $questionId = $pdo->lastInsertId();

    $insertionReponses = $pdo->prepare(
        "INSERT IGNORE INTO reponses (text, est_vrai, question_id)
        VALUES(:text, :est_vrai, :question_id)"
    );

    foreach($reponses as $rep){
        $insertionReponses->execute([
            'text' => $rep['texte'],
            'est_vrai' => $rep['correct'],
            'question_id' => $questionId
        ]);
    }

    echo json_encode([
        "success" => true,
        "redirect" => "ajouter_questions.php?quiz_id={$_SESSION['quiz_id']}"
    ]);
    exit();

} else {

    http_response_code(400);
    echo json_encode([
        "success" => false, 
        "erreurs" => $erreurs
    ]);
    exit();
};


?>