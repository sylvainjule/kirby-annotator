<?php 

function imageannotatorTranslation($string) {
    
    $imageannotatorTranslations = require __DIR__ . DS . 'translations.php';
    $language = substr(site()->user()->language(), 0, 2 );
    if (!array_key_exists($language, $imageannotatorTranslations)) {
      $language = 'en';
    }
    $imageannotatorTranslation = $imageannotatorTranslations[$language];
    
    if(array_key_exists($string, $imageannotatorTranslation)) {
      $string = $imageannotatorTranslation[$string];
    }
    return $string;
    
}