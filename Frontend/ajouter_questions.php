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
    <form action="" method="post">
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
        </div>
        <p class="erreur" id="erreur-global"></p>
    </form>
</main>

    <script>
        const listeReponses = document.getElementById('liste-reponses');
        const btnAjouterReponse = document.getElementById('btn-ajouter-reponse');
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
        
        // Validation de la soumission
        document.getElementById('formQuestion').addEventListener('submit', function(e) {
            const radios = document.querySelectorAll('input[type="radio"][name="bonne-reponse"]');
            let uneCochee = false;

            radios.forEach(radio => {
                if(radio.checked) uneCochee = true;
            });

            if(!uneCochee){ // si pas cochée
                e.preventDefault();
                document.getElementById('erreur-reponses').textContent = "Veuillez sélectionner la bonne réponse parmi les choix.";
            }
        });

    </script>

</body>
</html>