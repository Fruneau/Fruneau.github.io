---
layout: post
title: "Encodage et terminal"
date: 2008-03-02 11:57:00
comments: true
categories: Général
tags: [encodage, shell, terminal, zsh]
---
Beaucoup de personnes avec qui je discute sur IRC ont des problèmes avec l'encodage de leur terminal, de leur shell, de leur irssi, ou de tout autre logiciel en "ligne de commande". Comme j'en ai un peu marre d'expliquer la même chose toutes les semaines, voici une petite mise au point sur les réglages à faire pour travailler efficacement en ligne de commande.

<!-- more -->

Introduction
------------

Le point le plus important à mémoriser est que pour avoir une console avec un encodage spécifique il faut que plusieurs couches de logiciels utilisent le même encodage. Prenons le cas simple où nous avons un shell dans un terminal.

Le terminal est un logiciel qui ne fait que convertir les entrées de l'utilisateur vers des entrées compréhensibles par le logiciel qu'il fait tourner, et qui interprète la sortie de ce logiciel pour qu'elle soit lisible par l'utilisateur. Donc pour que le terminal affiche correctement ce qu'indique le shell, il faut qu'il utilise le même encodage que celui-ci. Si par exemple mon shell écrit de l'UTF-8 alors que mon terminal attend du latin1, les caractères multi-octets de l'UTF-8 seront mal interprétés et afficheront un caractère par octet. Ainsi un "é" en utf8 s'affiche comme un "Ã©" en latin1.

Pour l'entrée de l'utilisateur, c'est la même chose. Si mon terminal est en latin1 et mon shell en utf8, si j'entre un "é" dans mon terminal, celui-ci sera passé à mon terminal comme un seul octet (de valeur 0xE9). Or mon shell attend de l'utf8 et lorsqu'il reçoit un 0xE9, il considère que je viens d'entrer un octet d'un caractère multi-octets... les prochains caractères que j'entrerais seront donc ajoutés à mon 0xE9 jusqu'à ce que mon shell considère que j'ai entré un caractère... assez embêtant.

Il faut donc faire extrêmement attention à choisir un encodage unique compatible avec son shell et son terminal (et les logiciels qu'on compte utiliser).


Terminal
--------

Le réglage du terminal dépend exclusivement du logiciel en question. Certains terminaux permettent de choisir son encodage, d'autres non. Cela peut donc être un bon critère pour choisir son logiciel. Pour utiliser l'UTF-8, vous pouvez prendre Konsole, urxvt, le Terminal de Mac OS X...


Shell
-----

Le réglage du shell se fait par des variables d'environnement qu'on appelle couramment les "locales". Le réglage courant est accessible en tapant "locale" dans le shell. Un certain nombre de variables sont concernées :

*   gestion de la langue du shell (et des programmes liés)
*   gestion des formats (dates, monnaies, virgules...)
*   LC_ALL est un "fallback" (l'encodage par défaut si aucun autre n'a été défini pour une variable donnée).

Chaque variable est de la forme xx_YY.ZZ. xx_YY défini la zone géographique et la langue (fr_FR pour le français, en_US pour l'anglais-US). ZZ défini l'encodage. Il est important que toutes les variables utilisent le même encodage (mais elles peuvent avoir des langues différentes). Pour faire de l'UTF-8 pur, un bon choix est d'exporter LC_ALL="en_US.UTF-8" dans la configuration du shell (ou fr_FR.UTF-8 pour ceux qui veulent un shell en français).

Il faut par contre faire attention, l'UTF-8 n'est pas supporté par tous les shells (à partir de 4.3 pour zsh par exemple).


Irssi
-----

Pour irssi, il y a un point supplémentaire à prendre en compte : l'encodage du réseau. Ainsi si j'ai un channel en latin1 à afficher en utf8 (et sur lequel je veux poster). Il faut donc que irssi fasse de transcodage à la volée... et bien sûr il faut lui dire ce qu'il faut faire.

Tout d'abord il faut lui indiquer l'encodage du terminal. Pour ceci il y a la variable term_charset :

    settings = {
      "fe-common/core" = {
        term_charset = "UTF-8";
      };
    };

Ensuite, il faut indiquer pour chaque réseau (ou channel) l'encodage du réseau. Ce n'est en fait nécessaire que si l'encodage est différent de celui du terminal, mais ça ne coûte rien de le spécifier pour chaque réseau. Ainsi je suis sur RezoSup (qui est en latin1) et sur FreeNode (qui est en utf8), il me suffit d'ajouter :

    settings = {
      core = {
        recode = "yes";
      };
    };
    conversions = { 
      FreeNode = "UTF-8";
      Rezosup = "ISO-8859-1";
    };

Les entrées "FreeNode" et "Rezosup" doivent reprendre le nom du chatnet donné dans la section "servers" de la configuration. Pour spécifier l'encodage spécifique à un channel, il suffit de mettre `"Chatnet/#channel" = "encoding";`
