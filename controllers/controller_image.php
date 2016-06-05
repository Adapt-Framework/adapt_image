<?php

namespace adapt\image{
    
    /* Prevent direct access */
    defined('ADAPT_STARTED') or die;
    
    class controller_image extends \adapt\controller{
        
        public function __construct(){
            $this->model = new model_image();
            
            parent::__construct();
        }
        
        public function permission_action_save(){
            return $this->session->is_logged_in;
        }
        
        public function action_save(){
            
        }
        
        
        public function view_default(){
            if ($this->model->is_loaded){
                
                $asset_id = md5("image-" . $this->model->image_id);
                
                $if_none_match = "";
                if (isset($_SERVER['HTTP_IF_NONE_MATCH'])){
                    $if_none_match = str_replace("\"", "", $_SERVER['HTTP_IF_NONE_MATCH']);
                }
                
                if ($if_none_match == $asset_id){
                    header('HTTP/1.1 304 Not Modified');
                    header("Cache-Control: public");
                    header("Expires: ");
                    header("Content-Type: ");
                    header("Etag: \"{$asset_id}\"");
                    exit(1);
                }else{
                    
                    $cal = new \adapt\date($this->model->date_modified);
                    $cal->goto_months(11);
                    
                    $this->content_type = $this->model->content_type;
                    
                    header("Cache-Control: private");
                    header("Expires: " . $cal->date('D, d M Y H:i:s O'));
                    $cal->goto_months(-11);
                    header("Last-Modified: " . $cal->date('D, d M Y H:i:s O'));
                    header("Etag: \"{$asset_id}\"");
                    
                    $key = $this->model->file_key;
                    
                    return $this->file_store->get($key);
                }
                
            }else{
                $this->error(404);
            }
        }
        
        public function view_resize_to_height(){
            if ($this->model->is_loaded){
                
                $height = intval($this->request['height']);
                
                if ($height && $height > 0){
                    $children = $this->model->get();
                    
                    foreach($children as $child){
                        if ($child instanceof \adapt\model && $child->table_name == "image_version"){
                            if ($child->action_resized_to_height == "Yes" && $child->height == $height){
                                
                                /* Return the child */ 
                                $asset_id = md5("image_version-" . $child->image_version_id . "-resize_to_height-h" . $height);
                                
                                $if_none_match = "";
                                if (isset($_SERVER['HTTP_IF_NONE_MATCH'])){
                                    $if_none_match = str_replace("\"", "", $_SERVER['HTTP_IF_NONE_MATCH']);
                                }
                                
                                if ($if_none_match == $asset_id){
                                    header('HTTP/1.1 304 Not Modified');
                                    header("Cache-Control: public");
                                    header("Expires: ");
                                    header("Content-Type: ");
                                    header("Etag: \"{$asset_id}\"");
                                    exit(1);
                                }else{
                                    
                                    $cal = new \adapt\date($child->date_modified);
                                    $cal->goto_months(11);
                                    
                                    $this->content_type = $child->content_type;
                                    
                                    header("Cache-Control: private");
                                    header("Expires: " . $cal->date('D, d M Y H:i:s O'));
                                    $cal->goto_months(-11);
                                    header("Last-Modified: " . $cal->date('D, d M Y H:i:s O'));
                                    header("Etag: \"{$asset_id}\"");
                                    
                                    $key = $child->file_key;
                                    
                                    return $this->file_store->get($key);
                                }
                            }
                        }
                    }
                    
                    /* We never found a matching child so we are going to create one */
                    $image = new image($this->model->file_key);
                    
                    if (count($image->errors() == 0)){
                        /* Resize */
                        $image->resize_to_height($height);
                        
                        if ($key = $image->save()){
                            
                            /* Create a new image version */
                            $image_version = new model_image_version();
                            $image_version->action_resized_to_height = 'Yes';
                            $image_version->height = $height;
                            $image_version->content_type = $this->model->content_type;
                            $image_version->image_id = $this->model->image_id;
                            $image_version->file_key = $key;
                            $image_version->save();
                            
                            $cal = new \adapt\date($image_version->date_modified);
                            $cal->goto_months(11);
                            
                            $this->content_type = $image_version->content_type;
                            
                            header("Cache-Control: private");
                            header("Expires: " . $cal->date('D, d M Y H:i:s O'));
                            $cal->goto_months(-11);
                            header("Last-Modified: " . $cal->date('D, d M Y H:i:s O'));
                            header("Etag: \"{$asset_id}\"");
                            
                            return $this->file_store->get($key);
                            
                        }else{
                            $this->error(500);
                        }
                        
                    }else{
                        $this->error(500);
                    }
                    
                    
                    
                }else{
                    $this->error(500);
                }
                
            }else{
                $this->error(404);
            }
        }
        
        public function view_resize_to_width(){
            
        }
        
        public function view_resize(){
            
        }
        
        public function view_scale(){
            
        }
        
        public function view_rotate(){
            
        }
        
        public function view_gaussian_blur(){
            
        }
        
        public function view_crop(){
            
        }
        
        public function view_crop_from_center(){
            
        }
        
        public function view_square(){
            
        }
        
    }
    
}

?>