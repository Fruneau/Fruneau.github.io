---
layout: post
title: "Outils pratiques"
date: 2007-03-13 19:21:00
comments: true
categories: GeekTime
---
Etant donné que je travaille énormément avec [SVN](http://subversion.tigris.org). C'est un outils très addictif... à tel point que je l'utilise dès que je travaille sur un projet (seul ou à plusieurs), je crée un reposoire pour le projet (c'est un excellent moyen de ne pas perdre son travail suite à une fausse manoeuvre). Comme en plus je ne supporte pas les front-end pour SVN, je travaille toujours en ligne de commandes...

<!-- more -->

Pour pouvoir améliorer la sortie des commandes SVN dans mes consoles, j'ai fait surchargé la commande `svn` par une fonction `zsh` :


{% highlight bash %}
function svn() {
    if [[ $1 == "ci" || $1 == "commit" || $1 == "propedit" || $1 == "propset" || $1 == "help" ]]; then
        /usr/local/bin/svn $*
    elif [[ $1 == "preci" || $1 == "precommit" ]]; then
        /usr/local/bin/svn status | grep -v '?' | ~/.zsh/zshcolorsvn
    else
        /usr/local/bin/svn $* | ~/.zsh/zshcolorsvn
    fi
}
{% endhighlight %}

Cette fonction est donc très simple :

*   si la commande nécessite un interaction de l'utilisateur, on appel directement le programme `svn` (le cas de `help` permet de ne pas casser la tab-completion de `zsh`)
*   sinon on envoi la sortie de svn dans [un script](/mind/public/misc/zshcolorsvn) qui réalise sa coloration syntaxique

Ceci donne un résultat de la forme suivante :

![Exemple de svn diff]({{ site.url }}/assets/screenshots/svn-diff.jpg)

![Exemple de svn update))]({{ site.url }}/assets/screenshots/svn-status.jpg)

C'est donc extrêmement pratique ; ça permet de distinguer facilement les modifications, ça permet aussi de voir d'un seul coup d'oeil les conflits et la liste des fichiers modifiés. En plus ça fonctionne également avec des commandes comme `cvs` ou `diff` :


{% highlight bash %}
# SVN
function svn() {
    if [[ $1 == "ci" || $1 == "commit" || $1 == "propedit" || $1 == "propset" || $1 == "help" ]]; then
        /usr/local/bin/svn $*
    elif [[ $1 == "preci" || $1 == "precommit" ]]; then
        /usr/local/bin/svn status | grep -v '?' | ~/.zsh/zshcolorsvn
    else
        /usr/local/bin/svn $* | ~/.zsh/zshcolorsvn
    fi; fi
}

# SVN avec conversion de la sortie en latin1
function svn_utf8() {
    if [[ $1 == "ci" || $1 == "commit" ]]; then
        svn $*
    else
        svn $* | iconv -f utf8 -t iso-8859-1
    fi
}

function cvs() {
    if [[ $1 == "diff" ]]; then
        shift 1
        /usr/bin/cvs diff -ubN $* 2> /dev/null | ~/.zsh/zshcolorsvn
    elif [[ $1 == "ci" || $1 == "commit" || $1 == "propedit" || $1 == "propset" || $1 == "help" ]]; then
        /usr/bin/cvs $*
    else
        /usr/bin/cvs $* 2> /dev/null | ~/.zsh/zshcolorsvn
    fi;
}

function diff() { /usr/bin/diff -u $* | ~/.zsh/zshcolordiff }
{% endhighlight %}

Les scripts nécessaires sont disponibles : [zshcolorsvn](/public/misc/zshcolorsvn) et [zshcolordiff](/public/misc/zshcolordiff)

Seul défaut : pour pouvoir utiliser les commandes sans coloration... il faut taper chemin complet.
