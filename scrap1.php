<?php

// $string = 'Charge Desc: 1 Charge Description: DUI ALCOHOL OR DRUGS 1ST OFFENSE Charge Bond: 500.00';
// $wordlist = array("Charge Desc: ", "Desc", "Charge","1","Bond","500.00",":");

// foreach ($wordlist as &$word) {
//     $word = '/\b' . preg_quote($word, '/') . '\b/';
// }

// echo $string = preg_replace($wordlist, '', $string);

$fruits = array("d" => "lemon", "a" => "orange", "b" => "banana", "c" => "apple");
asort($fruits);
foreach ($fruits as $key => $val) {
    echo "$key = $val\n";
}
?>