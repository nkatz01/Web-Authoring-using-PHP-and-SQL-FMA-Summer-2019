<?php
$pageTitle = '404';
$heading.=$lang['404_page_1']."$id".$lang['404_page_2']; 
header('refresh:1.5;url='. $_SERVER['PHP_SELF']);//https://thisinterestsme.com/redirect-page-after-five-seconds/
 ?>