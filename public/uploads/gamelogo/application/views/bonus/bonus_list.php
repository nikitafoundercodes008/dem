 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Signup Bonus
      </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#">Signup Bonus</a></li>
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
        <th>Value</th>
        <th>Type</th>
         <th>Action</th> 
            </tr>
            </thead>
            <?php
            foreach ($my_match_data as $match)
            {
                ?>
                <tr>
            <td width="80px"><?php echo ++$start ?></td>
            
            <td><?php echo $match->value ?></td>
            <td><?php echo $match->type ?></td>
 
             <td width="200px">
                <?php 
                echo anchor(site_url('bonus/update/'.$match->id),'<button style="border-radius: 6px;" type="button" class="btn btn-success" ><i class="fa fa-pencil" aria-hidden="true"></i></button>'); 
                ?>
            </td> 
        </tr>
                <?php
            }
            ?>
                        </table>
                       
                      </div>
                    </div>
                 
                </div>
              </div>
            </div>
      
       
       


       