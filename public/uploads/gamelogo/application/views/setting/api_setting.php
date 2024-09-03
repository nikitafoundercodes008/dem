<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
    Api Setting
    </h1>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li class="breadcrumb-item"><a href="#">Api Setting</a></li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
  <div class="row mb-5">
    <div class="col-md-12">

      <div class="card card-body">

        <h3 class="card-title mt-0">Api Details</h3>

        <hr>

        <div class="form-group row mb-3">
          <label class="col-sm-3 control-label">API SERVER URL</label>
          <div class="col-sm-6">
              <div class="input-group mb-3">
              <input type="text" value="<?php echo $api_url; ?>" name="api_server_url" id="api_server_url" placeholder=""
              class="form-control border-0" required="" data-table="version" disabled>
                <div class="input-group-append">
                  <button class="btn btn-secondary" type="button" onclick="copyToClipboard('api_server_url')">Copy</button>
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

        <div class="row">
        <h3 class="card-title mt-0 col-md-6">Api Key</h3>

        <div class="col-md-6"><button class="btn btn-primary float-right" type="button" onclick="GenerateApiKey()" style="">Generate New Key</button></div>

        </div>
        <hr>

        <div class="table-responsive">
          <table id="datatable" class="table table-hover table-centered table-nowrap mb-0" style="width:100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>Key</th>
                <th>Created By</th>
                <th>IP Address</th>
                <th>Date Created</th>
                <th>Action</th>
              </tr>
            </thead>
          </table>
        </div>
        
      </div>
    </div>
  </div>
  
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
  $(document).ready ( function(){
    $('#datatable').dataTable({
                "order": [],
                "ordering": false,
                "processing": true,
                "serverSide": true,
                "bPaginate": false,
                "bLengthChange": false,
                "bFilter": false,
                "bInfo": false,
                "ajax": {
                    "url": "<?= site_url('Api_setting/getApiKeys') ?>",
                    "type":"GET",
                },
                "columns": [{
                        "data": "0",
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        "data": "1",
                        render: function (data) {
                            return '<button type="button" class="btn btn-secondary disabled" style="background-color:#e3ebf7!important;text-transform: unset !important;"><b>'+data+'</b></button>';
                        }
                    },
                    {
                        "data": "2"
                    },
                    {
                        "data": "3"
                    },
                    {
                        "data": "4"
                    },
                    {
                        "data": "5",
                        render: function (data) {
                           return '<button style="border-radius: 6px;" type="button" class="btn btn-info me-2" onClick="copyApiKey(\''+data+'\')"><i class="fa fa-clipboard" aria-hidden="true"></i></button><button style="border-radius: 6px;" type="button" class="btn btn-danger" onClick="deleteApiKey(\''+data+'\')"><i class="fa fa-trash" aria-hidden="true"></i></button>';
                        }
                    }
                ]
      });

  });

  function deleteApiKey(text) {
      Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#34c38f",
            cancelButtonColor: "#f46a6a",
            confirmButtonText: "Yes, Delete Key!"
        }).then(function (result) {
            if (result.value) {
              $.ajax({
                type:'POST',
                dataType:'JSON',
                url:'<?php echo base_url('Api_setting/deleteApiKey'); ?>',
                data:{key : text},
                success:function(data)
                {
                  if(data != "")
                  {
                    swal.fire({
                      title: 'Deleted!',
                      text: 'Api Key Deleted Successfully',
                      icon: 'success'
                    }).then(function () {
                      location.reload();
                    });
                  } 
                  else
                  {
                    swal.fire({
                      title: 'Error',
                      text: 'Something Went Wrong :(',
                      icon: 'error'
                    }).then(function () {
                      location.reload();
                    });
                  }
                }
              }); 
            }
        });
  }
  
  function copyApiKey(text) {
    navigator.clipboard.writeText(text).then(function () {
      swal.fire({
        title: 'Copied!',
        html: text + '<br>Successfully Copied to Clipboard',
        icon: 'success'
      });
    }, function (err) {
      swal.fire({
        title: 'Error',
        text: 'Something Went Wrong :(',
        icon: 'error'
      }).then(function () {
        location.reload();
      });
    });
  }

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

    function GenerateApiKey() {
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
                  url: '<?= site_url('Api_setting/genarateApiKey') ?>',
                  type: 'GET',
                  dataType:'text',
                    success: function(result){
                        swal.fire({
                            title: 'Successful!',
                            text: 'Api Key Genarated successfully!',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#556ee6',
                            cancelButtonColor: "#f46a6a"
                        }).then(function () {
                            location.reload();
                        });
                    }
                });
            }
        });
      }
</script>