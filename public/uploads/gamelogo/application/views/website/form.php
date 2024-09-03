<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Website content
      </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#">website</a></li>
        <li class="breadcrumb-item active">update</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
          
	 <div class="box box-default">
   
        <!-- /.box-header -->
        <div class="box-body wizard-content">
        <form action="<?php echo $action; ?>" method="post">
	    <div class="form-group">
            <label for="varchar">Content <?php echo form_error('editor1') ?></label>
             <textarea class="form-control" name="editor1"> <?php echo $content; ?></textarea>
        </div>
	   
	    <input type="hidden" name="id" value="<?php echo $id; ?>" /> 
	    <button type="submit" class="btn btn-primary"><?php echo $button ?></button> 
	    <a href="<?php echo site_url('Website') ?>" class="btn btn-default">Cancel</a>
	</form>
</div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
	  </section>
	  </div>
	  <script src="https://cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script>
	   <script>
                        CKEDITOR.replace( 'editor1' );
                </script>