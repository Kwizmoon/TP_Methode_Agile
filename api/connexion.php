<?php

session_start();

// Definit le format de reponse (format JSON)
header("Content-Type: application/json; charset=utf-8");

// Verifie la methode HTTP, accepte juste les requetes POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["message" => "Méthode non autorisée"]);
    exit;
}

// Recuper les donnees envoyer en JSON et les transforment en tableau PHP
$data = json_decode(file_get_contents("php://input"), true);

$nom = trim($data['nom'] ?? "");
$motDePasse = trim($data['motDePasse'] ?? "");
$role = $data['role'] ?? 'enseignant';
$erreurs = [];

// Validation du nom d'utilisateur
if($nom === ""){
    $erreurs["nom"] = "Le nom est obligatoire";
}

// Validation du mot de passe
if($motDePasse === ""){
    $erreurs["motDePasse"] = "Le mot de passe est obligatoire";
}

// Enregistre a la session et redirige le URL si ya pas d'erreurs
if(empty($erreurs)){
    // Enregistre a la session et redirige le URL    
    $_SESSION['nom'] = $nom;
    $_SESSION['role'] = $role;

    echo json_encode([
        "success" => true,
        "redirect" => "Dashboard.php"
    ]);
    exit();

} else { // SI ya des erreurs
    
    http_response_code(400);
    echo json_encode([
        "success" => false, 
        "erreurs" => $erreurs
    ]);
    exit();
}