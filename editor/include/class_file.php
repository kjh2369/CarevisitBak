<?php 

Class Fileup { 

    public  $uploaddir; 
    public  $allow_ext = array(); 
    public  $file_max_size; 

    private $uploadfile; 
    private $file_name; 
    private $mime_name; 
    private $tmp_name; 
    private $size; 

    function validate() { 

    if(!in_array($this->ext,$this->allow_ext)) { 
        return "Not a right type of file or no file selected"; 
        } 

        elseif($this->size > $this->file_max_size) { 
        return "Your file is too large to upload"; 
        } 
      
        else { 
        return self::upload();  
        }  
    } 

    function __construct($file,$uploaddir) { 
     
    if($uploaddir=="") { 
    $this->uploaddir = "."; 
      } else { 
        $this->uploaddir = $uploaddir; 
        if(!is_dir($this->uploaddir)) { 
        mkdir($this->uploaddir, 0777);  
      } 
    } 
        
    $this->uploadfile = $this->uploaddir . $update_image['name']; 
    $this->file_name1 = $update_image['name']; 
    $this->mime_name = basename($update_image['type']); 
    $this->size = basename($update_image['size']);    
    $this->tmp_name = $update_image['tmp_name'];    
    
    $this->temp_name = explode(".",$this->file_name1); 
    $this->ext = strtolower($this->temp_name[sizeof($this->temp_name)-1]);  
    $this->new_file_name = date(ymdhis).".".$this->ext;  
    $this->file_name = $this->new_file_name . '.' . $this->ext; 
    } 
    
    function getFilename() { 
    return $this->file_name1; 
    } 
            
    function getUpstring() { 
    return $this->upstring; 
    } 
            
    function upload() { 
    $this->upstring = $this->uploaddir . '/' . $this->temp_name[0]. '-'.$this->new_file_name; 
    move_uploaded_file($this->tmp_name ,$this->upstring) ; 
    } 
            
    function __destruct() { 
    unset($_FILES); 
    } 
        
} 
?> 
