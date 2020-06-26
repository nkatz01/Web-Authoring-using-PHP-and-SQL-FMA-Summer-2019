<?php
$pageTitle .= '';
/**
 * The following line provides for including the php script that will take care of preparing the upload form for display, validate field input and deal with the first stage processing the uploaded file, i.e. validating and moving the uploaded file to a new location.
 */
 
require_once $config['app_dir'] . '/includes/processForm.php';
/**
 * The following line provides for processing and storing the data input and all necessary information about the file as well as retrieving it from the database and preparing it for displaying it in whichever manor requested by the user.
 */

require_once $config['app_dir'] . '/classes/main.php';
 
 
if (!isset($_GET['format'])) { //if variable 'format' isn't set, request is coming not from hit on a particular individual image.
    $pageTitle = $lang['home'];
    $heading .= $lang['home_page'];
}

else {
    try { //although 'format' is set internally, by page main.php, this assures that 'format' isn't changed externally 
        if ($_GET['format'] == 'large') {
            $largeContent = '';
            $pageTitle    = $lang['large'];
            $heading      = $lang['back_link'];
            try { //assure that all 3 variable are set and that the id in the query string is legit in that it correctly corresponds to the image that's just ben clicked. (Protects against external tampering by user, in the address bar, after the image has been clicked).
                if (isset($_GET['id']) && (isset($detailsForLrgPage['details'][$_GET['id']])) && ($detailsForLrgPage['details'][$_GET['id']][5] == $_GET['id'])) {
                    $largeContent .= replaceTemplate($thumbnailTemp, array(
                        'desc',
                        'source',
                        'linkto',
                        'title',//which is in a p tag underneath the image - to be pop with title of our image
                        'id',//which is html title attribute - also to be populated with title of our image
                        'alt', //gen alt attribute of an img tag - also to be populated with title of our image
                        'styleImg'
                    ), array(
                        $detailsForLrgPage['details'][$_GET['id']][0],
                        $detailsForLrgPage['details'][$_GET['id']][1],
                        'index.php',
                        $detailsForLrgPage['details'][$_GET['id']][2],
                        $detailsForLrgPage['details'][$_GET['id']][3],
						$detailsForLrgPage['details'][$_GET['id']][3],
                        'largeImg'
                    ));
                    $formFdbk    = '';
                    $gallContent = $largeContent;
                } else {
                    throw new Exception($lang['id_unrecog']);
                }
            }
            catch (Exception $ex) {
                $formFdbk .= $lang['gen_exc'] . $ex->getMessage();
            }
        } else {
            throw new Exception($lang['format_unrecog']);
            
        }
    }
    catch (Exception $ex) {
        $formFdbk .= $lang['gen_exc'] . $ex->getMessage();
    }
    
    
    
}

?>
