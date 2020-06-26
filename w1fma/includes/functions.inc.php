<?php

/**
 * 
 * @param $temp, template to be populated, $objectFields, the name of the fields of the values in $rows_arr.
 * @return a template populated with the values in $rows_arr, assuming these placeholders are found in the template. If they aren't, an empty string is placed there instead.
 */

function replaceTemplate($temp, $objectFields, $rows_arr)
{
    for ($i = 0; $i < count($objectFields); $i++) {
        
        $keys[$i]   = '[+' . $objectFields[$i] . '+]';
        $values[$i] = $rows_arr[$i];
        
    }
    $temp  = str_replace($keys, $values, $temp);
    $regex = '/\[\+.*?\+\]/';
    return preg_replace($regex, '', $temp);
    
    
}

/**
 * Function that defines the criteria how the usort() in main.php should operate. 
 * @param an array files to check
 * @param an array of valid thumbpaths (extracted from  a table) to use for comparing the filenames against by help of the php function basname().
 * 
 */

function assureDtbsFileCorresp($files, $thumbpaths, $link_to_file)
{
    $i = 0;
    while ($i < (count($files))) {
        $found = false;
        $j     = 0;
        while ($j < (count($thumbpaths)) && strcmp($files[$i], basename($thumbpaths[$j])) >= 0) {
            
            if (strcmp($files[$i], basename($thumbpaths[$j])) == 0) { //we use strcmp() just in case the file system is case sensitive and allows two images with the same name so long as they're not the same case. 
                $found = true;
            }
            
            $j++;
        }
        
        
        if (!($found)) {
            if (is_file($link_to_file . $files[$i])) {
                unlink($link_to_file . $files[$i]);
            }
        }
        $i++;
    }
}

 function formatTime($date)
    {
        
        $date_string = strtotime('YY MM DD', $date);
        return $date_string;
    }




?>