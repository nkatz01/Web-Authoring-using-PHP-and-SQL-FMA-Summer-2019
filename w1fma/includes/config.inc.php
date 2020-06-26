<?php
/**
 * Configuration settings for My Photo Gallery Applications using MySQL and PHP (W1_FMA)
 *
 */

/**
 * Absolute path to application root directory (one level above current dir)
 */
$config['app_dir'] = dirname(dirname(__FILE__));

/**
 * Credentials to connect to the SQL database deployed together with the web application
 */
$config['app_name'] = 'Photo Gallery Application';
$config['db_user'] = 'xxxxx'; 
$config['db_pass'] = 'xxxxx'; 
$config['db_host'] = 'mysqlsrv.dcs.bbk.ac.uk'; 
$config['db_name'] = 'xxxxxx'; 
$config['port'] = 'xxxx';
 
 /**
 * Absolute path to directories/files requested during various processing stages, which include templates, language files and function calls.
 */
$config['en']= $config['app_dir'] . '/languages/' . 'en.php';
$config['language']= $config['app_dir'] . '/languages/';
$config['thumbs_dir'] = $config['app_dir'].'/thumbs/';
$config['upload_dir'] = $config['app_dir'] . '/uploads/';
$config['upload_temp_htm']=$config['app_dir'] .'/templates/uploadForm.html';
$config['thumb_temp_htm'] = $config['app_dir'].'/templates/listThumbs.html';
$config['functions_scrip'] = $config['app_dir'] .'/includes/functions.inc.php';
$config['html_header_tmp'] = $config['app_dir'].'/templates/head.html';
$config['html_footr_tmp'] =$config['app_dir'].'/templates/footer.html'; 
$config['en_php']='en.php';
$config['yi_php']='en.php';

 
date_default_timezone_set('Europe/London'); 
 ?>
