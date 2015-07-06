#Publinote

Gestion des classements Tutorat de première année de médecine.
Projet commun entre les tutorats de Lyon est et de Lyon sud.

##Instaler Publinote

Commencer par installer composer selon les instructions données ici: https://getcomposer.org/download/

Après avoir cloné le dépôt de Publinote, créer un fichier `.env` à partir du fichier `.env.example`. Modifier ce dernier fichier en y renseignant les informations d'accès à la base de données.

Installer les dépendances de Laravel et les mettre à jour avec
```
composer install
composer update
```

Générer la clé de l'application
```
php artisan key:generate
```

##Installer la base de données
Installer la migration avec
```
php artisan migrate
```
Mettre à jour la migration en cas de changements
```
php artisan migrate:rollback
```
Populer la base de données (phase de tests)
```
php artisan db:seed
```

##Configurer l'accès au CAS
Renommer le fichier `cas.php.example` en `cas.php` et y renseigner les informations nécessaires.

###Simuler une authentification CAS
Modifier la ligne 88 :
```
'cas_pretend_user' => env('CAS_PRETEND_USER', ''),
```
en
```
'cas_pretend_user' => env('CAS_PRETEND_USER', 'numéro'),
```
Les numéros CAS crées par le seeder sont :
- `p1501001` à `p1501003` pour les comptes **admin**,
- `p1502001` à `p1502020` pour les comptes **tuteur**,
- `p1503001` à `p1503090` pour les comptes **étudiant**,
- `p1504001` à `p1504010` pour les comptes **étudiant essa**.

## Laravel PHP Framework

[![Build Status](https://travis-ci.org/laravel/framework.svg)](https://travis-ci.org/laravel/framework)
[![Total Downloads](https://poser.pugx.org/laravel/framework/d/total.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/laravel/framework)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)
