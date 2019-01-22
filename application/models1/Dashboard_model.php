<?php 

class Dashboard_model extends CI_Model
{
	function __construct(){	
            parent::__construct(); 
 	}
        
         public function get_tbl_couts($table_name,$where=''){ 
            $this->db->select('*');
            $this->db->from($table_name); 
            $this->db->where('status',1);
            $this->db->where('deleted',0);
            if($where!='')$this->db->where($where);
            $result = $this->db->get()->result_array();  
            return count($result);
	}
         public function get_number_inv($tbl,$start='',$end='',$where=''){ 
            $this->db->select('id');
            $this->db->from($tbl); 
            $this->db->where('deleted',0);
            if(isset($start) && $start!='')$this->db->where('added_on >=', $start);
            if(isset($end) && $end!='')$this->db->where('added_on <=', $end);
            if(isset($where) && $where!='')$this->db->where($where);
            $result = $this->db->get()->result_array(); 
            return count($result);
	}
 
}
?>