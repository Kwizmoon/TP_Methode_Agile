<?php
    require_once '../middleware/auth.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la question</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require_once 'header.php'; ?>

<main class="main-builder">
    <form id="formModifier" novalidate>
        <h2>Modifier la question</h2>

        <div>
            <label for="question">Question : </label>
            <input type="text" id="question" required>
            <p class="erreur" id="erreur-question"></p>
        </div>

        <div class="reponses-section">
            <h3>Réponses (Maximum : 5)</h3>
            <div id="liste-reponses"></div>
            <button type="button" id="btn-ajouter-reponse">+ Ajouter une réponse</button>
            <p class="erreur" id="erreur-reponses"></p>
        </div>

        <div class="form-actions" style="margin-top: 20px;">
            <button type="submit" id="btn-valider">Terminer et appliquer les modifications</button>
            <button type="button" class="btn" id="btn-annuler">Annuler</button>
        </div>
        <p class="erreur" id="erreur-global"></p>
    </form>
</main>

<script>

    const listeReponses = document.getElementById('liste-reponses');
    const btnAjouterReponse = document.getElementById('btn-ajouter-reponse');
    const formModifier = document.getElementById('formModifier');
    const maxReponses = 5;

    // Recupere l'id de la question dans l'URL
    const questionId = new URLSearchParams(window.location.search).get('id');
    let quizId = null;

    // Cree une ligne de reponse (texte + bouton radio + bouton supprimer)
    function creerChampReponse(texte = '', correct = false) {
        if (listeReponses.children.length >= maxReponses) return;

        const div = document.createElement('div');
        div.className = 'reponse-ligne';
        div.style.display = 'flex';
        div.style.alignItems = 'center';
        div.style.gap = '10px';
        div.style.marginBottom = '10px';
        div.style.width = '100%';

        const inputTexte = document.createElement('input');
        inputTexte.type = 'text';
        inputTexte.style.flex = '1';
        inputTexte.value = texte;

        const radio = document.createElement('input');
        radio.type = 'radio';
        radio.name = 'bonne_reponse';
        radio.title = 'Définir comme une bonne réponse';
        radio.style.cursor = 'pointer';
        radio.style.transform = 'scale(1.2)';
        radio.checked = correct;

        const bouton = document.createElement('button');
        bouton.type = 'button';
        bouton.className = 'btn-supprimer';
        bouton.textContent = 'X';
        bouton.onclick = function () {
            div.remove();
            mettreAJour();
        };

        div.append(inputTexte, radio, bouton);
        listeReponses.appendChild(div);
        mettreAJour();
    }

    // Renumerote les placeholders et cache le bouton d'ajout au maximum
    function mettreAJour() {
        Array.from(listeReponses.children).forEach(function (ligne, i) {
            ligne.querySelector('input[type="text"]').placeholder = 'Réponse ' + (i + 1);
        });
        btnAjouterReponse.style.display = listeReponses.children.length >= maxReponses ? 'none' : 'block';
    }

    btnAjouterReponse.addEventListener('click', function () {
        creerChampReponse();
    });

    // Charge la question actuelle dans le formulaire
    async function chargerQuestion() {
        if (!questionId) {
            document.getElementById('erreur-global').textContent = 'Aucune question sélectionnée.';
            return;
        }

        try {
            const response = await fetch('../api/get_question.php?id=' + encodeURIComponent(questionId));
            const result = await response.json();

            if (!result.success) {
                document.getElementById('erreur-global').textContent = result.message || 'Question introuvable.';
                return;
            }

            quizId = result.question.quiz_id;
            document.getElementById('question').value = result.question.text;

            result.question.reponses.forEach(function (rep) {
                creerChampReponse(rep.text, rep.est_vrai == 1);
            });

        } catch (error) {
            console.error(error);
            document.getElementById('erreur-global').textContent = 'Impossible de communiquer avec le serveur.';
        }
    }

    // Retour a la page de details du quiz
    document.getElementById('btn-annuler').onclick = function () {
        window.location.href = quizId ? 'quiz_details.php?id=' + quizId : 'Dashboard.php';
    };

    // Enregistre les modifications
    formModifier.addEventListener('submit', async function (e) {
        e.preventDefault();

        document.getElementById('erreur-question').textContent = '';
        document.getElementById('erreur-reponses').textContent = '';
        document.getElementById('erreur-global').textContent = '';

        const question = document.getElementById('question').value.trim();
        const reponses = Array.from(listeReponses.querySelectorAll('.reponse-ligne')).map(function (ligne) {
            return {
                texte: ligne.querySelector('input[type="text"]').value.trim(),
                correct: ligne.querySelector('input[type="radio"]').checked
            };
        });

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
            const response = await fetch('../api/modifier_question.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    question_id: questionId,
                    question: question,
                    reponses: reponses
                })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                window.location.href = result.redirect;
            } else if (result.erreurs) {
                document.getElementById('erreur-question').textContent = result.erreurs.question || '';
                document.getElementById('erreur-reponses').textContent = result.erreurs.reponses || '';
                document.getElementById('erreur-global').textContent = result.erreurs.global || '';
            } else {
                document.getElementById('erreur-global').textContent = 'Une erreur est survenue.';
            }

        } catch (error) {
            console.error(error);
            document.getElementById('erreur-global').textContent = 'Impossible de communiquer avec le serveur.';
        }
    });

    chargerQuestion();

</script>

</body>
</html>