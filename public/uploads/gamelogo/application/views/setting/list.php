<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
    Setting
    </h1>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li class="breadcrumb-item"><a href="#">Setting</a></li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
  <div class="row mb-5">
    <div class="col-md-12">

      <div class="card card-body">

        <h3 class="card-title mt-0">Android Setting</h3>

        <hr>

        <div class="form-group row mb-3">
          <label class="col-sm-3 control-label">App Name</label>
          <div class="col-sm-6">
            <input type="text" value="<?php echo $config[0]->value; ?>" name="name" id="name" placeholder="Ex: Team11"
              class="form-control" required="" data-table="config">
          </div>
        </div>

        <div class="form-group row mb-3">
          <label class="col-sm-3 control-label">Package Name</label>
          <div class="col-sm-6">
            <input type="text" value="<?php echo $config[1]->value; ?>" name="package_name" id="package_name" placeholder="Ex: com.team.eleven"
              class="form-control" data-table="config">
          </div>
        </div>
        <div class="form-group row mb-3">
          <label class="control-label col-sm-3">Maintenance</label>
          <div class="col-sm-6">
          <?php if ($version->maintenance_status == 1) 
                    {
                      ?>
                    <label class="switch">
                      <input type="checkbox" class="toggleclass" name="active" data-id="<?php echo $version->id; ?>"
                        value="1" checked>
                      <span class="slider round"></span>
                    </label>
                    <?php
                    }
                    else
                    {
                      ?>
                    <label class="switch">
                      <input type="checkbox" class="toggleclass" data-id="<?php echo $version->id; ?>"
                        name="unactive" value="0">
                      <span class="slider round"></span>
                    </label>
                    <?php
            }
            ?>
            
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="row mb-5">
    <div class="col-md-12">

      <div class="card card-body">

        <h3 class="card-title mt-0">Firebase Setting</h3>

        <hr>

        <div class="form-group row mb-3">
          <label class="col-sm-3 control-label">Firebase Server Key</label>
          <div class="col-sm-6">
            <input type="text" value="<?php echo $config[2]->value; ?>" name="firebase_server_key" id="firebase_server_key" placeholder=""
              class="form-control" data-table="config">
          </div>
        </div>
        
      </div>
    </div>
  </div>

  <div class="row mb-5">
    <div class="col-md-12">

      <div class="card card-body">

        <h3 class="card-title mt-0">Sports Api</h3>

        <hr>

        <div class="form-group row mb-3">
          <label class="col-sm-3 control-label">Sportmonks Api Key</label>
          <div class="col-sm-6">
            <input type="text" value="<?php echo $config[3]->value; ?>" name="sportmonks_api_key" id="sportmonks_api_key" placeholder=""
              class="form-control" data-table="config">
          </div>
        </div>
        
      </div>
    </div>
  </div>

  <div class="row mb-5">
    <div class="col-md-12">

      <div class="card card-body">

        <h3 class="card-title mt-0">SMS Setting</h3>

        <hr>

        <div class="form-group row mb-3">
          <label class="col-sm-3 control-label">Twilio Sid</label>
          <div class="col-sm-6">
            <input type="text" value="<?php echo $config[4]->value; ?>" name="twilio_sid" id="twilio_sid" placeholder=""
              class="form-control" required="" data-table="config">
          </div>
        </div>

        <div class="form-group row mb-3">
          <label class="col-sm-3 control-label">Twilio Token</label>
          <div class="col-sm-6">
            <input type="text" value="<?php echo $config[5]->value; ?>" name="twilio_token" id="twilio_token" placeholder=""
              class="form-control" required="" data-table="config">
          </div>
        </div>

        <div class="form-group row mb-3">
          <label class="col-sm-3 control-label">Twilio Phone Number</label>
          <div class="col-sm-6">
            <input type="text" value="<?php echo $config[6]->value; ?>" name="twilio_phone_number" id="twilio_phone_number" placeholder=""
              class="form-control" required="" data-table="config">
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="row mb-5">
    <div class="col-md-12">

      <div class="card card-body">

        <h3 class="card-title mt-0">App Update Setting</h3>

        <hr>

        <div class="form-group row mb-3">
          <label class="col-sm-3 control-label">Latest Version</label>
          <div class="col-sm-6">
            <input type="text" value="<?php echo $version->new_version; ?>" name="new_version" id="new_version" placeholder=""
              class="form-control" required="" data-table="version">
          </div>
        </div>

        <div class="form-group row mb-3">
          <label class="col-sm-3 control-label">Apk Name</label>
          <div class="col-sm-6">
            <input type="text" value="<?php echo $version->apk_name; ?>" name="apk_name" id="apk_name" placeholder=""
              class="form-control" required="" data-table="version">
          </div>
        </div>

        <div class="form-group row mb-3">
          <label class="col-sm-3 control-label">Apk Url</label>
          <div class="col-sm-6">
            <input type="text" value="<?php echo $version->apk_url; ?>" name="apk_url" id="apk_url" placeholder=""
              class="form-control" required="" data-table="version">
          </div>
        </div>

        <div class="form-group row mb-3">
          <label class="col-sm-3 control-label">Update Note</label>
          <div class="col-sm-6">
            <textarea type="text" name="note" id="note" placeholder=""
              class="form-control" required="" data-table="version"><?php echo $version->note; ?></textarea>
          </div>
        </div>

      </div>
    </div>
  </div>
  
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
  $('input[type="text"], textarea[type="text"]').change(function() {
    updateConfig(this.id);
  });
  function updateConfig(ID) {
	     var type  = document.getElementById(ID).getAttribute("name");
	     var value = document.getElementById(ID).value;
       var table = document.getElementById(ID).getAttribute("data-table");

       $.ajax({
          type:'POST',
          dataType:'JSON',
          url:'<?php echo base_url('setting/updateConfig'); ?>',
          data:{type : type, value : value, table : table},
          success:function(data)
          {
            if(data != "")
            {
              alertify.success("Update successfully");
            } 
            else
            {
              alertify.error("Please try again");
            }
          }
        });
  };

  $('.toggleclass').change(function(){
	     var id    = $(this).data("id");
	     var status = $('#status').text();	     
        $.ajax({
          type:'POST',
          dataType:'JSON',
          url:'<?php echo base_url('setting/on_off'); ?>',
          data:{id : id},
          success:function(data)
          {
                if(data != "")
                {
                 	$('#status').html(data);
                 		
                    alertify.success("Update successfully");
                } 
                else
                {
                    alertify.error("Please try again");
                }
          }
        });
      });
</script>