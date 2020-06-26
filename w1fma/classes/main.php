<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require_once $config['functions_scrip'];
require_once 'Image.php';
$thumbnailTemp = $config['thumb_temp_htm'];

$db = new myDB($config, $lang);
if ($db->isConnecActive() == true) {
    
    if (($formIssueFree == true) && !(isset($_GET['image']))) { //case new image was submitted and also image details (in json format) were not requested via the 'image' variable.
        
        /**
         * Prepare an sql statement to insert all the details about the image into the database. The details we want to store are: 
         * 1. A path to the folder where we're going to keep the thumbnails.
         * 2. A path to the folder where we're going to keep the larger version of the image.
         * 3. The image title submitted by the user. 
         * 4. The image description submitted by the user.
         * 5. The height extracted from the image info.
         * 6. The width extracted from the image info.
         * 7. The original filename as name that the file had when it was submitted.
        */
         
         $sql = "INSERT INTO Images VALUES ( NULL ,'media/thumbs/" . $db->real_escape_string(basename($newNameAndPath)) ."','media/large/" . $db->real_escape_string(basename($newNameAndPath)). "','". $db->real_escape_string($oldNameOfFile)."','". $db->real_escape_string($_POST['image_title'])."','". $db->real_escape_string($_POST['image_desc'])."',". (getimagesize($newNameAndPath))[0].",".(getimagesize($newNameAndPath))[1].",'".$thisSha1."')";
         
        
        $results_success = $db->alterTable($sql); //statement performs query and populates myDB instance's variable $objects, with retrieved results
        
        if ($results_success != false) {
            $sql             = "SELECT * FROM Images WHERE id =" . $db->insert_id; //select image just uploaded in order to immediately display it to the user as soon as the page refreshes.
            $results_success = $db->runQuery($sql); //retrieve the details from the dtbs for that image
            if ($results_success === true) {
                $objects_array = $db->getObjects(); //in this event, only returns once object as only one row from the dtbs was requested.
                $img           = new Image(htmlentities($objects_array[0]->id), htmlentities($objects_array[0]->thumbpath), htmlentities($objects_array[0]->largepath), htmlentities($objects_array[0]->name), htmlentities($objects_array[0]->width), htmlentities($objects_array[0]->height));
                
                try {
                    if (strcmp($objects_array[0]->sha_1, $thisSha1) == 0) { //checks that image uploaded is same returned from dtbs and no one else inserted something in between.
                        
                        //assure that ima_resize, function belonging to class image, does its job successfully and created the two resized copies of the image.
                        if (($thumbCreated = $img->img_resize($newNameAndPath, 150, 0, $img)) == true && ($largeCreated = $img->img_resize($newNameAndPath, 600, 1, $img) == true)) {
                              /**
                             *  
                             Because the new image is appended to the existing images in the gallery, when the gallery already has a number of images up, it may not be intuitive to the user to realize they need to scroll down all the way to the end on the page to see that their image has loaded successfully.
                             */
                            
                            $formFdbk .= $lang['upld_succ'];
                            
                        }
                        
                        else {
                             throw new Exception($lang['copy_fail']);
                        }
                    } else {
                        throw new Exception($lang['dtbs_busy']);
                    }
                }
                catch (Exception $ex) {
                    /**
                     * if the copying failed, or if not same image returned is not the same as image inserted, delete the image from uploads in order to enable the user to upload again the image. 
                     */
                    unlink($newNameAndPath);
                    $sql             = "delete from Images WHERE id =" . $img->getID(); //also delete from database as we don't want broken img tags appearing in the gallery.
                    $results_success = $db->alterTable($sql);
                    $formFdbk .= $lang['gen_exc'] . $ex->getMessage();
                }
                
                
                
                
                
            }
            
            else { //case retrieval, frm dtabs, of that uploaded image failed
                $formFdbk .= $db->getQueryFdbk();
                $formFdbk .= $lang['img_fetch_fail'];
                
            }
        }
        
        
        else { //case insertion of the data associated with that newly uploaded image failed.
            $formFdbk .= $db->getQueryFdbk();
            $formFdbk .= $lang['dtbs_insert_fail'];
            /**
             *Delete the image from uploads in order to enable the user to upload again the image.
             */
            unlink($newNameAndPath);
            
            
        }
        
        
        
        
    }
    
    /**
     * In any event, even in a general case where the user didn't make an attempt to load an image but just loads the page, we want to display the gallery with all the stored images.
     */
    $sql             = "SELECT * FROM Images;"; // select all images from database
    $results_success = $db->runQuery($sql);
    
    if ($results_success === true) {
        
        $thumbnailTemp = file_get_contents($thumbnailTemp);
        
        $objects_array = $db->getObjects();
        $objectFields  = $db->getFields(); //get field names for retrieved rows from dtabs.
        foreach ($objects_array as $obj) {
            $images["$obj->id"] = new Image(htmlentities($obj->id), htmlentities($obj->thumbpath), htmlentities($obj->largepath), htmlentities($obj->name), htmlentities($obj->width), htmlentities($obj->height));
            $images["$obj->id"]->addTitle(htmlentities($obj->title)); //title and description are not in general necessary upon class Image creation but are needed in our case.
            $images["$obj->id"]->addDesc(htmlentities($obj->description));
        }
        
        
        if (!isset($_GET['image'])) { //only if image details (in json format) were not requested via the 'image' variable.
            foreach ($images as $key => $obj) {
                if (is_file($obj->getThumbPath()) && is_file($obj->getlargePath())) {
                    /**
                     *Only proceed if both copies still exist in their prospective locations. Protects against movement or deletion of large or thumbnail image files
                     */
                    $gallDispDetails                    = array(
                        $obj->getThumbPath(),
                        $obj->getTitle(),
                        $obj->getTitle(),
                        'thumImg',
                        $obj->getID()
                    );
                    $detailsForLrgPage['details'][$key] = $gallDispDetails;
                    /**
                     * because we want to keep the details also for when after the loop terminates, later for the processing of the large format of a given image, we're now creating a 2D array, with their IDs as the row identifier.
                     */
                    array_shift($detailsForLrgPage['details'][$key]); //remove the thumbpath from the array as it's 'largepath that's needed, as the source, for the large version of an image.
                    array_unshift($detailsForLrgPage['details'][$key], $obj->getlargePath()); //add the largepath
                    array_unshift($detailsForLrgPage['details'][$key], $obj->getDesc()); //add the description
                    
                    
                    $additDetail = http_build_query(array(
                        'page' => 'home',
                        'format' => 'large',
                        'id' => $obj->getID()
                    ), '', '&amp;');
                    /**
                     * create a query string/array containing 3 variables, page, format and (database) id associated with the image in whose href tag we're placing this query string.
                     */
                    
                    $linkToLrgPage = 'index.php?' . $additDetail; //we still want a hit on this image to come through the index.php page.
                    
                    
                    array_unshift($gallDispDetails, $linkToLrgPage);
                    /**
                     *Now we want to add the 'href link value' we've put together to the array of image details we've gathered for the gallery display and exchange all the place holders in the thumbnail template with them.
                     */
                    
                    $gallContent .= replaceTemplate($thumbnailTemp, array(
                        'linkto',
                        'source',
                        'title',
                        'alt',
                        'styleImg',
                        'id'
                    ), $gallDispDetails);
                } else {
                    if (is_file($config['upload_dir'] . basename($obj->getThumbPath()))) { //if it's not deleted already
                        unlink($config['upload_dir'] . basename($obj->getThumbPath())); //delete from upload in order to allow the user to upload again the image.
                    }
                    $sql = "delete from Images WHERE id =" . $obj->getID();
                    
                    /**
                     *also proceed to delete from database the records associated with these images in order to avoid broken img tags.
                     */
                    $results_success = $db->alterTable($sql);
                    
                    
                }
                
                
            }
            
            /**
             *  The following section does a general check assuring that if any record were deleted in dtbs, also delete the images, associated with those records, from uploads to allow the user to upload it again.
             */
            
            $dir   = $config['upload_dir'];
            $files = scandir($dir);
            array_splice($files, 0, 2); //remove the first two indices which are entries to the current directory and the parent directory.
            
            usort($images, array(
                $obj,
                "compareByFilename"
            )); //https://www.php.net/manual/en/function.usort.php
            
            $thumbpaths = array_map(array(
                $obj,
                "getThumbPaths"
            ), $images); //extracts thumbpaths property only to make a 1D array of them as more convenient to work with.
            assureDtbsFileCorresp($files, $thumbpaths, $config['upload_dir']); //call to function which removes the files from the uploads folder for which there is no corresponding record in the database. 
            
            
        } else {
            
            $imgId = $_GET['image']; //case image details (in json format) were requested via the 'image' variable.
            
            if ($imgId > 0) {
                if (isset($images["$imgId"])) { //assures that the id requested is legit in that it correctly corresponds to an id held in the $images array.
                    $obj         = $images["$imgId"];
                    $assoArr= array('filename' =>htmlentities($obj->getName()),$objectFields[4] =>$obj->getTitle(),$objectFields[5] =>  $obj->getDesc(),$objectFields[6] => getimagesize($obj->getlargePath())[0],$objectFields[7] => getimagesize($obj->getlargePath())[1]) ; 
                    $gallContent = json_encode($assoArr, JSON_UNESCAPED_UNICODE);
                    
                    
                    
                    
                } else {
                    $gallContent = $lang['img_no_exis'];
                }
            } else {
                $gallContent = $lang['img_no_exis'];
            }
        }
        
        
    }
    
    else { //case selecting all images from database failed
        if ($db->getQueryFdbk() == $lang['db_no_resu_error']) { //case the reason why result not successful because there were no results at all returned from dtbs, make sure that all physical files are deleted from upload location - protects against deletion of all records in dtbs but no clean-up of physical files.
            $dir   = $config['upload_dir'];
            $files = scandir($dir);
            array_splice($files, 0, 2);
            foreach ($files as $file) {
                unlink($config['upload_dir'] . $file);
            }
        }
        $formFdbk .= $db->getQueryFdbk();
    }
    
    
    
}


else { //case there isn't an active database connection.
    $formFdbk .= $db->getStatusMsg();
}





?>



