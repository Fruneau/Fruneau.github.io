---
layout: post
title: "Compter les fichiers"
date: 2007-08-30 23:12:00
comments: true
categories: GeekTime
---
C'est un peu la suite de mon post "Outils pratiques" où je donnais deux scripts permettant de rendre les commandes SVN plus conviviales. Encore une fois, je réinvente sans doute la roue (des outils équivalents doivent déjà exister... sans doute en mieux), mais je pense que chercher ce genre d'outils sur internet m'aurait pris plus de temps que ce qu'il m'a fallu pour le développer.

En ce moment je manipule des fichiers, beaucoup de fichiers (et même, beaucoup de gros fichiers), que j'ouvre, rouvre, et ferme et puis referme. Et à force d'ouvrir, on oublie parfois de refermer, et là, c'est comme une fuite de mémoire, sauf que le nombre limite de fichiers ouverts est beaucoup plus rapidement atteinte que la limite de mémoire... dans la configuration de base sur un linux, un programme n'a le droit qu'à 1024 descripteurs de fichiers. D'où mon problème : comment traquer les "file-handle leaks" ?

<!-- more -->

Pour le faire, je me suis fait rapidement un petit script qui permet d'analyser les données issues d'un `strace`. `strace` est, pour ceux qui ne le savent pas, un programme très pratique qui permet de lister les appels systèmes. Dans mon cas présents, surveiller les ouvertures fermetures de fichiers revient à traquer les commandes `open` (ouverture d'un fichier), et les commandes `close`. Donc, je récupère simplement la liste des appels à `open` et `close` :


{% highlight bash %}
strace -f -e trace=open,close mon programme 2> /quelque/part
{% endhighlight %}

Et ainsi, le fichier `/quelque/part` contient la liste complète des appels à `open` et `close` effectués par mon programme (et ses processus fils). Il ne reste plus alors qu'à analyser le contenu de `/quelque/part`. Pour ceci, il suffit de peu de lignes de code (en perl pour ma part, mais d'autres auraient fait la même chose en shell, python... ou n'importe quel langage de scripting) :


{% highlight perl %}
#!/usr/bin/perl 
 
my %files; 
my %modes; 
my %lines; 
 
my $lineNb = 0; 
my $maxOpened = 0; 
my $currentOpened = 0; 
my $totalOpened = 0; 
 
for $line (<STDIN>) { 
  if ($line =~ /open\\\\("([^""]+)", ([^\\\\)]+)\\\\)\\\\s*=\\\\s*(\\\\d+)/) { 
    $files{$3} = $1; 
    $modes{$3} = $2; 
    $lines{$3} = $lineNb; 
    $totalOpened++; 
    $currentOpened++; 
    if ($currentOpened > $maxOpened) { 
      $maxOpened = $currentOpened; 
    } 
  } 
  if ($line =~ /close\\\\((\\\\d+)\\\\)/ && $files{$1} ne '') { 
    $files{$1} = ''; 
    $currentOpened--; 
  } 
  $lineNb++; 
} 
 
print "$totalOpened files opened, max. $maxOpened at the same time
"; 
print "$currentOpened files not closed
"; 
for $id (keys %files) { 
  local $file = $files{$id}; 
  local $mode = $modes{$id}; 
  local $line = $lines{$id}; 
 
  if ($file ne '') { 
    print "[line $line] id=$id, open $file with mode $mode
"; 
  } 
} 
{% endhighlight %}

Pour simplifier le tout, on rajoute une fonction dans le `zshrc` pour wrapper tout ça, et ça donne (attention, ceci ne fonctionne que sous linux, `mktemp` n'a pas la même syntaxe sur MacOS, et surtout, `strace` n'est pas disponible sur Mac 


{% highlight bash %}
function checkFiles() { 
  TEMPFILE=`mktemp` 
  strace -f -e trace=open,close $* 2> $TEMPFILE 
  cat $TEMPFILE | ~/.zsh/trackFiles.pl 
  rm $TEMPFILE 
} 
{% endhighlight %}

Avec, ça, il ne me répond :


    % checkFiles cp test test2
    12 files opened, max. 2 at the same time
    0 files not closed

Si maintenant, je fais un programme minimaliste qui oublie de fermer un fichier :


{% highlight c %}
#include <stdio.h> 
 
int main() { 
  FILE* file = fopen("test", "r"); 
  return file != NULL ? 0 : 1; 
} 
{% endhighlight %}


    % gcc test.c -o tester
    % checkFiles ./tester
    3 files opened, max. 1 at the same time
    1 files not closed
    [line 4] id=3, open test with mode O_RDONLY

Voilà, maintenant je sais que mon programme oublie de fermer un fichier, que ce fichier s'appelle "test", et qu'il est ouvert en read-only. La ligne "4" est la ligne dans la sortie de `strace`, et n'a aucun rapport avec la ligne 4 du fichier source (contrairement aux apparences).
