---
layout: post
title: "Et si on oubliait les bases ?"
date: 2007-05-26 11:40:00
comments: true
categories: GeekTime
---
MacOS X est un Unix... compatible POSIX. Voilà ce qu'un certain nombre de personnes semblent oublier assez fréquemment. C'est assez dommage quand ces personnes programment pour MacOS, on se retrouve parfois avec du code complexe pour réécrire des fonctions POSIX (en moins bien ?).

<!-- more -->

En fait si je parle de ça, c'est parce que je viens de rencontrer le problème dans le cadre du développement de [VirtueDesktops](http://blog.mymind.fr/post/2007/05/18/VirtueDesktops-revient). Il se trouve qu'en parcourant le tracker du projet je suis tombé sur le [ticket 115](http://trac.virtuedesktops.info/ticket/115) qui s'intitule :


>  Patch to check group id of 'procmod' group

C'est intéressant... jusqu'à maintenant Virtue suppose que le gid du groupe procmod est celui par défaut de MacOS (c'est à dire qu'il vaut 9)... et le problème est donc que si un utilisateur a un procmod différent (ou si Apple décide un jour de changer le gid du groupe), le code actuel peut avoir des résultats inattendus, il faut donc faire un code plus portable qui recherche le gid de procmod au lieu de le stocker en dur. Ce n'est clairement pas une mauvaise idée ! Seul problème, le patch soumis est le suivant (au cas où certain voudrait réutiliser ce code, je tiens à signaler que c'est une mauvaise idée !) :


{% highlight c %}
#define NI_DOMAIN	"."
#define NI_PATH		"/name=groups/name=procmod"
#define NI_KEY		"gid"

// Sucked from netinfo-369.5/tools/niutil/niutil.c
ni_status ni_read_single_prop(char **property)
{
	const char *args[] = {NI_DOMAIN, NI_PATH, NI_KEY};
	
	char myname[] = "ni_read_single_prop";
	const bool opt_tag = false;
	const int timeout = 30;
	
	ni_namelist nl;
	void *domain;
	ni_id dir;	
	ni_status ret;

	if ((ret = do_open(myname, args[0], &domain, opt_tag, timeout, NULL, NULL)) != 0) return ret;

	/* args[1] should be a directory specification */
	ret = ni2_pathsearch(domain, &dir, args[1]);
	if (ret != NI_OK) {
		fprintf(stderr, "%s: can't open directory %s: %s
", myname, args[1], ni_error(ret));
		ni_free(domain);
		return ret;
	}

	/* get the property values for args[2] */
	NI_INIT(&nl);
	ret = ni_lookupprop(domain, &dir, args[2], &nl);
	if (ret != NI_OK) {
		fprintf(stderr, "%s: can't get property %s in directory %s: %s
", myname, args[2], args[1], ni_error(ret));
		ni_free(domain);
		return ret;
	}
	
	
	if (nl.ni_namelist_len != 1) {
		fprintf(stderr, "%s: expected length = 1, found length = %d
", myname, nl.ni_namelist_len);
		return NI_FAILED;
	}
	
	*property = (char*)calloc(strlen(nl.ni_namelist_val[0]) + 1, sizeof(char));
	strcpy(*property, nl.ni_namelist_val[0]);
	
	ni_namelist_free(&nl);
	ni_free(domain);
	
	return NI_OK;
}
{% endhighlight %}

Donc que fait cette fonction ? C'est assez simple : elle utilise l'utilitaire NetInfo d'Apple pour lire les informations relatives au chemin `/groups/procmod`. Une fois là dedans elle copie la valeur stockée à la clé `gid` et renvoie cette chaîne dans le pointeur passé en argument.

Il n'y a pas à dire... c'est une solution qui devrait fonctionner (enfin j'ai même un doute, car je ne vois pas le groupe procmod apparaître quand je regarde dans NetInfo). Seulement cette solution montre clairement que la personne qui l'a écrite savait que MacOS utilise un système de groupes, mais avait oublié qu'en fait c'est avant tout un système POSIX. Et là, ce qui est intéressant c'est que sous MacOS X on a accès à l'API POSIX... en particulier à la fonction `getgrnam` qui retourne les informations sur un groupe à partir de son nom.

Donc, pas besoin de dépendance vers NetInfo, pas besoin d'une recherche dans une arborescence abstraite : NetInfo n'est qu'une abstraction de la couche Unix pour simplifier l'accès aux données par les utilisateurs... mais ça ne doit pas être un framework d'abstraction de l'API POSIX ce qui entraîne nécessairement du code moins portable (et surtout, moins lisible dans le cas présent), moins rapide et plus lourd.

Ici, le code est utilisé dans un programme en C qui est chargé de mettre un objet dans le groupe procmod pour autoriser Virtue à effectuer certaines actions qui nécessitent un accès privilégié au serveur graphique... ce programme ne fait _que_ changer les permissions. Pourquoi utiliser une API lourde et spécifique à MacOS alors que ce type de programme pourrait clairement être utilisé sur n'importe quel Unix ?

Voici donc une solution qui fait la même chose que la fonction ci-dessus, en mieux et surtout en beaucoup moins de lignes :


{% highlight c %}
#include <grp.h>

static int getProcmodGid()
{
    struct group* groupDesc; 
    int gid = -1; 
    groupDesc = getgrnam("procmod"); 
    if (groupDesc) {
        gid = groupDesc->gr_gid; 
        free(groupDesc); 
    } 
    return gid; 
}
{% endhighlight %}

Pourquoi cette solution est-elle meilleure ?

*   elle retourne un entier... on a donc pas besoin de faire une conversion a posteriori
*   elle utilise l'API POSIX et est donc utilisable sur n'importe quel OS POSIX (donc quasiment tous... à l'exception de Windows)
*   elle évite toute manipulation inutile de chaîne de caractères
*   elle est ne nécessite pas de charger une bibliothèque supplémentaire : `getgrnam` est dans la `libc`
*   elle est drôlement plus courte non ?
