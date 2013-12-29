---
layout: post
title: "Mail.app, IMAP et signatures..."
date: 2007-06-11 23:24:00
comments: true
categories: Général
---
Les temps sont durs pour les utilisateurs de IMAP... jusqu'à présent je n'ai pas trouvé de client idéal pour travailler avec ce protocole :

*   Kmail plante lamentablement
*   Mail.app se synchronise mal
*   Outlook Express (et le tout nouveau Windows Mail) n'arrive pas à lister tous mes répertoires
*   Thunderbird rame comme un malheureux et me crée des répertoires que je ne veux pas avant de m'avoir demandé quoi que ce soit
*   mutt est trop casse-pied à configurer à mon goût (même si pratique parfois)

De fait, j'utilise le moins pire que j'ai trouvé, et qui en plus a le mérite de ne pas être trop lourd, bien intégré avec MacOS X, et qui, jusqu'à présent, n'a pas trop fait de bêtises sur mon IMAP. Ce client, pourtant pas réputé pour sa qualité, c'est évidemment Mail.app.

<!-- more -->

Un gros défauts de Mail.app (une fois configuré pour éviter toutes les fonctions décoratives qui ne servent à rien), est qu'il ne gère pas nativement PGP. Il faut donc, pour profiter de la vérification des mails mais également pour signer ses mails, installer GPGMail, un petit plug-in qui fonctionne, on va dire, pas trop mal.

En tout cas, GPGMail a toujours fonctionné correctement sur mon portable, mais depuis que celui-ci est en réparation (hum, d'ailleurs, j'aimerais bien savoir ce qu'il devient !), je suis obligé de travailler sur le fixe, et donc d'utiliser entre autre mon client mail sur le fixe. Malheureusement, sur mon fixe, GPGMail n'a __jamais__ réussi à vérifier une signature. Bizarre... les mêmes messages étaient pourtant correctement vérifiés par mon portable.

Comme, ça fait quelques temps que j'envisage d'ajouter le support de PGP (via GPG) à [Banana](http://opensource.polytechnique.org/banana), j'ai voulu regarder plus en détail comment fonctionne la signature d'un message. J'ai donc commencé à prendre des mails signés dans Mail.app, à en enregistrer les sources sur mon disque dur et à essayer de vérifier les signatures à la main, puis avec une version légèrement hackée de Banana. Malheureusement, ça n'a jamais fonctionner.

J'ai demandé à NC s'il pouvait me donner les sources d'un mail signé qui a le mérite d'apparaître comme correctement signé chez lui, et mal signé chez moi. J'ai comparé les 2 versions du mail... et le `diff` a été on ne peut plus troublant :


{% highlight diff %}
 --nextPart1631992.9B81mC3O8N
-Content-Type: text/plain;
-  charset="iso-8859-1"
 Content-Transfer-Encoding: quoted-printable
+Content-Type: text/plain;
+       charset=iso-8859-1
 Content-Disposition: inline
 ///

Le contenu de la partie signée n'est pas identique dans les deux mails. Pour ceux qui ne connaissent pas la structure d'un mail signé, je les invite à jeter un coup d'oeil à la [RFC1847|http://www.faqs.org/rfcs/rfc1847.html] qui se résume au schéma suivant :

{% endhighlight %}
Content-Type: multipart/signed; protocol="TYPE/STYPE";
         micalg="MICALG"; boundary="Signed Boundary"

--Signed Boundary
Stuff to sign

--Signed Boundary
Signature

--Signed Boundary--

    
    La partie différente entre chez NC et chez moi est la partie @@stuff to sign@@ (en fait ce sont les en-têtes de la partie signée du message).
    
    Donc, je me suis replongé dans les options de Mail.app : pourquoi modifie-t-il les sources de mon mail ? quelle est l'option qui est différente entre mon portable et mon fixe ? J'ai recherché dans les paramètres d'affichage et d'édition sans succès... et puis je me suis dit que j'allais regarder la configuration de mon compte IMAP.
    
    Une différence majeure entre mon fixe et mon portable est que mon portable est susceptible de ne pas être constamment connecté au serveur IMAP, donc il est configuré pour conserver une copie de tous les mails alors que sur mon fixe, Mail.app est sur la même machine que le serveur IMAP, pourquoi irais-je faire une copie supplémentaire des mails (ce qui se concrétiserait par des accès disques concurrents et quelques centaines de méga-octets de données dupliquées) ?
    
    Pourquoi ? et bien maintenant j'ai ma réponse... en demandant à Mail.app de garder une copie des mails, GPGMail arrive à vérifier les signatures, donc, dans ces conditions, il a accès aux sources non modifiées. Il y a donc quelque part dans Mail.app un outil qui transforme les sources des mails... assez hallucinant ! Il faut donc que je considère que si je demande à Mail.app de m'afficher les sources d'un message qui se trouve sur un serveur IMAP sans faire de copie locale, il est possible (probable ?) que les sources en question ne soient pas les vraies sources du message mais une version ''remasterisée'' par Apple.
    
    Même si je ne me fais pas trop d'illusion sur le sujet, j'espère que ce genre de détails (tout comme également la gestion des URLs dans le @@format=flowed@@, ou encore l'affichage de l'arborescence des message...) sera corrigé dans Leopard.
