# ❄️ SnowTricks - Site Communautaire de Snowboard 🏂

> **Ce projet a été réalisé dans le cadre de mon apprentissage pour le parcours d'OpenClassrooms (Développeur d'application PHP/Symfony).**  
> --> *Version : [English](README.md)* 📖

## 📖 Description

**SnowTricks** est une plateforme communautaire dédiée aux snowboarders.  
Les utilisateurs peuvent découvrir et partager des figures de snowboard, ainsi qu'interagir via un système de commentaires.

![Aperçu du projet SnowTricks](screenshot.jpg)

## 🚀 Fonctionnalités

- **Catalogue de figures** : Tous les visiteurs peuvent consulter la liste des figures avec leur description.
- **Gestion des figures** : Les utilisateurs inscrits peuvent ajouter, modifier et supprimer des figures.
- **Système de commentaires** : Les membres peuvent commenter les figures et échanger des astuces.
- **Authentification des utilisateurs** : Inscription et connexion sécurisées.
- **Améliorations de la sécurité** : Protection contre XSS, CSRF et injection SQL.
- **Interface responsive** : Design adapté à tous types d'écrans.

## 🚧 Installation

### Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- **PHP et Composer**
- **Symfony CLI**
- **MySQL**
- **Git**

### Étapes d'installation

1. **Cloner le dépôt**  
   ```sh
   git clone https://github.com/TolMen/OCProject6.git
   cd OCProject6
   ```

2. **Installer les dépendances**  
   ```sh
   symfony console composer install
   ```

3. **Configurer la base de données**  
   - Modifier le fichier `.env` en remplaçant la ligne suivante avec vos paramètres :  
     ```sh
     DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
     ```
   - Créer la base de données :  
     ```sh
     symfony console doctrine:database:create
     ```
   - Générer la structure de la base de données :  
     ```sh
     symfony console make:migration
     symfony console doctrine:migrations:migrate
     ```
   - (Optionnel) Insérer des données fictives :  
     ```sh
     symfony console doctrine:fixtures:load
     ```

4. **Configurer l'envoi d'e-mails**  
   - Modifier `.env.local` et définir le MAILER_DSN :  
     ```sh
     MAILER_DSN=smtp://votre_serveur_mail
     ```
---

Merci d'explorer ce projet.  
N'hésitez pas à l'explorer, le modifier et l'améliorer ! ✨  

**Pour toute question ou collaboration, n'hésitez pas à me contacter ! 📩**

[TolMen](https://github.com/TolMen) - [LinkedIn](https://www.linkedin.com/in/jessyfrachisse/)
