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

<main class="layout-creation main-builder">
    <section class="zone-formulaire">
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
    </section>

    <!-- COLONNE DROITE : Panneau d'aperçu -->
    <aside class="panneau-apercu">
        <div class="apercu-header">
            <h3>Aperçu du quiz</h3>
            <span id="compteur-questions" class="badge">0 question</span>
        </div>
        <ul id="liste-questions-apercu" class="liste-questions">
            <li class="etat-vide">Aucune question enregistrée pour le moment.</li>
        </ul>
    </aside>
</main>

    <script>
            const listeReponses = document.getElementById('liste-reponses');
            const btnAjouterReponse = document.getElementById('btn-ajouter-reponse');
            const formQuestion = document.getElementById('formQuestion');
            const maxReponses = 5;

            // Récupérer le ID du quiz
            const urlParams = new URLSearchParams(window.location.search);
            const quizId = urlParams.get('id') || urlParams.get('quiz_id');

            // Charger l'aperçu au chargement de la page si un ID existe
            document.addEventListener('DOMContentLoaded', () => {
                creerChampReponse();
                creerChampReponse();

                if (quizId) {
                    chargerApercuQuestions();
                }
            });

            // Fonction pour récupérer les questions depuis l'API
            async function chargerApercuQuestions() {
                if (!quizId) return;

                try {
                    const response = await fetch(`../api/get_questions.php?quiz_id=${quizId}`);
                    const data = await response.json();

                    if (data.success) {
                        afficherQuestionsDansPanneau(data.questions);
                    }
                } catch (error) {
                    console.error("Erreur lors du chargement de l'aperçu :", error);
                }
            }

            // Fonction pour dessiner la liste des questions dans le panneau de droite

            function afficherQuestionsDansPanneau(questions) {
                const listeEl = document.getElementById('liste-questions-apercu');
                const compteurEl = document.getElementById('compteur-questions');
                
                listeEl.innerHTML = '';

                const nb = questions.length;
                compteurEl.textContent = nb > 1 ? `${nb} questions` : `${nb} question`;

                if (nb === 0) {
                    listeEl.innerHTML = '<li class="etat-vide">Aucune question enregistrée pour le moment.</li>';
                    return;
                }

                questions.forEach((q, index) => {
                    const li = document.createElement('li');
                    li.className = 'carte-question-apercu';

                    // Nombre de réponses
                    const nbReponses = q.reponses ? q.reponses.length : 0;

                    // Trouver l'unique bonne réponse
                    let bonneReponseTexte = "Aucune";
                    if (q.reponses && q.reponses.length > 0) {
                        // On cherche la réponse dont est_vrai vaut 1, '1' ou true
                        const bonne = q.reponses.find(r => r.est_vrai == 1 || r.est_vrai === "1" || r.est_vrai === true);
                        if (bonne) {
                            const texteTrouve = bonne.text || bonne.texte || bonne.intitule;
                            if (texteTrouve) {
                                bonneReponseTexte = echapperHTML(texteTrouve);
                            }
                        }
                    }

                    li.innerHTML = `
                        <div class="question-numero">Question ${index + 1}</div>
                        <div class="question-texte"><strong>${echapperHTML(q.question)}</strong></div>
                        <div class="question-meta" style="margin-top: 6px; font-size: 0.85rem;">
                            <div>💬 ${nbReponses} réponse(s)</div>
                            <div style="color: #16a34a; margin-top: 3px;">✔ <strong>Correcte :</strong> ${bonneReponseTexte}</div>
                        </div>
                    `;
                    listeEl.appendChild(li);
                });
            }

            // Sécurité contre les failles XSS (Ajoutée ici pour corriger l'erreur 1)
            function echapperHTML(str) {
                if (!str) return '';
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            }

            // Fonction pour créer un champ de réponse dynamique
            function creerChampReponse(){
                const nombreActuel = listeReponses.children.length;
                if(nombreActuel >= maxReponses) return;
                
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
                } else {
                    btnAjouterReponse.style.display = 'block';
                }
            }

            btnAjouterReponse.addEventListener('click', creerChampReponse);
            
            // Soumission du formulaire avec l'API
            formQuestion.addEventListener('submit', async function(e) {
                e.preventDefault();

                document.getElementById('erreur-question').textContent = '';
                document.getElementById('erreur-reponses').textContent = '';
                document.getElementById('erreur-global').textContent = '';

                const inputQuestion = document.getElementById('question');
                const question = inputQuestion ? inputQuestion.value.trim() : '';
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
                            quiz_id: quizId, // Important pour lier la question au quiz
                            question: question,
                            reponses: reponses
                        })
                    });

                    const resultat = await response.json();

                    if (response.ok && resultat.success) {
                        // Utilisation de formQuestion.reset() pour réinitialiser proprement tout le formulaire d'un coup
                        formQuestion.reset();
                        listeReponses.innerHTML = '';
                        creerChampReponse();
                        creerChampReponse();

                        // Rafraîchir le panneau latéral sans recharger la page
                        if (quizId) {
                            chargerApercuQuestions();
                        }
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