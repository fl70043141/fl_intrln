<?php 

class Reports_all_model extends CI_Model
{
	function __construct(){	
            parent::__construct(); 
 	}
/*
-----------------------------------------------------------
                      STOCK SHEET REPORTS MODEL FUNCIONS
------------------------------------------------------------*/
        
    public function get_item_stocks($location_id='',$category_id='',$where=''){ 
//            echo '<pre>';            print_r($input); die;
        $this->db->select('is.*'); 
        $this->db->select('itm.item_name,itm.item_code,itm.item_category_id'); 
        $this->db->select('(select category_name from '.ITEM_CAT.' where id = itm.item_category_id)  as item_category_name');
        $this->db->select('(select location_name from '.INV_LOCATION.' where id = is.location_id)  as location_name');
        $this->db->select('(select location_code from '.INV_LOCATION.' where id = is.location_id)  as location_code');
        $this->db->select('(select unit_abbreviation from '.ITEM_UOM.' where id = is.uom_id)  as uom_name');
        $this->db->select('(select unit_abbreviation from '.ITEM_UOM.' where id = is.uom_id_2)  as uom_name_2');
        $this->db->join(ITEMS.' itm','itm.id = is.item_id'); 
        $this->db->from(ITEM_STOCK.' is'); 
        if($location_id!='')$this->db->where('is.location_id',$location_id);
        if($category_id!='')$this->db->where('itm.item_category_id',$category_id);
        if($where!='')$this->db->where($where);
        $this->db->where('is.deleted',0);
        $result = $this->db->get()->result_array();   
//echo $this->db->last_query(); die;
        return $result;
    }
/*
-----------------------------------------------------------
                  END    STOCK SHEET REPORTS MODEL FUNCIONS
------------------------------------------------------------*/
/*-----------------------------------------------------------
                      STOCK SHEET REPORTS GEMSTONE MODEL FUNCIONS
------------------------------------------------------------*/
        
    public function get_item_stocks_gemstones($data='',$where=''){ 
//            echo '<pre>';            print_r('$data'); die;
        $this->db->select('is.*'); 
        $this->db->select('itm.item_name,itm.item_code,itm.item_category_id'); 
        $this->db->select('(select category_name from '.ITEM_CAT.' where id = itm.item_category_id)  as item_category_name');
        $this->db->select('(select location_name from '.INV_LOCATION.' where id = is.location_id)  as location_name');
        $this->db->select('(select unit_abbreviation from '.ITEM_UOM.' where id = is.uom_id)  as uom_name');
        $this->db->select('(select unit_abbreviation from '.ITEM_UOM.' where id = is.uom_id_2)  as uom_name_2');
        $this->db->select('(select dropdown_value from '.DROPDOWN_LIST.' where id = itm.treatment)  as treatment_name');
        $this->db->select('(select dropdown_value from '.DROPDOWN_LIST.' where id = itm.color)  as color_name');
        $this->db->select('(select dropdown_value from '.DROPDOWN_LIST.' where id = itm.shape)  as shape_name');
        $this->db->join(ITEMS.' itm','itm.id = is.item_id'); 
        $this->db->join(ITEM_CAT.' itmc','itmc.id = itm.item_category_id'); 
        $this->db->from(ITEM_STOCK.' is'); 
        $this->db->where('itmc.is_gem',1); 
        
        if(isset($data['location_id']) && $data['location_id'] !='')$this->db->where('is.location_id',$data['location_id']);
        if(isset($data['item_category_id']) && $data['item_category_id'] !='')$this->db->where('itm.item_category_id',$data['item_category_id']);
        if(isset($data['treatment_id']) && $data['treatment_id'] !='')$this->db->where('itm.treatment',$data['treatment_id']);
        if(isset($data['item_code']) && $data['item_code'] !='')$this->db->like('itm.item_code',$data['item_code']);
        if(isset($data['color_id']) && $data['color_id'] !='')$this->db->where('itm.color',$data['color_id']);
        if(isset($data['shape_id']) && $data['shape_id'] !='')$this->db->where('itm.shape',$data['shape_id']);
        
        if(isset($data['min_weight']) && $data['min_weight'] >0)$this->db->where('is.units_available >',$data['min_weight']);
        if(isset($data['max_weight']) && $data['max_weight'] >0)$this->db->where('is.units_available <',$data['max_weight']);
        
        
        if($where!='')$this->db->where($where);
        $this->db->where('is.deleted',0);
        $result = $this->db->get()->result_array();   
//echo $this->db->last_query(); die;
        return $result;
    }
/*
-----------------------------------------------------------
                  END    STOCK SHEET REPORTS GEMSTONE MODEL FUNCIONS
------------------------------------------------------------*/
    
    
/*
-----------------------------------------------------------
                      Month LEDGER  REPORTS MODEL FUNCIONS
------------------------------------------------------------*/
        
    public function get_ledger_month($data,$where=''){
        $def_curcode = $this->session->userdata(SYSTEM_CODE)['default_currency'];
        $cur_det = get_currency_for_code($def_curcode);
        
        $this->db->select('sum(gt.amount*('.$cur_det['value'].'/gt.currency_value)) as glcm_tot_amount'); 
        $this->db->select('gt.*,(gt.amount*('.$cur_det['value'].'/gt.currency_value)) as amount_defcur'); 
        $this->db->select('glcm.account_name as glcm_account_name, glcm.account_code as glcm_code,glcm.id as glcm_id,glcm.account_type_id');  
        $this->db->select('gct.id as gct_id,gct.type_name,gct.class_id,gcc.class_name');  
        $this->db->from(GL_TRANS.' gt'); 
        $this->db->join(GL_CHART_MASTER.' glcm','glcm.account_code = gt.account_code'); 
        $this->db->join(GL_CHART_TYPE.' gct','gct.id= glcm.account_type_id'); 
        $this->db->join(GL_CHART_CLASS.' gcc','gcc.id= gct.class_id'); 
        
        if($data['from_date']!='' && $data['to_date']!='') $this->db->where("gt.trans_date>= ".$data['from_date']." AND gt.trans_date<= ".$data['to_date']." ");
        if($where!='')$this->db->where($where);
        $this->db->where('gt.deleted',0);
        $this->db->group_by('glcm.id');
        $result = $this->db->get()->result_array();   
//echo $this->db->last_query(); die;

//        echo '<pre>';        print_r($result); die;
        return $result;
    }
/*
-----------------------------------------------------------
                  END    MONTH LEDGER REPORTS MODEL FUNCIONS
------------------------------------------------------------*/
    
    
/*
-----------------------------------------------------------
                      DAILY LEDGER REPORTS MODEL FUNCIONS
------------------------------------------------------------*/
    public function get_ledger_day($data,$where=''){
        $this->db->select('sum(gt.amount) as glcm_tot_amount'); 
        $this->db->select('gt.*'); 
        $this->db->select('glcm.account_name as glcm_account_name, glcm.account_code as glcm_code,glcm.id as glcm_id,glcm.account_type_id');  
        $this->db->select('gct.id as gct_id,gct.type_name,gct.class_id,gcc.class_name');  
        $this->db->from(GL_TRANS.' gt'); 
        $this->db->join(GL_CHART_MASTER.' glcm','glcm.account_code = gt.account_code'); 
        $this->db->join(GL_CHART_TYPE.' gct','gct.id= glcm.account_type_id'); 
        $this->db->join(GL_CHART_CLASS.' gcc','gcc.id= gct.class_id'); 
        
        if($data['from_date']!='' && $data['to_date']!='') $this->db->where("gt.trans_date>= ".$data['from_date']." AND gt.trans_date<= ".$data['to_date']." ");
        if($where!='')$this->db->where($where);
        $this->db->where('gt.deleted',0);
        $this->db->group_by('glcm.id');
        $result = $this->db->get()->result_array();   
//echo $this->db->last_query(); die;
        return $result;
    }
/*
-----------------------------------------------------------
                  END    DAILY LEDGER  REPORTS MODEL FUNCIONS
------------------------------------------------------------*/
    
    
    
/*
-----------------------------------------------------------
                 LOCATION TRANSFER REPORTS MODEL FUNCIONS
------------------------------------------------------------*/
        
    public function get_location_transfers($data='',$where=''){ 
//            echo '<pre>';            print_r($data); die;
        $this->db->select('lt.*');  
        $this->db->select('(select location_name from '.INV_LOCATION.' where id = lt.from_location_id)  as from_location_name');
        $this->db->select('(select location_name from '.INV_LOCATION.' where id = lt.to_location_id)  as to_location_name');
         
        $this->db->from(LOCATION_TRASNFER.' lt'); 
        if($data['from_location_id']!='')$this->db->where('lt.from_location_id',$data['from_location_id']);
        if($data['to_location_id']!='')$this->db->where('lt.to_location_id',$data['to_location_id']); 
        if($data['from_date']!='' && $data['to_date']!='') $this->db->where("transfer_date>= ".$data['from_date']." AND transfer_date<= ".$data['to_date']." ");
           
        if($where!='')$this->db->where($where);
        $this->db->where('lt.deleted',0);
        $result = $this->db->get()->result_array();    
        return $result;
    }
    public function get_location_transfers_desc($transfer_id,$where=''){ 
        $this->db->select('ltd.*');   
        $this->db->select('itm.item_code');   
        $this->db->select('(select unit_abbreviation from '.ITEM_UOM.' where id = ltd.item_quantity_uom_id)  as uom_name');
        $this->db->select('(select unit_abbreviation from '.ITEM_UOM.' where id = ltd.item_quantity_uom_id_2)  as uom_name_2');
        
        $this->db->from(LOCATION_TRASNFER_DESC.' ltd'); 
        $this->db->join(ITEMS.' itm','ltd.item_id = itm.id'); 
        
        $this->db->where('ltd.transfer_id',$transfer_id);  
        if($where!='')$this->db->where($where);
        $this->db->where('ltd.deleted',0);  
        $result = $this->db->get()->result_array();  
         
        return $result;
    }
/*
---------------------------------------------------------------------
                END  LOCATION TRANSFER REPORTS MODEL FUNCIONS
----------------------------------------------------------------------*/
    
    
/*
-----------------------------------------------------------
                 CONSIGNEE STOCSHEET REPORTS MODEL FUNCIONS
------------------------------------------------------------*/
        
    public function get_consignee_submission_desc($data='',$where=''){ 
        $this->db->select('csd.*,sum(csd.item_quantity) as qty_1, sum(csd.item_quantity_2) as qty_2');
        $this->db->select('cs.consignee_id');
        $this->db->select('(select unit_abbreviation from '.ITEM_UOM.' where id = csd.item_quantity_uom_id)  as uom_name');
        $this->db->select('(select unit_abbreviation from '.ITEM_UOM.' where id = csd.item_quantity_uom_id_2)  as uom_name_2');
        $this->db->join(CONSIGNEE_SUBMISSION.' cs', 'cs.id = csd.cs_id'); 
        $this->db->from(CONSIGNEE_SUBMISSION_DESC.' csd'); 
        
        if($data['consignee_id']!='')$this->db->where('cs.consignee_id',$data['consignee_id']); 
        if($data['from_date']!='' && $data['to_date']!='') $this->db->where("cs.submitted_date>= ".$data['from_date']." AND cs.submitted_date<= ".$data['to_date']." ");
           
        if($where!='')$this->db->where($where);
        $this->db->where('cs.deleted',0);
        $this->db->group_by('csd.item_id');
        
        $result = $this->db->get()->result_array();    
//            echo '<pre>';            print_r($result); die;
        return $result;
    } 
        
    public function get_consignee_recieve_desc($data='',$where=''){ 
        $this->db->select('crd.*,sum(crd.item_quantity) as qty_1, sum(crd.item_quantity_2) as qty_2');
        $this->db->select('cr.consignee_id');
        $this->db->select('(select unit_abbreviation from '.ITEM_UOM.' where id = crd.item_quantity_uom_id)  as uom_name');
        $this->db->select('(select unit_abbreviation from '.ITEM_UOM.' where id = crd.item_quantity_uom_id_2)  as uom_name_2');
        $this->db->join(CONSIGNEE_RECIEVE.' cr', 'cr.id = crd.cr_id'); 
        $this->db->from(CONSIGNEE_RECIEVE_DESC.' crd'); 
        
        if($data['consignee_id']!='')$this->db->where('cr.consignee_id',$data['consignee_id']); 
        if($data['from_date']!='' && $data['to_date']!='') $this->db->where("cr.recieve_date>= ".$data['from_date']." AND cr.recieve_date<= ".$data['to_date']." ");
           
        if($where!='')$this->db->where($where);
        $this->db->where('cr.deleted',0);
        $this->db->group_by('crd.item_id');
        
        $result = $this->db->get()->result_array();    
//            echo '<pre>';            print_r($result); die;
        return $result;
    } 
/*
---------------------------------------------------------------------
                END  CONSIGNEE STOCSHEET REPORTS MODEL FUNCIONS REPORTS MODEL FUNCIONS
----------------------------------------------------------------------*/
	   
 
}
?>