<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
    Cron Setting
    </h1>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li class="breadcrumb-item"><a href="#">Cron Setting</a></li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
  <div class="row mb-5">
    <div class="col-md-12">

      <div class="card card-body">

        <h3 class="card-title mt-0">Cron Setting</h3>

        <hr>

        <div class="form-group row mb-3">
          <label class="control-label col-sm-3">Cron Status</label>
          <div class="col-sm-6">
          <?php if ($config[7]->value == 1) 
                    {
                      ?>
                    <label class="switch">
                      <input type="checkbox" class="toggleclass" name="cron_status" id="cron_status"
                        value="1" data-table="config" checked>
                      <span class="slider round"></span>
                    </label>
                    <?php
                    }
                    else
                    {
                      ?>
                    <label class="switch">
                      <input type="checkbox" class="toggleclass"
                        name="cron_status" id="cron_status" value="0" data-table="config">
                      <span class="slider round"></span>
                    </label>
                    <?php
            }
            ?>
            
          </div>
        </div>

        <div class="form-group row mb-3">
          <label class="col-sm-3 control-label">Cron Key</label>
          <div class="col-sm-6">
            
              <div class="input-group mb-3">
                <input type="text" value="<?php echo $config[8]->value; ?>" name="cron_key" id="cron_key" placeholder=""
                  class="form-control border-0" data-table="config" disabled>
                <div class="input-group-append">
                  <button class="btn btn-secondary" type="button" onclick="copyToClipboard('cron_key')">Copy</button>
                  <button class="btn btn-primary" type="button" onclick="Generate_Cron_Key()">Generate New Key</button>
                </div>
              </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="row mb-5">
    <div class="col-md-12">

      <div class="card card-body">

        <h3 class="card-title mt-0">Cron</h3>

        <hr>
        <div class="alert alert-secondary" role="alert">
            <strong>Hourly Cron: </strong> <code> wget -O /dev/null <?php echo base_url(); ?>Cron/hourly/<span id="hourlyCronKey"><?php echo $config[8]->value; ?></span></code>
        </div>
        <div class="alert alert-secondary" role="alert">
            <strong>Daily Cron: </strong> <code> wget -O /dev/null <?php echo base_url(); ?>Cron/daily/<span id="dailyCronKey"><?php echo $config[8]->value; ?></span></code>
        </div>
        <div class="alert alert-secondary" role="alert">
            <strong>Weekly Cron: </strong> <code> wget -O /dev/null <?php echo base_url(); ?>Cron/weekly/<span id="weeklyCronKey"><?php echo $config[8]->value; ?></span></code>
        </div>
        <div class="alert alert-secondary" role="alert">
            <strong>Monthly Cron: </strong> <code> wget -O /dev/null <?php echo base_url(); ?>Cron/monthly/<span id="monthlyCronKey"><?php echo $config[8]->value; ?></span></code>
        </div>
        

      </div>
    </div>
  </div>
  
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
    $('input[type="text"], input[type="checkbox"]').change(function() {
      updateConfig(this.id);
    });

    function copyToClipboard(element) {
            document.getElementById(element).disabled = false;
            var copyText = document.getElementById(element);
            copyText.focus();
            copyText.select();
            try {
              var successful = document.execCommand('copy');
              var msg = successful ? 'successful' : 'unsuccessful';
                swal.fire({
                    title: 'Copied!',
                    html: copyText.value + '<br>Successfully Copied to Clipboard',
                    icon: 'success'
                });
            } catch (err) {
                swal.fire({
                    title: 'Error',
                    text: 'Something Went Wrong :(',
                    icon: 'error'
                }).then(function () {
                    location.reload();
                });
            }
            document.getElementById(element).disabled = true;
    }

    function Generate_Cron_Key() {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#34c38f",
            cancelButtonColor: "#f46a6a",
            confirmButtonText: "Yes, Generate New Key!"
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                  url: '<?= site_url('Cron_setting/genarateCronKey') ?>',
                  type: 'GET',
                  dataType:'text',
                    success: function(result){
                      var jsonData = JSON.parse(result);
                      if(jsonData.status == "success") {
                        document.getElementById("cron_key").value = jsonData.data;
                        $("#hourlyCronKey").html(jsonData.data);
                        $("#dailyCronKey").html(jsonData.data);
                        $("#weeklyCronKey").html(jsonData.data);
                        $("#monthlyCronKey").html(jsonData.data);
                        alertify.success("Cron Key Updated successfully");
                      } else {
                        alertify.error("Something Went Wrong");
                      }
                    }
                });
            }
        });
      }


    function updateConfig(ID) {
        var type  = document.getElementById(ID).getAttribute("name");
        var table = document.getElementById(ID).getAttribute("data-table");

        if(document.getElementById(ID).getAttribute("type") == "text") {
            var value = document.getElementById(ID).value;
        } else if(document.getElementById(ID).getAttribute("type") == "checkbox") {
            if ($('#'+ID).is(':checked')) {
                var value = 1;
            } else {
                var value = 0;
            }
        
        }

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

  </script>