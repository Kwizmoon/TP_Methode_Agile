<?php

session_start();
require __DIR__ . '/../config/Database.php';

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
$champsValides = true;

// Validation du nom d'utilisateur
if($nom === ""){
    $erreurs["nom"] = "Le nom est obligatoire";
    $champsValides = false;
}

// Validation du mot de passe
if($motDePasse === ""){
    $erreurs["motDePasse"] = "Le mot de passe est obligatoire";
    $champsValides = false;
}

// Validation du user/motDePasse dans la BD, Limit de 1 user recuperer
$userExist = $pdo->prepare(
    'SELECT id, nom, mot_de_passe, role FROM users
    WHERE nom = :nom
    AND role = :role
    LIMIT 1   
    '
);
$userExist->execute([
    'nom' => $nom,
    'role' => $role
]);

// Recupere la ligne sous forme de tableau associatif
$user = $userExist->fetch();

// Verifier si l'utilisateur existe et si le mot de passe haché correspond
if (!$user || !password_verify($motDePasse, $user['mot_de_passe'])) {
    if ($champsValides) {
        $erreurs["bd"] = "Nom d'utilisateur, mot de passe ou rôle incorrect.";
    }
}

// Enregistre a la session et redirige le URL si ya pas d'erreurs
if(empty($erreurs)){
    // Enregistre a la session et redirige le URL    
    $_SESSION['id'] = $user['id']; 
    $_SESSION['nom'] = $user['nom'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['authenticated'] = true;


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