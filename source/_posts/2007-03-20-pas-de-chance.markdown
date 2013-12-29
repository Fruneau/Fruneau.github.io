---
layout: post
title: "Pas de chance"
date: 2007-03-20 18:59:00
comments: true
categories: Général
---
J'en ai marre des Macs qui plantent à longueur de temps... Pourquoi est-ce que toutes les autres personnes qui ont des machines Apple s'en sortent sans problème alors que moi j'enchaîne les crashs inexplicables, bug matériels, etc...

<!-- more -->

Déjà mon PowerMac... impossible d'utiliser les fonctionnalités réseau avancées, ça fait freezer la machine. Pourtant je l'ai pris exprès avec 2 cartes réseau __justement__ pour pouvoir m'en servir comme routeur. Finalement, il me fait serveur (IMAP, WEB, FTP, AFP, IRC...), mais depuis le passage à Mac OS 10.4.9$$__Edit du 5 avril 2007 :__ en fait après quelques investigations, il est apparu que c'est `stunnel` que j'utilisais assez abondamment qui générait un zombie à chaque connexion. Malheureusement, comme je ne pouvais pas lancer `ps` pour voir la liste des process au moment où la machine avait planté, je ne pouvais pas voir les zombies (le moniteur d'activité de Mac OS ne les fait pas apparaître). J'ai réduit mon usage de `stunnel` au strict nécessaire et j'ai mis un cron qui relance le service toutes les heures et bizarrement, je n'ai plus de problèmes$$, j'ai régulièrement des messages `fork failed: resource temporarily unavailable` et un flood de `proc: table is full` dans les logs. Au lieu d'avoir une machine stable comme elle l'était avant cette mise à jour, je me retrouve avec un serveur qu'il me faut rebooter une fois par jour.

A chaque reboot, je modifie plus d'options en espérant que _cette_ fois, ce sera la bonne :

*   mettre `launchctl limit maxproc 512 2048`
*   mettre `ulimit -a 512` (au lancement des scripts d'init et de mon shell)
*   mettre `kern.maxproc=2048` et `kern.maxprocperuid=512` dans mon /etc/sysctl.conf

De son côté mon portable (un MacBook Pro rev 1.1 - Core Duo) a la carte graphique grillée... super ! j'ai des [points partout dans l'affichage](/mind/public/screenshots/bugs.png). Pourtant je ne me suis pas amusé à l'overclockée... c'est juste que les ventilateurs semblent oublier de tourner quand la machine chauffe. Pour parachever le tout, depuis quelques jours il ne fonctionne plus correctement sur batterie... à 60% ou 80% de batterie (selon les fois), il s'éteint brutalement... "Ghostage" de batterie paraît-il.

Tout ça m'énerve... c'est dommage, parce que j'aime bien Mac OS, c'est un système confortable à utiliser (même si certaines fonctionnalités de KDE me manquent).
