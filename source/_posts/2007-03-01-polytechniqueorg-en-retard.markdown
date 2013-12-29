---
layout: post
title: "Polytechnique.org en retard ?"
date: 2007-03-01 16:28:00
comments: true
categories: Polytechnique.org
---
[Polytechnique.org](https://www.polytechnique.org) est, comme l'indique le nom du logiciel OpenSource sur lequel le site repose ([plat/al](http://opensource.polytechnique.org/platal/)), une plateforme pour les anciens élèves de l'[École](http://www.polytechnique.fr)... c'est à dire que Polytechnique.org devrait :

*   apporter de la vie dans la communauté
*   faciliter les mises en relation entre les membres
*   offrir des services à la communauté

Polytechnique.org offre ces trois services... mais pourtant moins d'un utilisateur sur 5 se connecte régulièrement au site (une fois par semaine - et le plus souvent c'est pour accéder à des sites qui utilisent l'authentification de Polytechnique.org), environ un sur 3 se connecte une fois par mois, et un sur 3 ne se connecte tout bonnement jamais. Face à cela, on dénombre un grand nombre d'utilisateurs qui sont intéressés par un groupe Polytechnique sur des outils comme Linked-In... pourquoi ? parce que le rôle de Polytechnique.org n'est rempli qu'à moitié et ce pour plusieurs raisons :

<!-- more -->

Ergonomie
=========

La catastrophe sur Polytechnique.org c'est l'ergonomie... ce n'est certes pas la fin du monde (on peut encore s'y retrouver), mais le site ne donne pas envie de s'y arrêter. Je vais essayer de détailler les divers problèmes :

Le menu
-------

![Menu du site]({{ site.url }}/assets/screenshots/menu.jpg)

Le menu de Polytechnique.org témoigne de la richesse du site : il est long. Comme tout menu, il a fallu choisir l'ordre des entrées. Le choix de cet ordonnancement laisse clairement à désirer. Par exemple : où chercher un lien vers l'annuaire ? Dans _Services_ ou _Communauté X_ ? Etant un utilisateur du site depuis longtemps, je mets toujours autant de temps avant de trouver ce que je cherche dans le menu.

Peut-être devrions-nous revoir tout bonnement l'organisation du menu, par exemple avec des catégories plus parlantes, par exemple un premier jet pourrait donner (mais ça mérite d'être travaillé) :

*   Mes emails
    *   Mes redirections
    *   Mes abonnements
    *   Antispam
*   Annuaire
    *   Rechercher
    *   Mon profil
*   Communauté
    *   Annonces
    *   Lettre mensuelle
    *   Discussions
*   Emploi

Deux sites séparés
--------------------

Polytechnique.org c'est aussi Polytechnique.net : le site des groupes. Le fait d'avoir 2 sites totalement séparés pose un grand nombre de problème, depuis le partage de l'authentification, jusqu'à la cohérence des données.

Il faut clairement envisager de fusionner les deux sites dans un avenir plus ou moins proche si on veut que les utilisateurs s'y retrouvent. De la même manière qu'on peut réorganiser le menu, on peut réorganiser l'accès général au site... Par exemple, on pourrait envisager de créer un menu tabulé : dans chaque tab, on retrouve un menu spécifique. Par exemple, on pourrait, au lieu d'avoir un menu à deux niveaux, des tabs (Mes emails, Annuaire, Annonces...) et dans chaque tabs un menu spécifique donnant accès aux fonctionnalités correspondantes.

__Avantages :__ On aurait ainsi un proprement rangé... le site étant modulaire, chaque module pourrait lui même générer ses entrées dans les menus/tabs, l'intégration des Polytechnique.net à Polytechnique.org ne serait que l'ajout d'un tab "Mes groupes X"  
__Défauts :__ On a pas d'accès facile à l'ensemble des fonctionnalités... c'est le seul défaut (pas des moindres) que je vois.

C'est une solution qui mérite qu'on s'y intéresse... le choix de la meilleur ergonomie possible nécessitera de toute façon des tests.

Dinosaure
---------

__Toutes__ les skins de Polytechnique.org sont carrées... En particulier la skin par défaut a très mal vieilli. Les polices y sont énormes, toutes les informations sont dans des boîtes carrées. Le site à sérieusement besoin d'une petite cure de jouvence pour retrouver un design aguichant.


[Web 2.0](http://en.wikipedia.org/wiki/Web_2.0)
===============================================

Dire que son site est Web 2.0 est à la mode... Polytechnique.org ne l'est pas vraiment, et ceci pour diverses raisons.

La pointe de la technologie
---------------------------

Tout d'abord le site est trop statique. Quand la page d'accueil est chargée, elle est chargée... à la rigueur on peut fermer quelques boites mais c'est tout. Le site est statique... Le Web actuel s'ouvre vers des pages vivantes avec lesquels l'utilisateur peut interagir... Le plus souvent cela repose sur des technologies du type Ajax. Polytechnique.org utilise un peu Ajax, mais on va dire que c'est _artisanal_... il y a un gros boulot à faire de ce côté si on veut que le site devienne un outil convivial pour l'utilisateur.

Une autre technologie qui fait le succès du web est l'édition dynamique de contenu. Polytechnique.org utilise du wiki pour l'édition de sa documentation... mais, en grande majorité, ceci se limite à la simplification du travail d'administration du site. Par contre pour tous les contenus dynamiques, nous n'offrons que du texte brut à nos utilisateurs. Là où se trouve le vrai problème, c'est sur l'outil d'hébergement que nous offrons aux groupes : [Diogenes](http://opensource.polytechnique.org/diogenes).

Diogenes a mal vieilli parce que sont apparus face à lui des outils plus simples à utiliser. Aujourd'hui Diogenes mérite un gros décrassage si on veut pouvoir continuer à l'utiliser.

Vie sociale
-----------

Là où Polytechnique.org est le plus en retard, c'est sur les outils qui permettent de donner vie à la communauté. Certes nous avons nos forums, mais ils ne sont pas vraiment mis en avant sur le site... Nous n'avons en effet nulle part des liens qui permettent aux utilisateurs de commenter l'actualité de la communauté. Pour améliorer ça, nous pourrions par exemple associer à chaque annonce une discussion sur un forum dédié (discussion qui serait initiée par la publication de l'annonce en question sur le forum en plus de la page d'accueil).

Conclusion
==========

Voilà, tout ceci un peu en vrac pour dire que nous avons des outils, de très bons outils même, largement en avance sur l'utilisation qu'en font les utilisateurs. Malheureusement nous souffrons d'un manque d'adaptation à l'utilisateur : l'utilisateur recherche avant tout un site facile à utiliser sur lequel il puisse faire ce qu'il cherche le plus rapidement possible.

Il est nécessaire de réorienter fortement le développement vers l'ergonomie dans les mois qui viennent : à terme il faudrait que les utilisateurs puissent utiliser Polytechnique.org comme une page d'accueil conviviale offrant des services tels qu'on en trouve couramment sur les principaux portails (recherche Internet, outils de gestion [PIM](http://en.wikipedia.org/wiki/Personal_information_manager)...) tout en offrant les services de base d'une plateforme pour alumnis.
