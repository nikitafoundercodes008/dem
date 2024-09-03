<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Player
      </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#">Player</a></li>
        <li class="breadcrumb-item active">Create</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
          
        <div class="box box-default">
            
            <!-- /.box-header -->
            <div class="box-body wizard-content">
                <h2 style="margin-top:0px">Players <?php echo $button ?></h2>
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="varchar">Name <?php echo form_error('name') ?></label>
                        <input type="text" readonly="readonly" class="form-control" name="name" id="name" placeholder="Name" value="<?php echo $row->name; ?>" />
                    </div>
                    <div class="form-group">
                        <label for="varchar">Designation <?php echo form_error('designationid') ?></label>
                        <input type="text" readonly="readonly" class="form-control" name="name" id="name" placeholder="Designation Name" value="<?php echo $row->title; ?>" />
                    </div>
                    <div class="form-group">
                        <label for="varchar">Team <?php echo form_error('teamid') ?></label>
                        <input type="text" readonly="readonly" class="form-control" name="name" id="name" placeholder="Team Name" value="<?php echo $row->team_name; ?>" />
                    </div>
                    <div class="form-group">
                        <label for="varchar">Series Points <?php echo form_error('credit_points') ?></label>
                        <input type="number" class="form-control" name="credit_points" id="credit_points" placeholder="Credit Points" value="<?php echo $row->ppoints; ?>" />
                    </div>
                    <div class="form-group">
                        <label for="varchar">Selection Percent</label>
                        <input type="number" maxlength="100" max="100" class="form-control" name="SelectionPercent" id="SelectionPercent" placeholder="Selection Percent" value="<?php echo $row->selection_percent; ?>" />
                    </div>
                    <input type="hidden" name="id" value="<?php echo $row->m_p_id; ?>" /> 
                    <input type="hidden" name="match_id" value="<?php echo $row->match_id; ?>" /> 
                    <button type="submit" class="btn btn-primary"><?php echo $button ?></button> 
                </form>
            </div>
            <!-- /.box-body -->
        </div>
          <!-- /.box -->
    </section>
</div>