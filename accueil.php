<?php 

$nom = $_SESSION['nom'] ?? "";
$motDePasse = $_SESSION['motDePasse'] ?? "";
$erreurs = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nom = trim($_POST['nom'] ?? "");
    $motDePasse = trim($_POST['motDePasse'] ?? "");

    // Validation du nom d'utilisateur
    if($nom === ""){
        $erreurs["nom"] = "Le nom est obligatoire";
    // mb_substr($nom, start_length, end_length)
    }else if(mb_substr($nom, 0, 1) !== mb_strtoupper($nom, 0, 1)){
        $erreurs["nom"] = "Le nom d'utilisateur n'existe pas ou n'est pas valide";
    }

    // Validation du mot de passe
    if($motDePasse === ""){
        $erreurs["motDePasse"] = "Le mot de passe est obligatoire";
    }
    else if(mb_substr($nom, 0, 1) !== mb_strtoupper($nom, 0, 1)){
        $erreurs["motDePasse"] = "Le mot de passe n'est pas valide";
    }
}



?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <main>


    </main>
    
</body>
</html>