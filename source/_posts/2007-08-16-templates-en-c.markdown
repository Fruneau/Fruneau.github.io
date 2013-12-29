---
layout: post
title: "Templates en C"
date: 2007-08-16 22:53:00
comments: true
categories: GeekTime
---
En C++, il existe un mécanisme extrêmement pratique pour généré du code générique : les templates. Une fonction templatée est une fonction dont le code comporte un trou qui sera remplacé à la compilation par le nom d'un type, ou une valeur... Par exemple :


    template <class T> 
    T read(const char *buffer) 
    {
        T val;
        memcpy(&val, buffer, sizeof(T));
        return val;
    } 

Cette fonction lit un objet de type T sur un buffer. L'intérêt de cette fonction est très compréhensible : quel que soit le type qu'on fournit à la fonction, elle va fonctionner, en adaptant la taille à lire au type. C'est donc beaucoup plus rapide que d'écrire une fonction pour chaque type... et l'utilisation est également très simple :


    read<int>(const char* buffer) // lit un entier sur le buffer
    read<double>(const char* buffer) // lit un double sur le buffer
    read<MaClass>(const char* buffer) // lit un objet de type "MaClass"

Mais cette syntaxe n'est qu'un sucre syntaxique, car en fait, on peut également faire des templates en C...

<!-- more -->

 Comment fonctionne les templates ?
-----------------------------------

En fait un template est, comme son nom l'indique, qu'un modèle de fonction. Lors de la compilation d'un programme qui utilise des templates, le compilateur regarde la liste des instances de cette fonction qui sont utilisées et les génère. Plus explicitement, si j'appelle read<double>(), le compilateur va générer cette fonction :


{% highlight cpp %}
double read<double>(const char *buffer) 
{
    double val;
    memcpy(&val, buffer, sizeof(double));
    return val;
}
{% endhighlight %}

Ainsi, l'appel à read<double> devient un appel de fonction classique.

 Jeu de préprocesseur
----------------------

Le préprocesseur en C possède des outils sympathique... Nous nous attarderons particulièrement sur le ##. Il s'agit tout simplement d'un opérateur de concaténation. Donc :


{% highlight c %}
1  define Truc(Machin) Truc ## Machin

Truc(Bidule) /* génère TrucBidule */
{% endhighlight %}

C'est donc très pratique. On peut facilement par exemple, imaginer une petite macro du genre :


{% highlight c %}
1  define maFonction(Type) maFonction_ ## Type
{% endhighlight %}

Si dans ce cas, on défini par exemple maFonction_int, maFonction_double, maFonction_MaStruct (oui, les classes n'existent pas en C), on pourra faire


{% highlight c %}
maFonction(int)(args); /* appelle maFonction_int */
maFonction(double)(args); /* appelle maFonction_double */
maFonction(MaStruct)(args); /* appelle maFonction_MaStruct */
{% endhighlight %}

Maintenant, il ne reste plus qu'à générer les fonctions... sans avoir à toutes les écrire une à une. Pour ceci, nous allons encore une fois profiter de la présence du préprocesseur.


{% highlight c %}
1  define maFonctionBuild(Type) \\\\
  Type maFonction_ ## Type(const char* buffer) { \\\\
    Type val; \\\\
    memcpy(&val, buffer, sizeof(Type)); \\\\
    return val; \\\\
  } 

maFonctionBuild(int) /* génère maFonction_int */
maFonctionBuild(double) /* génère maFonction_double */
maFonctionBuild(MaStruct) /* génère maFonction_MaStruct */
{% endhighlight %}

Il ne reste donc qu'à appeler maFonctionBuild(Type) sur chacun des types pour lesquels nous avons besoin d'instancier la fonction, et d'appeler maFonction(Type) chaque fois qu'on veut appeler maFonction pour le type en question. D'une certaine manière on peut dès lors dire que les fonctions sont :


{% highlight c %}
Type maFonction(Type)(const char* buffer);
{% endhighlight %}

 Et voilà !
------------

Nous avons donc une implémentation générique grâce au préprocesseur associé à un appel typé. Ce n'est évidemment qu'une astuce de préprocesseur, mais cela donne une souplesse syntaxique agréalble : "J'appelle maFonction appliquée sur les entiers avec les arguments args".

Tout ceci est certes, moins souple que les templates C++ :

*   il n'y a pas d'auto-instanciation par le compilateur : il faut "manuellement" instancier la fonction pour tous les types pour lesquels ont l'appelle
*   il n'y a pas de "type check" : les seules limites sont celle de la compilation
*   le correction des erreurs de compilation est fastidieuse car toutes les erreurs apparaissent comme étant à la ligne d'appel à maFonctionBuild
*   il est rapidement fastidieux de rajouter l'antislash à la fin de chaque ligne de la définition de la fonction

Comme on le voit dans les choix de l'exemple, pour les entrées/sorties, ça permet d'avoir une seule fonction à écrire tout en gardant la flexibilité d'une fonction par type de données.
