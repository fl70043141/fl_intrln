<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
         function __construct() {
            parent::__construct();
            $this->load->model('Dashboard_model'); 
        }

	public function index(){ 
            
            $data                 = $this->getData(); 
            $data['barchart']     = $this->get_barchart_data(); 
            $data['donut']     = $this->get_donut_data(); 
            $data['main_content'] = 'dashboard/index'; 
            $this->load->view('includes/template',$data);
	}
        function getData(){
            
            $data['total_1']= array(
                                                'label' =>'Total Invoices',
                                                'count' => $this->Dashboard_model->get_tbl_couts(INVOICES),
                                                );
            $data['total_2']= array(
                                            'label' =>'Customers',
                                            'count' => $this->Dashboard_model->get_tbl_couts(CUSTOMERS),
                                            );
            $data['total_3']= array(
                                            'label' =>'Items',
                                            'count' => $this->Dashboard_model->get_tbl_couts(ITEMS),
                                            );
            $data['total_4']= array(
                                        'label' =>'Categories',
                                        'count' => $this->Dashboard_model->get_tbl_couts(ITEM_CAT),
                                        );
            return $data;
        }
        
        function get_barchart_data(){
            $data_bc = array();
            for ($i = 0; $i < 7; $i++) {
                $date_start = date('Y-m-1', strtotime("-$i month"));
                $date_end = date('Y-m-t', strtotime("-$i month"));
                
                $data_bc[$i]['month']= date('M',strtotime("-$i month"));
                $data_bc[$i]['res']=$this->Dashboard_model->get_number_inv(SALES_ORDERS,$date_start,$date_end);
                $data_bc[$i]['inv']=$this->Dashboard_model->get_number_inv(INVOICES,$date_start,$date_end);
            }
            
//            echo '<pre>';            print_r($data_bc);die;
            return $data_bc;
        }
        
        function get_donut_data(){
            $data_donut = array();
                $data_donut['res']=$this->Dashboard_model->get_number_inv(SALES_ORDERS);
                $data_donut['inv']=$this->Dashboard_model->get_number_inv(INVOICES);
                $data_donut['itm']=$this->Dashboard_model->get_number_inv(SALES_ORDERS,'','',' invoiced=0');
            
//            echo '<pre>';            print_r($data_donut);die;
            return $data_donut;
        }

        public function test(){
            $this->load->model('Dashboard_model');
            echo '<pre>';            print_r($this->getData()); die;

        }
        
        
        function fl_ajax(){  
//            echo '<pre>';            print_r($this->input->post()); die;
            $func = $this->input->post('function_name');
            $param = $this->input->post();
            
            if(method_exists($this, $func)){ 
                (!empty($param))?$this->$func($param):$this->$func();
            }else{
                return false;
            }
        }
        function get_temp_salesorder_user(){ //for Top header data
           
            $this->load->model('Sales_order_items_model');
            $res = $this->Sales_order_items_model->get_temp_so_item_user();
//            echo '<pre>';            print_r(count($res)); die;
            
            foreach ($res as $res1){
                echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-bell-o"></i>
                        <span id="count_temp_so_list" class="label label-warning">'.count($res).'</span>
                      </a>
                      <ul class="dropdown-menu">
                        <li class="header">Pending Temp Items</li>
                        <li>
                          <!-- inner menu: contains the actual data -->
                          <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 200px;"><ul class="menu" style="overflow: hidden; width: 100%; height: 200px;">
                             <li>
                              <a href="#">
                                <i class="fa fa-edit text-red"></i> '.count($res1).' Items in list
                              </a>
                            </li>
                          </ul><div class="slimScrollBar" style="background: rgb(0, 0, 0); width: 3px; position: absolute; top: 0px; opacity: 0.4; display: block; border-radius: 7px; z-index: 99; right: 1px;"></div><div class="slimScrollRail" style="width: 3px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;"></div></div>
                        </li> 
                      </ul>';
            }
        }
        function get_temp_sales_invoice_user(){ //for Top header data
           
            $this->load->model('Sales_pos_model');
            $res = $this->Sales_pos_model->get_temp_invoice_paused($this->session->userdata(SYSTEM_CODE)['ID']);
//            echo '<pre>';            print_r($res); die;
            if(!empty($res)){
                echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                            <i class="fa fa-file"></i>
                            <span id="count_temp_invoice_list" class="label label-warning">'.count($res).'</span>
                          </a>
                          <ul class="dropdown-menu">
                            <li class="header togg_down">Pending Temp Invoices</li>
                            <div class="box-footer no-padding">
                                <ul class="nav nav-stacked">
                            ';
                foreach ($res as $res1){
                    $items_count = count(json_decode($res1['item_data'],true));
                    echo' 
                          <li><a class="invtmp" id="invtemp_'.$res1['id'].'" href="#" style="color:black;">'.$res1['temp_invoice_no'].' <span class="pull-right badge bg-blue">'.$items_count.' items</span></a></li> 
                        ';
                }
                echo ' </ul>
                      </div> ';
            }
        }
        
        
}
