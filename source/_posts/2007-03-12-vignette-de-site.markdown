---
layout: post
title: "Vignette de site"
date: 2007-03-12 10:40:00
comments: true
categories: Polytechnique.org
---
Pour continuer sur le sujet de [mon billet précédent](/post/2007/03/10/A-croire-que-certains-le-font-expres), voici une solution simple pour réaliser des vignettes de site web.

<!-- more -->

Technique
---------

Pour faire une vignette d'un site web la solution est simple :
1   on lance un navigateur (c'est à dire un moteur de rendu) et on ouvre la page dont on eut le screenshot
1   on fait un screenshot, c'est à dire qu'on copie la fenêtre d'affichage dans une image
1   on fait du post-traitement sur l'image obtenue pour l'adapter à nos besoins

Donc, rien de bien compliqué... le problème est maintenant le faire de manière automatisée...

Il existe divers outils pour le faire... la plupart des sites qui fournissent des services de screenshot sur Internet font leur captures avec Internet Explorer (IE6 la plupart de temps), Exalead le fait avec un moteur KHTML, il existe des outils libres qui le font avec [Gecko](http://www.hackdiary.com/archives/000055.html), avec [WebKit](http://www.paulhammond.org/webkit2png/) ou [KHTML](http://khtml2png.sourceforge.net/).

Personnellement [j'ai travaillé avec khtml2png](/post/2007/03/10/A-croire-que-certains-le-font-expres).

Mise en place
-------------

Que ce soit la solution qui utilise Gecko, ou celle qui utilise KHTML, il est nécessaire de faire fonctionner le programme dans un serveur X, car il faut réaliser le rendu dans une fenêtre avant d'en faire une capture. Quand on a un serveur X déjà en place, ça ne pose pas de problème... mais quand on veut faire tourner ça sur un serveur, ça devient plus casse-pied. La solution la plus courante est d'utiliser un `vncserver`.

Le lancement du `vncserver` nécessite de respecter quelques contraintes :

*   il faut que le rendu se fasse en couleur vraies... `VNC` offre deux modes : 8bits et 32bits, il faut donc choisir 32bits (qui n'est pas le mode par défaut)
*   plus la résolution du serveur X est grande, plus on pourra faire des captures de grande taille (ou faire moins de capture si on fait du pavage). De plus, `khtml2png` contient un bug qui entraîne des bandes grises dans la capture si celle-ci est trop grande (par exemple, avec un serveur de 1280x960, je ne peux pas faire de capture 1024x768). C'est un problème sur lequel je vais travailler durant la semaine.$$Edit 12 mars à 23h40 : Ce problème est corrigé dans [la toute dernière version](/public/khtml2png/khtml2png-fru-last.tar.bz2). On peut de fait envisager d'utiliser un serveur X plus petit sans bug, le rendu reste plus rapide lorsque le serveur X est suffisamment grand.$$

Ainsi, j'utilise la commande suivante pour lancer mon `vncserver` :

{% highlight bash %}
vncserver :60 -geometry 1280x960 -depth 32
{% endhighlight %}

Ensuite, il suffit de faire tourner notre outils dans l'environnement graphique qu'on vient d'obtenir. Avec khtml2png, cela donne :

{% highlight bash %}
./khtml2png2 --display :60 --nocrashhandler --width 840 --height 600 http://fruneau.rznc.net/ /tmp/test-fru.png
{% endhighlight %}

Les différentes options données sont :

*   `display` est une option Qt qui spécifie le serveur X sur lequel il faut faire tourner le programme. En mettant DISPLAY=":60.0" comme variable d'environnement, on devrait (je n'ai pas testé) obtenir le même résultat
*   `nocrashhandler` est une option KDE qui indique qu'on ne veut pas que KDE lance le KDE-Crash Handler en cas de plantage... celui-ci irait encombrer le serveur graphique. Etant donné qu'on a pas accès à ce serveur graphique, autant éviter de le remplir de fenêtre inutiles
*   `width` et `height` donne la taille du screenshot
*   ensuite on donne l'URL et le nom du fichier (le format est deviné à partir de l'extension)

La ligne précédente donne le résultat suivant :

[((/public/khtml2png/.test-fru_m.jpg](Test de screenshots avec KHTML2PNG|C))|/public/khtml2png/test-fru.png)

Attention tout de même, la version 2.5.0 (et sans doute les précédentes) de `khtml2png` est bloquante dans le cas où elle rencontre un type mime non supporté par l'installation actuelle de KDE. Il vaut mieux utiliser [ma dernière version](/public/khtml2png/khtml2png-fru-last.tar.bz2) qui corrige ce bug. Dans tous les cas, en installant les modules adaptés (comme flash), on obtient un meilleur rendu.
