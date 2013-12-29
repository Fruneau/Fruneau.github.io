---
layout: post
title: "Comprendre du code"
date: 2007-08-15 01:28:00
comments: true
categories: GeekTime
tags: [Devel]
---
J'ai tenté cet après-midi une petite expérience sur IRC. J'ai posté 3 lignes de code en demandant "que font ces trois lignes". Après quelques minutes (sans doute trop peu), j'ai donné la solution car personne n'avait vraiment trouvé. En effet, même si on réussi facilement à lire le code et à reconstituer la suite d'opération qu'il génère, il est très difficile de vraiment comprendre ce qu'il fait, d'imaginer son application dans un contexte et d'en déduire son usage.

Juste pour continuer à m'amuser, et pour amuser ceux qui se perdraient ici, voici le code en question.

<!-- more -->


{% highlight c %}
do {
  ++(*pos);
} while (*(pos++) == 0);
{% endhighlight %}

Vous pouvez remarquer dès à présent que ce code n'est pas volontairement rendu illisible (il serait facile de supprimer des parenthèses et de changer le test de nullité en une négation. Le but est donc de comprendre la suite d'instruction, de dire ce qu'elle fait et ensuite de comprendre dans quel contexte elle est utilisable. La question est aussi de savoir en combien de temps vous allez trouver la solution.

<script type="text/javascript" src="/jquery.js"></script>
<script type="text/javascript">//<![CDATA[
function show_answer() {
  $("#show_answer").hide();
  $("#answer").show("slow");
  return false;
}
//]]></script>
<p>
<a href="" onclick="return show_answer()" id="show_answer">Cliquer ici pour afficher la répondre</a>
</p>
<div id="answer" style="display: none">
 Fonctionnement
---------------

Si on lit le code, on voit qu'il fait la suite d'opération suivante :

1   on incrémente la valeur pointée par pos
1   on regarde si la valeur obtenue est nulle
1   on incrémente le pointeur pos
1   si le test 2 est vrai, on retourne en 1, sinon, on quitte la boucle

Il s'agit donc d'une petite boucle qui incrémente des objets successifs jusqu'à ce qu'elle en trouve un qui, une fois incrémenté, n'est pas nul.

Voilà donc la solution du pauvre au problème, celle qu'on peut trouver en lisant le code, sans chercher à le comprendre.

 Interprétation
----------------

Mais en fait, ce code va plus loin. Imaginons maintenant la zone mémoire sur laquelle pos pointe initialement :

     oct1 oct2 oct3 oct4
      ^
     pos

L'octet 1 à une certaine valeur. Je l'incrémente et je m'aperçois que sa nouvelle valeur est 0. Quand un octet incrémenté peut-il retomber à 0 ? Quand il fait un overflow. On a donc fait déborder le premier octet... et quand cet octet déborde on incrémente le suivant :

        _+1__
       |     |
     oct1 oct2 oct3 oct4
           ^
          pos

Il s'agit donc ni plus ni moins que d'une retenue (comme quand on apprend à faire les additions ou les multiplications) : quand je fais déborder un octet, je déplace le surplus vers le suivant. Finalement, ces 3 lignes de code ne sont que l'implémentation d'une incrémentation, mais sur plusieurs octets... et même sur un nombre non défini d'octets (puisqu'il n'y a pas de limite explicitée sur le nombre d'étapes autorisées).

Pour être plus précis, il s'agit d'un incrémenteur d'entier stocké en Little Endian sur un nombre illimité d'octets.

 Encore ?
---------

Bon, pour ceux qui tiennent vraiment à s'assurer que ce n'est pas du code illisible, voici une version vraiment illisible de la même machine :

{% highlight c %}
for(++*pos;!*pos++;++*pos);
{% endhighlight %}

(Merci Falco)

Tout ceci n'était bien sûr qu'un exemple. Il est toujours très dur de comprendre du code, alors pour éviter de passer dix minutes sur 3 lignes lors de la prochaine relecture il suffit de documenter ce qu'on écrit.

</div>
