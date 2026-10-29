<?php

session_start();
require __DIR__ .   '/../config/Database.php';

header("Content-Type : application/json; charset=utf-8");

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
if(!is_array($reponses) ||count($reponses) < 2){ 
    $erreurs["reponses"] = "Minimum de 2 réponses.";
    $champValides = false;
}

// Validation si une case est cochée
if($radioChoisie === null || $radioChoisie === ""){
    $erreurs['radioChoisie'] = "Aucune bonne réponse.";
    $champValides = false;
}





?>