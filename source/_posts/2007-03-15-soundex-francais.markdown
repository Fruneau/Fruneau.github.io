---
layout: post
title: "Soundex Français"
date: 2007-03-15 18:06:00
comments: true
categories: Polytechnique.org
---
Pour faire une recherche phonétique, on utilise souvent ce qu'on appelle une transcription __soundex__ des mots. C'est une réécriture du mot, dans un alphabet restreint et sur un nombre de caractères restreint également. La plupart des algorithmes qu'on peut trouver sur internet sont conçus uniquement à la langue anglaise. Pour utiliser la recherche phonétique en français, il faut donc adapter cet algorithme.

L'implémentation française la plus courante utilise l'[algorithme décrit par Frédéric Brouard](http://sqlpro.developpez.com/cours/soundex/). Malheureusement cet algorithme ne me satisfait pas vraiment, car il n'est finalement pas très adapté à langue française.

<!-- more -->

En effet cet algorithme a pas mal de défauts :

*   il est très aléatoire dans le cas de dédoublement de consonnes. Par exemple, il ne permet de trouver que _bananne_ (soundex `BNN`) est une approximation de _banane_ (soundex `BN`)
*   il est très peu sélectif sur les voyelles. Par exemple _poulpe_ et _palpa_ ont le même soundex (`PLP`) alors que pour moi, poulpe et palpa sont deux mots éloignés, malgré leur consonnes communes. Par contre, il ne reconnaîtra pas Aymeric (soundex `AYMR`) comme étant un homophone de Emeric (soundex `EMRC`)...
*   il n'est pas vraiment capable de distinguer les conjugaisons. Par exemple _palper_ (soundex `PLPR`) et _palpé_ (soundex `PLP`) sont éloignés l'un de l'autre.

J'ai donc réalisé une nouvelle implémentation de `soundex` qui est plus adaptée (à mon sens) à la langue française. Je n'ai évidemment pas pour ambition de réaliser une solution parfaite (ce qui est impossible à faire à partir d'un système automatisé sans dictionnaire).

Algorithme
==========

Mon algorithme, comme je l'ai déjà dit, est une adaptation de celui de [Frédéric Brouard](http://sqlpro.developpez.com/cours/soundex/). Il se compose de 2 étapes principales :

1   le préformatage, qui consiste à transformer le chaîne de caractère brute, en une chaîne analysable.
1   l'analyse de la chaîne, et la recherche de entités phoniques élémentaires qui la composent

Préformatage
-------------

Le préformatage est extrêmement simple :

1   On converti la chaîne en majuscule
1   On converti chaque caractère accentué vers son caractère non-accentué correspondant
1   On filtre pour ne conserver que les lettres (de A à Z)

Ainsi après ce premier filtrage, _Mac-Cartney_ devient _MACCARTNEY_ et _palpé_ sera _PALPE_

Traitement des données
-----------------------

Le traitement des données consiste à reconnaître les sonorités. Pour ceci on utilise la table de conversion suivante qui associe à chaque sonorité complexe un caractère :

<table>
  <tr><th>Combinaison</th><th>Caractère</th></tr>
  <tr><td>G (prononcé GUE), C (prononcé Q), CK, K, QU, Q</td><td>K</td></tr>
  <tr><td>CH, SH, SCH</td><td>9</td></tr>
  <tr><td>ST</td><td>T</td></tr>
  <tr><td>PF (en début de mot), PH</td><td>F</td></tr>
  <tr><td>Z (en fin de mot)</td><td>ZE</td></tr>
  <tr><td>Z, ZZ, C (prononcé SE)</td><td>S</td></tr>
  <tr><td>G (prononcé JE)</td><td>J</td></tr>
  <tr><td>EAU, AU</td><td>O</td></tr>
  <tr><td>IN, UN, AIN, EIN (proncés UN)</td><td>1</td></tr>
  <tr><td>AON, AOM, EN, AN (prononcés EN)</td><td>A</td></tr>
  <tr><td>EY, EI, AY, AI, OE, OEU, EU, ER</td><td>E</td></tr>
  <tr><td>OI</td><td>O</td></tr>
  <tr><td>ILLE, I</td><td>Y</td></tr>
  <tr><td>OU, OW</td><td>U</td></tr>
  <tr><td>ON, OM</td><td>O</td></tr>
  <tr><td>KN (en début de mot)</td><td>N</td></tr>
</table>

Une fois qu'on a cette table de correspondances (elle n'est peut-être pas exhaustive... je n'hésiterais pas à l'améliorer), on applique toutes les modifications, on supprime les lettres qui sont présentes en doublon et finalement on supprime les H restants. Il faut également supprimer les caractères muets en fin de mot. En français, ce seront les X, T, D, S (et le L qui les précède si il existe) ou les E.

Ensuite, il faut faire un nettoyage sur la chaîne de caractère. Ce nettoyage consiste à rechercher les phonèmes importants et à supprimer les caractères muets ou peu audibles : on veut identifier ce qui fait la particularité d'une syllabe. Frédéric Brouard considère que toutes les voyelles (exceptés les Y précédés d'une voyelle) placées autre part qu'en début de mot sont insignifiantes... ça me paraît le gros point faible de sa méthode. Personnellement j'ai essayé d'identifier une liste de sonorité dominantes. Voici celles que j'ai actuellement :

<table>
  <tr><th>Sonorité</th><th>Informations</th></tr>
  <tr>
    <td>K, T, P</td>
    <td>Ce sont les consonnes qui sont très marquées, qui rythment le mot</td>
  </tr>
  <tr>
    <td>A, U, O, 1</td>
    <td>Ce sont les son qu'on entend le mieux, contrairement aux I ou aux E qui mettent plus en avant les consonnes. Le A est à mon avis particulier, car il est faible mais suffisamment marquant pour effacer des consonnes comme le R</td>
  </tr>
  <tr>
    <td>Y</td>
    <td>Y n'est marquant que lorsqu'il a un rôle de consonne : quand il permet de lier deux parties du mot, comme dans ''voyelle'', où il lie le ''vo'' et le ''elle''. Il est donc important lorsqu'il est encadré par une ou des voyelles. Dans les autres cas, c'est une voyelle faible</td>
  </tr>
  <tr>
    <td>L</td>
    <td>Contrairement aux K, T et P, le L permet d'adoucir le mot, en particulier lorsqu'il sert de liaison entre une consonne et une voyelle. Il est donc important de le conserver</tr>
  </tr>
</table>

Ces choix sont relativement arbitraires. Le comportement de l'algorithme est alors simple :

*   Si une voyelle est liée à une consonne de liaison (qui fait le lien entre la voyelle et une autre consonne, c'est souvent le cas du L ou du R, on exclut de ce cas les consommes fortes)
*   Si en plus il s'agit d'une voyelle forte
    *   alors, on supprime la consonne de liaison et on garde la voyelle

Ensuite, on supprime les voyelles faibles (sauf si elles commencent le mot). Pour cette suppression, on considère le A comme une voyelle faible.

Pour terminer on coupe la chaîne obtenue à 4 caractères (mais il est parfaitement envisageable de prendre un soundex sur plus (ou moins) de caractères. Simplement, dans la plupart des cas, 4 caractères suffisent amplement à avoir un mot significatif.

Implémentation
===============

Mon implémentation en PHP est téléchargeable [ici](/soundex_fr.php?action=download). Elle est testable [plus bas dans cette même page](/mind/post/2007/03/15/Soundex-Francais#test). Voici le code de la fonction soundex_fr (je ne garantis pas que le code disponible sur cette page reste constamment à jour, contrairement au lien de téléchargement) :$$L'algorithme prend en compte les modifications fournies par les lecteurs et dont vous retrouverez le détail dans les commentaires. Merci donc aux commentateurs, et particulièrement à [David](http://azur.ironie.org/)$$


{% highlight php %}
function soundex_fr($sIn) 
{ 
    static $convVIn, $convVOut, $convGuIn, $convGuOut, $accents; 
    if (!isset($convGuIn)) { 
        $accents = array('É' => 'E', 'È' => 'E', 'Ë' => 'E', 'Ê' => 'E', 
                    'Á' => 'A', 'À' => 'A', 'Ä' => 'A', 'Â' => 'A', 'Å' => 'A', 'Ã' => 'A', 
                    'Ï' => 'I', 'Î' => 'I', 'Ì' => 'I', 'Í' => 'I', 
                    'Ô' => 'O', 'Ö' => 'O', 'Ò' => 'O', 'Ó' => 'O', 'Õ' => 'O', 'Ø' => 'O', 
                    'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U', 
                    'Ç' => 'C', 'Ñ' => 'N', 'Ç' => 'S', '¿' => 'E', 
                    'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e', 
                    'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a', 'å' => 'a', 'ã' => 'a', 
                    'ï' => 'i', 'î' => 'i', 'ì' => 'i', 'í' => 'i', 
                    'ô' => 'o', 'ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'õ' => 'o', 'ø' => 'o', 
                    'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u', 
                    'ç' => 'c', 'ñ' => 'n'); 
        $convGuIn  = array( 'GUI', 'GUE', 'GA', 'GO', 'GU', 'SCI', 'SCE', 'SC', 'CA', 'CO', 
                            'CU', 'QU', 'Q', 'CC', 'CK', 'G', 'ST', 'PH'); 
        $convGuOut = array( 'KI', 'KE', 'KA', 'KO', 'K', 'SI', 'SE', 'SK', 'KA', 'KO', 
                            'KU', 'K', 'K', 'K', 'K', 'J', 'T', 'F'); 
        $convVIn   = array( '/E?(AU)/', '/([EA])?[UI]([NM])([^EAIOUY]|$)/', '/[AE]O?[NM]([^AEIOUY]|$)/', 
                            '/[EA][IY]([NM]?[^NM]|$)/', '/(^|[^OEUIA])(OEU|OE|EU)([^OEUIA]|$)/', '/OI/', 
                            '/(ILLE?|I)/', '/O(U|W)/', '/O[NM]($|[^EAOUIY])/', '/(SC|S|C)H/', 
                            '/([^AEIOUY1])[^AEIOUYLKTPNR]([UAO])([^AEIOUY])/', '/([^AEIOUY]|^)([AUO])[^AEIOUYLKTP]([^AEIOUY1])/', '/^KN/', 
                            '/^PF/', '/C([^AEIOUY]|$)/',  '/E(Z|R)$/', 
                            '/C/', '/Z$/', '/(?<!^)Z+/', '/H/', '/W/'); 
        $convVOut  = array( 'O', '1\\\\3', 'A\\\\1', 
                            'E\\\\1', '\\\\1E\\\\3', 'O', 
                            'Y', 'U', 'O\\\\1', '9',- 
                            '\\\\1\\\\2\\\\3', '\\\\1\\\\2\\\\3', 'N', 
                            'F', 'K\\\\1', 'E', 
                            'S', 'SE', 'S', '', 'V'); 
    } 
    // Si il n'y a pas de mot, on sort immédiatement
    if ( $sIn === '' ) return '    '; 
    // On supprime les accents- 
    $sIn = strtr( $sIn, $accents); 
    // On met tout en minuscule- 
    $sIn = strtoupper( $sIn ); 
    // On supprime tout ce qui n'est pas une lettre
    $sIn = preg_replace( '`[^A-Z]`', '', $sIn ); 
    // Si la chaîne ne fait qu'un seul caractère, on sort avec.
    if ( strlen( $sIn ) === 1 ) return $sIn . '   '; 
    // on remplace les consonnances primaires
    $sIn = str_replace( $convGuIn, $convGuOut, $sIn ); 
    // on supprime les lettres répétitives 
    $sIn = preg_replace( '`(.)\\\\1`', '$1', $sIn ); 
    // on réinterprète les voyelles 
    $sIn = preg_replace( $convVIn, $convVOut, $sIn); 
 
    // on supprime les terminaisons T, D, S, X (et le L qui précède si existe)
    $sIn = preg_replace( '`L?[TDX]?S?$`', '', $sIn ); 
    // on supprime les E, A et Y qui ne sont pas en première position 
    $sIn = preg_replace( '`(?!^)Y([^AEOU]|$)`', '\\\\1', $sIn); 
    $sIn = preg_replace( '`(?!^)[EA]`', '', $sIn); 
    return substr( $sIn . '    ', 0, 4); 
} 
{% endhighlight %}

Tests
=====

Avec cette implémentation, on a par exemple :

<table>
  <tr><th>Mot</th><th>Soundex</th><th>Mot</th><th>Soundex</th></tr>
  <tr><td>Aymeric</td><td>EMRK</td><td>Emeric</td><td>EMRK</td></tr>
  <tr><td>Banane</td><td>BNN</td><td>Bananne</td><td>BNN</td></tr>
  <tr><td>Palper</td><td>PLP</td><td>Palpé</td><td>PLP</td></tr>
  <tr><td>Palpa</td><td>PLP</td><td>Poulpe</td><td>PULP</td></tr>
  <tr><td>Mario</td><td>MRYO</td><td>Marion</td><td>MRYO</td></tr>
</table>

On a par contre des résultats moyens lorsque des H séparent des voyelles faibles : par exemple _Mouahaha_ donne MU (contre M avec la version précédente).

~test~
<script type="text/javascript" src="/ajax.js"></script>
<script type="text/javascript">
function get_soundex()
{
    Ajax.update_html("sdx_result", "/soundex_fr.php?text=" + encodeURIComponent(document.getElementById("sdx_form").value));
    return false;
}
</script>
<div>
<form action="" method="get" onsubmit="return false;">
   <p>
      <strong>Entrer un texte à convertir : </strong>
      <input type="text" id="sdx_form" name="sdx_form" value="" />
      <input type="submit" name="submit" value="Obtenir le Soundex"  onclick="get_soundex(); return false;"/><br />
      <strong>Résultat : <span id="sdx_result" style="color: red"> </span></strong>
   </p>
</form>
</div>

_N'hésitez pas à faire des tests et à me faire part des résultats qui semblent anormaux._

Conclusion
==========

Cette version est loin d'être parfaite, mais elle est plus adaptée au français que celle de Frédéric Brouard. Elle n'est d'ailleurs pas terminée et risque d'évoluer dans un futur proche, en particuliers pour ce qui est des tables de transcriptions et de prédominances.
