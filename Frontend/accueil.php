


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
        
        <form action="" method="post" id="loginForm">
            <div>
                <label for="nom">Nom d'utilisateur : </label>
                <input type="text" name="nom" id="nom">
                <p class="erreur" id="erreur-nom"></p>
                
            </div>

            <div>
                <label for="motDePasse">Mot de passe : </label>
                <div class="input-wrapper">
                    <input type="password" name="motDePasse" id="motDePasse">
                    <button type="button" id="togglePassword" onclick="togglePasswordVisibility()">
                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                <p class="erreur" id="erreur-motDePasse"></p>
            </div>

            <button type="submit">Se connecter</button>
            <p class="erreur" id="erreur-bd"></p>
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

    function togglePasswordVisibility(){
        const passwordInput = document.getElementById('motDePasse');
        const eyeIcon = document.getElementById('eye-icon');

        if(passwordInput.type === 'password'){
            passwordInput.type = 'text';

            eyeIcon.innerHTML = `
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
            `;
        }else{
            passwordInput.type = 'password';
            eyeIcon.innerHTML = `
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            `
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
            const response = await fetch('../api/connexion.php', {
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
                if (result.erreurs.bd) {
                    document.getElementById('erreur-bd').textContent = result.erreurs.bd;
                }

            }
        } catch (error) {
            console.error('Erreur réseau:', error);
        }

        
    });
</script>