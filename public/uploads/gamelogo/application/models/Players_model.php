<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Players_model extends CI_Model
{

    public $table = 'players';
    public $id = 'id';
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
        $this->db->where('p.id', $id);
        $this->db->select('p.*,d.title,t.team_name');
        $this->db->from('players p');
        $this->db->join('team t','t.team_id=p.teamid');
        $this->db->join('designation d','d.id=p.designationid');

        return $this->db->get($this->table)->row();
    }
    
    // get total rows
    function total_rows($q = NULL) {
        $this->db->like('id', $q);
	    $this->db->or_like('name', $q);
	    $this->db->or_like('designationid', $q);
	    $this->db->or_like('teamid', $q);
	    $this->db->or_like('credit_points', $q);
	    $this->db->or_like('image', $q);
	    $this->db->or_like('created_date', $q);
	    $this->db->or_like('modified_date', $q);
	    $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    // get data with limit and search
    function get_limit_data($limit, $start = 0, $q = NULL) {
        
 //    $this->db->order_by($this->id, $this->order);
 //    $this->db->limit($limit, $start);

    // $this->db->like('id', $q);
	// $this->db->or_like('name', $q);
	// $this->db->or_like('designationid', $q);
	// $this->db->or_like('teamid', $q);
	// $this->db->or_like('credit_points', $q);
	// $this->db->or_like('image', $q);
	// $this->db->or_like('created_date', $q);
	// $this->db->or_like('modified_date', $q);

    $this->db->select('p.*,d.title,t.team_name');
    $this->db->from('players p');
    $this->db->order_by('p.id', 'DESC');
    $this->db->join('team t','t.team_id=p.teamid');
    $this->db->join('designation d','d.id=p.designationid');
   // $this->db->limit($limit, $start);
    $query = $this->db->get();
    if($query->num_rows > 0) 
    {
        return $query->result();
    } else {
        return $query->result();
    }
       
    }

    // insert data
    function insert($data)
    {
        $this->db->insert($this->table, $data);
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

    //get designation
    function getDesignation()
    {
        $query = $this->db->get('designation');
        return $query->result();
    }

    //get team
    function getTeam()
    {
        $query = $this->db->get('team');
        return $query->result();
    }

    function get_players_by_teamid($id)
    {
        $this->db->select('*,players.id AS pla_id,players.image as p_img');
        $this->db->where('teamid',$id);
        $this->db->from('players');
        $this->db->join('designation','designation.id=players.designationid');
        return $this->db->get()->result();
    }

}