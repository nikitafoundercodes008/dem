<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Withdrow_request extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Transaction_model');
        $this->load->library('form_validation');

        if (!isset($_SESSION['id'])){
            redirect(site_url()."/login");
        }
    }

    public function index()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));
        
        if ($q <> '') {
            $config['base_url'] = base_url() . 'players/index.html?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'players/index.html?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'players/index.html';
            $config['first_url'] = base_url() . 'players/index.html';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Transaction_model->total_withdrow_request_rows($q);
        $request = $this->Transaction_model->get_limit_data($config['per_page'], $start, $q);
        //print_r($request);die();
        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'request_data' => $request,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('../modules/loggedin_template/header',$data);
        $this->load->view('withdrow_request/request_list', $data);
        $this->load->view('../modules/loggedin_template/footer',$data);
    }

    

    public function _rules() 
    {
	$this->form_validation->set_rules('name', 'name', 'trim|required');
	$this->form_validation->set_rules('designationid', 'designationid', 'trim|required');
	$this->form_validation->set_rules('teamid', 'teamid', 'trim|required');
	$this->form_validation->set_rules('credit_points', 'credit points', 'trim|required');
	// $this->form_validation->set_rules('image', 'image', 'trim|required');
	// $this->form_validation->set_rules('created_date', 'created date', 'trim|required');
	// $this->form_validation->set_rules('modified_date', 'modified date', 'trim|required');

	$this->form_validation->set_rules('id', 'id', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}
