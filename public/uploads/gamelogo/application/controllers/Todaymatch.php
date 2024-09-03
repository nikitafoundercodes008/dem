
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Todaymatch extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Match_model');
        $this->load->model('Match_players_model');
        $this->load->library('form_validation');    
        //require_once APPPATH . "/third_party/PHPExcel.php";    

        if (!isset($_SESSION['id'])){
            redirect(site_url()."/login");
        }
    }

    public function index()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));
        
        if ($q <> '') {
            $config['base_url'] = base_url() . 'todaymatch/index.html?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'todaymatch/index.html?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'todaymatch/index.html';
            $config['first_url'] = base_url() . 'todaymatch/index.html';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
       // $config['total_rows'] = $this->Match_model->total_rows($q);
        $match = $this->Match_model->get_today_match_data();
    
        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'match_data' => $match,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => "", //$config['total_rows'],
            'start' => $start,
        );
		$this->load->view('../modules/loggedin_template/header',$data);
        $this->load->view('todaymatch/match_list', $data);
		$this->load->view('../modules/loggedin_template/footer',$data);
    }

    public function players()
    {
    	$match_id = $this->uri->segment('3');
    	$mid = base64_decode($match_id);
    	$data['match_players_data'] = $this->Match_players_model->get_match_player_data($mid);
    	$this->load->view('../modules/loggedin_template/header',$data);
        $this->load->view('todaymatch/match_players_list');
        $this->load->view('../modules/loggedin_template/footer');
    }

    
    public function update($id) 
    {
        $row = $this->Match_players_model->get_match_player_details($id);
        //echo $this->db->last_query();die;
        if ($row) {
        	$data = array(
            'button' => 'Update',
            'action' => site_url('todaymatch/update_action'),
		);

        $data['row']  = $row;
     // echo "<pre>";   print_r($data);die;
        	$this->load->view('../modules/loggedin_template/header',$data);
            $this->load->view('todaymatch/players_form', $data);
				$this->load->view('../modules/loggedin_template/footer',$data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
             redirect(site_url('todaymatch'));
           
        }
    }

    public function update_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('id', TRUE));
        } else {

        	$match_id = $this->input->post('match_id', TRUE);
        	
        	$data = array(
        		'points' =>$this->input->post('credit_points',TRUE),
        		'selection_percent' =>$this->input->post('SelectionPercent',TRUE),
        		 );
            $this->Match_players_model->update($this->input->post('id', TRUE), $data);
            $this->session->set_flashdata('message', 'Update Record Success');
            redirect(site_url('todaymatch/players/'.base64_encode($match_id)));
           
        }
    }

    public function _rules() 
    {
		$this->form_validation->set_rules('credit_points', 'Credit Pints', 'trim|required');
		$this->form_validation->set_rules('SelectionPercent', 'Selection Percent', 'trim|required');
		$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}
?>    