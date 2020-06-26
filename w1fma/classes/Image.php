<?php
/**
 * Each instance of Class Image represents an Image object together with its attributes such as width and height, title and description etc. It also includes both instance and static functions to query for some of their attributes, provided upon creation of the instance, add attributes or operate on them. 
 *
 */
class Image
{
    /**
     * @param string $thumbpath holds the path to the folder in which a thumbnail version of the image is supposed to reside.
     * @param string $tlargepath holds the path to the folder in which a larger version of the image is supposed to reside.
     * @param string $title holds the title of an image provided by the user.
	 * @param string $name holds the name the image had when it was first uploaded
     * @param string $description holds the description  of an image provided by the user.
     * @param int $width holds the width of an image, extracted from the image details by the aid of a php provided function such as getimagesize().
     * @param int $height holds the height of an image, extracted from the image details by the aid of a php provided function such as getimagesize().
     * @param int $id holds the primary key of the record in the table that holds the details of $this image instance. 
     * @param int $THUMB, a static CONSTANT variable to which to compare the local parameter that the user passes, to indicate the size that the user is requesting the image to be copied/resized as.
     * @param int $LARGE, a static CONSTANT variable to which to compare  the local parameter that the user passes, to indicate the size that the user is requesting the image to be copied/resized as.
     *
     */
    private $thumbpath;
    private $largepath;
    private $title;
	private $name; 
    private $description;
    private $width;
    private $height;
    private $id;
    private static $THUMB = 0;
    private static $LARGE = 1;
    
    /**
     * Image constructor, receives a number of parameters crucial for the purpose of  retrieval, resizing and presentation of an image. For the purpose of this application, the parameters come from a table in which we will have stored the details associated with a given image, after the image was uploaded by the user.
     * @param int $id, primary key of the record in the table that holds the details of $this image instance. 
     * @param string $thumbpath the path to which we will order function img_resize() to store the thumbnail version of the this image after successful copying/resizing.
     * @param string $largepath the path to which we will order function img_resize() to store the thumbnail version of the this image after successful copying/resizing.
	 * @param string $name the name the image had when it was first uploaded
     * @param int $width the original width of the image extracted from the dtbs.
     * @param int $height the original height of the image extracted from the dtbs.
     */

    public function __construct($id, $thumbpath, $largepath, $name, $width, $height)
    {
        $this->id        = $id;
        $this->thumbpath = $thumbpath;
        $this->largepath = $largepath;
		$this->name = $name; 
        $this->width     = $width;
        $this->height    = $height;
    }
    
    /**
     * @param string $title associated with this image instance. For the purpose of this application, this parameter is provided by the user, as an additional piece of information, upon image upload.
     * 
     */

    public function addTitle($title)
    {
        $this->title = $title;
        
    }
    /**
     * @param string $title associated with this image instance. For the purpose of this application, this parameter is provided by the user, as an additional piece of information, upon image upload.
     *
     */

    public function addDesc($description)
    {
        
        $this->description = $description;
    }
    
    /**
     * @return, thumbpath associated with this Image.
     *
     */
    
    public function getThumbPath()
    {
        return htmlentities($this->thumbpath);
    }
    /**
     * @return id associated with this Image.
     *
     */
    public function getID()
    {
        return htmlentities($this->id);
    }
    /**
     * @return description associated with this Image.
     *
     */
    public function getDesc()
    {
        
        if (isset($this->description)) {
            return htmlentities($this->description);
        } else {
            return '';
        }
    }
    /**
     * @return title associated with this Image.
     *
     */
    public function getTitle()
    {
        if (isset($this->title)) {
            return htmlentities($this->title);
        } else {
            return '';
        }
    }
    /**
     * @return largepath associated with this Image.
     *
     */
    public function getlargePath()
    {
        return htmlentities($this->largepath);
    }
    
	 /**
     * @return original name associated with this Image.
     *
     */
	public function getName(){
		return htmlentities($this->name); 
	}
    /**
     * @param img_a and $img_b, two instances of class Image to compare their thumbpath properties in order to determine precedence according to lexicographic ordering
     * @return -1, 0 or 1.
     *
     */
    public static function compareByFilename($img_a, $img_b)
    {
        return strcmp(basename($img_a->getThumbPath()), basename($img_b->getThumbPath()));
    }
    /**
     * 
     * @param $img, in instance of class Image.
     * @return thumbpath -> its thumpath property
     */
    public static function getThumbPaths($img)
    {
        return $img->getThumbPath();
    }
    /**		
     * Resize images
     *
     * Function to resize images to fit area specified when called.
     * 
     * @param string $in_img_file link to image file we wish to resize
     * @param int $benchmark, the maximum of both width and height the image is allowed to be resized to.
     * @param int $size, of value 1 or 0 to indicate whether image should be copied to the location where the thumbnails are stored or otherwise, to the location where the larger version of the images are stored.
     * @param $image, instance of class Image on which to perform a resize operation
     * @return boolean indicating success or not
     */
    
    public static function img_resize($in_img_file, $benchmark, $size, $image)
    {
        $orig_w = $image->width;
        $orig_h = $image->height;
        $src    = imagecreatefromjpeg($in_img_file);
        
        /**
         * Check if image is smaller (in both directions) than required image. If so, use original image dimensions. Otherwise, Test orientation of image and set new dimensions appropriately. i.e. calculate the scale factor
         *
         */
        
        If ($orig_w <= $benchmark & $orig_h <= $benchmark) {
            $new_w = $orig_w;
            $new_h = $orig_h;
        } elseif (($orig_w > $benchmark & $orig_h > $benchmark & ($orig_w >= $orig_h)) || ($orig_w > $benchmark & $orig_h <= $benchmark)) {
            $new_w = $benchmark;
            $new_h = $new_w * ($orig_h / $orig_w);
        }
        
        else {
            
            $new_h = $benchmark;
            $new_w = $new_h * ($orig_w / $orig_h);
        }
        
        /**
         *    Create the new canvas ready for resampled image to be copied onto it.
         */
        
        $imgCanvas = imagecreatetruecolor($new_w, $new_h);
        
        /**
         * Resample input image onto newly created canvas
         */
        imagecopyresampled($imgCanvas, $src, 0, 0, 0, 0, $new_w, $new_h, $orig_w, $orig_h);
        
        if ($size == self::$THUMB) {
            /**
             *  Create output jpeg at quality level of 90
             */
            $success = imagejpeg($imgCanvas, './' . $image->thumbpath, 90);
        } else {
            $success = imagejpeg($imgCanvas, './' . $image->largepath, 90);
        }
        
        /**
         * Destroy any intermediate image files
         */
        imagedestroy($src);
        imagedestroy($imgCanvas);
        
        /**
         * Return a value indicating success or failure (true/false)
         */
        return $success;
    }
    
}
?>