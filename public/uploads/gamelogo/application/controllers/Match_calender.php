<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Match_calender extends MY_Controller
{
    public $token;

    function __construct()
    {
        parent::__construct();
        $this->load->model('Match_calender_model');
        $this->load->model('config_model');
        $this->load->library('form_validation');

        $this->token = $this->config_model->get_by_type("sportmonks_api_key")->value;
    }
    

    public function index()
    {
        $Startdate = date('Y-m-d');
        $Enddate = date( "Y-m-d", strtotime( "+10 days"));

        $api_url  = "https://cricket.sportmonks.com/api/v2.0/fixtures?api_token=".$this->token."&filter[starts_between]=".$Startdate.",".$Enddate."&include=league,localteam,visitorteam,league";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$api_url);
        $result=curl_exec($ch);
        curl_close($ch);
        $cricketMatches= json_decode(json_encode(json_decode($result)), True);
       

        foreach($cricketMatches['data'] as $item) { 
           
                if($item['type'] ==="Test" or $item['type'] ==="T20I" or $item['type'] ==="T20" or $item['type'] ==="Woman ODI"  or $item['type'] ==="Test/5day" or $item['type'] ==="Test/4day" or $item['type'] ==="ODI")
                {    
                    $d  =   explode("T", $item['starting_at']);
                    $t = explode(".", $d['1']);

                     
                    $main_time = $d[0]. ' ' . $t[0];

                    $date = new DateTime($main_time, new DateTimeZone('GMT'));
                    $date->setTimezone(new DateTimeZone('Asia/Kolkata'));
                    $time1 = $date->format('Y-m-d H:i:s');
                    $time = date('Y-m-d H:i:s');      
                    $afterdate = date( "Y-m-d H:i:s", strtotime( "+3 days"));

                if($afterdate >= $time1)
                {
                    if($item['localteam_id'] !="")
                    {
                        $team1 = $this->db->get_where('team',array('unique_id'=>$item['localteam_id']))->row();
                        if($team1 !="")
                        {
                            $team1id = $team1->team_id;
                        } 
                        else
                        {
                            if($item['localteam']['image_path'] !="")
                            {
                               $logo_url = $item['localteam']['image_path'];
                            }    
                            else 
                            {    
                                $logo_url =  base_url('uploads/team_default.png');
                            } 

                            $data = array('team_name' =>$item['localteam']['name'],
                                'unique_id'=>$item['localteam']['id'],
                                'team_short_name' =>$item['localteam']['code'],
                                'team_image'=>$logo_url,
                                    );

                            $this->db->insert('team',$data);
                            $team1id =$this->db->insert_id();
                        }   
                         
                    }
                    if($item['visitorteam_id'] !="")
                    {

                        $team2 = $this->db->get_where('team',array('unique_id'=>$item['visitorteam_id']))->row();
                        if($team2 !="")
                        {
                            $team2id = $team2->team_id;
                        } 
                        else
                        {
                            if($item['visitorteam']['image_path'] !="")
                            {
                               $logo_url = $item['visitorteam']['image_path'];
                            }    
                            else 
                            {    
                                $logo_url =  base_url('uploads/team_default.png');
                            } 

                            $data = array('team_name' =>$item['visitorteam']['name'],
                                'unique_id'=>$item['visitorteam']['id'],
                                'team_short_name' =>$item['visitorteam']['code'],
                                'team_image'=>$logo_url,
                                    );

                            $this->db->insert('team',$data);
                            $team2id =$this->db->insert_id();
                        }   
                         
                    }

                    if($item['type'] == "Test/5day" or $item['type'] == "Test/4day")
                    {
                        $match_type = "Test";
                    }
                    else
                    {
                        $match_type = $item['type'];
                    }    
                    
                    $data = array('unique_id' =>$item['id'],
                                    'teamid2' =>$team2id,
                                    'teamid1' =>$team1id,
                                    'type' =>$match_type,
                                    'title'=>$item['localteam']['name'] .' vs ' .$item['visitorteam']['name'] ,
                                    'time'=>$time1,
                                    'match_status'=> "Fixture",
                                    'league_name'=>$item['league']['name'],
                                    'created_date'=>$time,
                                    'match_date_time'=>$time1,
                                );

                    if(!empty($item['id']) && !empty($item['localteam_id']) && !empty($item['visitorteam_id']))
                    {
                        $match_result =  $this->Match_calender_model->get_by_unique_id($item['id']);
                        if($match_result =="")
                        {
                            $season_id =  $item['season_id'];
                            $localteam_id =  $item['localteam_id'];
                            $visitorteam_id =  $item['visitorteam_id'];
                            $api_url_first  = "https://cricket.sportmonks.com/api/v2.0/teams/".$localteam_id."/squad/".$season_id."?api_token=".$this->token."";

                            $api_url_second  = "https://cricket.sportmonks.com/api/v2.0/teams/".$visitorteam_id."/squad/".$season_id."?api_token=".$this->token."";

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_URL,$api_url_first);
                            $result=curl_exec($ch);
                            curl_close($ch);
                            $First_teamplayers_data= json_decode(json_encode(json_decode($result)), True);

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_URL,$api_url_second);
                            $resulttwo=curl_exec($ch);
                            curl_close($ch);
                            $Second_teamplayers_data= json_decode(json_encode(json_decode($resulttwo)), True);

                            $count_team_first = count($First_teamplayers_data['data']['squad']);
                            $count_team_second = count($Second_teamplayers_data['data']['squad']);
                            if(isset($count_team_first) && $count_team_first > 0 && isset($count_team_second) && $count_team_second > 0){

                            $id =  $this->Match_calender_model->insert($data);
                            $contests =  $this->db->get('contest_defalut')->result_array();

                            foreach ($contests as $contest) {
                                    $array = array('contest_name' =>$contest['contest_name'],
                                                'contest_tag' =>$contest['contest_tag'],
                                                'winners' =>$contest['winners'],
                                                'prize_pool' =>$contest['prize_pool'],
                                                'total_team' =>$contest['total_team'],
                                                'join_team' =>$contest['join_team'],
                                                'entry' =>$contest['entry'],
                                                'contest_description' =>$contest['contest_description'],
                                                'contest_note1' =>$contest['contest_note1'],
                                                'contest_note2' =>$contest['contest_note2'],
                                                'winning_note' =>$contest['winning_note'],
                                                'type' =>$contest['type'],
                                                'match_id'=>$id,
                                            );
                                    $this->db->insert('contest',$array);
                                    $contest_last_id = $this->db->insert_id();

                                    $winnings =  $this->db->get_where('winning_information_default',array('contest_id'=>$contest['contest_id']))->result_array();
                                    foreach ($winnings as $winning) {
                                               $winning_data = array('contest_id' => $contest_last_id,
                                                    'rank' =>$winning['rank'],
                                                    'from_rank' =>$winning['from_rank'],
                                                    'to_rank' =>$winning['to_rank'],
                                                    'price' =>$winning['price'],
                                                );
                                               $this->db->insert('winning_information',$winning_data);
                                    }
                                
                            }
                          
                        foreach ($First_teamplayers_data['data']['squad']as $player) 
                        {

                            $team_unique_id =  $First_teamplayers_data['data']['id'];
                            $teamid = $this->db->get_where('team',array('unique_id'=>$team_unique_id))->row()->team_id;
                            
                            $pid = $player['id'];

                            $playerid = $this->db->get_where('players',array('pid' =>$pid))->row();

                            $playing_role = $player['position']['name'];

                            if($playing_role =="Bowler")
                            {
                                $role = "2";
                            }
                            else if($playing_role =="Batsman")
                            {
                                $role = "1";
                            }
                            else if($playing_role =="Wicketkeeper")
                            {
                                $role = "4";
                            }
                            else
                            {
                                $role = "3";
                            }


                            $image_path = $player['image_path'];

                            if (strpos($image_path, 'cricket/players') !== false) {
                                $players_image = $image_path;
                            }
                            else
                            {
                                $players_image = base_url('uploads/player/default.png');
                            }

                                
                            if($playerid =="")
                            {
                                $playerinfo = array(
                                        'name' =>$player['fullname'],
                                        'bats' =>$player['battingstyle'],
                                        'bowls' =>$player['bowlingstyle'],
                                        'dob' =>$player['dateofbirth'],
                                        'nationality' =>"",
                                        'pid' =>$player['id'],
                                        'credit_points'=>rand('7','10'),
                                        'designationid' =>$role,
                                        'created_date'=>$time,
                                        'teamid'=>$teamid,
                                        'image'=>$players_image
                                    );

                                    $this->db->insert('players',$playerinfo);
                                    $p_id = $this->db->insert_id();

                                    $info =  $this->db->get_where('players',array('id'=>$p_id))->row();
                                    $m_player = array('matchid' =>$id,
                                                'teamid'=>$teamid,
                                                'playerid'=>$info->id,
                                                'designationid'=>$role,
                                                'created_date'=>$time,
                                    );
                                    $this->db->insert('match_players',$m_player);
                            }   
                            else
                            {
                                $m_player = array('matchid' =>$id,
                                                'teamid'=>$teamid,
                                                'playerid'=>$playerid->id,
                                                'designationid'=>$role,
                                                'created_date'=>$time,
                                    );
                                $this->db->insert('match_players',$m_player);
                            } 

                        
                        }

                        foreach ($Second_teamplayers_data['data']['squad']as $playerss) 
                        {
                            $team_unique_id =  $Second_teamplayers_data['data']['id'];
                            $teamid = $this->db->get_where('team',array('unique_id'=>$team_unique_id))->row()->team_id;
                            
                            $pid = $playerss['id'];

                            $playerid = $this->db->get_where('players',array('pid' =>$pid))->row();

                            $playing_role = $playerss['position']['name'];

                            if($playing_role =="Bowler")
                            {
                                $role = "2";
                            }
                            else if($playing_role =="Batsman")
                            {
                                $role = "1";
                            }
                            else if($playing_role =="Wicketkeeper")
                            {
                                $role = "4";
                            }
                            else
                            {
                                $role = "3";
                            }

                            $image_path = $playerss['image_path'];

                            if (strpos($image_path, 'cricket/players') !== false) {
                                $players_image = $image_path;
                            }
                            else
                            {
                                $players_image = base_url('uploads/player/default.png');
                            }


                            if($playerid =="")
                            {
                                $playerinfo = array(
                                        'name' =>$playerss['fullname'],
                                        'pid' =>$playerss['id'],
                                        'bats' =>$playerss['battingstyle'],
                                        'bowls' =>$playerss['bowlingstyle'],
                                        'dob' =>$playerss['dateofbirth'],
                                        'nationality' =>"",
                                        'credit_points'=>rand('7','10'),
                                        'designationid' =>$role,
                                        'created_date'=>$time,
                                        'teamid'=>$teamid,
                                        'image'=>$players_image
                                    );

                                    $this->db->insert('players',$playerinfo);
                                    $p_id = $this->db->insert_id();

                                    $info =  $this->db->get_where('players',array('id'=>$p_id))->row();
                                    $m_player = array('matchid' =>$id,
                                                'teamid'=>$teamid,
                                                'playerid'=>$info->id,
                                                'designationid'=>$role,
                                                'created_date'=>$time,
                                    );
                                    $this->db->insert('match_players',$m_player);
                            }   
                            else
                            {
                                $m_player = array('matchid' =>$id,
                                                'teamid'=>$teamid,
                                                'playerid'=>$playerid->id,
                                                'designationid'=>$role,
                                                'created_date'=>$time,
                                    );
                                $this->db->insert('match_players',$m_player);
                            } 
                        
                        }

                       
                               
                    }
                  }    

                }
                } 

            } 
        }
                  
            
    }
}
