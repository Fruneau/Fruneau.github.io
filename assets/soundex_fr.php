<?php
/***************************************************************************
 *  Copyright (C) 2007 Polytechnique.org                                   *
 *  http://opensource.polytechnique.org/                                   *
 ***************************************************************************/

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
        $convVOut  = array( 'O', '1\3', 'A\1',
                            'E\1', '\1E\3', 'O',
                            'Y', 'U', 'O\1', '9', 
                            '\1\2\3', '\1\2\3', 'N',
                            'F', 'K\1', 'E',
                            'S', 'SE', 'S', '', 'V');
    }
    // Si il n'y a pas de mot, on sort immédiatement 
    if ( $sIn === '' ) return '    ';
    // On supprime les accents 
    $sIn = strtr( $sIn, $accents);
    // On met tout en minuscule 
    $sIn = strtoupper( $sIn );
    // On supprime tout ce qui n'est pas une lettre 
    $sIn = preg_replace( '`[^A-Z]`', '', $sIn );
    // Si la chaîne ne fait qu'un seul caractère, on sort avec. 
    if ( strlen( $sIn ) === 1 ) return $sIn . '   ';
    // on remplace les consonnances primaires 
    $sIn = str_replace( $convGuIn, $convGuOut, $sIn );
    // on supprime les lettres répétitives
    $sIn = preg_replace( '`(.)\1`', '$1', $sIn );
    // on réinterprète les voyelles
    $sIn = preg_replace( $convVIn, $convVOut, $sIn);

    // on supprime les terminaisons T, D, S, X (et le L qui précède si existe) 
    $sIn = preg_replace( '`L?[TDX]?S?$`', '', $sIn );
    // on supprime les E, A et Y qui ne sont pas en première position
    $sIn = preg_replace( '`(?!^)Y([^AEOU]|$)`', '\1', $sIn);
    $sIn = preg_replace( '`(?!^)[EA]`', '', $sIn);
    return substr( $sIn . '    ', 0, 4);
}

setLocale(LC_ALL, "fr_FR.UTF-8");

function is_utf8($s)
{
    return @iconv('utf-8', 'utf-8', $s) == $s;
}

if (@$_GET['action'] == 'download') {
    header("Content-Type: text/plain; charset=utf-8; name=\"soundex_fr.php\"");
    header("Content-Disposition: attachment; filename=\"soundex_fr.php\"");
    readfile(__FILE__);
    exit;
}

$text = @$_GET['text'];
if ($text && !is_utf8($text)) {
    $text = utf8_encode($text);
}

header("Content-Type: text/html; charset=utf-8");
echo soundex_fr($text);

// vim:set et sw=4 sts=4 sws=4 enc=utf-8:
?>
