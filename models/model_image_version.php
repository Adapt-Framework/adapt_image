<?php

namespace adapt\image{
    
    class model_image_version extends \adapt\model {
        
        public function __construct($id = null, $data_source = null) {
            parent::__construct('image_version', $id, $data_source);
        }
        
        public function pget_asset_id(){
            return md5($this->file_key);
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
                    $actions['actions'] = explode(",", $actions['actions']);
                    foreach($actions['actions'] as $action) {
                        
                        switch($action){
                        case "resize_to_height":
                            $key_pairs['action_resized_to_height'] = 'Yes';
                            $key_pairs['height'] = $actions['height'];
                            break;
                        case "resize_to_width":
                            $key_pairs['action_resized_to_width'] = 'Yes';
                            $key_pairs['width'] = $actions['width'];
                            break;
                        case "resize":
                            $key_pairs['action_resized'] = 'Yes';
                            $key_pairs['height'] = $actions['height'];
                            $key_pairs['width'] = $actions['width'];
                            break;
                        case "scale":
                            $key_pairs['action_scaled'] = 'Yes';
                            $key_pairs['scale'] = $actions['scale'];
                            break;
                        case "gaussian_blur":
                            $key_pairs['action_gaussian_blur'] = 'Yes';
                            break;
                        case "crop":
                            $key_pairs['action_cropped'] = 'Yes';
                            $key_pairs['height'] = $actions['height'];
                            $key_pairs['width'] = $actions['width'];
                            $key_pairs['start_x'] = $actions['x'];
                            $key_pairs['start_y'] = $actions['y'];
                            break;
                        case "crop_from_center":
                            $key_pairs['action_cropped_from_center'] = 'Yes';
                            $key_pairs['height'] = $actions['height'];
                            $key_pairs['width'] = $actions['width'];
                            break;
                        case "square":
                            $key_pairs['action_squared'] = 'Yes';
                            $key_pairs['size'] = $actions['size'];
                            break;
                        }
                    }
                }
                
                foreach($key_pairs as $key => $value) {
                    if ($value) $where->add(new sql_cond($key, sql::EQUALS, sql::q($value)));
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