---
layout: post
title: "Khtml2png 2.6.5 est sorti"
date: 2007-06-05 22:31:00
comments: true
categories: GeekTime
tags: [Devel, khtml2png]
---
Après 3 mois d'un travail pas très intensif, une nouvelle version de [khtml2png](http://khtml2png.sf.net) est disponible. Cette version est partie du fait que les versions précédentes du programme ont parfois du mal à gérer les grandes captures d'écran (en fait, des bugs peuvent apparaître dès que la taille de la zone à capturer est plus grande que la taille affichable).

Donc, rapidement après la sortie de la 2.6.0, j'avais envoyé un correctif (en fait une réécriture du moteur de rendu) au développeur de `khtml2png`. Malheureusement, ce correctif ne fonctionnait pas correctement chez lui. Donc, pendant 3 mois, j'ai fait du débuggage à distance : j'envoie une version modifiée (1 ou 2 lignes à chaque fois), j'attends 2 ou 3 semaine une réponse, etc... Du développement efficace !

Bon, toujours est-il que maintenant, la version 2.6.5 fonctionne à la fois chez moi (à la fois Debian + xvnc et sur MacOS X), et chez Hauke (sur Debian également, mais avec des réglages différents).

Le Changelog annonce :


>  fix: Now produces screenshots on my Debian Etch system under KDE 3.5.5 without glitches. %%%
>  fix: Maybe better working on other systems too. Please test.

J'aimerais y rajouter quelques points :

*   meilleur moteur de rendu (qui ne scroll plus, mais déplace la fenêtre pour s'affranchir de certains bugs de KDE et/ou Qt)
*   meilleure détection de la taille de la capture à réaliser
*   possibilité de choisir le comportement de `khtml2png` face aux redirections, au javascript, au java, au flash... via la ligne de commande
*   la détection automatique de la dimension par un `id` devient compatible avec le format utilisé dans les version <= 2.5

Comme d'habitude, vous pouvez télécharger [ma dernière version du programme](/mind/public/khtml2png/khtml2png-fru-last.tar.bz2).
