<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Points distribution rules List
      </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#">Points distribution </a></li>
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
                                <th>Title</th>
                                <th>T10 Score</th>
                                <th>T20 Score</th>
                                <th>ODI Score</th>
                                <th>Test Score</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                         <?php
            foreach ($points_distribution_rules_data as $points_distribution_rules)
            {              
                ?>
                <tr>
            <td width="80px"><?php echo ++$start ?></td>
            <td><?php echo $points_distribution_rules->title ?></td>
            <td><?php echo $points_distribution_rules->t10score ?></td>
            <td><?php echo $points_distribution_rules->t20score ?></td>
            <td><?php echo $points_distribution_rules->odiscore ?></td>
            <td><?php echo $points_distribution_rules->testscore ?></td>
            <td><?php echo $points_distribution_rules->description ?></td>
            <td width="200px">
              <?php 
                echo anchor(site_url('points_distribution_rules/update/'.$points_distribution_rules->id),'<button style="border-radius: 6px;" type="button" class="btn btn-success" ><i class="fa fa-pencil" aria-hidden="true"></i></button>'); 
             
                  ?>
                </td>
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
      
       
       