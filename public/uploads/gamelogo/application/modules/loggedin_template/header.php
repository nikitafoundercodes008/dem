<?php

if (!isset($_SESSION['id'])){
    redirect(site_url());
}

$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);

 $i =  $this->uri->segment(1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN"
    "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_TITLE; ?></title>
    <link rel="icon" href="<?php echo base_url('assets/img/dd2.png');?>">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
 
  <!-- font awesome -->
  <!-- <link rel="stylesheet" href="<?php echo base_url();?>assets/vendor_components/font-awesome/css/font-awesome.css"> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <!-- ionicons -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/vendor_components/Ionicons/css/ionicons.css">
  
  <!-- theme style -->
  <link rel="stylesheet" href="<?php echo base_url('assets/');?>css/master_style.css">
  
  <!-- maximum_admin skins. choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo base_url('assets/');?>css/skins/_all-skins.css">
  
  <!-- morris chart -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/vendor_components/morris.js/morris.css">
  
  <!-- jvectormap -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/vendor_components/jvectormap/jquery-jvectormap.css">
  
  <!-- date picker -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/vendor_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.css">
  
  <!-- daterange picker -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/vendor_components/bootstrap-daterangepicker/daterangepicker.css">
  
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/vendor_plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.css">
   <link href="<?php echo base_url('assets/css/plugins/dataTables/datatables.min.css');?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/plugins/alertify/alertify.core.css');?>" rel="stylesheet">   
    <link href="<?php echo base_url('assets/css/plugins/dataTables/jquery.dataTables.css');?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/plugins/dataTables/select.dataTables.min.css');?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/plugins/alertify/alertify.default.css');?>" rel="stylesheet">

    <link href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">

  <!-- google font -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
  <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.css">
     
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/app.css">

<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css" rel="stylesheet"/>
  </head>

<body class="skin-black">
<div class="wrapper">


  <header class="main-header">
      <a class="logo sidebar-brand d-flex align-items-center justify-content-center"
          href="<?php echo base_url();?>/dashboard">
          <div class="sidebar-brand-icon">
              <img class="" style="height: 32px;" src="<?php echo base_url();?>assets\images\icon_no_bg.png">
          </div>
          <div class="sidebar-brand-text mx-2"><?php echo SITE_TITLE; ?></div>
      </a>
      <!-- Header Navbar: style can be found in header.less -->
      <nav class="navbar navbar-expand navbar-light bg-white topbar static-top shadow">
          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebar-toggle" data-toggle="push-menu" role="button" class="btn btn-link rounded-circle">
              <i class="fa fa-bars"></i>
          </button>

          <!-- Topbar Search -->
          <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
              <div class="input-group">
                  <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                      aria-label="Search" aria-describedby="basic-addon2">
                  <div class="input-group-append">
                      <button class="btn btn-primary" type="button">
                          <i class="fas fa-search fa-sm"></i>
                      </button>
                  </div>
              </div>
          </form>

          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">

              <!-- Nav Item - Search Dropdown (Visible Only XS) -->
              <li class="nav-item dropdown no-arrow d-sm-none">
                  <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown"
                      aria-haspopup="true" aria-expanded="false">
                      <i class="fa fa-search fa-fw"></i>
                  </a>
                  <!-- Dropdown - Messages -->
                  <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                      aria-labelledby="searchDropdown">
                      <form class="form-inline mr-auto w-100 navbar-search">
                          <div class="input-group">
                              <input type="text" class="form-control bg-light border-0 small"
                                  placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                              <div class="input-group-append">
                                  <button class="btn btn-primary" type="button">
                                      <i class="fa fa-search fa-sm"></i>
                                  </button>
                              </div>
                          </div>
                      </form>
                  </div>
              </li>

              <div class="topbar-divider d-none d-sm-block"></div>

              <!-- Nav Item - User Information -->
              <li class="nav-item dropdown no-arrow">
                  <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown"
                      aria-haspopup="true" aria-expanded="false">
                      <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo SITE_TITLE; ?></span>
                      <img class="img-profile rounded-circle"
                          src="<?php echo base_url();?>assets\images\undraw_profile.svg">
                  </a>
                  <!-- Dropdown - User Information -->
                  <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                      aria-labelledby="userDropdown">
                      <a class="dropdown-item" href="<?php echo site_url('account'); ?>">
                          <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                          Profile
                      </a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="<?php echo site_url('login/logout/');?>"
                          onclick="return FB.logout()">
                          <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                          Logout
                      </a>
                  </div>
              </li>

          </ul>

      </nav>
  </header>
  
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" data-widget="tree">
      

            <div class="sidebar-heading">MAIN NAVIGATION</div>
            <li <?php if($i == "dashboard"){?>class="nav-item active" <?php } else { ?> class="nav-item" <?php } ?>>
                <a class="nav-link" href="<?php echo site_url('dashboard'); ?>">
                    <i class="fa fa-house-chimney"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- <hr class="sidebar-divider"> -->
            <li  <?php if($i == "banners"){?>class="nav-item active" <?php } else { ?> class="nav-item" <?php } ?>>
                <a class="nav-link" href="<?php echo site_url('banners'); ?>">
                    <i class="fa fa-signs-post"></i>
                    <span>Banners</span></a>
            </li>
            <li  <?php if($i == "match"){?>class="nav-item active" <?php } else { ?> class="nav-item" <?php } ?>>
                <a class="nav-link" href="<?php echo site_url('match'); ?>">
                    <i class="fa-brands fa-fantasy-flight-games"></i>
                    <span>Fixture Match</span></a>
            </li>
            <li  <?php if($i == "livematch"){?>class="nav-item active" <?php } else { ?> class="nav-item" <?php } ?>>
                <a class="nav-link" href="<?php echo site_url('livematch'); ?>">
                    <i class="fa fa-square-poll-vertical"></i>
                    <span>Live Match</span></a>
            </li>
            <li  <?php if($i == "old_match"){?>class="nav-item active" <?php } else { ?> class="nav-item" <?php } ?>>
                <a class="nav-link" href="<?php echo site_url('old_match'); ?>">
                    <i class="fa fa-square-poll-vertical"></i>
                    <span>Result Match</span></a>
            </li>
            <li  <?php if($i == "cancel"){?>class="nav-item active" <?php } else { ?> class="nav-item" <?php } ?>>
                <a class="nav-link" href="<?php echo site_url('cancel'); ?>">
                    <i class="fa fa-square-poll-vertical"></i>
                    <span>Cancelled Match</span></a>
            </li>
            <li  <?php if($i == "withdrow_request"){?>class="nav-item active" <?php } else { ?> class="nav-item" <?php } ?>>
                <a class="nav-link" href="<?php echo site_url('withdrow_request'); ?>">
                    <i class="fa fa-money-bill"></i>
                    <span>Withdrow request</span></a>
            </li>
            <li  <?php if($i == "bonus"){?>class="nav-item active" <?php } else { ?> class="nav-item" <?php } ?>>
                <a class="nav-link" href="<?php echo site_url('bonus'); ?>">
                    <i class="fa fa-money-check-dollar"></i>
                    <span>Signup Bonus</span></a>
            </li>
            <li  <?php if($i == "default_contest"){?>class="nav-item active" <?php } else { ?> class="nav-item" <?php } ?>>
                <a class="nav-link" href="<?php echo site_url('default_contest'); ?>">
                    <i class="fa fa-code-pull-request"></i>
                    <span>Default Contest</span></a>
            </li>
            <li  <?php if($i == "notification"){?>class="nav-item active" <?php } else { ?> class="nav-item" <?php } ?>>
                <a class="nav-link" href="<?php echo site_url('notification'); ?>">
                    <i class="fa fa-bell"></i>
                    <span>Notification</span></a>
            </li>
            <li  <?php if($i == "user"){?>class="nav-item active" <?php } else { ?> class="nav-item" <?php } ?>>
                <a class="nav-link" href="<?php echo site_url('user'); ?>">
                    <i class="fa fa-users"></i>
                    <span>Users</span></a>
            </li>
            <li  <?php if($i == "kyc"){?>class="nav-item active" <?php } else { ?> class="nav-item" <?php } ?>>
                <a class="nav-link" href="<?php echo site_url('kyc'); ?>">
                    <i class="fa fa-check-to-slot"></i>
                    <span>KYC Status</span></a>
            </li>
            <li  <?php if($i == "points_distribution_rules"){?>class="nav-item active" <?php } else { ?> class="nav-item" <?php } ?>>
                <a class="nav-link" href="<?php echo site_url('points_distribution_rules'); ?>">
                    <i class="fa fa-circle-dollar-to-slot"></i>
                    <span>Point Distribution</span></a>
            </li>
            <li  <?php if($i == "players"){?>class="nav-item active" <?php } else { ?> class="nav-item" <?php } ?>>
                <a class="nav-link" href="<?php echo site_url('players'); ?>">
                    <i class="fa fa-people-group"></i>
                    <span>Players</span></a>
            </li>
            <li  <?php if($i == "team"){?>class="nav-item active" <?php } else { ?> class="nav-item" <?php } ?>>
                <a class="nav-link" href="<?php echo site_url('team'); ?>">
                    <i class="fa-brands fa-teamspeak"></i>
                    <span>Team</span></a>
            </li>
            <li  <?php if($i == "website"){?>class="nav-item active" <?php } else { ?> class="nav-item" <?php } ?>>
                <a class="nav-link" href="<?php echo site_url('website'); ?>">
                    <i class="fa fa-blog"></i>
                    <span>Website</span></a>
            </li>
            <li  <?php if($i == "account"){?>class="nav-item active" <?php } else { ?> class="nav-item" <?php } ?>>
                <a class="nav-link" href="<?php echo site_url('account'); ?>">
                    <i class="fa fa-user"></i>
                    <span>Account</span></a>
            </li>
            <li  <?php if($i == "api_setting"){?>class="nav-item active" <?php } else { ?> class="nav-item" <?php } ?>>
                <a class="nav-link" href="<?php echo site_url('api_setting'); ?>">
                    <i class="fa fa-bolt "></i>
                    <span>Api Setting</span></a>
            </li>
            <li  <?php if($i == "cron_setting"){?>class="nav-item active" <?php } else { ?> class="nav-item" <?php } ?>>
                <a class="nav-link" href="<?php echo site_url('cron_setting'); ?>">
                    <i class="fa fa-tasks"></i>
                    <span>Cron Setting</span></a>
            </li>
            <li  <?php if($i == "setting"){?>class="nav-item active" <?php } else { ?> class="nav-item" <?php } ?>>
                <a class="nav-link" href="<?php echo site_url('setting'); ?>">
                    <i class="fa fa-cog"></i>
                    <span>Setting</span></a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle" data-toggle="push-menu"></button>
            </div>
      </ul>
    </section>
  </aside>