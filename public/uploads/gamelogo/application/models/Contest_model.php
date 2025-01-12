<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Contest_model extends CI_Model
{

    public $table = 'contest';
    public $id = 'contest_id';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
    }

    // get all
    function get_all()
    {
        $this->db->order_by($this->id, $this->order);
        return $this->db->get($this->table)->result();
    }

    // get data by id
    function get_by_id($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->get($this->table)->row();
    }
    
    // get total rows
    function total_rows($q = NULL) {
        $this->db->like('contest_id', $q);
	$this->db->or_like('contest_name', $q);
	$this->db->or_like('contest_tag', $q);
	$this->db->or_like('winners', $q);
	$this->db->or_like('prize_pool', $q);
	$this->db->or_like('total_team', $q);
	$this->db->or_like('join_team', $q);
	$this->db->or_like('entry', $q);
	$this->db->or_like('contest_description', $q);
	$this->db->or_like('contest_note1', $q);
	$this->db->or_like('contest_note2', $q);
	$this->db->or_like('match_id', $q);
	$this->db->or_like('type', $q);
	$this->db->from($this->table);
        return $this->db->count_all_results();
    }

    // get data with limit and search
    function get_limit_data($match_id) {
        $this->db->order_by($this->id, $this->order);
        $this->db->where('match_id', $match_id);
	    return $this->db->get($this->table)->result();
    }

    // insert data
    function insert($data)
    {
        $this->db->insert($this->table, $data);
    }

    function insert_winnig($data)
    {
        $this->db->insert('winning_information', $data);
    }

    // update data
    function update($id, $data)
    {
        $this->db->where($this->id, $id);
        $this->db->update($this->table, $data);
    }

    // delete data
    function delete($id)
    {
        $this->db->where($this->id, $id);
        $this->db->delete($this->table);
    }

    function get_match_by_id($id)
    {
        $this->db->where('match_id',$id);
        return $this->db->get('match')->row();
    }


    function match_list()
    {
        $today = date("Y-m-d H:i:s");
        $this->db->select('match_id,title,time');
        $this->db->where('time >', $today);
        return  $this->db->get('match')->result();
    }

     // get data with limit and search
    function get_limit_data_contest($limit, $start = 0, $q = NULL,$id) {
    $this->db->order_by('winning_info_id', 'ASC');
    $this->db->where('contest_id',$id);
    return $this->db->get('winning_information')->result();
    }

    // get total rows
    function total_rows_contest($q = NULL , $id) {
    $this->db->like('contest_id', $q);
    $this->db->or_like('rank', $q);
    $this->db->or_like('price', $q);
    $this->db->from('winning_information');
    $this->db->where('contest_id',$id);
    return $this->db->count_all_results();
    }

    // get data by id
    function get_winninginfo_by_id($id)
    {
        $this->db->where('winning_info_id', $id);
        return $this->db->get('winning_information')->row();
    }

    function update_winninginfo($id, $data)
    {
        $this->db->where('winning_info_id', $id);
        return $this->db->update('winning_information', $data);
    }

    // delete data
    function winninginfo_delete($id)
    {
        $this->db->where('winning_info_id', $id);
        $this->db->delete('winning_information');
    }

    function get_contest_by_matchid($id)
    {
        $this->db->from($this->table);
        $this->db->where('match_id',$id);
        $this->db->order_by($this->id, $this->order);
        $query= $this->db->get();
        $result = $query->result_array();
        $options= "<option value=''>Select Contest</option>";
        foreach ($result as $key => $value) {
            $options .="<option value = ".$value['contest_id'].">".$value['contest_name']."</option>";
        }
        return $options;
    }

    function get_contest_by_match($id)
    {
        $this->db->from($this->table);
        $this->db->where('match_id',$id);
        $this->db->order_by($this->id, $this->order);
        $query= $this->db->get();
        $result = $query->result_array();
        return $result;
    }
    
    function get_contest_id_by_match_id($id)
    {
        $this->db->select('contest_id');
        $this->db->from($this->table);
        $this->db->where('match_id',$id);
        $this->db->order_by($this->id, $this->order);
        $query= $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    function get_leaderboard($contest_id , $match_id)
    {
        $this->db->select('user_id,rank');
        $this->db->where('contestid',$contest_id);
        $this->db->where('matchid',$match_id);
        $this->db->order_by('rank','asc');
        $resp = $this->db->get('leaderboard');
        if($resp->num_rows() > 0)
        {
            return $resp->result_array();
        }    
        else
        {
            return false;
        }    

    }

    function get_winning_price($rank,$contest_id)
    {
        $this->db->select('price');
        $this->db->where('contest_id',$contest_id);
        $this->db->where($rank.' BETWEEN from_rank AND to_rank');
        $resp = $this->db->get('winning_information')->row()->price;
        if($resp)
        {
            return $resp;
        }    
        else
        {
            return false;
        }    
    }

    function contest_winning_amount_credit($user_id,$price,$contest_id)
    {
        $winning_amount = array('user_id' =>$user_id,
                        'amount'=>$price,
                        'type'=>"winning",
                        'transaction_status'=>'SUCCESS',
                        'contest_id'=>$contest_id,
                        'transection_mode'=>'for winning contest',
                        'created_date'=> date('Y-m-d H:i:s'),
                        );
        $this->db->insert('transection',$winning_amount);
    }



}