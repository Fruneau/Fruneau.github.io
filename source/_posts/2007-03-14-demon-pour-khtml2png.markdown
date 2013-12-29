---
layout: post
title: "Démon pour khtml2png"
date: 2007-03-14 18:24:00
comments: true
categories: Polytechnique.org
tags: [C, Devel, khtml2png, khtmld]
---
Une fois qu'on a un outils pour faire les vignettes de site, il devient utile de pouvoir automatiser le travail. Pour ceci, il existe un [démon pour khtml2png](http://wiki.goatpron.de/project/khtmld). Malheureusement (encore une fois), cet outil ne correspondait pas exactement à mes besoins. Donc je l'ai partiellement réécrit (mais rien d'extraordinaire).

<!-- more -->

Ce démon qui s'appelle `khtmld` fonctionne simplement : il lit une file et à chaque entrée dans cette file, il lance `khtml2png`. Donc, il y a deux étapes.

La première consiste à lancer le démon. Pour ceci, j'ai développé un script d'init... qui sera donc probablement lancé par l'utilisateur `root`. Il suffit de modifier ce script pour régler les variables en fonction de son installation, puis dans lancer la commande :

{% highlight bash %}
./init-script.sh start
{% endhighlight %}

Ensuite il n'y a plus qu'à donner au démon la liste des actions à réaliser sous la forme$$Attention à ne pas mettre d'espace dans le nom du fichier et dans l'url.$$ :

{% highlight bash %}
echo "url fichier" >> /tmp/khtmld.spool
{% endhighlight %}

Le principal développement que j'ai réalisé sur `khtmld` permet de donner au processus de `khtml2png` les droits d'un utilisateur du choix de la personne qui a lancé le démon (certes rien d'extraordinaire, mais ça manquait au programme).

Voilà, donc un système pour automatiser la réalisation de vignettes même si il reste quelques points sur lesquels il faut réfléchir :

*   on ne veut pas un service ouvert (faire un screenshot est une opération lourde)
*   on ne peut pas mettre ce service sur un serveur de prod (il est hors de question de lancer un X en production)

Donc il faut s'orienter sur la réalisation d'un serveur de screenshot avec authentification et modération... non-trivial.

Pour ceux qui voudraient récupérer ma version, elle est disponible dans [les téléchargements](/mind/public/khtml2png/khtmld-fru-last.tar.bz2).
