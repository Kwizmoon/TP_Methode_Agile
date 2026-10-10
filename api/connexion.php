<?php


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


// Verifie les champs ne sont pas vides sinon envoie erreur 400 avec message
if (
    !is_array($data) ||
    empty($data["nom"]) ||
    empty($data["motDePasse"])
) {
    http_response_code(400);
    echo json_encode((["message" => "Informations manquantes"]));
    exit;
}