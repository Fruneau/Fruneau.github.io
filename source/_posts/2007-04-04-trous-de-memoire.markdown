---
layout: post
title: "Trous de mémoire"
date: 2007-04-04 00:01:00
comments: true
categories: GeekTime
tags: [Mac]
---
Quand j'utilise mon ordinateur, j'aime bien que son fonctionnement soit fluide. C'est à dire que lorsque je passe d'une application à l'autre (ce qui arrive régulièrement), je ne veux pas qu'il y ait 10 secondes de latence parce que ma machine "swap". Malheureusement, je ne connais aucun OS sur lequel ça ne swap pas. Par exemple, sur mon portable à l'instant même j'ai 2Go de swap (donc 700Mo utilisés) sachant qu'à côté j'ai 600Mo de RAM utilisés (et les quelques 400Mo de RAM qui restent servent de cache disque...).

Comment un ordinateur sur lequel tourne un navigateur web, un client IRC et un client mail peut avoir autant de RAM occupée ???

<!-- more -->

La réponse n'est pas très dure à trouver... il suffit de se munir de différents petits outils :

*   un logiciel de monitoring (par exemple le _Moniteur d'activité_ de Mac OS)
*   d'un outil comme le programme `leaks` qui détecte les zone de mémoire perdues (les memory-leaks)

Et là, c'est assez surprenant. Commençons par trier les programmes par mémoire occupée dans le moniteur :

1   Le noyau arrive en premier... bon, je ne sais pas ce qui est compté comme mémoire utilisée par le noyau, on va dire que c'est normal
1   Le serveur graphique en deuxième...
1   Colloquy (client IRC) avec 47Mo de mémoire _réelle_ (284Mo de mémoire virtuelle)
1   Mail avec 39Mo/270Mo
1   Vienna (syndicateur RSS) 34Mo/286Mo
1   Safari (browser) 31Mo/240Mo... mais je viens juste de le relancer et je n'ai que 2 onglets ouverts dessus
1   VirtueDesktop (gestionnaire de bureaux virtuels) 27Mo/247Mo
1   GeekTool (un petit outil qui permet de plaquer des logs/images... sur le bureau) 26Mo/230Mo

Bon, là où ce n'est pas drôle je viens de relancer Safari et GeekTool. A la rigueur, à part GeekTool qui n'a absolument rien à faire dans le top8, le classement n'est pas très surprenant. Là où il y a un problème c'est quand on regarde la sortie de `leaks`. Pour ceux qui ne peuvent pas tester, ça ressemble à ça :


    Leak: 0x0066abd0  size=16       string ''
    Leak: 0x006e1680  size=16       string ''
    Leak: 0x0d5f8cf0  size=16       instance of 'NSCFString'
            0xa080c260 0x0001078c 0x74737504 0x4346006c     `........ustl.FC
    Leak: 0x0d590470  size=16
            0x00000000 0x00000000 0x00000000 0x00010001     ................
    ...

Donc, comme je ne vais pas lire toute la sortie, je vais faire `leaks [programme] | grep Leak | wc -l` sur les programmes qui tournent sur mon Mac ce qui donnera le nombre de trous de mémoire. Tout d'abord commençons par ceux que je viens de relancer (GeekTool et Safari) :

*   GeekTool : 1632 leaks dont la taille varie de 16 à 1024 octets
*   Safari : 6 leaks dont la taille varie également de 16 à 1024 octets

Puisque Colloquy est en tête du top des programmes qui utilisent le plus de mémoire... un petit test s'impose : 15212 leaks (je l'ai relancé il y a 24 heures). Le temps de faire ce test je revérifie GeekTool qui lui même est passé à 1747 leaks (juste pendant le temps que j'ai pris pour écrire ces quelques lignes).

Pour me rassuré je décide de tester sur différents autres programmes :

*   WindowServer (le serveur graphique de Mac OS) : 360 leaks 
*   Address Book : 1460 leaks (bon, il a 11 jours d'uptime aussi, c'est pas rien)
*   Terminal : 0 (mais je viens de le relancer... il a planté alors que je lançais `leaks` sur `Terminal`)
*   Finder : 334
*   Mail : 321
*   Dock : 15
*   GeekTool : 1995 (et oui, ça a encore augmenté)
*   ssh : 6 !
*   Vienna : 10824 (il a 24 heures d'uptime)

Je me décide à lancer Firefox, juste pour voir... juste après le lancement, Firefox est déjà à 1580 memory leaks... bon, peut-être que Camino est plus léger, c'est vrai qu'avec ses 236 leaks, il fait bonne figure.

Pour résumer :

*   Les programmes Apple sont peut troués (la plupart de ceux que j'ai cité ci-dessus ont un uptime comparable à celui de ma machine, c'est à dire 11 jours... 300 trous de mémoire en 11 jours, ce n'est pas miraculeux, mais c'est _tolérable_).
*   Les programmes de tierce partie sont des monstres. Comment un navigateur comme Firefox qui cherche à s'imposer comme une référence peut-il se permettre d'avoir déjà perdu 1580 zones mémoires en 5 secondes de fonctionnement ? Comment un outil qui ne fait rien comme GeekTool peut-il prendre plus de mémoire que Safari (il vient juste de le dépasser dans mon moniteur d'activité) et perdre plusieurs centaines de zones mémoire par minute ?

Quand on développe un programme, la gestion de la mémoire est une priorité... et ce, quel que soit le langage qu'on utilise. Je n'ai pas envie d'acheter 1Go de RAM supplémentaire alors que tous les programmes que j'utilise devrait rentrer dans 512Mo de RAM, mais malheureusement, comme les programmeurs semblent avoir oublié qu'un programme ce n'est pas que un tas de fonctionnalités mises bout à bout, ma machine rame de telle sorte que parfois il faut plusieurs secondes pour passer d'un programme à l'autre.

Je me demandais pourquoi Apple avait mis en place un GC pour l'objective-C dans le prochain MacOS, au moins maintenant je suis fixé : ça permettra aux programmes faits par des personnes qui ne savent pas coder de ne pas pourrir ma machine. Heureusement que les gens qui savent programmer se mettent au service de ceux qui ne le savent pas pour faire des outils qui transforment les mauvais programme en programme correct.

Désolé d'être aussi méchant, mais ça m'énerve (tiens GeekTool prend encore 2Mo de RAM supplémentaires$$Le temps de relire, GeekTool et Safari ont supplanté Vienna au top8, GeekTool est passé à 7135 leaks !!!, Colloquy est passé à 15318 et Safari reste à 6$$$$Après une bonne nuit de sommeil le classement a changé : GeekTool est désormais entre le noyau et le serveur graphique avec plus de 36222 leaks et 106Mo de mémoire utilisés, Vienna est passé à 11400 leaks, Colloquy à 16300 et Safari à 111$$$$Avec 24h d'uptime, Safari et GeekTool tiennent la tête du classement avec respectivement 117 et 114Mo de RAM utilisée. 683 leaks pour Safari et 43479 leaks pour GeekTool. Colloquy est monté à 18110 leaks et Vienna à 14902$$).
