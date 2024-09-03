<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Payment extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Match_model');
        $this->load->model('Contest_model');   
        $this->load->model('User_model');    

		if (!isset($_SESSION['id'])){
            redirect(site_url()."/login");
        }
    }


    public function send()
    {
    	$id = $this->uri->segment('3');
    	$match_id = base64_decode($id);

    	$match_status =  $this->Match_model->match_status_check($match_id);
    	if($match_status =="Result")
    	{
    		$contest_list = $this->Contest_model->get_contest_id_by_match_id($match_id);
    		if(isset($contest_list) && !empty($contest_list))
    		{
    		    foreach ($contest_list as $contest) {
    			$leaderboards = $this->Contest_model->get_leaderboard($contest['contest_id'],$match_id);
				foreach ($leaderboards as $leaderboard) 
				{
					$winning_price = $this->Contest_model->get_winning_price($leaderboard['rank'],$contest['contest_id']);
					if($winning_price !="")
    				{
    					$this->Contest_model->contest_winning_amount_credit($leaderboard['user_id'],$winning_price,$contest['contest_id']);
    				}	
				}
    		}


            $data = array('payment_status' =>'1');
            $this->Match_model->update($match_id , $data);

    		$this->session->set_flashdata('message','Payment transfer complete');
    		redirect('old_match');
    		    
    		}
    		else
    		{
    		    $this->session->set_flashdata('message','No record found...');
    	    	redirect('old_match');
    		}
    	}	
    	else
    	{
    		$this->session->set_flashdata('message','Match is not complete');
    		redirect('old_match');
    	}	
    }
    
    
    
    public function refund()
    {
    	$id = $this->uri->segment('3');
    	$match_id = base64_decode($id);

    	$match_status =  $this->Match_model->match_status_cancel_check($match_id);
    	if($match_status)
    	{
    		$users_list = $this->Match_model->get_my_match_which_cancelled($match_id);
    		
    		if(isset($users_list) && !empty($users_list))
    		{
    		    foreach ($users_list as $user) 
    		{
        		if(isset($user['credit_type']) && !empty($user['credit_type']))
                {
                    $data = array('user_id'=>$user['user_id'],
                                'amount'=>$user['credit_type'],
                                'type'=>'credit',
                                'transaction_status'=>'SUCCESS',
                                'created_date'=>date('Y-m-d h:i:s'),
                                'transection_mode'=>'Refund for match cancel'
                    );
                    amount_refund($data);
                }
                if(isset($user['bonus_type']) && !empty($user['bonus_type']))
                {
                    $data = array('user_id'=>$user['user_id'],
                                'amount'=>$user['bonus_type'],
                                'type'=>'bonus',
                                'transaction_status'=>'SUCCESS',
                                'created_date'=>date('Y-m-d h:i:s'),
                                'transection_mode'=>'Refund for match cancel'
                    );
                    amount_refund($data);
                }
                if(isset($user['winning_type']) && !empty($user['winning_type']))
                {
                    $data = array('user_id'=>$user['user_id'],
                                'amount'=>$user['winning_type'],
                                'type'=>'winning',
                                'transaction_status'=>'SUCCESS',
                                'created_date'=>date('Y-m-d h:i:s'),
                                'transection_mode'=>'Refund for match cancel'
                    );
                    amount_refund($data);
                }
    		}
    		
            $dataupd = array('refund' =>'1');
            $this->Match_model->update($match_id , $dataupd);

    		$this->session->set_flashdata('message','Payment transfer complete');
    		redirect('cancel');
    		}
    		else
    		{
    		    $this->session->set_flashdata('message','No user join this match...');
    	    	redirect('cancel');
    		}
    		
    	}	
    	else
    	{
    		$this->session->set_flashdata('message','Match is not cancel');
    		redirect('cancel');
    	}	
    }
    
    

}


