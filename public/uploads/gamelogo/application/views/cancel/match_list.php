<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Cancel Match
      </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#">match</a></li>
        <li class="breadcrumb-item active">cancel</li>
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
                              <th>Match </th>
                              <th>Match Time</th>
                              <th>Match Type</th>
                              <th>Payment Status</th>
                              <th>Send Payment</th>
                            </tr>
                          </thead>
                          <tbody>
                         <?php
            foreach ($match_data as $match)
            {
                ?>
                <tr>
            <td width="80px"><?php echo ++$start ?></td>
            <td><?php echo $match->title; ?></td>
            <td><?php echo $match->match_date_time; ?></td>
            <td><?php echo $match->type; ?></td>
            <td><?php if($match->refund =="1"){ echo '<button style="border-radius: 6px;" type="button" class="btn btn-success">Done</button>' ;}else {echo '<button style="border-radius: 6px;" type="button" class="btn btn-info">Not Done</button>'; } ?></td>
            <td><?php if($match->refund =="1"){ ?>
            <button style="border-radius: 6px;" type="button" class="btn btn-success">Payment done</button>
            
            <?php  }else { echo anchor(site_url('payment/refund/'.base64_encode($match->match_id)),'<button style="border-radius: 6px;" type="button" class="btn btn-info"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>','onclick="javasciprt: return confirm(\'Are You Sure ?\')"'); } ?></td>
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
      
       
       