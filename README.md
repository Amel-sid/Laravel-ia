# 🛡️ Policify - Plateforme de Cybersécurité pour PME

> **Simplifiez votre mise en conformité cybersécurité avec l'intelligence artificielle**

![Laravel](https://img.shields.io/badge/Laravel-11.x-red?style=flat&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue?style=flat&logo=php)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-blue?style=flat&logo=tailwindcss)
![License](https://img.shields.io/badge/License-MIT-green?style=flat)

## 📋 Description

**Policify** est une plateforme web innovante conçue spécialement pour les PME françaises qui souhaitent **sécuriser leur infrastructure informatique** sans complexité technique.

### 🎯 Objectifs
- **Démocratiser la cybersécurité** pour les petites et moyennes entreprises
- **Automatiser la création de documents** de sécurité conformes (PSSI, chartes, procédures)
- **Fournir un diagnostic rapide** du niveau de sécurité IT
- **Accompagner la mise en conformité** RGPD et réglementations sectorielles

### ⭐ Fonctionnalités principales

#### 🔍 **Diagnostic de Sécurité Intelligent**
- Évaluation automatisée en 5 minutes
- Scoring détaillé par domaine de sécurité
- Recommandations personnalisées
- Suivi des améliorations dans le temps

#### 🤖 **Génération de Documents par IA**
- **PSSI** (Politique de Sécurité des Systèmes d'Information)
- **Chartes utilisateur** personnalisées
- **Procédures de sauvegarde** adaptées
- Contenu adapté au secteur et à la taille de l'entreprise

#### 📊 **Dashboard de Pilotage**
- Vue d'ensemble du niveau de sécurité
- Alertes et rappels automatiques
- Historique des évaluations
- Plans d'action prioritaires

#### 🎓 **Centre de Formation**
- Modules d'e-learning interactifs
- Sensibilisation aux risques cyber
- Bonnes pratiques sectorielles
- Certifications de formation

## 🚀 Technologies utilisées

### Backend
- **Laravel 11** - Framework PHP moderne et sécurisé
- **PHP 8.2+** - Langage de programmation
- **MySQL** - Base de données relationnelle
- **Groq AI** - Intelligence artificielle pour la génération de contenu

### Frontend
- **Blade Templates** - Moteur de templates Laravel
- **TailwindCSS** - Framework CSS utilitaire
- **Alpine.js** - Framework JavaScript léger
- **Chart.js** - Graphiques et visualisations

### Sécurité & Conformité
- **Chiffrement des données sensibles**
- **Authentification sécurisée**
- **Conformité RGPD** native
- **Journalisation des accès**

## 🛠️ Installation

### Prérequis
- PHP 8.2 ou supérieur
- Composer
- Node.js & NPM
- MySQL 8.0+

### Étapes d'installation

```bash
# Cloner le repository
git clone https://github.com/votre-username/policify.git
cd policify

# Installer les dépendances PHP
composer install

# Installer les dépendances JavaScript
npm install && npm run build

# Configurer l'environnement
cp .env.example .env
php artisan key:generate

# Configurer la base de données dans .env
# DB_DATABASE=policify
# DB_USERNAME=votre_user
# DB_PASSWORD=votre_password

# Configurer l'API Groq dans .env
# GROQ_API_KEY=votre_api_key

# Exécuter les migrations
php artisan migrate --seed

# Démarrer le serveur de développement
php artisan serve
```

### Configuration requise

```env
# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=policify
DB_USERNAME=root
DB_PASSWORD=

# API Intelligence Artificielle
GROQ_API_KEY=gsk_your_groq_api_key

# Mail (optionnel)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
```

## 🎯 Public cible

### 👥 Entreprises visées
- **PME de 10 à 500 employés**
- **Tous secteurs d'activité** (priorité : services, commerce, santé)
- **Dirigeants et responsables IT** non-experts en cybersécurité
- **Entreprises soumises au RGPD** et réglementations sectorielles

### 💼 Cas d'usage typiques
- Startup technologique souhaitant sécuriser sa croissance
- Cabinet médical devant protéger les données patients
- Commerce en ligne nécessitant la conformité RGPD
- Bureau d'études gérant des données sensibles clients

## 📈 Roadmap

### Version 1.0 (Actuelle)
- ✅ Diagnostic de sécurité automatisé
- ✅ Génération de PSSI, chartes et procédures
- ✅ Dashboard de pilotage
- ✅ Interface utilisateur intuitive

### Version 1.1 (Prochaine)
- 🔄 Module de formation e-learning
- 🔄 Notifications automatiques
- 🔄 Export PDF des documents
- 🔄 API pour intégrations tierces

### Version 2.0 (Futur)
- 🎯 Surveillance continue des menaces
- 🎯 Intégration avec outils IT existants
- 🎯 Marketplace de solutions cybersécurité
- 🎯 Module de gestion des incidents

## 🤝 Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Fork le projet
2. Créez une branche pour votre fonctionnalité (`git checkout -b feature/nouvelle-fonctionnalite`)
3. Committez vos changements (`git commit -m 'Ajout d'une nouvelle fonctionnalité'`)
4. Push vers la branche (`git push origin feature/nouvelle-fonctionnalite`)
5. Ouvrez une Pull Request

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 📞 Contact & Support

- **Email** : contact@policify.fr
- **Documentation** : [docs.policify.fr](https://docs.policify.fr)
- **Support** : [support.policify.fr](https://support.policify.fr)

---

<p align="center">
<strong>🛡️ Sécurisez votre entreprise en toute simplicité avec Policify</strong>
</p>