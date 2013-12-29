---
layout: post
title: "Shell interdit"
date: 2007-04-13 16:20:00
comments: true
categories: Général
---
Etant donné que [Etch](http://www.debian.org/releases/etch/) est sorti et propose désormais `zsh` à la version 4.3.2, je me suis dit que le moment était venu de passer en UTF-8. En effet, `zsh` 4.3 est la première version de ce shell à supporter UTF-8. La migration en UTF-8 n'a pas spécialement posé de problème non plus sur mes Macs.

<!-- more -->

En effet, le passage à `zsh` 4.3 sur Mac OS se résume à deux étapes :

*   compiler `zsh-devel` avec `port` (`port install zsh-devel +utf8`)
*   changer mon shell en mettant `/opt/local/bin/zsh` (en passant par l'utilitaire NetInfo)

Et là... tout va bien. Pomme+N pour relancer une nouvelle fenêtre du Terminal de Mac OS me lance bien un `zsh` 4.3 avec UTF-8.

Par contre, il m'a fallu redémarrer ma machine... et là au démarrage j'ai la fenêtre suivante :

((/public/screenshots/error-terminal.png|Erreur du Terminal avec le ZSH de port))

Sans réfléchir, je décide de changer de shell depuis mon autre machine... en `ssh`. Et j'ai réalisé après coup que le shell s'est correctement lancé dans mon `ssh`... il s'agit donc d'une restriction imposée par Terminal !

Je remplace donc mon `/bin/zsh` par un lien symbolique vers `/opt/local/bin/zsh`, change mon shell via `chsh -s /bin/zsh`, et là, Terminal fonctionne de nouveau.

Merci Apple pour cette restriction débile...
