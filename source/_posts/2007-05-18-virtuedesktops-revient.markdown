---
layout: post
title: "VirtueDesktops revient..."
date: 2007-05-18 12:25:00
comments: true
categories: GeekTime
tags: [Devel, Mac, VirtueDesktops]
---
Voici plus ou moins trois mois que j'ai découvert [synergy](http://synergy2.sf.net), c'est vraiment très agréable de pouvoir contrôler les deux ordinateurs sans changer de clavier/souris continuellement. Seul problème, c'est que sur la machine qui héberge le serveur `synergy`, [VirtueDesktops](http://virtuedesktops.info), un _excellent_ gestionnaire de bureaux virtuels pour MacOS, n'arrête pas de crasher. J'avais donc posté un bugreport sur le trac de Virtue... malheureusement pour diverses raisons, Tony Arnold a décidé peu après de stopper le développement de Virtue.

<!-- more -->

J'ai donc pris mon courage à 2 mains, et je me suis doucement plongé dans le code de VirtueDesktops (c'est vraiment bien les applications OpenSource). Il y a maintenant deux semaines, j'ai soumis le patch permettant de corriger le crash dont je souffrais. Depuis, ce patch a été publié ce qui a conduit la [release de la bêta 3 de Virtue 0.54](http://virtuedesktops.info/index.php/2007/05/14/virtuedesktops-054-beta-3/).

Je me suis relancé dans le développement cette semaine en me fixant comme objectif de corriger plusieurs comportements énervants de Virtue. Par exemple, si je dis que `Mail.app` doit être sur le bureau Mail, je veux que __toutes__ les fenêtres de `Mail.app` aillent forcément sur ce bureau. Seul problème, c'est que lorsque je reçois une notification de l'arrivée d'un nouveau mail et que je clique sur cette notification, une fenêtre avec le message s'ouvre sur le bureau courant, et Virtue tourne pour atteindre le bureau de Mail.app... résultat : j'ai changé de bureau mais je n'ai pas accès à la fenêtre que je voulais voir. J'ai corrigé ce comportement, désormais, la fenêtre est automatiquement déplacée sur le bureau de `Mail.app` lors de son ouverture.

Autres corrections diverses :

*   lorsqu'on ferme une application Virtue reste sur le bureau courant (sauf si il n'y a plus rien sur le bureau courant)
*   lorsqu'on choisi de changer de bureau, Virtue active désormais la dernière application active connue pour le nouveau bureau
*   lorsqu'on restaure une fenêtre qui était dans le Dock, soit l'application a le droit d'être sur le bureau courant et la fenêtre est affichée sur ce bureau sans chercher à changer de bureau, soit l'application est attribuée à un bureau et dans ce cas la fenêtre est restaurée sur le bureau de l'application et Virtue change de bureau
*   ...

Ces changements ont abouti à la [bêta 4](http://virtuedesktops.info/index.php/2007/05/18/virtuedesktops-054-beta-4/) et se résument à :

*   Changing to desktops when you launch, activate, deactivate, minimise, restore or quit an application should be much more consistent;
*   Window ordering when changing desktops should be much more consistent;
