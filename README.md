# MY-boutique

# Description

MY Boutique est une application web e-commerce développée en PHP suivant l'architecture MVC (Modèle-Vue-Contrôleur). Cette plateforme permet aux utilisateurs de parcourir des produits, gérer un panier d'achats, passer des commandes et participer à des événements.

## Fonctionnalités

# Utilisateurs

- Inscription et connexion sécurisées
- Gestion de profil utilisateur
- Différents niveaux d'accès (client, administrateur)
- Historique des commandes
- Produits

- Catalogue de produits avec pagination
- Recherche et filtrage
- Affichage détaillé des produits
- Système d'avis et de notation
- Panier et Commandes

- Ajout/suppression d'articles au panier
- Mise à jour des quantités en AJAX
- Processus de commande sécurisé
- Historique des commandes
- Événements

- Calendrier d'événements
- Détails des événements
- Création et gestion d'événements
- Administration

- Tableau de bord administrateur
- Gestion des utilisateurs
- Gestion des produits
- Gestion des événements
- Suivi des commandes
- Améliorations Techniques

# Sécurité

- Hashage des mots de passe avec bcrypt (via password_hash())
- Protection contre les injections SQL
- Validation des entrées utilisateur
- Gestion sécurisée des sessions
- Accessibilité

- Structure sémantique HTML5
- Attributs ARIA pour les éléments interactifs
- Messages d'erreur accessibles
- AJAX

- Mise à jour du panier sans rechargement de page
- Soumission et gestion des avis
- Mise à jour des quantités de produits
- Filtrage dynamique des produits
- Architecture MVC

- Séparation claire des responsabilités
- Modèles pour la logique métier
- Contrôleurs pour gérer les requêtes
- Routeur pour diriger les requêtes

# Installation

- Clonez ce dépôt sur votre serveur web local ou distant
- Créer votre base de données 
- Configurez les paramètres de connexion à la base de données dans config/config.php
- Assurez-vous que votre serveur web est configuré pour exécuter PHP
- Accédez à l'application via votre navigateur

# Configuration requise

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache, Nginx, etc.)
- Extensions PHP : PDO, mysqli, mbstring

# Utilisation

## Accès client

- Créez un compte via la page d'inscription
- Parcourez les produits et ajoutez-les à votre panier
- Passez des commandes et consultez votre historique
  
## Accès administrateur

- Connectez-vous avec des identifiants administrateur
- Accédez au tableau de bord via /admin.php
- Gérez les utilisateurs, produits, commandes et événements
