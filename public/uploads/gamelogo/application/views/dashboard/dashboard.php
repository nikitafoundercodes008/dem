<div class="content-wrapper" style="min-height: 1147px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard 
      </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="breadcrumb-item active">Dashboard </li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
    <div class="row">
      <!-- Total Contest -->
    <div class="col-xl-6 col-md-12 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                Total Contest</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_contest; ?></div>
              </div>
              <div class="col-auto">
                <i class="fa fa-clipboard-list fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Total Join Members -->
      <div class="col-xl-6 col-md-12 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                Total Join Members</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $join_members->num_rows; ?></div>
              </div>
              <div class="col-auto">
                <i class="fa fa-user fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <!-- Total Fixture Match -->
      <div class="col-xl-4 col-md-8 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                Total Fixture Match</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo isset($upcoming_match) ? $upcoming_match:0 ; ?></div>
              </div>
              <div class="col-auto">
                <i class="fa fa-clipboard-list fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Total Live Match -->
      <div class="col-xl-4 col-md-8 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                Total Live Match</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo isset($Live_matches) ? $Live_matches:0; ?></div>
              </div>
              <div class="col-auto">
                <i class="fa fa-clipboard-list fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Total Result Match -->
      <div class="col-xl-4 col-md-8 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                Total Result Match</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo isset($result_matches) ? $result_matches:0; ?></div>
              </div>
              <div class="col-auto">
                <i class="fa fa-clipboard-check fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    </section>
    <!-- /.content -->
  </div>
  