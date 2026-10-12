


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
        
        <input type="hidden" name="role" id="roleInput" value="enseignant">

        <div class="switch-role-container">
            <button type="button" 
                    class="switch-btn active" 
                    id="btn-enseignant" 
                    onclick="setRole('enseignant')">
                Enseignants
            </button>
            <button type="button" 
                    class="switch-btn" 
                    id="btn-etudiant" 
                    onclick="setRole('etudiant')">
                Étudiants
            </button>
        </div>
        </div>
        <form action="" method="post" id="loginForm">
            <div>
                <label for="nom">Nom d'utilisateur : </label>
                <input type="text" name="nom" id="nom">
                <p class="erreur" id="erreur-nom"></p>
                
            </div>

            <div>

                <label for="motDePasse">Mot de passe : </label>
                <input type="password" name="motDePasse" id="motDePasse">
                <p class="erreur" id="erreur-motDePasse"></p>
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

        // vide les erreurs et les style precedents
        document.getElementById('erreur-nom').textContent = '';
        document.getElementById('erreur-motDePasse').textContent = '';
        document.getElementById('nom').classList.remove('input-erreur');
        document.getElementById('motDePasse').classList.remove('input-erreur');

        const data = {
            nom: document.getElementById('nom').value,
            motDePasse: document.getElementById('motDePasse').value,
            role: document.getElementById('roleInput').value
        };
        try {
            const response = await fetch('api/connexion.php', {
            method : 'POST',
            headers : {'Content-Type' : 'application/json'},
            body: JSON.stringify(data)
            });

            const result = await response.json();

            if(result.success){
                // rediriction a la page url specifier dans connexion.php
                window.location.href = result.redirect;
            } else if (result.erreurs) {

                // Met les text erreurs en bas des champs quand il ya une erreur
                if (result.erreurs.nom) {
                        document.getElementById('erreur-nom').textContent = result.erreurs.nom;
                        document.getElementById('nom').classList.add('input-erreur');
                    }
                if (result.erreurs.motDePasse) {
                    document.getElementById('erreur-motDePasse').textContent = result.erreurs.motDePasse;
                    document.getElementById('motDePasse').classList.add('input-erreur');
                }

            }
        } catch (error) {
            console.error('Erreur réseau:', error);
        }
        
    });
</script>