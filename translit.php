<?
function translStr($string) {
    $string = transliterator_transliterate("Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();", $string);
    $string = preg_replace('/[-\s]+/', '-', $string);
    return trim($string, '-');
}