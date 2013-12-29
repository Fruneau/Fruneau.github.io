---
layout: post
title: "Pourquoi faire propre quand on peut faire sale ?"
date: 2007-02-23 23:08:36 +0100
comments: true
categories: Polytechnique.org
tags: [Devel, HTML, IE6, platal]
---
Il y a quelques jours, [mYk](http://myks.org) est venu me voir pour me dire qu'il avait réussi à se débrouiller pour
que IE6 fasse un rendu correct de son site web perso... c'est à dire que le site (XHTML avec une CSS non triviale)
ressemble à quelque chose sur IE6.

Il m'explique alors que IE6 a 2 modes de fonctionnement pour le rendu :

*   Le premier, qu'il appelle "mode normal" fait un rendu correct (pas parfait, mais correct)
*   Le second, (dont je ne me souviens plus le nom à dormir debout), a quant à lui un rendu "tout pourri"... par
    exemple, le margin: auto; de CSS ne fonctionne pas, le margin est à l'intérieur des boîtes et une partie de la CSS
    n'est tout bonnement pas pris en compte.

<!-- more -->

Mais alors, comment passer d'un mode à l'autre ? Un des facteur, toujours d'après mYk est la présence d'un tag `<?xml ...>` en en-tête du fichier, comme par exemple :

{% highlight HTML %}
<?xml version="1.0" encoding="iso-8859-15"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  </head>
...
{% endhighlight %}

Donc, j'ai testé sur [plat/al](http://opensource.polytechnique.org/platal/), le site web de Polytechnique.org : le résultat est impressionnant !

Tout d'abord, voici l'état actuel de le prod de Polytechnique.org sous IE6 :

![Polytechnique.org sous IE6 avec `<?xml>`]({{ site.url }}/assets/screenshots/ie6-prod.jpg)

Le corps du site n'est pas centré, le menu est mal formaté, les polices sont énormes, les backgrounds ne sont pas tous respectés... donc clairement c'est IE6 dans toute sa grandeur. Maintenant, en supprimant le `<?xml version="1.0" encoding="iso-8859-15"?>` du code (il faut faire abstraction du bandeau de développement) :

![Version de développement de Polytechnique.org sous IE6, sans le `<?xml ...>`]({{ site.url }}/assets/screenshots/ie6-dev.jpg)

Cette fois-ci la mise en page est correcte, le menu qui est dans un tableau bicolore est __vraiment__ bicolore... on a là un site rendu quasiment aussi bien que sur les navigateurs récents, et en particuliers, le rendu d'IE6 dans ces conditions est quasiment celui d'IE7 (en tout cas, ils ont les mêmes bugs sur les fieldset/legend en particuliers).

Donc, il semblerait qu'IE7 n'a pas un moteur de rendu si _révolutionnaire_... au moins, les utilisateurs d'IE6 retrouveront enfin un site agréable à utiliser lors de la prochaine release de plat/al... d'ici un gros mois sans doute.

Edit du 21 mars 2007 : pour plus de détail sur les modes de fonctionnement des navigateurs, vous pouvez regarder sur [cette page](http://hsivonen.iki.fi/doctype/).
