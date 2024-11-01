<?php

// get language from ini file
function wfsLoadTranslatingLangText_SimpleContact($defaultLang) 
{
    global $translatingTextArr_SimpleContact;	
	$defaultLang = ($defaultLang == '' ? 'en' : $defaultLang);
	$lang = $_REQUEST["lang"] == '' ? $defaultLang : $_REQUEST["lang"] ;
    $langFilePath = dirname(__FILE__) . '/../languages/'.$lang.'.ini';
    $translatingTextArr_SimpleContact = parse_ini_file($langFilePath);
}

// translate text with current language
function wfsTranslateWithDefault_SimpleContact($text, $default) 
{ 
    global $translatingTextArr_SimpleContact;
    return $translatingTextArr_SimpleContact[$text] != '' ? $translatingTextArr_SimpleContact[$text] : $default;
}
function wfsTranslate_SimpleContact($text) 
{ 
    return wfsTranslateWithDefault_SimpleContact($text, $text);
}

?>
