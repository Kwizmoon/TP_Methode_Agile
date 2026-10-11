<?php 


session_start();

$nom = $_SESSION['nom'] ?? "";
$motDePasse = $_SESSION['motDePasse'] ?? "";
$role = $_POST['role'] ?? 'enseignant';
$erreurs = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nom = trim($_POST['nom'] ?? "");
    $motDePasse = trim($_POST['motDePasse'] ?? "");
    $role = $_POST['role'] ?? 'enseignant';

    // Validation du nom d'utilisateur
    if($nom === ""){
        $erreurs["nom"] = "Le nom est obligatoire";
    }

    // Validation du mot de passe
    if($motDePasse === ""){
        $erreurs["motDePasse"] = "Le mot de passe est obligatoire";
    }
    

    if(empty($erreurs)){
        $_SESSION['nom'] = $nom;
        $_SESSION['role'] = $role;

        if($role === 'enseignant'){
            header('Location: Dashboard.php');
            exit();
        }else{
            header('Location: Dashboard.php');
            exit();
        }
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
        
        <input type="hidden" name="role" id="roleInput" value="<?= htmlspecialchars($_POST['role'] ?? 'enseignant') ?>">

        <div class="switch-role-container">
            <button type="button" 
                    class="switch-btn <?= (($_POST['role'] ?? 'enseignant') === 'enseignant') ? 'active' : '' ?>" 
                    id="btn-enseignant" 
                    onclick="setRole('enseignant')">
                Enseignants
            </button>
            <button type="button" 
                    class="switch-btn <?= (($_POST['role'] ?? '') === 'etudiant') ? 'active' : '' ?>" 
                    id="btn-etudiant" 
                    onclick="setRole('etudiant')">
                Étudiants
            </button>
        </div>
        </div>
        <form action="" method="post" id="loginForm">
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
<script>
    function setRole(role){
        const inputRole = document.getElementById('roleInput');
        const btnEtudiant = document.getElementById('btn-etudiant');
        const btnEnseignant = document.getElementById('btn-enseignant');

        inputRole.value = role;

        if(role === 'enseignant'){
            btnEnseignant.classList.add('active');
            btnEtudiant.classList.remove('active');
        }else{
            btnEtudiant.classList.add('active');
            btnEnseignant.classList.remove('active');
        }
    }

    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const data = {
            nom: document.getElementById('nom').value,
            motDePasse: document.getElementById('motDePasse').value,
            role: document.getElementById('roleInput').value
        };

        const response = await fetch('api/connexion.php', {
            method : 'POST',
            headers : {'Content-Type' : 'application/json'},
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if(result.success){
            window.location.href = result.redirect;
        }
    });
</script>