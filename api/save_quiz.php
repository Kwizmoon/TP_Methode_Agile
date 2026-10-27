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

$titre = trim($data['titre'] ?? "");
$description = trim($data['description'] ?? "");
$erreurs = [];
$champsValides = true;

// Validation du titre
if($titre === ""){
    $erreurs["titre"] = "Le titre est obligatoire";
    $champsValides = false;
}

// Validation de la description
if($motDePasse === ""){
    $erreurs["description"] = "La description est obligatoire";
    $champsValides = false;
}