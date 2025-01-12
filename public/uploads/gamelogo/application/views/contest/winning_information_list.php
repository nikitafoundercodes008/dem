<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Winnig Information List
      </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#">winnig information</a></li>
        <li class="breadcrumb-item active">Create</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
          
   <div class="box box-default">
        
        <!-- /.box-header -->
        <div class="box-body wizard-content">
                    <div class="ibox-title">
                        <?php
                        $id = $this->uri->segment('3');
                        echo anchor(site_url('contest/winnig_information_create/'.$id),'Create', 'class="btn btn-primary"'); ?>
                     
                    </div>
                    
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
        <th>Contest Name</th>
        <th>Rank</th>
        <th>Price</th>
        <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                         <?php
             foreach ($winning_data as $winning)
            {

                ?>
                <tr>
            <td width="80px"><?php echo ++$start ?></td>
            <td><?php
            $contest = $this->Contest_model->get_by_id($winning->contest_id);
             echo $contest->contest_name ?></td>
            <td><?php echo $winning->rank ?></td>
            <td><?php echo $winning->price ?></td>
            <!-- <td style="text-align:center" width="200px">
                <?php 
                //echo anchor(site_url('contest/winnig_information_read/'.$winning->winning_info_id),'Read'); 
               // echo ' | '; 
                //echo anchor(site_url('contest/winnig_information_update/'.$winning->winning_info_id),'Update'); 
                //echo ' | '; 
                //echo anchor(site_url('contest/winnig_information_delete/'.$winning->winning_info_id."/".$winning->contest_id),'Delete','onclick="javasciprt: return confirm(\'Are You Sure ?\')"'); 
                ?>
            </td> -->
             <td style="text-align:center" width="200px">
              <?php 
              echo anchor(site_url('contest/winnig_information_read/'.$winning->winning_info_id),'<button style="border-radius: 6px;" type="button" class="btn btn-info"><i class="fa fa-book" aria-hidden="true"></i></button>'); 
                echo " ";
                echo anchor(site_url('contest/winnig_information_update/'.$winning->winning_info_id),'<button style="border-radius: 6px;" type="button" class="btn btn-success" ><i class="fa fa-pencil" aria-hidden="true"></i></button>'); 
              echo " ";
              echo anchor(site_url('contest/winnig_information_delete/'.$winning->winning_info_id."/".$winning->contest_id),'<button style="border-radius: 6px;" type="button" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>','onclick="javasciprt: return confirm(\'Are You Sure ?\')"'); 
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
      
       
       