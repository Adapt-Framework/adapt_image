<?php

namespace adapt\image{
    
    class model_image_version extends \adapt\model {
        
        public function __construct($id = null, $data_source = null) {
            parent::__construct($id, $data_source);
        }
        
        public function load_by_image_id_and_actions($image_id, $actions){
            $this->initialise();
            
            if (is_assoc($actions)) {
                $sql = $this->data_source->sql;
                
                $sql->select('*')
                    ->from($this->table_name);
                
                $where = new sql_and(
                    new sql_cond('image_id', sql::EQUALS, sql::q($image_id)),
                    new sql_cond('date_deleted', sql::IS, new sql_null())
                );
                
                $key_pairs = [
                    'action_resized_to_height' => 'No',
                    'action_resized_to_width' => 'No',
                    'action_resized' => 'No',
                    'action_scaled' => 'No',
                    'action_gaussian_blur' => 'No',
                    'action_cropped' => 'No',
                    'action_cropped_from_center' => 'No',
                    'action_squared' => 'No'
                ];
                
                if (isset($actions['actions'])) {
                    $actions = explode(",", $actions);
                    foreach($actions as $action) {
                        switch($action){
                        case "resize_to_height":
                            $key_pairs['action_resized_to_height'] = 'Yes';
                            break;
                        case "resize_to_width":
                            $key_pairs['action_resized_to_width'] = 'Yes';
                            break;
                        case "resize":
                            $key_pairs['action_resized'] = 'Yes';
                            break;
                        case "scale":
                            $key_pairs['action_scaled'] = 'Yes';
                            break;
                        case "gaussian_blur":
                            $key_pairs['action_gaussian_blur'] = 'Yes';
                            break;
                        case "crop":
                            $key_pairs['action_cropped'] = 'Yes';
                            break;
                        case "crop_from_center":
                            $key_pairs['action_cropped_from_center'] = 'Yes';
                            break;
                        case "square":
                            $key_pairs['action_squared'] = 'Yes';
                            break;
                        }
                    }
                }
                
                if (isset($actions['height'])) $key_pairs['height'] = $actions['height'];
                if (isset($actions['width'])) $key_pairs['width'] = $actions['width'];
                if (isset($actions['scale'])) $key_pairs['scale'] = $actions['scale'];
                if (isset($actions['x'])) $key_pairs['x'] = $actions['x'];
                if (isset($actions['y'])) $key_pairs['y'] = $actions['y'];
                if (isset($actions['size'])) $key_pairs['size'] = $actions['size'];
                
                foreach($key_pairs as $key => $value) {
                    $where->add(new sql_cond($key, sql::EQUALS, sql::q($value)));
                }
                
                $sql->where($where);
                
                $results = $sql->execute()->results();
                
                if (count($results)) {
                    return $this->load_by_data($results[0]);
                }
                
            } else {
                $this->error('No actions provided');
            }
            
            return false;
        }
        
    }
    
}