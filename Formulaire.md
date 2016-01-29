# Personnalisation du formulaire #

Un formulaire d'inscription et de désinscription par défaut est disponible en pointant sur le script OVH Mailing-list (soit le paramètre enregistré pour $config['url']).

Pour configurer un autre formulaire :

  * Action : adresse du script + index.php
  * Méthode : GET ou POST (as you want)
  * Nom de la variable de l'email : email
  * Nom et valeurs de la variable de la commande : action (subscribe ou unsubscribe)