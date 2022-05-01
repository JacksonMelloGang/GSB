# GSB Project, BTS SIO 2022

## Pré-Requis
- MySQL
- Apache
- PHP

# Documents
Trello: https://trello.com/b/25YOP1LN/gsb

## Fonctionnement
Chaque pages est construite à partir d'un layout (template), (layout disponible dans /views/layout/),\
afin de pouvoir utiliser ses layout, nous devons définir 2 valeurs: `$title` et `$content`,\
`$title` est défini de manière classique (`$title = "exemple titre";`), tandis que\
`$content` est défini par l'appel de 2 fonctions, `ob_start()` et `ob_get_clean()`.\
Ces 2 fonctions permettent de créer un buffer (mémoire tempon) ou le contenu que l'on souhaite afficher sur la page sera inséré dans ce buffer. lorsque qu'on appelle la fonction `ob_get_clean()`, on arrête ce buffer et on assigne son contenu à la variable `$content` de cette manière `$content = ob_get_clean()`.\



# Images
Base de Donnée\
![Database](./markdown/images/database.png "Database Visualizer")