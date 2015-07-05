#Publinote

Gestion des classements Tutorat de première année de médecine.
Projet commun entre les tutorats de Lyon est et de Lyon sud.

##Instaler Publinote

Commencer par installer composer selon les instructions données ici: https://getcomposer.org/download/

Après avoir cloné le dépôt de Publinote, renommer le fichier `.env.example` en `.env`. Modifier ce dernier fichier en y renseignant les informations d'accès à la base de données.

Installer les dépendances de Laravel et les mettre à jour avec
```
composer install
composer update
```

Générer la clé de l'application
```
php artisan key:generate
```

## Laravel PHP Framework

[![Build Status](https://travis-ci.org/laravel/framework.svg)](https://travis-ci.org/laravel/framework)
[![Total Downloads](https://poser.pugx.org/laravel/framework/d/total.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/laravel/framework)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)
