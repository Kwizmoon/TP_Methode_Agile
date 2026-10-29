<?php
    require_once '../middleware/auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter les questions</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
<?php require_once 'header.php'; ?>

<main class="main-builder">
    <form id="formQuestion" novalidate>
        <h2>Ajouter une question</h2>
        <div>
            <label for="question">Question : </label>
            <input type="text" name="question" id="question" required>
            <p class="erreur" id="erreur-question"></p>
        </div>

        <div class="reponses-section">
            <h3>Réponses (Maximum : 5)</h3>
            <div id="liste-reponses">

            </div>
            <button type="button" id="btn-ajouter-reponse" class="btn-secondaire">+ Ajouter une réponse</button>
            <p class="erreur" id="erreur-reponses"></p>
        </div>

        <div class="form-actions" style="margin-top: 20px;">
            <button type="submit" id="btn-valider">Ajouter la question</button>
            <button class="btn" onclick="window.location.href='Dashboard.php';">Terminer</button>
        </div>
        <p class="erreur" id="erreur-global"></p>
    </form>
</main>

    <script>
        const listeReponses = document.getElementById('liste-reponses');
        const btnAjouterReponse = document.getElementById('btn-ajouter-reponse');
        const formQuestion = document.getElementById('formQuestion');
        const maxReponses = 5;

        // Fonction pour créer un champ de réponse dynamique
        function creerChampReponse(){
            const nombreActuel = listeReponses.children.length;

            if(nombreActuel >= maxReponses) return; // si > 5
            
            const index = nombreActuel + 1;
            const div = document.createElement('div');
            div.className = 'reponse-ligne';
            
            div.style.display = 'flex';
            div.style.flexDirection = 'row';
            div.style.alignItems = 'center';
            div.style.gap = '10px';
            div.style.marginBottom = '10px';
            div.style.width = '100%';

            div.innerHTML = `
                <input type="text" name="reponses[]" placeholder="Réponse ${index}" required style="flex: 1;">
                <input type="radio" name="bonne_reponse" value="${nombreActuel}" required title="Définir comme une bonne réponse" style="cursor: pointer; transform: scale(1.2);">
                <button type="button" class="btn-supprimer" onclick="supprimerReponse(this)">X</button>
            `;

            listeReponses.appendChild(div);
            verifierLimite();

        }
        function supprimerReponse(bouton){
            const ligne = bouton.closest('.reponse-ligne');
            if(ligne){
                ligne.remove();
            }
            mettreAJour();
            verifierLimite();
        }

        function mettreAJour(){
            const lignes = listeReponses.children;
            for(let i = 0; i < lignes.length; i++){
                const radio = lignes[i].querySelector('input[type="radio"]');
                const texte = lignes[i].querySelector('input[type="text"]');
                radio.value = i;
                texte.placeholder = `Réponse ${i + 1}`;
            }
        }

        function verifierLimite(){
            if(listeReponses.children.length >= maxReponses){
                btnAjouterReponse.style.display = 'none';
            }else{
                btnAjouterReponse.style.display = 'block';
            }
        }
        // Écouteur sur le bouton d'ajout
        btnAjouterReponse.addEventListener('click', creerChampReponse);
        
        // Soumission du formulaire avec l'API
        formQuestion.addEventListener('submit', async function(e) {
            e.preventDefault();

            document.getElementById('erreur-question').textContent = '';
            document.getElementById('erreur-reponses').textContent = '';
            document.getElementById('erreur-global').textContent = '';

            const question = document.getElementById('question').value.trim();
            const lignes = listeReponses.querySelectorAll('.reponse-ligne');

            const reponses = Array.from(lignes).map(ligne => ({
                texte: ligne.querySelector('input[type="text"]').value.trim(),
                correct: ligne.querySelector('input[type="radio"]').checked
            }));

            if (question === '') {
                document.getElementById('erreur-question').textContent = 'La question est obligatoire.';
                return;
            }

            if (reponses.length < 2) {
                document.getElementById('erreur-reponses').textContent = 'Minimum de 2 réponses.';
                return;
            }

            if (reponses.some(rep => rep.texte === '')) {
                document.getElementById('erreur-reponses').textContent = 'Une réponse ne peut pas être vide.';
                return;
            }

            if (reponses.filter(rep => rep.correct).length !== 1) {
                document.getElementById('erreur-reponses').textContent = 'Vous devez sélectionner une bonne réponse.';
                return;
            }

            try {
                const response = await fetch('../api/save_questions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        question: question,
                        reponses: reponses
                    })
                });

                const resultat = await response.json();

                if (response.ok && resultat.success) {
                    window.location.href = resultat.redirect;
                } else if (resultat.erreurs) {
                    if (resultat.erreurs.question) {
                        document.getElementById('erreur-question').textContent = resultat.erreurs.question;
                    }

                    if (resultat.erreurs.reponses || resultat.erreurs.reponse_text) {
                        document.getElementById('erreur-reponses').textContent =
                            resultat.erreurs.reponses || resultat.erreurs.reponse_text;
                    }

                    if (resultat.erreurs.correct) {
                        document.getElementById('erreur-reponses').textContent = resultat.erreurs.correct;
                    }
                } else {
                    document.getElementById('erreur-global').textContent = 'Une erreur est survenue.';
                }

            } catch (erreur) {
                console.error(erreur);
                document.getElementById('erreur-global').textContent = 'Impossible de communiquer avec le serveur.';
            }
        });

    </script>

</body>
</html>