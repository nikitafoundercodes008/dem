<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Points Distribution Rules
      </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#">Points Distribution</a></li>
        <li class="breadcrumb-item active">Create</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
          
     <div class="box box-default">
   <div class="box-tools ">
                <a href="<?php echo site_url('points_distribution_rules') ?>" class="btn btn-primary">Points Distribution</a>
          </div>
        <!-- /.box-header -->
        <div class="box-body wizard-content">
        <h2 style="margin-top:0px">Points Distribution <?php echo $button ?></h2>
        <form action="<?php echo $action; ?>" method="post">
        <div class="form-group">
            <label for="varchar">Title <?php echo form_error('title') ?></label>
            <input type="text" class="form-control" name="title" id="title" placeholder="Title" value="<?php echo $title; ?>" />
        </div>
        <div class="form-group">
            <label for="decimal">T10 Scrore <?php echo form_error('t10score') ?></label>
            <input type="text" class="form-control" name="t10score" id="t10score" placeholder="T10 Score" value="<?php echo $t10score; ?>" />
        </div>
        <div class="form-group">
            <label for="decimal">T20 Scrore <?php echo form_error('t20score') ?></label>
            <input type="text" class="form-control" name="t20score" id="t20score" placeholder="T20 Score" value="<?php echo $t20score; ?>" />
        </div>
        <div class="form-group">
            <label for="decimal">ODI Scrore <?php echo form_error('odiscore') ?></label>
            <input type="text" class="form-control" name="odiscore" id="odiscore" placeholder="ODI Score" value="<?php echo $odiscore; ?>" />
        </div>
        <div class="form-group">
            <label for="decimal">Scrore <?php echo form_error('testscore') ?></label>
            <input type="text" class="form-control" name="testscore" id="testscore" placeholder="Scrore" value="<?php echo $testscore; ?>" />
        </div>
        <div class="form-group">
            <label for="mediumtext">Description <?php echo form_error('description') ?></label>
            <input type="text" class="form-control" name="description" id="description" placeholder="Description" value="<?php echo $description; ?>" />
        </div>
        <!-- <div class="form-group">
            <label for="datetime">Created Date <?php echo form_error('created_date') ?></label>
            <input type="text" class="form-control" name="created_date" id="created_date" placeholder="Created Date" value="<?php echo $created_date; ?>" />
        </div>
        <div class="form-group">
            <label for="timestamp">Modified Date <?php echo form_error('modified_date') ?></label>
            <input type="text" class="form-control" name="modified_date" id="modified_date" placeholder="Modified Date" value="<?php echo $modified_date; ?>" />
        </div> -->
        <input type="hidden" name="id" value="<?php echo $id; ?>" /> 
        <button type="submit" class="btn btn-primary"><?php echo $button ?></button> 
        <a href="<?php echo site_url('points_distribution_rules') ?>" class="btn btn-default">Cancel</a>
    </form>
</div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
      </section>
      </div>
