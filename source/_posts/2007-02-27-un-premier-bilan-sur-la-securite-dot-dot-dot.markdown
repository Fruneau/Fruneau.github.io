---
layout: post
title: "Un premier bilan sur la sécurité..."
date: 2007-02-27 16:47:51 +0100
comments: true
categories: Polytechnique.org
tags: [platal, Sécurité, Usurpation]
---
Sans entrer dans les détails du fonctionnement des filtres de protection de [Polytechnique.org](https://www.polytechnique.org) (je laisse ce plaisir à ceux qui auront le courage de lire le code de [plat/al](http://opensource.polytechnique.org/platal/)), nous avons ces derniers mois fortement amélioré les outils de surveillance. Alors qu'il y a encore un an, nous ne détections que quelques usurpations par an, nous sommes actuellement à environ une usurpation détectée par semaine.

<!-- more -->

De quoi s'inquiéter ?
=====================

Certes la fréquence des usurpations est plus importante que nous pensions mais plus on a d'outils de protection, plus on a d'usurpations... non seulement parce qu'on en détecte plus, mais aussi parce que d'une certaine manière, on provoque les usurpations. En effet, lorsque nous détectons une usurpation, nous fermons le compte. Lorsque l'usurpateur se rend compte qu'il a perdu l'accès, il usurpe (ou tout au moins tente d'usurper) un autre compte.

Ainsi, nous avions détecté un usurpateur mi-janvier. Suite à celà il s'est réinscrit une fois. Nous l'avons trouvé et nous avons redésactivé le compte... il s'est de nouveau réinscrit, mais pour une raison que je ne comprends pas, nous avons manqué cette inscription... mais finalement, il a oublié son compte, et hier il a retenté de se connecter. Mais cette fois-ci son IP était bannie, et c'est donc 2 comptes qu'il a tenté d'usurper tour à tour.

C'est donc __5__ comptes pour une personne... pas mal non ? Nous n'aurions pas détecté la première de ces usurpations, il en serait resté à une seule. 

De plus, la plus grande majorité des comptes usurpés sont abandonnés par leur usurpateur dans le mois qui suit l'inscription. Nous détectons donc majoritairement des comptes abandonnés. Les usurpations sont donc gênantes, mais certainement pas inquiétantes. A force de tests, nous épurons petit-à-petit la base.

Donc, je ne pense pas qu'il y ait de quoi s'inquiéter de la fréquence des usurpations et la proportion de comptes usurpés de notre base.

Sécuriser une inscription peu sélective
=======================================

La faille de Polytechnique.org est la faible sélectivité de son système d'inscription. Le problème est que nous avons une base des alumnis et qu'il faut faire correspondre chaque entrée à la bonne personne physique. Il est quasiment trivial pour n'importe qui de trouver les informations nécessaire pour remplir le formulaire d'inscription... comment s'assurer que finalement il s'agit bien de la bonne personne ?

Nous avons donc plusieurs niveaux de filtrages :

1  Le premier consiste à vérifier autant que possible la crédibilité des informations données dans le formulaire d'inscription
1  Le deuxième consiste à réaliser un filtrage "humain"... il suffit de vérifier pour chaque inscription les informations de chaque inscription
1  Traitement comportemental. C'est le filtrage qu'il nous faut améliorer actuellement
1  La vérification de cohérence de la base de donnée (à partir d'idées données de temps en temps, on défini un critère d'usurpation _probable_, et on check)

Là où nous avons fait beaucoup de progrès, c'est sur le points 1 et 2 : nous sommes désormais en mesure de contrer la plupart des usurpations dès l'inscription alors qu'il y a quelques mois, nous n'avions que le point 4... c'est à dire des vérifications éparses.

Nos points faibles... et nos évolutions
=======================================

Tout d'abord, si je me permets d'écire ce billet, c'est que je pense que nous sommes suffisamment sécurisés pour contrer les petits plaisantins qui en lisant ce texte voudraient tenter une usurpation... ce n'est pas de la provocation, mais un bilan.

Nos points faibles sont actuellement dans l'analyse de la base établie avant la mise en place de nos filtres. Il s'agit donc d'améliorer les points 3 et 4 indiqués précédemment. Le 3 repose principalement sur un facteur humain : non seulement personne n'est parfait, et l'usurpateur fera quasiment toujours une erreur, mais en plus il sera en contact avec d'autres utilisateurs et c'est au travers de ses échanges (que ce soit via une fiche de l'annuaire ou via des discussions par mail ou sur un forum) que nous pourrons envisager de le cerner.

La vérification de la base de données nécessite des idées... mais également des informations : nous pouvons vérifier la cohérence de notre base sur le fondement de quelques idées, mais rien ne vaut la comparaison avec des informations que nous savons exactes. Obtenir ces informations est le point bloquant... et là encore il y a un facteur humain.

Nous ne pouvons donc qu'espérer que la prochaine fusion de notre annuaire avec celui de l'[AX](http://www.polytechnicien.com), qui est vérifié par des secrétaires qui ont les moyens matériels et temporels de le faire, sera le meilleur moyen de nettoyer notre annuaire.
