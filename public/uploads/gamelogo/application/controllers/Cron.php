<?php
set_time_limit(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends MY_Controller{
    public $api_token;

    function __construct()
	{
		parent::__construct();
		$this->load->model('Cron_model');
        $this->load->model('Match_calender_model');
        $this->load->model('config_model');
        $this->load->library('form_validation');

        $this->api_token = $this->config_model->get_by_type("sportmonks_api_key")->value;
	}


    public function index($slug=''){
    	echo "This is cron.";
    }

    public function hourly($cron_key = ''){
        if(!empty($cron_key) && $cron_key !='' && $cron_key !=NULL):
            if($this->Cron_model->check_cron_key($cron_key)):
                if($this->Cron_model->is_cron_enabled($cron_key)):
                    
                    $this->match_playing_eleven();
                    $this->update_match_status_from_live_to_result();
                    $this->update_points();
                    $this->match_cancelled();
                    $this->update_score_points();
                    $this->update_match_status();
                    $this->update_match_time();
                    
                    echo 'Cron process finished';
                else:
                    echo 'Cron is Disabled.';
                endif;
            else:
                echo 'Cron key is invalid.';
            endif;
        else:
            echo 'Cron key must not be null or empty.';
        endif;
    }

    public function daily($cron_key = ''){
        if(!empty($cron_key) && $cron_key !='' && $cron_key !=NULL):
            if($this->Cron_model->check_cron_key($cron_key)):
                if($this->Cron_model->is_cron_enabled($cron_key)):

                    $this->Match_calender();
                    $this->match_playing_eleven();
                    $this->update_match_status_from_live_to_result();
                    $this->update_points();
                    $this->match_cancelled();
                    $this->update_score_points();
                    $this->update_match_status();
                    $this->update_match_time();
                    
                    echo 'Cron process finished';
                else:
                    echo 'Cron is Disabled.';
                endif;
            else:
                echo 'Cron key is invalid.';
            endif;
        else:
            echo 'Cron key must not be null or empty.';
        endif;
    }

    public function weekly($cron_key = ''){
        if(!empty($cron_key) && $cron_key !='' && $cron_key !=NULL):
            if($this->Cron_model->check_cron_key($cron_key)):
                if($this->Cron_model->is_cron_enabled($cron_key)):
                    
                    echo 'Cron process finished';
                else:
                    echo 'Cron is Disabled.';
                endif;
            else:
                echo 'Cron key is invalid.';
            endif;
        else:
            echo 'Cron key must not be null or empty.';
        endif;
    }

    public function monthly($cron_key = ''){
        if(!empty($cron_key) && $cron_key !='' && $cron_key !=NULL):
            if($this->Cron_model->check_cron_key($cron_key)):
                if($this->Cron_model->is_cron_enabled($cron_key)):
                    
                    echo 'Cron process finished';
                else:
                    echo 'Cron is Disabled.';
                endif;
            else:
                echo 'Cron key is invalid.';
            endif;
        else:
            echo 'Cron key must not be null or empty.';
        endif;
    }


    public function Match_calender()
    {
        $Startdate = date('Y-m-d');
        $Enddate = date( "Y-m-d", strtotime( "+10 days"));

        $api_url  = "https://cricket.sportmonks.com/api/v2.0/fixtures?api_token=".$this->api_token."&filter[starts_between]=".$Startdate.",".$Enddate."&include=league,localteam,visitorteam,league";
        
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
                            $api_url_first  = "https://cricket.sportmonks.com/api/v2.0/teams/".$localteam_id."/squad/".$season_id."?api_token=".$this->api_token."";

                            $api_url_second  = "https://cricket.sportmonks.com/api/v2.0/teams/".$visitorteam_id."/squad/".$season_id."?api_token=".$this->api_token."";

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

    function match_playing_eleven()
    {        
        $matchs = $this->Cron_model->get_match_less_than_one_hour();  
        if(is_array($matchs)) {
            foreach($matchs as $match)
            {
                $m_id = $match['unique_id'];
                $api_url = "https://cricket.sportmonks.com/api/v2.0/fixtures/".$m_id."?api_token=".$this->api_token."&include=lineup";
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL,$api_url);
                $result=curl_exec($ch);
                curl_close($ch);
                $result_responce = json_decode(json_encode(json_decode($result)), True);
                 
                $playing11 = $result_responce['data']['lineup'];
                
                if( isset($playing11) && count($playing11) > 0 ) { 
                    $this->Cron_model->update_match_play_eleven($m_id);
                    foreach($playing11 as $team)
                    { 
                        $p_id =  $this->Cron_model->get_match_player_id($m_id,$team['id']);
                        $p_elleven = array('playing_status'=>'1');
                        $this->db->where('id',$p_id['m_p_id']);
                        $this->db->update('match_players',$p_elleven);
                    }
                }
            }
        }
    }

    //// API for update match status from live to result
    function update_match_status_from_live_to_result()
    {
        $live_matches = $this->Cron_model->get_all_live_match();

        foreach ($live_matches as $live_match) 
        {
            $api_url = "https://cricket.sportmonks.com/api/v2.0/fixtures/".$live_match->unique_id."?api_token=".$this->api_token."";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL,$api_url);
            $result=curl_exec($ch);
            curl_close($ch);
            $cricketMatches= json_decode(json_encode(json_decode($result)), True); //json_decode($result);
            
            if($cricketMatches['data']['status'] =="Finished")
            {  
                $match_result =  $this->Cron_model->get_match_by_unique_id($live_match->unique_id);
            
                if($match_result !="")
                {        
                    $winner =   $this->db->get_where('team',array('unique_id'=>$cricketMatches['data']['winner_team_id']))->row();
                    $tosswinner =   $this->db->get_where('team',array('unique_id'=>$cricketMatches['data']['toss_won_team_id']))->row();
                            
                    $matchdata = array('match_status'=>"Result",
                            'winner_team'=>$winner->team_id,
                            'toss_winner_team'=>$tosswinner->team_id,
                            'match_status_note'=>$cricketMatches['data']['note']
                        );

                    $data = array('match_status'=>"Result",
                            'winner_team'=>$winner->team_id,
                            'toss_winner_team'=>$tosswinner->team_id
                        );    
                        
                    $this->Cron_model->update_match_status_by_unique_id($match_result->match_id,$matchdata);
                    $this->Cron_model->update_match_status_by_match_id($match_result->match_id);
                    $this->update_leaderboard_user($match_result->match_id);
                    $this->update_rank_user($match_result->match_id);
                    $this->update_winning_amount_user($match_result->match_id);
                                         
                }
            }  
        }
    }

    // API for update leaderbord points
    function update_leaderboard_user($request)
    {   
        $id['match_id'] = $request;  
        
        $resp = $this->Cron_model->get_team_record($id);
        foreach ($resp as $key) 
        {
            $batting = $this->Cron_model->my_team_player_batting($key->user_id, $key->id);
            $balling = $this->Cron_model->my_team_player_balling($key->user_id, $key->id);
            $fielding = $this->Cron_model->my_team_player_fielding($key->user_id, $key->id);
            $batting_second = $this->Cron_model->my_team_player_batting_second($key->user_id, $key->id);
            $bolling_second = $this->Cron_model->my_team_player_bolling_second($key->user_id, $key->id);
            $fielding_second = $this->Cron_model->my_team_player_fielding_second($key->user_id, $key->id);

            $total_points = $batting + $balling + $fielding + $batting_second + $bolling_second + $fielding_second;
            
            $this->Cron_model->update_leaderboard($key->id,$key->match_id,$key->user_id,$total_points);
            
        }   
    }

    function update_rank_user($request)
    {
        $id = $request; 

        $contests = $this->Cron_model->get_total_contest_for_match($id);

        foreach ($contests as $contest) 
        {               
            $resp = $this->Cron_model->get_team_record_by_rank_data($id, $contest['contest_id']);
            $i = 1;             
            foreach ($resp as $key) 
            {   
                $this->Cron_model->update_rankby_points($key->id, $i);
                $i++;
            }   
        }
    }

    // API for update winning amount in user account
    function update_winning_amount_user($request)
    {
        $resp = $this->Cron_model->get_team_record_by_rank($request);
        foreach ($resp as $value) 
        {           
            $price = $this->Cron_model->get_price($value->rank,$value->contestid);            
            $winning_amount = array('user_id' =>$value->user_id,
                        'amount'=>$price,
                        'type'=>"winning",
                        'transaction_status'=>'SUCCESS',
                        'contest_id'=>$value->contestid,
                        'transection_mode'=>'for winning contest',
                        'created_date'=> date('Y-m-d H:i:s'),
                        );

            $this->Cron_model->common_insert($winning_amount,'transection');          
        }
    }

    //API for update points for playing eleven
    function update_points()
    {
        header('Content-type: application/json');
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata,true); 
        $matchs = $this->Cron_model->get_all_live_match();
        if(count($matchs) > 0)
        {   
            foreach ($matchs as $match) 
            {
                $id = $this->Cron_model->get_match($match->match_id);                
                $api_url = "https://cricket.sportmonks.com/api/v2.0/fixtures/".$id->unique_id."?api_token=".$this->api_token."&include=lineup";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL,$api_url);
                $result=curl_exec($ch);
                curl_close($ch);
                $resp = json_decode(json_encode(json_decode($result)), True);     
                $score = $this->Cron_model->playing_eleven('1'); 
                if($match->match_status =="Live")
                {
                    if($id->type =="ODI" or $id->type =="Woman ODI")
                    {   
                        $teamA =  $resp['data']['lineup'];
                        foreach ($teamA as $player) 
                        {    
                            if($player['id'] != "")
                            {
                                $playerinfo = $this->Cron_model->player_info($player['id']);
                                $player_id = $playerinfo->id;  
                                $this->Cron_model->playing_eleven_score($score->odiscore,$player_id,$id->match_id);
                                $this->user_playing_eleven($score->odiscore,$player_id,$id->match_id);
                            }
                        }                     
                    }
                    else if($id->type =="T10")
                    {   
                        $teamA =  $resp['data']['lineup'];
                        foreach ($teamA as $player) 
                        {    
                            if($player['id'] != "" )
                            {
                                $playerinfo = $this->Cron_model->player_info($player['id']);
                                $player_id = $playerinfo->id;  
                                $this->Cron_model->playing_eleven_score($score->t10score,$player_id,$id->match_id);
                                $this->user_playing_eleven($score->t10score,$player_id,$id->match_id);
                            }
                        }                     
                    }

                    else if($id->type =="Twenty20" or $id->type =="T20" or $id->type =="Woman T20" or $id->type =="T20I")
                    {                       
                        $teamA =  $resp['data']['lineup'];
                        foreach ($teamA as $player) 
                        {    
                            if($player['id'] != "")
                            {
                                $playerinfo = $this->Cron_model->player_info($player['id']);
                                $player_id = $playerinfo->id;  
                                $this->Cron_model->playing_eleven_score($score->t20score,$player_id,$id->match_id);
                                $this->user_playing_eleven($score->t20score,$player_id,$id->match_id);
                            }   
                        }
                    }

                    else if($id->type =="Test")
                    {   
                        $teamA =  $resp['data']['lineup'];
                        foreach ($teamA as $player) 
                        { 
                            if($player['id'] != "")
                            {
                                $playerinfo = $this->Cron_model->player_info($player['id']);
                                $player_id = $playerinfo->id;  
                                $this->Cron_model->playing_eleven_score($score->testscore,$player_id,$id->match_id);
                                $this->user_playing_eleven($score->testscore,$player_id,$id->match_id);
                            }
                        }
                    }
                }                
            }
        }           
    }

    // APi for user plauying eleven score update 
    function user_playing_eleven($score,$player_id,$match_id)
    { 
        $allteams = $this->Cron_model->all_team_for_this_match($match_id);
        
        foreach ($allteams as $allteam) {
            $pla = $this->checkplayer($player_id,$allteam);

            if($pla !="")
            {                           
                $captain = $this->check_captain($pla['id']);
                $vicecaptain = $this->check_vicecaptain($pla['id']);
                if($captain['is_captain'] =="1")
                {   
                    $data = array('total_points' =>2*$score,'playing_score'=>'1');
                    $this->Cron_model->update_user_points_for_match($data,$pla);
                    
                }
                elseif ($vicecaptain['is_vicecaptain'] =="1") {
                    $data = array('total_points' =>1.5*$score,'playing_score'=>'1');
                    $this->Cron_model->update_user_points_for_match($data,$pla);
                }
                else
                {
                    $data = array('total_points' =>$score,'playing_score'=>'1');
                    $this->Cron_model->update_user_points_for_match($data,$pla);
                }   
            }               
        }
        
    }

    // API for check user player is playing or not not
    function checkplayer($player_id,$allteam)
    {
        return $this->Cron_model->check_playerfor_user_bymatch($player_id,$allteam);
    }

    // API for check user player is captain or not not
    function check_captain($id)
    {
        return $this->Cron_model->check_playerfor_user_captain($id);
    }

    // API for check user player is vice captain or not not
    function check_vicecaptain($id)
    {
        return $this->Cron_model->check_playerfor_user_vicecaptain($id);
    }

    function match_cancelled()
    {
        $today_matches = $this->Cron_model->get_today_match();
        if(is_array($today_matches)) {
            foreach ($today_matches as $today_matche) 
            {
                $api_url = "https://cricket.sportmonks.com/api/v2.0/fixtures/".$today_matche['unique_id']."?api_token=".$this->api_token."";
    
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL,$api_url);
                $result=curl_exec($ch);
                curl_close($ch);
                $result_responce = json_decode(json_encode(json_decode($result)), True);
    
                if($result_responce['data']['status'] == "Postp" or $result_responce['data']['status'] = "Cancl")
                {    
                   $response = $this->Cron_model->get_match_id_by_unique_id($today_matche['unique_id']);
                   
                    if($response !="")
                    {
                        $status = $result_responce['data']['note'];
                        $data = array('match_status'=>'Result','match_status_note'=>$status,'cancelled'=>'1');
                       
                        $this->Cron_model->update_match_status_by_unique_id($response->match_id, $data);
                      
                        $dataone = array('match_status'=>'Result','cancelled'=>'1');
                      
                        $this->Cron_model->update_match_status_by_unique_id_user($response->match_id, $dataone);
                    } 
                }
            }
        }       
    }

    // API for update points for playing eleven
    function update_score_points()
    {
        header('Content-type: application/json');
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata,true); 
        
        $matchs = $this->Cron_model->get_all_live_match();
        
        if(count($matchs) > 0)
        {   
            foreach ($matchs as $match) 
            {
                $id = $this->Cron_model->get_match($match->match_id);

                $api_url = "https://cricket.sportmonks.com/api/v2.0/fixtures/".$id->unique_id."?api_token=".$this->api_token."&include=batting,bowling";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL,$api_url);
                $result=curl_exec($ch);
                curl_close($ch);
                $resp = json_decode(json_encode(json_decode($result)), True);
              
                if($id->type =="ODI")
                {   
                    $playing_eleven = $this->Cron_model->playing_eleven("1");
                    $single = $this->Cron_model->playing_eleven("2");
                    $duck = $this->Cron_model->playing_eleven("8");
                    $six = $this->Cron_model->playing_eleven("10");
                    $four = $this->Cron_model->playing_eleven("9");   
                    $fifty = $this->Cron_model->playing_eleven("11"); 
                    $hundred = $this->Cron_model->playing_eleven("12");
                    $between50_60 = $this->Cron_model->playing_eleven("33");
                    $between40_50 = $this->Cron_model->playing_eleven("34");
                    $below40 = $this->Cron_model->playing_eleven("35");
                    
                    foreach ($resp['data']['batting'] as $player) 
                    {
                        $single_runs = $single->odiscore;
                        $four_runs = $four->odiscore;
                        $six_runs = $six->odiscore;

                        $six_hits = $player['six_x'];
                        $four_hits = $player['four_x'];
                        $run_hits = $player['score'];
                        $strike_rate = $player['rate'];

                        $single_hits = $run_hits ;//- $four_hits*4 - $six_hits*6;   

                        if($single_hits =="0")
                        {
                            $playerinfo = $this->Cron_model->player_info($player['player_id']);
                            if($playerinfo->designationid !="2")
                            {
                                $total_score = $playing_eleven->odiscore + $duck->odiscore;
                                    $player_id = $playerinfo->id; 

                                $this->Cron_model->update_playing_eleven_score($total_score,$player_id,$id->match_id);

                                $this->update_playing_eleven_score_for_user_battng($total_score,$player_id,$id->match_id);
                            } 
                        }   
                        else
                        {
                            if($strike_rate <60 && $strike_rate >50)
                            {
                                $s_rate = $between50_60->odiscore;
                            }
                            else if($strike_rate <40.99 && $strike_rate >40)
                            {
                                $s_rate = $between40_50->odiscore;
                            }
                            else if($strike_rate <40)
                            {
                                $s_rate = $below40->odiscore;
                            }

                            if($single_hits >=50 && $single_hits <100)
                            {
                                $total_score = $single_runs*$single_hits + $four_runs*$four_hits + $six_runs*$six_hits + $playing_eleven->odiscore + $fifty->odiscore +$s_rate;
                            }
                            else if($single_hits >=100)
                            {
                                $total_score = $single_runs*$single_hits + $four_runs*$four_hits + $six_runs*$six_hits + $playing_eleven->odiscore + $hundred->odiscore +$s_rate;
                            }
                            else
                            {
                                $total_score = $single_runs*$single_hits + $four_runs*$four_hits + $six_runs*$six_hits + $playing_eleven->odiscore +$s_rate;
                            }

                            $playerinfo = $this->Cron_model->player_info($player['player_id']);
                            $player_id = $playerinfo->id;  

                            $this->Cron_model->update_playing_eleven_score($total_score,$player_id,$id->match_id); 

                            $this->update_playing_eleven_score_for_user_battng($total_score,$player_id,$id->match_id);
                            }                        
                    }
                    

                    $wicket = $this->Cron_model->playing_eleven("3");

                    $maiden_over = $this->Cron_model->playing_eleven("13");

                    $four_wicket = $this->Cron_model->playing_eleven("14");
                    $five_wicket =$this->Cron_model->playing_eleven("15");

                    $between45_35 =$this->Cron_model->playing_eleven("23");
                    $between349_25 =$this->Cron_model->playing_eleven("24");
                    $below25 =$this->Cron_model->playing_eleven("25");
                    $between7_8 =$this->Cron_model->playing_eleven("26");
                    $between801_9 =$this->Cron_model->playing_eleven("27");
                    $above9 =$this->Cron_model->playing_eleven("28");

                    foreach ($resp['data']['bowling'] as $bowling) 
                    {
                        $Maiden  = $bowling['medians'];
                        $wickets = $bowling['wickets'];
                        $Economic = $bowling['rate'];
                        
                        $maiden_over_bolled = $Maiden*$maiden_over->odiscore;

                        $wickets_taken = $wickets*$wicket->odiscore;

                        if($wickets =="4")
                        {
                            $wickets_taken_four = $four_wicket->odiscore;
                        }
                        else
                        {
                            $wickets_taken_four = 0;
                        }
                        if($wickets >="5")
                        {
                            $wickets_taken_five = $five_wicket->odiscore;
                        }
                        else
                        {
                            $wickets_taken_five = 0;
                        }

                        if($Economic <4.5 && $Economic >3.5)
                        {
                            $economic_rate = $between45_35->odiscore;
                        }

                        else if($Economic <3.49 && $Economic >2.5)
                        {
                            $economic_rate = $between349_25->odiscore;
                        }

                        else if($Economic <2.5)
                        {
                            $economic_rate = $below25->odiscore;
                        }   
                        else if($Economic <8 && $Economic >7)
                        {
                            $economic_rate = $between7_8->odiscore;
                        }

                        else if($Economic <= 9 && $Economic >8.01)
                        {
                            $economic_rate = $between801_9->odiscore;
                        }
                        else if($Economic >9)
                        {
                            $economic_rate = $above9->odiscore;
                        }
                        else if($Economic >4.5 && $Economic < 7)
                        {
                            $economic_rate = "0";
                        } else {
                            $economic_rate = "0";
                        }  

                        $bolling_score = $maiden_over_bolled+$wickets_taken+$wickets_taken_four+$wickets_taken_five+$economic_rate;
                        $playerinfo = $this->Cron_model->player_info($bowling['player_id']);
                        $player_id = $playerinfo->id;  

                        $this->Cron_model->update_playing_eleven_bolling_score($bolling_score,$player_id,$id->match_id);  

                        $this->update_playing_eleven_score_for_user_bolling($bolling_score,$player_id,$id->match_id);
                        
                    }   

                    $catch_points = $this->Cron_model->playing_eleven("4");

                    foreach ($resp['data']['batting'] as $key ) {
                        if($key['catch_stump_player_id'] !="")
                        {
                           $fielding[] = $key['catch_stump_player_id'];
                        }    
                    }
                    $fieldings = array_count_values($fielding);
                    

                    foreach ($fieldings as $key => $value) {

                        $fielding_score = $value*$catch_points->odiscore;

                        $playerinfo = $this->Cron_model->player_info($key);
                        $player_id = $playerinfo->id;  

                        $this->Cron_model->update_playing_eleven_fielding_score($fielding_score,$player_id,$id->match_id); 
                        $this->update_playing_eleven_score_for_user_fielding($fielding_score,$player_id,$id->match_id);
                        
                    }
                }

                else if($id->type =="T10")
                {   
                    $playing_eleven = $this->Cron_model->playing_eleven("1");

                    $single = $this->Cron_model->playing_eleven("2");
                    $duck = $this->Cron_model->playing_eleven("8");   
                    $four = $this->Cron_model->playing_eleven("9");   
                    $six = $this->Cron_model->playing_eleven("10");
                    $fifty = $this->Cron_model->playing_eleven("11"); 
                    
                    $morethan30 = $this->Cron_model->playing_eleven("38");
                    $between90_100 = $this->Cron_model->playing_eleven("45");
                    $between80_90 = $this->Cron_model->playing_eleven("46");
                    $below80 = $this->Cron_model->playing_eleven("47");

                    foreach ($resp['data']['batting'] as $player) 
                    {
                        $single_runs = $single->t10score;
                        $four_runs = $four->t10score;
                        $six_runs = $six->t10score;

                        $six_hits = $player['six_x'];
                        $four_hits = $player['four_x'];
                        $run_hits = $player['score'];
                        $strike_rate = $player['rate'];
                        $single_hits = $run_hits;

                        $playerinfo = $this->Cron_model->player_info($player['player_id']);
                        $player_id = $playerinfo->id;

                        if($single_hits =="0")
                        {
                            if($playerinfo->designationid !="2")
                            {
                                $total_score = $playing_eleven->t10score + $duck->t10score;
                                $this->Cron_model->update_playing_eleven_score($total_score,$player_id,$id->match_id);

                                $this->update_playing_eleven_score_for_user_battng($total_score,$player_id,$id->match_id);
                            }                           
                        }
                        else
                        {
                            if($strike_rate <90 && $strike_rate >100)
                            {
                                $s_rate = $between90_100->t10score;
                            }
                            else if($strike_rate <89.99 && $strike_rate >80)
                            {
                                $s_rate = $between80_90->t10score;
                            }
                            else if($strike_rate <80)
                            {
                                $s_rate = $below80->t10score;
                            }
                            else
                            {
                                $s_rate = 0;
                            }   

                            if($single_hits >=50 && $single_hits <100)
                            {
                                $total_score = $single_runs*$single_hits + $four_runs*$four_hits + $six_runs*$six_hits + $playing_eleven->t10score + $morethan30 ->t10score + $fifty->t10score +$s_rate;
                            }
                            else if($single_hits >=30 && $single_hits <50)
                            {
                                $total_score = $single_runs*$single_hits + $four_runs*$four_hits + $six_runs*$six_hits + $playing_eleven->t10score + $morethan30 ->t10score +$s_rate;
                            }
                            else
                            {
                                $total_score = $single_runs*$single_hits + $four_runs*$four_hits + $six_runs*$six_hits + $playing_eleven->t10score +$s_rate;
                            }   

                            $this->Cron_model->update_playing_eleven_score($total_score,$player_id,$id->match_id);
                            
                            $this->update_playing_eleven_score_for_user_battng($total_score,$player_id,$id->match_id);
                        }                         
                                               
                    }

                    $wicket = $this->Cron_model->playing_eleven("3");

                    $maiden_over = $this->Cron_model->playing_eleven("13");
                    $two_wicket = $this->Cron_model->playing_eleven("36");
                    $three_wicket =$this->Cron_model->playing_eleven("37");
                    $below6 =$this->Cron_model->playing_eleven("39");
                    $between6_7 =$this->Cron_model->playing_eleven("40");
                    $between7_8 =$this->Cron_model->playing_eleven("41");
                    $between11_12 =$this->Cron_model->playing_eleven("42");
                    $between12_13 =$this->Cron_model->playing_eleven("43");
                    $above13 =$this->Cron_model->playing_eleven("44");
                    
                    foreach ($resp['data']['bowling'] as $bowling) 
                    {
                        $Maiden  = $bowling['medians'];
                        $wickets = $bowling['wickets'];
                        $Economic = $bowling['rate'];

                        $maiden_over_bolled = $Maiden*$maiden_over->t10score;

                        $wickets_taken = $wickets*$wicket->t10score;

                        if($wickets =="2")
                        {
                            $wickets_taken_two = $two_wicket->t10score;
                        }
                        else
                        {
                            $wickets_taken_two = 0;
                        }
                        if($wickets >="3")
                        {
                            $wickets_taken_three = $three_wicket->t10score;
                        }
                        else
                        {
                            $wickets_taken_three = 0;
                        }

                        if($Economic <6 )
                        {
                            $economic_rate = $below6->t10score;
                        }

                        else if($Economic <7 && $Economic >6)
                        {
                            $economic_rate = $between6_7->t10score;
                        }
                        else if($Economic <8 && $Economic >7)
                        {
                            $economic_rate = $between7_8->t10score;
                        }
                        else if($Economic <12 && $Economic >11)
                        {
                            $economic_rate = $between11_12->t10score;
                        }
                        else if($Economic <13 && $Economic >12)
                        {
                            $economic_rate = $between12_13->t10score;
                        }
                        else if($Economic >13)
                        {
                            $economic_rate = $above13->t10score;
                        }

                        $bolling_score = $maiden_over_bolled+$wickets_taken+$wickets_taken_two+$wickets_taken_three+$economic_rate;

                        $playerinfo = $this->Cron_model->player_info($bowling['player_id']);
                        $player_id = $playerinfo->id;  

                        $this->Cron_model->update_playing_eleven_bolling_score($bolling_score,$player_id,$id->match_id);  
                        $this->update_playing_eleven_score_for_user_bolling($bolling_score,$player_id,$id->match_id);
                    
                    }

                    $catch_points = $this->Cron_model->playing_eleven("4");

                    foreach ($resp['data']['batting'] as $key ) {
                        if($key['catch_stump_player_id'] !="")
                        {
                           $fielding[] = $key['catch_stump_player_id'];
                        }    
                    }
                    $fieldings = array_count_values($fielding);
                    

                    foreach ($fieldings as $key => $value) {

                        $fielding_score = $value*$catch_points->t10score;

                        $playerinfo = $this->Cron_model->player_info($key);
                        $player_id = $playerinfo->id;  

                        $this->Cron_model->update_playing_eleven_fielding_score($fielding_score,$player_id,$id->match_id); 
                        $this->update_playing_eleven_score_for_user_fielding($fielding_score,$player_id,$id->match_id);
                    }
                }

                else if($id->type =="Twenty20" or $id->type =="T20" or $id->type =="Woman T20" or $id->type =="T20I")
                {  
                    $playing_eleven = $this->Cron_model->playing_eleven("1");

                    $single = $this->Cron_model->playing_eleven("2");
                    $six = $this->Cron_model->playing_eleven("10");
                    $duck = $this->Cron_model->playing_eleven("8");   
                    $four = $this->Cron_model->playing_eleven("9");   
                    $fifty = $this->Cron_model->playing_eleven("11"); 
                    $hundred = $this->Cron_model->playing_eleven("12");   

                    $between60_70 = $this->Cron_model->playing_eleven("30");
                    $between59_60 = $this->Cron_model->playing_eleven("31");
                    $below50 = $this->Cron_model->playing_eleven("32");

                    foreach ($resp['data']['batting'] as $player)
                    {
                        $single_runs = $single->t20score;
                        $four_runs = $four->t20score;
                        $six_runs = $six->t20score;

                        $six_hits = $player['six_x'];
                        $four_hits = $player['four_x'];
                        $run_hits = $player['score'];
                        $strike_rate = $player['rate'];
                        $single_hits = $run_hits;// - $four_hits*4 - $six_hits*6;

                        $playerinfo = $this->Cron_model->player_info($player['player_id']);
                        $player_id = $playerinfo->id;

                            if($single_hits =="0")
                            {
                                if($playerinfo->designationid !="2")
                                {
                                    $total_score = $playing_eleven->t20score + $duck->t20score;
                                    $this->Cron_model->update_playing_eleven_score($total_score,$player_id,$id->match_id);

                                    $this->update_playing_eleven_score_for_user_battng($total_score,$player_id,$id->match_id);
                                }                           
                            }
                            else
                            {
                                if($strike_rate <70 && $strike_rate >60)
                                {
                                    $s_rate = $between60_70->t20score;
                                }
                                else if($strike_rate <59.99 && $strike_rate >50)
                                {
                                    $s_rate = $between59_60->t20score;
                                }
                                else if($strike_rate <50)
                                {
                                    $s_rate = $below50->t20score;
                                }
                                else
                                {
                                    $s_rate = 0;
                                }   

                                if($single_hits >=50 && $single_hits <100)
                                {
                                    $total_score = $single_runs*$single_hits + $four_runs*$four_hits + $six_runs*$six_hits + $playing_eleven->t20score + $fifty->t20score +$s_rate;
                                }
                                else if($single_hits >=100)
                                {
                                    $total_score = $single_runs*$single_hits + $four_runs*$four_hits + $six_runs*$six_hits + $playing_eleven->t20score + $hundred->t20score +$s_rate;
                                }   
                                else
                                {
                                    $total_score = $single_runs*$single_hits + $four_runs*$four_hits + $six_runs*$six_hits + $playing_eleven->t20score +$s_rate;
                                }   
                                
                                
                                $this->Cron_model->update_playing_eleven_score($total_score,$player_id,$id->match_id);

                                $this->update_playing_eleven_score_for_user_battng($total_score,$player_id,$id->match_id);
                            }
                         
                        }                       
                    
                    $wicket = $this->Cron_model->playing_eleven("3");

                    $maiden_over = $this->Cron_model->playing_eleven("13");
                    $four_wicket = $this->Cron_model->playing_eleven("14");
                    $five_wicket =$this->Cron_model->playing_eleven("15");
                    $between6_5 =$this->Cron_model->playing_eleven("17");
                    $between5_4 =$this->Cron_model->playing_eleven("18");
                    $below4 =$this->Cron_model->playing_eleven("19");
                    $between9_10 =$this->Cron_model->playing_eleven("20");
                    $between10_11 =$this->Cron_model->playing_eleven("21");
                    $above11 =$this->Cron_model->playing_eleven("22");
                    
                    foreach ($resp['data']['bowling'] as $bowling) {

                        $Maiden  = $bowling['medians'];
                        $wickets = $bowling['wickets'];
                        $Economic = $bowling['rate'];

                        $maiden_over_bolled = $Maiden*$maiden_over->t20score;

                        $wickets_taken = $wickets*$wicket->t20score;

                        if($wickets =="4")
                        {
                            $wickets_taken_four = $four_wicket->t20score;
                        }
                        else
                        {
                            $wickets_taken_four = 0;
                        }
                        if($wickets >="5")
                        {
                            $wickets_taken_five = $five_wicket->t20score;
                        }
                        else
                        {
                            $wickets_taken_five = 0;
                        }

                        if($Economic <6 && $Economic >5)
                        {
                            $economic_rate = $between6_5->t20score;
                        }

                        else if($Economic <4.99 && $Economic >4)
                        {
                            $economic_rate = $between5_4->t20score;
                        }

                        else if($Economic <4)
                        {
                            $economic_rate = $below4->t20score;
                        }   
                        else if($Economic <10 && $Economic >9)
                        {
                            $economic_rate = $between9_10->t20score;
                        }

                        else if($Economic <11 && $Economic >10.01)
                        {
                            $economic_rate = $between10_11->t20score;
                        }
                        else if($Economic >11)
                        {
                            $economic_rate = $above11->t20score;
                        }
                        else if($Economic > 6 && $Economic <9)
                        {
                            $economic_rate = "0";
                        } else {
                            $economic_rate = "0";
                        }

                        $bolling_score = $maiden_over_bolled+$wickets_taken+$wickets_taken_four+$wickets_taken_five+$economic_rate;

                        $playerinfo = $this->Cron_model->player_info($bowling['player_id']);
                        $player_id = $playerinfo->id;  

                        $this->Cron_model->update_playing_eleven_bolling_score($bolling_score,$player_id,$id->match_id);  
                        $this->update_playing_eleven_score_for_user_bolling($bolling_score,$player_id,$id->match_id);
                    }
                    

                    $catch_points = $this->Cron_model->playing_eleven("4");

                    foreach ($resp['data']['batting'] as $key ) {
                        if($key['catch_stump_player_id'] !="")
                        {
                           $fielding[] = $key['catch_stump_player_id'];
                        }    
                    }
                    $fieldings = array_count_values($fielding);
                    

                    foreach ($fieldings as $key => $value) {

                        $fielding_score = $value*$catch_points->t20score;

                        $playerinfo = $this->Cron_model->player_info($key);
                        $player_id = $playerinfo->id;  

                        $this->Cron_model->update_playing_eleven_fielding_score($fielding_score,$player_id,$id->match_id); 
                        $this->update_playing_eleven_score_for_user_fielding($fielding_score,$player_id,$id->match_id);
                    }
                }

                else if($id->type =="Test")
                {
                    $playing_eleven = $this->Cron_model->playing_eleven("1");
                    $single = $this->Cron_model->playing_eleven("2");
                    $wicket = $this->Cron_model->playing_eleven("3");
                    $catch_points = $this->Cron_model->playing_eleven("4");
                    $six = $this->Cron_model->playing_eleven("10");
                    $duck = $this->Cron_model->playing_eleven("8");   
                    $four = $this->Cron_model->playing_eleven("9");   
                    $fifty = $this->Cron_model->playing_eleven("11"); 
                    $hundred = $this->Cron_model->playing_eleven("12");   
                    $four_wicket = $this->Cron_model->playing_eleven("14");
                    $five_wicket =$this->Cron_model->playing_eleven("15");

                    $single_runs = $single->testscore;
                    $four_runs = $four->testscore;
                    $six_runs = $six->testscore;
                        foreach ($resp['data']['batting'] as $player)
                        {
                            if($player['scoreboard'] =="S1" or $player['scoreboard'] =="S2" )
                            {
                                $six_hits = $player['six_x'];
                                $four_hits = $player['four_x'];
                                $run_hits = $player['score'];
                                $single_hits = $run_hits;
                                $playerinfo = $this->Cron_model->player_info($player['player_id']);
                                $player_id = $playerinfo->id;

                                if($single_hits =="0")
                                {
                                    if($playerinfo->designationid !="2")
                                    {
                                        $total_score = $playing_eleven->testscore + $duck->testscore;
                                        
                                        $this->Cron_model->update_playing_eleven_score($total_score,$player_id,$id->match_id);

                                        $this->update_playing_eleven_score_for_user_battng($total_score,$player_id,$id->match_id);
                                    }                           
                                }
                                else
                                {
                                    if($single_hits >=50 && $single_hits <100)
                                {
                                    $total_score = $single_runs*$single_hits + $four_runs*$four_hits + $six_runs*$six_hits + $playing_eleven->testscore + $fifty->testscore;
                                }
                                else if($single_hits >=100)
                                {
                                    $total_score = $single_runs*$single_hits + $four_runs*$four_hits + $six_runs*$six_hits + $playing_eleven->testscore + $hundred->testscore;
                                }   
                                else
                                {
                                    $total_score = $single_runs*$single_hits + $four_runs*$four_hits + $six_runs*$six_hits + $playing_eleven->testscore;
                                }       
                                $this->Cron_model->update_playing_eleven_score($total_score,$player_id,$id->match_id);
                             
                                $this->update_playing_eleven_score_for_user_battng($total_score,$player_id,$id->match_id);
                                }   
                            }  
                            
                            else if($player['scoreboard'] =="S3" or $player['scoreboard'] =="S4" )
                            { 
                                $six_hits = $player['six_x'];
                                $four_hits = $player['four_x'];
                                $run_hits = $player['score'];
                                $single_hits = $run_hits;
                                
                                $playerinfo = $this->Cron_model->player_info($player['player_id']);
                                $player_id = $playerinfo->id;

                                if($single_hits =="0")
                                {
                                    if($playerinfo->designationid !="2")
                                    {
                                        $total_score =$duck->testscore;
                                    
                                        $this->Cron_model->update_playing_eleven_score_second_innings($total_score,$player_id,$id->match_id);

                                        $this->update_playing_eleven_score_for_user_battng_second($total_score,$player_id,$id->match_id);
                                    }                           
                                }
                                else
                                {
                                    if($single_hits >=50 && $single_hits <100)
                                {
                                    $total_score = $single_runs*$single_hits + $four_runs*$four_hits + $six_runs*$six_hits + $fifty->testscore;
                                }
                                else if($single_hits >=100)
                                {
                                    $total_score = $single_runs*$single_hits + $four_runs*$four_hits + $six_runs*$six_hits + $hundred->testscore;
                                }   
                                else
                                {
                                    $total_score = $single_runs*$single_hits + $four_runs*$four_hits + $six_runs*$six_hits;
                                }       
                                $this->Cron_model->update_playing_eleven_score_second_innings($total_score,$player_id,$id->match_id);
                                
                                $this->update_playing_eleven_score_for_user_battng_second($total_score,$player_id,$id->match_id);
                                }   
                                
                            }
                        }   
                        
                        foreach ($resp['data']['bowling'] as $bowling) {
                            if($bowling['scoreboard'] =="S1" or $bowling['scoreboard'] =="S2")
                            {
                            $wickets = $bowling['wickets'];

                            $wickets_taken = $wickets*$wicket->testscore;

                            if($wickets =="4")
                            {
                                $wickets_taken_four = $four_wicket->testscore;
                            }
                            else
                            {
                                $wickets_taken_four = 0;
                            }
                            if($wickets >="5")
                            {
                                $wickets_taken_five = $five_wicket->testscore;
                            }
                            else
                            {
                                $wickets_taken_five = 0;
                            }

                            $bolling_score =$wickets_taken+$wickets_taken_four+$wickets_taken_five;

                            $playerinfo = $this->Cron_model->player_info($bowling['player_id']);
                            $player_id = $playerinfo->id;  

                            $this->Cron_model->update_playing_eleven_bolling_score($bolling_score,$player_id,$id->match_id);  
                            $this->update_playing_eleven_score_for_user_bolling($bolling_score,$player_id,$id->match_id);
                            }

                            else if($bowling['scoreboard'] =="S3" or $bowling['scoreboard'] =="S4")
                            {
                                $wickets = $bowling['wickets'];

                                $wickets_taken = $wickets*$wicket->testscore;

                                if($wickets =="4")
                                {
                                    $wickets_taken_four = $four_wicket->testscore;
                                }
                                else
                                {
                                    $wickets_taken_four = 0;
                                }
                                if($wickets >="5")
                                {
                                    $wickets_taken_five = $five_wicket->testscore;
                                }
                                else
                                {
                                    $wickets_taken_five = 0;
                                }

                                $bolling_score =$wickets_taken+$wickets_taken_four+$wickets_taken_five;

                                $playerinfo = $this->Cron_model->player_info($bowling['player_id']);
                                $player_id = $playerinfo->id;  

                                $this->Cron_model->update_playing_eleven_bolling_score_second_innings($bolling_score,$player_id,$id->match_id);   
                                $this->update_playing_eleven_score_for_user_bolling_second($bolling_score,$player_id,$id->match_id);
                            }
                        }   
                        

                        $catch_points = $this->Cron_model->playing_eleven("4");

                        foreach ($resp['data']['batting'] as $key ) {
                            if($key['catch_stump_player_id'] !="")
                            {
                               $fielding[] = $key['catch_stump_player_id'];
                            }    
                        }
                        $fieldings = array_count_values($fielding);
                        

                        foreach ($fieldings as $key => $value) {

                            $fielding_score = $value*$catch_points->testscore;

                            $playerinfo = $this->Cron_model->player_info($key);
                            $player_id = $playerinfo->id;  

                            $this->Cron_model->update_playing_eleven_fielding_score($fielding_score,$player_id,$id->match_id); 
                            $this->update_playing_eleven_score_for_user_fielding($fielding_score,$player_id,$id->match_id);
                        }
                                      
                }   
            }
        }           
    }

    // Api for update playing eleven score for batting
    function update_playing_eleven_score_for_user_battng($score,$player_id,$match_id)
    {       
        $allteams = $this->Cron_model->all_team_for_this_match($match_id);

        foreach ($allteams as $allteam) {
            $pla = $this->checkplayer($player_id,$allteam);
            
            if($pla !="")
            {                           
                $captain = $this->check_captain($pla['id']);
                $vicecaptain = $this->check_vicecaptain($pla['id']);
                if($captain['is_captain'] =="1")
                {   
                    $data = array('total_points' =>2*$score);
                    $this->Cron_model->update_user_points_for_match($data,$pla);
                    
                }
                elseif ($vicecaptain['is_vicecaptain'] =="1") {
                    $data = array('total_points' =>1.5*$score);
                    $this->Cron_model->update_user_points_for_match($data,$pla);
                }
                else
                {
                    $data = array('total_points' =>$score);


                    $this->Cron_model->update_user_points_for_match($data,$pla);
                }
                
            }   
            
        }

    }

     // Api for update playing eleven score for bolling 
     function update_playing_eleven_score_for_user_bolling($score,$player_id,$match_id)
     {
         $allteams = $this->Cron_model->all_team_for_this_match($match_id);
 
         foreach ($allteams as $allteam) {
             $pla = $this->checkplayer($player_id,$allteam);
             
             if($pla !="")
             {                           
                 $captain = $this->check_captain($pla['id']);
                 $vicecaptain = $this->check_vicecaptain($pla['id']);
                 if($captain['is_captain'] =="1")
                 {   
                     $data = array('bolling_points' =>2*$score);
                     $this->Cron_model->update_user_points_for_match($data,$pla);
                     
                 }
                 elseif ($vicecaptain['is_vicecaptain'] =="1") {
                     $data = array('bolling_points' =>1.5*$score);
                     $this->Cron_model->update_user_points_for_match($data,$pla);
                 }
                 else
                 {
                     $data = array('bolling_points' =>$score);
                     $this->Cron_model->update_user_points_for_match($data,$pla);
                 }
             }   
         }
     }

     // Api for update playing eleven score for fielding 
    function update_playing_eleven_score_for_user_fielding($score,$player_id,$match_id)
    {
        $allteams = $this->Cron_model->all_team_for_this_match($match_id);

        foreach ($allteams as $allteam) {
            $pla = $this->checkplayer($player_id,$allteam);
            
            if($pla !="")
            {                           
                $captain = $this->check_captain($pla['id']);
                $vicecaptain = $this->check_vicecaptain($pla['id']);
                if($captain['is_captain'] =="1")
                {   
                    $data = array('fielding_points' =>2*$score);
                    $this->Cron_model->update_user_points_for_match($data,$pla);
                    
                }
                elseif ($vicecaptain['is_vicecaptain'] =="1") {
                    $data = array('fielding_points' =>1.5*$score);
                    $this->Cron_model->update_user_points_for_match($data,$pla);
                }
                else
                {
                    $data = array('fielding_points' =>$score);
                    $this->Cron_model->update_user_points_for_match($data,$pla);
                }
                
            }   
            
        }
    }

    // Api for update playing eleven score for second batting for test match
    function update_playing_eleven_score_for_user_battng_second($score,$player_id,$match_id)
    {       
        $allteams = $this->Cron_model->all_team_for_this_match($match_id);

        foreach ($allteams as $allteam) {
            $pla = $this->checkplayer($player_id,$allteam);
            
            if($pla !="")
            {                           
                $captain = $this->check_captain($pla['id']);
                $vicecaptain = $this->check_vicecaptain($pla['id']);
                if($captain['is_captain'] =="1")
                {   
                    $data = array('second_innings_batting' =>2*$score);
                    $this->Cron_model->update_user_points_for_match($data,$pla);
                    
                }
                elseif ($vicecaptain['is_vicecaptain'] =="1") {
                    $data = array('second_innings_batting' =>1.5*$score);
                    $this->Cron_model->update_user_points_for_match($data,$pla);
                    
                }
                else
                {
                    $data = array('second_innings_batting' =>$score);
                    $this->Cron_model->update_user_points_for_match($data,$pla);
                }
                
            }   
            
        }

    }

     // Api for update playing eleven score for second bolling for test match
     function update_playing_eleven_score_for_user_bolling_second($score,$player_id,$match_id)
     {
         $allteams = $this->Cron_model->all_team_for_this_match($match_id);
 
         foreach ($allteams as $allteam) {
             $pla = $this->checkplayer($player_id,$allteam);
             
             if($pla !="")
             {                           
                 $captain = $this->check_captain($pla['id']);
                 $vicecaptain = $this->check_vicecaptain($pla['id']);
                 if($captain['is_captain'] =="1")
                 {   
                     $data = array('second_innings_bolling' =>2*$score);
                     $this->Cron_model->update_user_points_for_match($data,$pla);
                     
                 }
                 elseif ($vicecaptain['is_vicecaptain'] =="1") {
                     $data = array('second_innings_bolling' =>1.5*$score);
                     $this->Cron_model->update_user_points_for_match($data,$pla);
                 }
                 else
                 {
                     $data = array('second_innings_bolling' =>$score);
                     $this->Cron_model->update_user_points_for_match($data,$pla);
                 }
             }   
         }
     }

     function update_match_status()
    {
        $time = date('Y-m-d H:i:00');
        $response = $this->Cron_model->get_match_status_by_type($time,'Fixture');
        if(count($response) >0)
        {
            foreach ($response as $resp) {
                $id = $resp->match_id;

                $api_url  = "https://cricket.sportmonks.com/api/v2.0/fixtures/".$resp->unique_id."?api_token=".$this->api_token."";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL,$api_url);
                $result=curl_exec($ch);
                curl_close($ch);
                $resp = json_decode(json_encode(json_decode($result)), True);

                $match_status =  $resp['data']['status'];
                
                if($match_status =="Finished")
                {
                    $data = array('time'=>"0000-00-00 00:00:00",
                        "match_status" =>"Result",
                        'matchStarted'=>'1'
                    );
                }
                elseif($match_status =="live")
                {
                    $data = array('time'=>"0000-00-00 00:00:00",
                        "match_status" =>"Live",
                        'matchStarted'=>'1'
                    );                    
                }
                elseif($match_status =="Cancl")
                {
                    $data = array('time'=>"0000-00-00 00:00:00",
                        "match_status" =>"Result",
                        'matchStarted'=>'0',
                        'cancelled'=>'1'
                    );                    
                }
                elseif($match_status =="Postp")
                {
                    $data = array('time'=>"0000-00-00 00:00:00",
                        "match_status" =>"Result",
                        'matchStarted'=>'0',
                        'cancelled'=>'1'
                    );
                }

                $this->db->where('match_id',$id);
                $this->db->update('match',$data);

                $this->db->where('match_id',$id);
                $this->db->update('my_match',$data);
            }
        }       
    }

    function update_match_time()
    {
        $today_matches = $this->Cron_model->get_today_fixture_match();
        if(is_array($today_matches)) {
            foreach ($today_matches as $today_match) 
            {
                $api_url = "https://cricket.sportmonks.com/api/v2.0/fixtures/".$today_match['unique_id']."?api_token=".$this->api_token."";
                    
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL,$api_url);
                $result=curl_exec($ch);
                curl_close($ch);
                $match = json_decode(json_encode(json_decode($result)), True);
    
                $d  =   explode("T", $match['data']['starting_at']);
                $t = explode(".", $d['1']);
    
                $main_time = $d[0]. ' ' . $t[0];
    
                $date = new DateTime($main_time, new DateTimeZone('GMT'));
                $date->setTimezone(new DateTimeZone('Asia/Kolkata'));
                $match_update_time = $date->format('Y-m-d H:i:s');
    
                $saved_time  =$today_match['match_date_time'];
                $current_time = date('Y-m-d H:i:s'); 
                if($match_update_time !=$saved_time)
                {
                    if($match_update_time >= $current_time)
                    {
                        $match_data = array('time'=>$match_update_time,'match_date_time'=>$match_update_time);
                        $data = array('time'=>$match_update_time);
                        $this->Cron_model->update_match_status_by_unique_id($today_match['match_id'], $match_data);
                        $this->Cron_model->update_match_status_by_unique_id_user($today_match['match_id'], $data);
                    }               
                } 
            }
        }
    }
}