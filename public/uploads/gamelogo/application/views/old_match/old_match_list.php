<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Result Match
      </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#">match</a></li>
        <li class="breadcrumb-item active">Create</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
          
	 <div class="box box-default">
        
        <!-- /.box-header -->
        <div class="box-body wizard-content">
                    
            <div class="col-md-4 text-center">
                <div style="margin-top: 8px" id="message">
                    <?php echo $this->session->userdata('message') <> '' ? $this->session->userdata('message') : ''; ?>
                </div>
            </div>
              <div class="ibox-content">
                      <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" >
                          <thead>
                            <tr>
                              <th>No</th>
                              <th>Tean First</th>
                              <th>Tean Second</th>
                              <th>Match Status</th>
                              <th>Match Time</th>
                              <th>Match Type</th>
                              <th>Join Team Count</th>
                              <th>View Contests</th>
                              <th>Payment Status</th>
                              <th>Send Payment</th>
                            </tr>
                          </thead>
                          <tbody>
                         <?php
                         $start = 0;
            foreach ($match_data as $match)
            {
                ?>
                <tr>
            <td width="80px"><?php echo ++$start ?></td>
            <td><?php
               $teamone =  $this->match_model->team_name($match->teamid1);
             echo $teamone->team_name; ?></td>
            <td><?php 
              $teamtwo = $this->match_model->team_name($match->teamid2);
            echo $teamtwo->team_name; ?></td>
            <td><?php echo $match->match_status; ?></td>
            <td><?php echo $match->match_date_time; ?></td>
            <td><?php echo $match->type; ?></td>
            <td> 
              <?php $team_count = $this->match_model->total_team_join($match->match_id);
              echo count($team_count)
               ?>
             </td>
            <td><a href="<?php echo site_url('old_match/contest_list/'.$match->match_id);?>"><button style="border-radius: 6px; background-color: #55acee;" type="button" class="btn text-white"><i class="fa fa-eye me-2"></i>View</button> </a></td> 
          <!--   <td><a href="<?php // echo site_url('old_match/teams_list/'.$match->match_id);?>">View</a></td>  -->
            <td><?php if($match->payment_status =="1"){ echo '<span class="badge badge-success">Complete</span>' ;}else {echo '<span class="badge badge-danger">Incomplete</span>'; } ?></td>
            <td><?php if($match->payment_status =="1"){ ?>
            <span class="badge badge-success">Payment done</span>
            
            <?php  }else { echo anchor(site_url('payment/send/'.base64_encode($match->match_id)),'<button style="border-radius: 6px; background-color: #55acee;" type="button" class="btn text-white"><i class="fa fa-paper-plane me-2"></i>Send</button>','onclick="javasciprt: return confirm(\'Are You Sure ?\')"'); } ?></td>
        </tr>
              <?php
            }
            ?>
              </tbody>
              
            </table>
          </div>
        </div>
     
    </div>
  </div>
</div>
      
       
       