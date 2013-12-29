---
layout: post
title: "Un client RSS pour lire les Forums NNTP et les Mailing-Lists..."
date: 2007-02-25 19:05:42 +0100
comments: true
categories: Polytechnique.org
tags: [Banana, Devel, Mailing-Lists, NNTP, PHP, RSS]
---
Si il y a un bout de code dont je suis content au sein de [Polytechnique.org](https://www.polytechnique.org), c'est [Banana](http://opensource.polytechnique.org/banana/). Banana est à l'origine un client Web pour le protocole NNTP (c'est à dire le protocole utilisé par Usenet). Depuis sa dernière version (la 1.5), Banana est capable d'utiliser n'importe quel protocole comme source (les protocoles actuellement implémentés étant le NNTP et la lecture de MBox, mais on pourrait envisager d'ajouter le support des Maildir ou d'IMAP sans problème), ainsi, à Polytechnique.org, nous utilisons Banana pour offrir une plateforme Web vers [nos forums](https://www.polytechnique.org/banana) et pour mettre en ligne les archives des Mailing-Lists que nous hébergeons.

Banana est à mon avis un bon outils : il permet un rendu plus que correct de la plupart des mails HTML (même ceux dont le formatage est défini dans une feuille de style), il affiche les discussions proprement et rapidement (grâce à un système de cache du côté serveur). C'est donc un gros plus en comparaison des interfaces habituelles de consultation d'archives de Mailing-List qu'on peut trouver sur Internet... et puis, ce n'est pas un forum php, pas de fioritures à la phpbb : Banana est clair, lisible et rapide... et facile à installer. Pour s'en convaincre, il suffit de regarder la [fichier d'exemple](http://opensource.polytechnique.org/viewsvn/filedetails.php?repname=Banana&path=/trunk/examples/index.php&rev=0&sc=1) qui fournit un banana totalement fonctionnel (à chacun ensuite d'y ajouter sa couche d'authentification si nécessaire).

La grande nouveauté de la prochaine version de Banana est l'intégration de flux RSS... ainsi il sera possible de lire les forums sans se connecter au site et sans client news, ou de suivre les discussions des mailing-lists sans relever son courrier (le premier des 2 cas étant certainement le plus intéressant). Pour les utilisateurs de Polytechnique.org, la fonctionnalité est d'ores et déjà disponible sur [ma version de développement](http://dev.m4x.org/~x2003bruneau/banana). Deux types de flux son disponibles : soit un flux par groupe, soit un flux regroupant tous les groupes auxquels on est abonné.
