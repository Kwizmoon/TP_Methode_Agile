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
    }else if(mb_substr($nom, 0, 1) !== mb_strtoupper(mb_substr($nom, 0, 1))){
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

    <?php require_once 'header.php'; ?>
    <main>
        <form action="" method="post">
            <div>
                <label for="nom">Nom d'utilisateur : </label>
                <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($nom); ?>"
                class="<?= isset($erreurs['nom']) ? 'input-erreur' : '' ?>">
                    <?php if(isset($erreurs['nom'])): ?>
                        <p class="erreur"><?= htmlspecialchars($erreurs['nom']) ?></p>
                    <?php endif; ?> 
            </div>

            <div>

                <label for="motDePasse">Mot de passe : </label>
                <input type="password" name="motDePasse" id="motDePasse" value="<?= htmlspecialchars($motDePasse); ?>"
                class="<?= isset($erreurs['motDePasse']) ? 'input-erreur' : '' ?>">
                    <?php if(isset($erreurs['motDePasse'])): ?>
                        <p class="erreur"><?= htmlspecialchars($erreurs['motDePasse']) ?></p>
                    <?php endif; ?>
            </div>

            <button type="submit">Se connecter</button>
        </form>
    </main>
</body>
</html>