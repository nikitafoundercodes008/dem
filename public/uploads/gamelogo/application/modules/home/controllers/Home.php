<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {
	
	public function __construct() 
	{
        parent::__construct();    
   	}

	public function index()
	{	
	    $this->db->select('teamid1,teamid2,match_date_time,title,unique_id');
    	$this->db->where('match_status','Fixture');
    	$this->db->order_by('match_date_time','asc');
    	$this->db->limit(9);
    	$data['upcoming_matches'] =  $this->db->get('match')->result();
		$data['version'] =  $this->db->get('version')->row(); 
    	
		$this->load->view('header',$data);
		$this->load->view('home',$data);
		$this->load->view('footer',$data);
	}

	public function about_us()
	{	
	    $this->db->select('content');
	    $this->db->where('id','3');
		$data['content']=$this->db->get('website')->row_array();
		$this->load->view('about',$data);
		$this->load->view('footer');
	}
	
	public function withdraw_cash()
	{
	    $this->db->select('content');
	    $this->db->where('id','4');
		$data['content']=$this->db->get('website')->row_array();
		$this->load->view('withdraw_cash',$data);
		$this->load->view('footer');
	}
	
	public function refund_policy()
	{
	    $this->db->select('content');
	    $this->db->where('id','6');
		$data['content']=$this->db->get('website')->row_array();
		$this->load->view('refund_policy',$data);
		$this->load->view('footer');
	}
	
	public function legality()
	{
	    $this->db->select('content');
	    $this->db->where('id','5');
		$data['content']=$this->db->get('website')->row_array();
		$this->load->view('legality',$data);
		$this->load->view('footer');
	}

	public function terms_and_conditions()
	{
	    $this->db->select('content');
	    $this->db->where('id','1');
		$data['content']=$this->db->get('website')->row_array();
		$this->load->view('terms_and_conditions',$data);
		$this->load->view('footer');
	}

	public function privacy_policy()
	{
	    $this->db->select('content');
	    $this->db->where('id','2');
		$data['content']=$this->db->get('website')->row_array();
		$this->load->view('privacy_policy',$data);
		$this->load->view('footer');
	}

	public function points_system()
	{
		$this->db->select('title,t10score,t20score,odiscore,testscore,description');
		$data['points']=$this->db->get('points_distribution_rules')->result_array();
		$this->load->view('points_system',$data);
		$this->load->view('footer');
	}
	
	// create xlsx
    public function createExcell($match_id,$contest_id) {
    
    
        $this->db->select('match_status');
        $this->db->where('match_id',$match_id);
        $this->db->where('match_status !=',"Fixture");
        $match_check =$this->db->get('match')->row();
        
        if($match_check !="")
        {
            $fileName = 'data-'.time().'.csv';  
            // load excel library
            $this->load->library('excel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
    
    
            $this->db->select('teamid,TeamName,name');
            $this->db->where('matchid',$match_id);
            $this->db->where('contestid',$contest_id);
            $count = $this->db->get('leaderboard')->result_array();
    
            if(isset($count) && !empty($count))
            {
                // set Header
                $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Team Name');
                $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Player 1');
                $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Player 2');
                $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Player 3');
                $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Player 4');
                $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Player 5');
                $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Player 6'); 
                $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Player 7');
                $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Player 8');
                $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Player 9');
                $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Player 10');
                $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Player 11');
                // set Row
                    $rowCount = 2;
                    $key = array();
               foreach ($count as $key) {
                    $this->db->select('my_team_player.id as p_id ,players.name,is_captain,is_vicecaptain');
                    $this->db->where('my_team_player.my_team_id',$key['teamid']);
                    $this->db->join('players','players.id = my_team_player.player_id');
                    $detail = $this->db->get('my_team_player')->result_array();
                 
                    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $key['name']);
                    
                    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $detail[0]['name'] .' ' . get_cpatain($detail[0]['p_id']) . ' '. get_vicecpatain($detail[0]['p_id']) );
                    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $detail[1]['name'] .' ' . get_cpatain($detail[1]['p_id']) . ' '. get_vicecpatain($detail[1]['p_id']) );
                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $detail[2]['name'] .' ' . get_cpatain($detail[2]['p_id']) . ' '. get_vicecpatain($detail[2]['p_id']) );
                    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $detail[3]['name'] .' ' . get_cpatain($detail[3]['p_id']) . ' '. get_vicecpatain($detail[3]['p_id']) );
                    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $detail[4]['name'] .' ' . get_cpatain($detail[4]['p_id']) . ' '. get_vicecpatain($detail[4]['p_id']) );
                    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $detail[5]['name'] .' ' . get_cpatain($detail[5]['p_id']) . ' '. get_vicecpatain($detail[5]['p_id']) );
                    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $detail[6]['name'] .' ' . get_cpatain($detail[6]['p_id']) . ' '. get_vicecpatain($detail[6]['p_id']) );
                    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $detail[7]['name'] .' ' . get_cpatain($detail[7]['p_id']) . ' '. get_vicecpatain($detail[7]['p_id']) );
                    $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $detail[8]['name'] .' ' . get_cpatain($detail[8]['p_id']) . ' '. get_vicecpatain($detail[8]['p_id']) );
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $detail[9]['name'] .' ' . get_cpatain($detail[9]['p_id']) . ' '. get_vicecpatain($detail[9]['p_id']) );
                    $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $detail[10]['name'] .' ' . get_cpatain($detail[10]['p_id']) . ' '. get_vicecpatain($detail[10]['p_id']) );
               
                    $rowCount++;
               }
    
                $filename = "PlayerDetails". date("Y-m-d").".csv";
                header('Content-Type: application/vnd.ms-excel'); 
                header('Content-Disposition: attachment;filename="'.$filename.'"');
                header('Cache-Control: max-age=0'); 
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');  
                $objWriter->save('php://output'); 
            }

        }  
    
        
    }
	
	
	
}
