---
layout: post
title: "Deux approches..."
date: 2007-03-28 00:52:00
comments: true
categories: GeekTime
tags: [Devel, PHP, Qt]
---
Les deux APIs avec lesquelles j'ai eu le plus la chance de travailler sont celle de [Qt](http://doc.trolltech.com/) et celle de [PHP](http://www.php.net)... il n'y a pas à dire, entre ces deux bibliothèques, c'est comme le jour et la nuit.

<!-- more -->

L'API de Qt est intuitive et est conçue pour l'être. Si je veux avoir l'âge du capitaine, je ferais toujours `capitaine->age()`, et si je veux définir son âge, ce sera `capitaine->setAge(42)`. Cette API est tellement prévisible qu'au bout de quelques jours d'utilisation, on n'utilise la documentation que pour chercher de nouvelles fonctionnalités, mais très rarement pour chercher des fonctions courantes. En plus, ce qui est remarquable, c'est qu'au fur et à mesure des versions de Qt, l'accent est mis sur la simplification de l'API... Qt 4 est bien plus intuitive que Qt 3 qui est pourtant déjà très facile à utiliser.

La qualité de la documentation de Qt y est également pour quelque chose : toutes les méthodes sont documentées, avec la plupart du temps un exemple. Le tout étant très facilement accessible grâce à l'assistant, un petit programme qui permet de consulter la documentation en intégrant des outils pour naviguer simplement entre les pages.

Par contre, je ne sais pas pourquoi, mais en PHP, l'API est incohérente... l'exemple le plus simple est sans doute celui du remplacement et de la recherche dans une variable. Si, par exemple, je veux rechercher un élément :

{% highlight php %}
//Dans une chaîne de caractères :
strpos($string, $findme);
//Dans un tableau, c'est l'inverse :
in_array($findme, $array);
array_search($findme, $array);
{% endhighlight %}

Si je veux faire un remplacement dans une chaîne de caractères :

{% highlight php %}
strtr($string, $search, $replace);
str_replace($search, $replace, $string);
preg_replace($search, $replace, $string);
{% endhighlight %}

Chic ! Grâce à ça, même si je connais le nom de la fonction à utiliser, je suis quasiment forcé à chaque fois d'aller regarder dans la documentation de PHP (ce que j'ai fait pour écrire ce paragraphe, sinon j'aurais _'forcément_' inversé les arguments)... heureusement que celle-ci est bien faite et permet de trouver rapidement les fonctions dont on connaît le nom.

Quand est-ce que PHP se décidera à revoir son API pour qu'elle soit aussi intuitive que celle de Qt ? Malheureusement, sans doute jamais : il faut garder (autant que possible) la compatibilité avec les versions précédentes de PHP. Dommage, parce qu'à mon avis, si PHP continue à étoffer son API sans la rendre cohérente, sa popularité va diminuer progressivement dans les années qui viennent.
