---
layout: post
title: "Afficher des discussions"
date: 2007-11-01 17:24:00
comments: true
categories: Polytechnique.org
---
Lorsqu'un logiciel a pour vocation d'afficher des discussions, on attend de sa part qu'il nous permette de voir simplement qui répond à qui, dans quel contexte... Ce n'est pas toujours ce qui est le mieux fait. Par exemple, les programmes de fora en ligne à la mode (phpBB par exemple) affiche les discussion comme une succession de rectangles juxtaposés et seul le contenu du message permet de voir qu'il en cite un autre. D'autres logiciels comme Mail.app ont ce défaut et parfois la fâcheuse manie de ne pas vouloir corriger ce problème.

L'affichage de l'arborescence dans [Banana](http://opensource.polytechnique.org/banana) est une des fonctionnalités clés... et elle va beaucoup changer dans la prochaine version.

<!-- more -->

 Une ligne par entrée
======================

La solution habituelle pour afficher l'arborescence est d'utiliser un message par ligne de telle, des + ou - pour ouvrir ou fermer les noeuds. C'est la solution actuelle de banana.

((/public/screenshots/old-thread.png|Banana Thread up to 1.7))

Avec cette solution, on perd très rapidement en lisibilité : dès que la discussion dépasse une vingtaine de messages, l'arborescence devient très haute et plus ça va, plus le titre dérive vers la droite rendant parfois le lien inaccessible. Lorsqu'il y a un troll, il est de fait très courant que certains nouveaux messages se trouvent perdus plusieurs pages en arrière dans l'arborescence, ou que sur certains navigateurs, il soit difficile d'y accéder. De plus l'interface se trouve souvent surchargée, à la limite de la lisibilité : c'est dur de faire tenir un maximum d'informations en un minimum de place en gardant la lisibilité de l'ensemble.


 Une solution plus visuelle
===========================

Je ne sais pas combien de personnes connaissent [MacSoup](http://home.snafu.de/stk/macsoup/). Il s'agit d'un petit client NNTP pour MacOS, qui en soit n'a pas beaucoup d'intérêt (il est payant et est relativement limité). Le principal atout de MacSoup est son interface de visualisation des threads (les utilisateurs diront qu'il y a bien plus que l'interface graphique, mais également l'interface clavier etc...). On trouve sur internet quelques captures d'écran en cherchant dans les [moteurs de recherche d'image](http://www.exalead.com/image/results?q=macsoup%20screenshot) :

((http://www.fen-net.de/~xx511/bilder/macsoup/Thread.gif))

Cette interface est compacte, visuelle et permet d'accéder rapidement à n'importe quel message du thread. Pour la prochaine version de Banana, je me suis fortement inspiré de cette interface pour réécrire de 0 l'affichage de l'arborescence. Ceci donne :

((/public/screenshots/new-thread.png|Thread view in Banana))

Il s'agit de la même discussion que précédemment. On voit donc ici facilement l'arborescence. Lorsqu'un message est non lu, la branche à laquelle il est attaché est noire au lieu de grise ce qui permet de l'identifier du premier coup d'oeil. Les couleurs de fond des noeuds (une idée de [Falco](http://www.falco.bz)) sont obtenue à partir d'un hash quelconque sur l'émetteur et permettent donc d'identifier les messages envoyés par la même personne. Lorsqu'on laisse la souris sur un noeud, le nom de l'expéditeur et l'heure du post s'affichent (malheureusement le pointeur de la souris n'apparaît pas sur la capture d'écran)... et bien sûr quand on clique sur un noeud, on va sur le message correspondant.

Lorsqu'on est sur un message, on garde également la vue du thread ce qui permet de toujours savoir où on est dans la discussion :

((/public/screenshots/new-thread-nav.png|Thread view with selected message))

Il y a encore un peu de travail à faire pour améliorer les performances de la génération des arbres et pour augmenter sa compacité (éviter les branches qui descendent très bas alors qu'elles auraient pu trouver leur place dans le l'espace vide disponible).
