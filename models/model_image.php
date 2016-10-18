<?php

namespace adapt\image{
    
    class model_image extends \adapt\model{
        
        const EVENT_ON_LOAD_BY_FILE_KEY = 'model.on_load_by_file_key';
        
        public function __construct($id = null, $data_source = null){
            parent::__construct('image', $id, $data_source);
        }
        
        public function pget_asset_id(){
            return md5($this->file_key);
        }
        
        public function load_by_file_key($key){
            $this->initialise();
    
            /* Make sure name is set */
            if (isset($key)){
    
                /* We need to check this table has a name field */
                $fields = array_keys($this->_data);
                
                if (in_array('file_key', $fields)){
                    $sql = $this->data_source->sql;
    
                    $sql->select('*')
                        ->from($this->table_name);
    
                    /* Do we have a date_deleted field? */
                    if (in_array('date_deleted', $fields)){
    
                        $name_condition = new sql_cond('file_key', sql::EQUALS, sql::q($key));
                        $date_deleted_condition = new sql_cond('date_deleted', sql::IS, new sql_null());
    
                        $sql->where(new sql_and($name_condition, $date_deleted_condition));
    
                    }else{
    
                        $sql->where(new sql_cond('file_key', sql::EQUALS, sql::q($key)));
                    }
                    
                    /* Get the results */
                    $results = $sql->execute()->results();
    
                    if (count($results) == 1){
                        $this->trigger(self::EVENT_ON_LOAD_BY_FILE_KEY);
                        return $this->load_by_data($results[0]);
                    }elseif(count($results) == 0){
                        $this->error("Unable to find a record with key {$key}");
                    }elseif(count($results) > 1){
                        $this->error(count($results) . " records found with key '{$key}'.");
                    }
    
                }else{
                    $this->error('Unable to load by file_key, this table has no \'file_key\' field.');
                }
            }else{
                $this->error('Unable to load by file_key, no file_key supplied');
            }
    
            return false;
        }
        
    }
    
}