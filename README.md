# germ
Gestion d’église : ressources et membres

##C’est quoi ?

Germ est un système de gestion d’église. Il est pour le moment à l’état d’ébauche.

Le périmètre fonctionnel devrait couvrir la liste des membres, le carnet d’adresse, le planning, l’envoi d’emails automatisé, partage de données… Le projet évolue par itérations selon la méthode Agile.

##Installer Germ

###Prérequis

- Installer et configurer Git, avec un compte Github
- Installer et configurer le SGBD PostGreSql. Un compte et une base de données associée doivent être créés
- Installer et configurer un serveur web (Apache ou nginx)

###Installation

Dans un terminal, exécuter

```
git clone git@github.com:vibby/germ.git
cd germ
```

Installer les dépendances avec composer
```
composer install
```
À l’issue de cette commande, les paramètres requis seront demandés par prompt

Créer la structure de la base de données
```
vendor/bin/phinx migrate
```

Pour le dévelopement, vous pouvez insérez des données de test, notament le login / mot de passe : «jojo» / «test»
```
vendor/bin/phinx migrate -c phinx-dev.yml
```

Configurer votre serveur web (nginx ou apache) pour pointer sur le dossier web.
Vous pouvez maintenant vous rendre sur l’application dans un navigateur web.

Acunne donnée n’est installé, à faire à la main dans la base de donnée pour le moment.

##Installer Germ pour docker

###Prérequis

- Installer et configurer Git, avec un compte Github
- Installer et configurer docker

###Installation


```
git clone git@github.com:vibby/germ.git
cd germ/docker
docker-compose up
```

Installer les dépendances avec composer
```
docker-compose exec germ-php-fpm composer install
```
À l’issue de cette commande, les paramètres requis seront demandés par prompt

Créer la structure de la base de données
```
docker-compose exec germ-php-fpm vendor/bin/phinx migrate
```

Pour le dévelopement, vous pouvez insérez des données de test
```
docker-compose exec germ-php-fpm vendor/bin/phinx migrate -c phinx-dev.yml
```

Installer les assets
```
docker-compose exec germ-php-fpm bin/console assets:install --symlink
```

Vous pouvez maintenant vous rendre sur l’application dans un navigateur web sur le port 8080 de votre machine locale : http://localhost:8080.

## Faire évoluer Germ

### Fonctionement

Pour proposer des modifications, forker le projet (bouton «Fork» en faut à droite). Puis installer votre version avec la commande ``git clone git@github.com:vibby/germ.git``. Finaliser l’installation avec la méthode ci-dessus. Vous pouvez ainsi développer, faire des comits, pousser sur Github. Et quand la modification est «mûre», proposez-la par la méthode de la «pull request». Les utilisateurs vous en seront reconaissants !

### Modification du modèle de données

Les modifications sur les données et leur structure doivent être faits dans des migrations
```
bin/console do:mi:cr
```

