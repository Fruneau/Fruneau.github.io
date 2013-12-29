---
layout: post
title: "La Banane et l'escargot"
date: 2007-05-07 17:44:00
comments: true
categories: Polytechnique.org
---
La release de [Banana](http://opensource.polytechnique.org/banana/) 1.6 en même temps que celle de plat/al 0.9.14 a mis en évidence un certain nombre de faiblesses dans Banana. En particuliers la génération du spool (mise en cache de l'arborescence des messages) et des flux RSS s'est révélé extrêmement lourde pour plusieurs raisons :

*   l'accès aux mbox des Mailing-Lists nécessite d'appel du `mbox-helper`, et donc un `fork`... opération lourde, qui répétée plusieurs fois par mbox devient rapidement très lourde lorsqu'on a plusieurs dizaines de Mailing-Lists.
*   le traitement des données par PHP est loin d'être immédiats... et il y a clairement des goulots d'étranglement dans le code.

C'est pour ces raisons que j'ai passé Banana au `profiler`, c'est à dire que j'ai analysé l'exécution de Banana à l'aide d'un outil qui permet de tracer l'exécution du programme et mettant un accent particuliers sur le temps d'exécution de chaque fonction. L'outil que j'ai trouvé pour faire ça est [xdebug](http://www.xdebug.org), utilisé conjointement à [KCacheGrind](http://kcachegrind.sourceforge.net/).

<!-- more -->

Forks
=====

Je dois avouer que lorsque j'avais fait mes tests, je n'avais pas plusieurs centaines de Mailing-Lists à traiter... et je ne m'attendais pas à ce qu'une fois passée en production le script de mise à jour des flux RSS puisse prendre toutes les ressources de la machine pendant plusieurs minutes. Il a donc fallu sérieusement restructurer la gestion des accès aux Mailing-Lists pour restreindre au maximum le nombre d'accès au `mbox-helper` (un petit programme écrit en C qui se charge de tous les accès aux mbox), et, en cas d'accès, limiter au maximum le temps passer sur le `mbox-helper`.

Donc désormais le `mbox-helper` n'est plus appelé que si la mbox a changé depuis le dernier passage (le changement étant détecté par la taille du fichier). Ce qui permet donc de supprimer le lancement d'environ 500 `mbox-helper` lors des rafraîchissements des spools (en effet, un nombre négligeable de Mailing-List aura des nouveaux messages lors du passage du script toutes les 5, 10 ou 20 minutes). Ajouté à cela la correction d'un bug qui faisait que l'appel au `mbox-helper` oubliait de spécifier l'offset où chercher le message à traiter et qui forçait donc le `mbox-helper` à relire la totalité de la mbox, on peut se permettre de supposer que la prochaine version de Banana sera plus efficace pour la gestion des mbox.


Array_shift
===========

Piles
-----

Il est souvent extrêment pratique d'utiliser une pile de données. Cela permet de traiter les informations dans l'ordre de la pile sans excès de mémoire puisque chaque élément est dépilé avant d'être traité. C'est une technique que j'aime particulièrement lorsque j'ai une suite de lignes à traiter : je prend un tableau contenant une ligne par entrée et je le parcours avec `array_shift` qui permet de dépiler le premier élément du tableau. On obtient ainsi un code de la forme


{% highlight php %}
while (!is_null($line = array_shift($lines))) {
    do_something($line);
}
do_something($lines);
{% endhighlight %}

Un `foreach` peut très bien faire la même chose, mais on perd les avantages de la piles. Avec cette structure, pour avoir le même comportement que la boucle précédente, il faut ajouter un `unset()` :


{% highlight php %}
foreach ($lines as $key=>&$value) {
    do_something($line);
    unset($lines[$key]);
}
do_something($lines);
{% endhighlight %}

Certes le `foreach` sera sensiblement plus rapide que le `while`/`array_shift()` car il comprend un appel de fonction à chaque itération, mais on peut s'attendre raisonnablement à ce que cet appel soit en O(1), et ait donc un coût négligeable.

Profiler
--------

Là où il y a un problème c'est que le profiler m'indique que `array_shift` prend 57% du temps d'exécution de Banana. Ces 57% sont partagés entre 80000 appels à la fonction, mais le plus marquant c'est que parmi ces 80000 appels, ce ne sont que 24000 d'entre eux qui prennent la quasi-totalité du temps. Pourquoi ces appels particuliers sont-ils si lourd alors que les 60000 autres ont un coût parfaitement négligeable.

La seule différence entre ces deux cas d'appels c'est que les _lourds_ traitent un énorme tableau de 24000 lignes, alors que les _légers_ traitent un grand nombre de petits tableaux de quelques dizaines de lignes chacun. Il est donc extrêmement clair que `array_shift` __n'est pas__ une fonction en O(1)... J'ai donc changé la structure de code qui reflétait la structure de données par une simple boucle qui perd en lisibilité dans [ce patch](http://opensource.polytechnique.org/viewsvn/diff.php?path=/trunk/banana/mbox.inc.php&rev=248&repname=Banana). Après ce changement, les 60000 `array_shift` restant ne prennent que 0.16% du temps d'exécution de Banana...
