# TP_Methode_agile

**Travail de fin de session**

## Vision stratégique du projet

Le projet consiste à développer une application web complète, simulant un module de gestion d'examens et de questionnaires en ligne. Cette application web doit fonctionner en deux étapes clés : l'enseignant conçoit d'abord le quiz (questions à choix multiples QCM et Vrai/Faux, propositions et bonnes réponses) depuis son espace personnel, puis les étudiants se connectent à la session pour y répondre en direct depuis leur propre appareil. En plus, cette application doit intégrer un verrouillage rigoureux limitant chaque étudiant à une seule tentative par test.

## Sprint 1 : Projet + authentification

### Objectif

Développer une application permettant à un enseignant de se connecter de manière sécurisée et d'accéder à son tableau de bord.

### Tâches et outils utilisés

| Tâche                                                 | Outils utilisés |
| ----------------------------------------------------- | --------------- |
| Définir l'architecture du projet                      |   diagram UML   |
| Créer la base de données et la table des utilisateurs |   PHP - Sqlite  |
| Créer la page de connexion                            |   HTML-CSS-JS   |
| Valider les champs du formulaire                      |   PHP           |
| Développer l'API de connexion                         |   PHP           |
| Implémenter l'authentification côté serveur           |   PHP           |
| Gérer le token d'authentification                     |   PHP           |
| Créer le tableau de bord                              |   HTML-CSS-JS   |
| Tester la connexion et les accès protégés             |   -             |
| Documenter le projet                                  |   Github        |

### Résultat attendu

À la fin du Sprint 1, un enseignant doit pouvoir saisir ses identifiants, être authentifié par l'API et accéder à son tableau de bord. Les utilisateurs non authentifiés ne doivent pas pouvoir accéder au tableau de bord.

<img width="725" height="742" alt="Diagram architecture" src="https://github.com/user-attachments/assets/51c0394f-1064-4da5-b435-aa1d04d0d865" />

## Sprint 2 : Création des quiz + questions

### Objectif

Permettre à l'enseignant de créer un quiz et d'y ajouter des questions sous forme de QCM et/ou Vrai/Faux

### Tâches et outils utilisés

| Tâche                                                 | Outils utilisés |
| ----------------------------------------------------- | --------------- |
| Créer la table des quiz.                              |                 |
| Créer la table des questions.                         |                 |
| Créer la table des réponses.                          |                 |
| Définir les relations entre les tables.               |                 |
| Créer l'interface "Créer un quiz".                    |                 |
| Ajouter les champs titre et description.              |                 |
| Créer l'API de création d'un quiz.                    |                 |
| Afficher la liste des quiz de l'enseignant.           |                 |
| Créer l'interface "Ajouter une question".             |                 |
| Permettre d'ajouter au maximum cinq réponse.          |                 |
| Permettre de sélectionner la bonne réponse.           |                 |
| Enregistrer les questions et les réponses.            |                 |
| Afficher les questions du quiz.                       |                 |
| Ajouter un bouton de validation des formulaires.      |                 |
| Tester la création d'un quiz complet.                 |                 |

### Résultat attendu
Un enseignant peut créer un quiz et construire ses questions sous forme QCM ou Vrai/Faux avec leurs réponses.






