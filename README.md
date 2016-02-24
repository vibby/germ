# germ
Gestion d’église : ressources et membres

##C’est quoi ?

Germ est un système de gestion d’église. Il est pour le moment à l’état d’ébauche.

Le périmètre fonctionnel devrait couvrir la liste des membres, le carnet d’adresse, le planning, l’envoi d’emails automatisé, partage de données… Le projet évolue par itérations selon la méthode Agile.

##Prérequis

- Installer et configurer le SGBD PostGreSql. Un compte et une base de données associée doivent être créés
- Installer et configurer un serveur web (Apache ou nginx)
- Installer et configurer Git, avec un compte Github

##Installer Germ

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

Configurer le système de migrations de bdd (nom de la base de données, d’utilisateur, et le mot de passe)
```
cp ./phinx.yml.dist ./phinx.yml
vim ./phinx.yml
```

Éxécuter les migrations (remplissage de la base de données, structure et données)
```
php vendor/robmorgan/phinx/bin/phinx migrate
```

Configurer le serveur web pour pointer sur le dossier ``germ/web``

Vous pouvez maintenant vous rendre sur l’application dans un navigateur web, selon le domaine défini dans votre configuration de serveur web.

## Faire évoluer Germ

### Fonctionement

Pour proposer des modifications, forker le projet (bouton «Fork» en faut à droite). Puis installer votre version avec la commande ``git clone git@github.com:vibby/germ.git``. Finaliser l’installation avec la méthode ci-dessus. Vous pouvez ainsi développer, faire des comits, pousser sur Github. Et quand la modification est «mûre», proposez-la par la méthode de la «pull request». Les utilisateurs vous en seront reconaissants !

### Modification du modèle de données

Les modifications sur les données et leur structure doivent être faits dans des migrations
```
php vendor/robmorgan/phinx/bin/phinx create DescriptionDeLEvolution
```

Éditer ensuite le fichier ``./app/migration/xxxxxxxxx_DescriptionDeLEvolution.php`` pour ajouter les commandes SQL désirée.

Jouer ensuite la migration avec la commande
```
php vendor/robmorgan/phinx/bin/phinx migrate
```

Il faut ensuite mettre à jour le modèle coté PHP, grâce à POMM
```
bin/console pomm:generate:schema-all -d src/GermBundle/Model/ -a 'GermBundle\Model' --psr4 germ public
```

Vos entités sont maintenant prêtes a être exploitée, en tirant parti de toutes les fonctions de PostGreSql : tableaux, json, etc.

