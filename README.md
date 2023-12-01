# Binsta
An Instagram inspired social media for code snippets

The web app has the following functions:
- Account registratie en login
- Zoekbalk waarmee je naar gebruikers kunt zoeken
- Profiel wijzigen
    - Biografie en naam wijzigen
    - Profielfoto uploaden
    - Wachtwoord wijzigen
- Posts
    - Code snippets aanmaken met tekstveld
    - Programmeertaal kiezen
    - Syntax highlighting
    - Caption toevoegen die onder de post wordt getoond
- Feed
    - Lijst zien van meest recente posts
    - Posts op feed liken
    - Commenten op posts
    - Doorklikken naar het profiel van de gebruiker
- Profiel van gebruiker
    - Weergave van bio en profielfoto
    - Persoonlijke feed van posts van die gebruiker

## Installation

1) Download and unzip project

2) Install xampp and set the public folder as the DocumentRoot

3) Install dependencies with composer:
```bash
composer install
```

4) Start xampp

5) Make a MySQL database with the name: 'binsta'

6) Make sure there is a user with the the username and password set to: 'bit_academy' (Or change the username and password in public.php and seeder.php)

7) Seed the database:
```bash
php seeder.php
```

8) See the project by accessing the page through your local host
