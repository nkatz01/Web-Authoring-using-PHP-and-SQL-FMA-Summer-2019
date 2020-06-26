<?php
/**
 * $formIssueFree will serve in this script and in main.php to indicate whether an attempt was made to submit the form.
 */
$formIssueFree = false;
$fileExists    = false;
require_once $config['functions_scrip'];

/**
 * Prepare the fields of the upload form page for the user to use.
 */
$uploadPage = $config['upload_temp_htm'];
$formFdbk .= file_get_contents($uploadPage);
$keys[]   = '[+url+]';
$keys[]   = '[+head_1+]';
$keys[]   = '[+form_head+]';
$keys[]   = '[+ent_titl+]';
$keys[]   = '[+ent_desc+]';
$keys[]   = '[+submit+]';
$values[] = htmlentities($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8');
$values[] = $lang['head_1'];
$values[] = $lang['form_head'];
$values[] = $lang['ent_titl'];
$values[] = $lang['ent_desc'];
$values[] = $lang['submit'];
$formFdbk = str_replace($keys, $values, $formFdbk);

function my_autoloader($class)
{
    include 'classes/' . $class . '.php';
}
spl_autoload_register('my_autoloader');


if (isset($_POST['singlefileupload'])) {
    
    
    $newPathUpToBase = $config['upload_dir'];
    
    if ($_FILES['userfile']['error'] === UPLOAD_ERR_OK) {
        
        if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
            
            //save the user's name for file to assure later that the same file isn't uploaded twice.
            $oldNameOfFile  = pathinfo($_FILES['userfile']['name'], PATHINFO_FILENAME);
            $newNameForFile = $oldNameOfFile . '_' . date('Ymd') . '_' . time();
            $fileExt        = pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
            $oldNameOfFile .= '.' . $fileExt;
            $tempPath = $_FILES['userfile']['tmp_name'];
            
            /**
             * Assure user doesn't upload same file twice by taking the SHA1 from the current one and comparing it with the SHA1s of all the other files in the upload folder.
             *
             */
            $thisSha1 = sha1(file_get_contents($tempPath));
            $dir      = new DirectoryIterator($newPathUpToBase);
            foreach ($dir as $fileinfo) { //https://stackoverflow.com/questions/4202175/php-script-to-loop-through-all-of-the-files-in-a-directory
                if (!$fileinfo->isDot()) {
                    $sha1 = sha1(file_get_contents($newPathUpToBase . $fileinfo->getFilename()));
                    if ($sha1 == $thisSha1) {
                        $fileExists = true;
                    }
                }
            }
            
            if (mime_content_type($tempPath) === 'image/jpeg') //assure that it's an jpeg image mime type.
                {
                $newNameAndPath = $newPathUpToBase . $newNameForFile . '.' . $fileExt;
                
                if (!$fileExists && (trim($_POST['image_title']) != '') && (trim($_POST['image_desc']) != '')) { //assure also that both text fields have been filled out.
                    
                    
                    
                    
                    
                    if (move_uploaded_file($tempPath, $newNameAndPath)) {
                        /**
                         *if the image details such as size are not extractable from the file, we cannot allow the file to be uploaded as we may not be able to resize the image as per the specifications
                         */
                        if (getimagesize($newNameAndPath) != false) {
                            
                          
                            
                            
                            $formIssueFree = true;
                        } else {
                            $formFdbk .= $lang['param_fetch_fail'];
                            unlink($newNameAndPath); //delete the image, already moved, upon failure to extract image details.
                        }
                        
                    } else {
                        $formFdbk .= $lang['gen_prob_encoutr'];
                        
                    }
                }
                
                else { //case file already exists
                    if ((trim($_POST['image_title']) != '') && (trim($_POST['image_desc']) != '')) {
                        $formFdbk .= $lang['file_exis'];
                    }
                    
                    else { //case one of the other fields is empty
                        $formFdbk .= $lang['fill_all_flds'];
                    }
                }
            }
            
            
            else { //case wrong mime type
                
                
                
                $formFdbk .= $lang['file_wrong_type'];
                
            }
            
            
        }
        
        else { //file not uploaded via HTTP post. Maybe malicious attack.
            
            
            $formFdbk .= $lang['file_no_safe'];
            
        }
    }
    
    else //case file not uploaded due to technical fault
        {
        
        if ($_FILES['userfile']['error'] === UPLOAD_ERR_INI_SIZE) {
            
            
            $formFdbk .= $lang['mx_file_size_php'];
        } elseif ($_FILES['userfile']['error'] === UPLOAD_ERR_FORM_SIZE) {
            
            $formFdbk .= $lang['mx_file_size_form'];
        } elseif ($_FILES['userfile']['error'] === UPLOAD_ERR_PARTIAL) {
            
            $formFdbk .= $lang['partial_upload'];
        } elseif ($_FILES['userfile']['error'] === UPLOAD_ERR_NO_FILE) {
            
            $formFdbk .= $lang['file_no_upload'];
        }
        
        else {
            
            $formFdbk .= $lang['unknown_issue'];
            $formFdbk .= $_FILES['userfile']['error'];
        }
        
        
        
    }
    
    
    
    
    
    
    
    if ($formIssueFree == false) { //have content user did fill out, re-appear etc - test other fields.
        
        if (trim($_POST['image_title']) != '') {
            
            $formFdbk = str_replace('[+error1+]', '', $formFdbk);
        } else {
            $formFdbk = str_replace('[+error1+]', $lang['ent_file_ttl'], $formFdbk);
            
        }
        
        if (trim($_POST['image_desc']) != '') {
            
            $formFdbk = str_replace('[+error2+]', '', $formFdbk);
            
        } else {
            $formFdbk = str_replace('[+error2+]', $lang['ent_file_desc'], $formFdbk);
            
        }
        /**
         * regardless, whether user filled out field or left field blank, field content that re-appears will be correct.
         */
        $formFdbk = str_replace('[+title+]', htmlentities($_POST['image_title']), $formFdbk);
        $formFdbk = str_replace('[+description+]', htmlentities($_POST['image_desc']), $formFdbk);
        
    } else { //case no Issue with form, i.e. file has been moved okay, clear fields ready for next upload.
        $formFdbk = str_replace('[+title+]', '', $formFdbk);
        $formFdbk = str_replace('[+description+]', '', $formFdbk);
        $formFdbk = str_replace('[+error1+]', '', $formFdbk);
        $formFdbk = str_replace('[+error2+]', '', $formFdbk);
        
        
        
        
        
        
    }
    
    
    
    
}

else { //case attempt at submission has not been made at all, prevent placeholders from displaying.
    $formFdbk = str_replace('[+title+]', '', $formFdbk);
    $formFdbk = str_replace('[+description+]', '', $formFdbk);
    $formFdbk = str_replace('[+error1+]', '', $formFdbk);
    $formFdbk = str_replace('[+error2+]', '', $formFdbk);
    
    
    
}





?>

    
 
