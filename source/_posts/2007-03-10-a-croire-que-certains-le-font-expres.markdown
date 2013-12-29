---
layout: post
title: "A croire que certains le font exprès !"
date: 2007-03-10 19:57:00
comments: true
categories: Polytechnique.org
tags: [Cpp, Devel, Qt, Sécurité, khtml2png]
---
Depuis trois jours, je travaille sur la mise ne place d'un outil permettant de faire des aperçus graphiques de site web (des vignettes façon [Exalead](http://www.exalead.fr) par exemple). C'est une fonctionnalité très à la mode... et c'est aussi un bon moyen d'avoir un aperçu d'un site. Donc dans ce cadre, j'ai commencé à travailler sur [khtml2png](http://khtml2png.sourceforge.net). Malheureusement, comme il ne correspondait pas exactement à mes besoins, j'ai commencé à en modifier le code source... et j'ai eu quelques surprises !

<!-- more -->

Strings
-------

Ma première (mauvaise) surprise a été de constater que le code était loin d'être propre. Il s'agit d'un programme écrit avec la bibliothèque [Qt](http://www.trolltech.com/qt) (KHTML oblige), et pourtant la gestion des chaînes de caractère est faite pour moitié avec QString, pour moitié avec l'API C... sans vérification de la taille des buffers.

Par exemple :

{% highlight cpp %}
QString filename = "/tmp/khtml2png.png";
int g = sprintf(nrStr,"_x%iy%i",xNr,yNr);
nrStr[g]='\\0';
filename+=QString(nrStr);
{% endhighlight %}

Quitte à utiliser l'API C autant le faire proprement, en vérifiant la taille du buffer (dans le cas présent, le buffer nrStr avait une taille de 10)... mais en plus pourquoi faire ça de cette manière alors que Qt fournit l'API suffisant pour coder ces 4 lignes sans le moindre risque de buffer overflow ?

//
[cpp]
QString filename = QString( "/tmp/khtml2png.png_x%1y%2").arg(xNr).arg(yNr);
//

Ensuite (toujours dans la gestion des chaînes de caractères), la méthode pour éviter le buffer overflow était le "Je fais un gros buffer, au moins je suis sûr que ça ne débordera pas"... mais le problème, c'est que ça peut déborder !


{% highlight cpp %}
char 	[5000], //command line string

memset(cmd,0,sizeof(cmd));

//append the row images from top to bottom to one image
strcpy(cmd,"convert ");

for (int y=0; y < yNr; y++)
    sprintf(cmd,"%s /tmp/khtml2png.png_r%i",cmd,y);
		
sprintf(cmd,"%s -append %s",cmd,args->arg(1));
system(cmd);
{% endhighlight %}

Dans ce cas précis, `args->arg(1)` est contrôlé par l'utilisateur (c'est en fait le fichier destination de la capture demandée... le `sprintf` est donc une __erreur impardonnable__... sans compter qu'en plus cet argument qui est passé à un exécutable externe sans le moindre check sur le contenu de la chaîne. On risque non seulement le buffer overflow, mais également l'injection de code dans le `system` (par exemple, je peux appeler mon fichier `fichier;sh`, ça me lancera un shell avec les droits de l'utilisateur qui a lancé le programme.

Une solution (non testée) pourrait être :


{% highlight cpp %}
QProcess process("convert");
for (int y=0 ; y < yNr ; y++) {
    process.addArgument(QString("/tmp/khtml2png.png_r%i").arg(y);
}
process.addArgument("-append");
process.addArgument(args->arg(1));
process.start();
while (process.isRunning()) {
    wait(10); // nombre choisi aléatoirement
}
{% endhighlight %}

Cette solution a le mérite :

*   d'être safe car elle ne permet pas d'injecter des commandes dans notre code
*   d'être safe car elle ne permet pas de buffer overflow

Bien sûr ceci repose sur la confiance qu'on peut avoir en Qt...


Images
------

Pour générer le screenshot, khtml2png fait plusieurs captures dans une fenêtre en scrollant à l'intérieur de manière à recouvrir une zone aillant la taille demandée par l'utilisateur. C'est très bien... malheureusement, encore une fois je me demande quel est l'intérêt d'utiliser Qt dans le cas présent. La technique de khtml2png (version 2.5.0) est de faire une capture d'écran à chaque fois... puis de la stocker dans un fichier temporaire indexé avec le numéro du morceau en X et en Y.

Une fois tous les morceaux obtenus, le programme utilise `convert` de ImageMagick pour coller les morceaux de chaque ligne, puis une deuxième fois (enfin, ce n'est pas la deuxième fois, mais la deuxième boucle) pour coller les différentes lignes et convertir dans le format voulu (c'est en fait le dernier morceau de code que j'ai collé ci-dessus).

Pourquoi faire si compliqué ?

*   On ne peut pas lancer deux fois khtml2png en même temps (le programme utilisant toujours le même nom pour les fichiers temporaires)
*   Qt aussi est capable d'enregistrer les fichiers dans différents formats... pas besoin de ImageMagick pour ça
*   khtml2png dépend d'un programme extérieur qu'il n'est pas certain de trouver sur les machines des utilisateurs...

Ma technique est beaucoup plus simple, ne nécessite de lancer __aucun__ programme externe... et en plus se révèle plus légère en quantité de mémoire (parce que j'utilise des pointeurs au lieu de recopier les images dans tous les sens en en créant plusieurs instances à chaque fois)

1   tout d'abord je crée une image vierge de la taille voulue par l'utilisateur
1   je fais les captures du site et au lieu de les stockers dans un fichier, je les colle au bon endroit dans mon image
1   j'utilise Qt pour enregistrer l'image au format désiré par l'utilisateur (en fonction de l'extension)...

Ainsi, j'ai supprimé la _totalité_ du code qui utilisait des traitements de chaînes de caractères (qui n'étaient autres que les appels à `convert`), ce qui supprime toutes les failles dont j'ai parlé ci-dessus... en gardant les mêmes fonctionnalités que la version publique du programme.


Besoins particuliers
--------------------

J'ai également rencontré un besoin qui a mon avis n'était pas prévu par les développeurs du programme : étant donné que je le fais tourner dans un environnement graphique inaccessible (en fait dans un `vncserver`), je ne vois pas ce qu'il se passe. Or, KHTML déclenche automatiquement une boîte de dialogue modale dès qu'il rencontre un objet d'un type mime inconnu... dans mon cas il s'agissait d'un applet active-x qui traîne sur le [site de l'Ecole Polytechnique](http://www.polytechnique.fr).

J'ai donc cherché pendant 2 jours comment contourner le programme : il me faut pouvoir avoir accès à la boîte de dialogue créée, et la supprimer puisque l'API de [KHML](http://api.kde.org/3.1-api/khtml/html/classKHTMLPart.html) ne permet pas de désactiver ces intrusions directement depuis le programme.

Tout d'abord j'ai cru qu'en réimplémentant la méthode `showError` ça marcherait... mais en fait non, elle n'est pas appelée dans le cas présent, c'est uniquement généré à l'intérieur du moteur KHTML... la classe `KHTMLPart` n'est jamais notifiée de l'apparition de cette boîte de dialogue.

J'ai donc cherché qui pouvait bien être le père de ce `QMessageBox`... et en fait, c'est le `KHTMLPart::view()`. N'ayant pas vraiment la possibilité de substituer à cet objet, un objet de mon choix, j'ai ajouté un `eventFilter` qui filtre les messages reçus par la fenêtre de rendu... et lorsque apparaît un `QDialog` (classe mère de `QMessageBox`), je me charge directement de le rendre non modal, de le découpler du view et de le détruire)... et là, victoire !!!

Je comprends parfaitement que les développeurs n'aient pas pensé que ces boîtes de dialogues puissent être gênantes car ils doivent travailler dans un environnement graphique et les voir... moi non ! Mais en fait, ce que je comprends moins bien c'est qu'ils ont clairement déjà rencontré le problème dans le cadre de la réalisation du screenshot. En effet, on y trouve le code (pour sélectionner les fenêtres-filles qui méritent d'apparaître dans le screenshot) :


{% highlight cpp %}
if ( child->isWidgetType() &&
    ((QWidget *)child)->geometry().intersects( w->rect() ) &&
    ! child->inherits( "QDialog" ) ) {
{% endhighlight %}

Les développeurs ont donc déjà rencontré des QDialog... pourquoi ne pas déjà les avoir désactivés ?


Conclusion
----------

J'ai donc maintenant un khtml2png adapté à mes besoins. Je vais soumettre mon code aux développeurs du projet.

À savoir qu'à l'heure actuelle (et je n'ai pas fini le nettoyage), mon code fait 80 lignes de moins que le code dont je suis parti... et ce malgré l'ajout de fonctionnalités.

[Télécharger la dernière version](/mind/public/khtml2png/khtml2png-fru-last.tar.bz2)
