<?php

namespace extensions\image{
    
    /* Prevent direct access */
    defined('ADAPT_STARTED') or die;
    
    class controller_image extends \frameworks\adapt\controller{
        
        public function view_default(){
            if (isset($this->request['key'])){
                
                $meta = $this->file_store->get_meta_data($this->request['key']);
                
            }else{
                $this->error(404);
            }
        }
        
    }
    
}

?>