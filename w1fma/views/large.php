<?php

require_once   '../includes/config.inc.php';
 require_once $config['functions_scrip'];




	$allHeader = $config['html_header_tmp'];
	$tpl       = file_get_contents($allHeader);
		$thumbnailTemp = $config['thumb_temp_htm'] ;   

  $thumbnailTemp = file_get_contents($thumbnailTemp);
		 
			$keys[] = '[+styles+]';
			$keys[]    = '[+pageTitle+]';
			$keys[] = '[+app_name+]';
			$keys[] = '[+home+]'; 
			$keys[] = '[+nav+]'; 
			$keys[]    = '[+heading+]';
			$keys[]    = '[+content+]';
			$keys[] = '[+images+]';
			$values[] =  '../styles/styles.css'; 
			$values[]  = $pageTitle;
			$values[] = $lang['app_name']; 
			$values[] = $lang['home'];
			$values[]  = '../index.php';
			$values[]  = $heading;
			$values[]  = '';
			$values[]  = $largeContent;
		  $largeContent   = str_replace($keys,$values , $tpl);	
		  echo( $largeContent);
		  include_once $config['html_footr_tmp']  ;
		  

?>