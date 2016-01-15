<?php

/*
 * This class is loosly based on SimpleImage
 * by Simon Jarvis (http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php#sthash.Rq6mjecu.dpuf)
 */

class image extends adapt{
    
    protected $_image;
    protected $_type;
    
    
    public function __construct($filename = null){
        parent::__construct();
        if ($filename) $this->load($filename);
    }
    
    /***
     * Properties
     */
    public function aget_width(){
        return imagesx($this->_image);
    }
    
    public function aget_height(){
        return imagesy($this->_image);
    }
    
    
    /***
     * Functions
     */
    
    public function load($filename){
        $image_info = getimagesize($filename);
        $this->_type = $image_info[2];
        
        switch($this->_type){
        case IMAGETYPE_JPEG:
            $this->_image = imagecreatefromjpeg($filename);
            break;
        case IMAGETYPE_PNG:
            $this->_image = imagecreatefrompng($filename);
            break;
        }
    }
    
    public function save($filename, $image_type = null, $compression = 75){
        if (!$image_type) $image_type = $this->_type;
        
        switch($image_type){
        case IMAGETYPE_JPEG:
            imagejpeg($this->_image, $filename, $compression);
            break;
        case IMAGETYPE_PNG:
            imagepng($this->_image, $filename);
        }
    }
    
    /***
     * Manipulation functions
     */
    public function resize_to_height($height){
        $ratio = $height / $this->height;
        $width = $this->width * $ratio;
        $this->resize($width, $height);
    }
    
    public function resize_to_width($width){
        $ratio = $width / $this->width;
        $height = $this->height * $ratio;
        $this->resize($width, $height);
    }
    
    public function resize($width, $height){
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $this->_image, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
        $this->_image = $new_image;
    }
    
    public function scale($scale){
        $width = $this->width * $scale / 100;
        $height = $this->height * $scale / 100;
        $this->resize($width, $height);
    }
    
    public function rotate($degrees){
        
    }
    
    public function gaussian_blur(){
        imagefilter($this->_image, IMG_FILTER_GAUSSIAN_BLUR);
    }
    
    
    public function crop($x, $y, $width, $height){
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $this->_image, 0, 0, $x, $y, $width, $height, $width, $height);
        $this->_image = $new_image;
    }
    
    public function crop_from_center($width, $height){
        if (isset($width) || isset($height)){
            
            if (is_null($width)) $width = $height;
            if (is_null($height)) $height = $width;
            
            $offset_x = ($this->width / 2) - ($width / 2);
            $offset_y = ($this->height / 2) - ($height / 2);
            $this->crop($offset_x, $offset_y, $width, $height);
        }
    }
    
    public function square($size){
        
        if ($this->width > $this->height){
            $diff = $this->width - $this->height;
            $x = floor($diff / 2);
            
            $new_image = imagecreatetruecolor($this->height, $this->height);
            imagecopyresampled($new_image, $this->_image, 0, 0, $x, 0, $this->height, $this->height, $this->height, $this->height);
            $this->_image = $new_image;
        }
        
        if ($this->height > $this->width){
            $diff = $this->height - $this->width;
            $y = floor($diff / 2);
            
            $new_image = imagecreatetruecolor($this->width, $this->width);
            imagecopyresampled($new_image, $this->_image, 0, 0, 0, $y, $this->width, $this->width, $this->width, $this->width);
            $this->_image = $new_image;
        }
        
        
        $this->resize($size, $size);
    }
    
}

?>