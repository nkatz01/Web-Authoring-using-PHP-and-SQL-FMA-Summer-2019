<?php

require_once dirname(__FILE__) . '/includes/config.inc.php';

/**
 * if no lang parameter (and no page id) is set, check whether the reason is because cookies have already been set for this user. If it is set already, take the lang setting from there, otherwise, user isn't interested in setting language explicitly and so set language to default = English.
 */

if ((!isset($_GET['page'])) && (!isset($_GET['lang']))) { //neither are set
    
    
    
    $id = 'home';
    
    if (isset($_COOKIE['lang'])) {
        
        $pathToLangFile = $config['language'] . $_COOKIE['lang'];
    } else {
        $pathToLangFile = $config['en'];
        
        
    }
    
    
    
}

/**
 * Else if either (but not both) were set by user, set the other variable accordingly. 
 *  
 *
 */

elseif (isset($_GET['page']) xor (isset($_GET['lang']))) {
    if (!isset($_GET['page'])) { //only lang is set (reset lang coockie)
        
        $id = 'home';
        
        $pathToLangFile = $config['language'] . htmlentities($_GET['lang']);
        setcookie('lang', htmlentities($_GET['lang']), 2147483647, '/');
        
        
    } elseif (!isset($_GET['lang'])) { //only page is set
        $id = $_GET['page'];
        if (!isset($_COOKIE['lang'])) {
            
            
            $pathToLangFile = $config['en'];
        } else {
            
            
            $pathToLangFile = $config['language'] . $_COOKIE['lang'];
        }
    }
}

/**
 * Else if both parameters were explicitly set by user, remember to reset language cookie accordingly.
 */

else { //all are set
    
    $pathToLangFile = $config['language'] . $_GET['lang'];
    $id             = $_GET['page'];
    setcookie('lang', htmlentities($_GET['lang']), 2147483647, '/');
}

$pageTitle   = '';
$heading     = '';
$content     = '';
$formFdbk    = '';
$gallContent = '';
$allHeader   = $config['html_header_tmp'];
$tpl         = file_get_contents($allHeader);
$allFooter   = file_get_contents($config['html_footr_tmp']);

/**
 * 
 *Avoid site from breaking in the event the user puts in an unrecognized language request
 *
 */
try {
    if (is_file($pathToLangFile)) {
        require_once $pathToLangFile;
        /**
         * If the user only set the page variable to 'home' or the page variable and also a valid language to lang, or even when the user set nothing at all, the normal flow of events will continue through including the 'home' page.
         */

        switch ($id) {
            case 'home':
                include 'views/home.php';
                break;
            default:
                include 'views/404.php';
        }
        
        /**
         * If image is not set, for upload form and the gallery is displayed. 
         */
        
        if (!isset($_GET['image'])) {
            
            $keys[] = '[+styles+]';
            $keys[] = '[+pageTitle+]';
            $keys[] = '[+app_name+]';
            $keys[] = '[+home+]';
            $keys[] = '[+nav+]';
            $keys[] = '[+heading+]';
            $keys[] = '[+content+]';
            $keys[] = '[+images+]';
            
            $values[] = 'styles/styles.css';
            $values[] = $pageTitle;
            $values[] = $lang['app_name'];
            $values[] = $lang['home'];
            $values[] = 'index.php';
            $values[] = $heading;
            $values[] = $formFdbk;
            $values[] = $gallContent;
            $content  = str_replace($keys, $values, $tpl);
            $content .= $allFooter;
        }
        /**
         * Else, only json is displayed
         *
         */
        else {
            header('Content-type: application/json');
            $content .= $gallContent;
        }
    }
    
    
    
    
    else {
        throw new Exception('Language ' . basename($pathToLangFile) . ' not recognized');
    }
}

catch (Exception $ex) {
    $yesterday = time() - (24 * 60 * 60);
    setcookie('lang', '', $yesterday, '/');//delete the unrecognized language cookie so that page doesn't break again when loaded afresh.
    
    /**
     * display an almost empty page except a warning that the language requested is unrecognized.
     */

    $keys[] = '[+styles+]';
    $keys[] = '[+pageTitle+]';
    $keys[] = '[+app_name+]';
    $keys[] = '[+home+]';
    $keys[] = '[+nav+]';
    $keys[] = '[+heading+]';
    $keys[] = '[+content+]';
    $keys[] = '[+images+]';
    
    $values[] = 'styles/styles.css';
    $values[] = $pageTitle;
    $values[] = '';
    $values[] = '';
    $values[] = 'index.php';
    $values[] = $heading;
    $values[] = 'Fatal Excpetion raised : ' . $ex->getMessage();//cannot use language file as language not set.
    ;
    $values[] = $gallContent;
    $content .= str_replace($keys, $values, $tpl);
    $content .= $allFooter;
    
}
/**
 * In any event, there will be some content to echo out.
 */

finally{
		echo($content);
		}

?>