<footer class="main-footer">
                <div class="container">
                    <div class="copyright text-center">
                    © <script>document.write(new Date().getFullYear())</script> Dooo<span class="d-none d-sm-inline-block"> - Crafted with <i class="fa-solid fa-heart text-danger"></i> by OneByte Solution.</span>
                    </div>
                </div>
            </footer>


            <script src="<?php echo base_url();?>assets/js/app.js"></script>

            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
	<!-- jQuery 3 -->
	<script src="<?php echo base_url();?>assets/vendor_components/jquery/dist/jquery.js"></script>
	
	<!-- jQuery UI 1.11.4 -->
	<script src="<?php echo base_url();?>assets/vendor_components/jquery-ui/jquery-ui.js"></script>
	
	<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
	<script>
	  $.widget.bridge('uibutton', $.ui.button);
	</script>
	<script type="text/javascript"> var BASE_URL = "<?php echo base_url();?>"; </script>
    <script type="text/javascript"> var SITE_URL = "<?php echo site_url(); ?>"; </script>
    
	<!-- popper -->
	<script src="<?php echo base_url();?>assets/vendor_components/popper/dist/popper.min.js"></script>
	
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>

	<!-- Morris.js charts -->
	<script src="<?php echo base_url();?>assets/vendor_components/raphael/raphael.js"></script>
	<script src="<?php echo base_url();?>assets/vendor_components/morris.js/morris.js"></script>
	
	<!-- Sparkline -->
	<script src="<?php echo base_url();?>assets/vendor_components/jquery-sparkline/dist/jquery.sparkline.js"></script>
	
	<!-- jvectormap -->
	<script src="<?php echo base_url();?>assets/vendor_plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>	
	<script src="<?php echo base_url();?>assets/vendor_plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
	
	<!-- jQuery Knob Chart -->
	<script src="<?php echo base_url();?>assets/vendor_components/jquery-knob/js/jquery.knob.js"></script>
	
	<!-- daterangepicker -->
	<script src="<?php echo base_url();?>assets/vendor_components/moment/min/moment.min.js"></script>
	<script src="<?php echo base_url();?>assets/vendor_components/bootstrap-daterangepicker/daterangepicker.js"></script>
	
	<!-- datepicker -->
	<script src="<?php echo base_url();?>assets/vendor_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>

	<!-- Bootstrap WYSIHTML5 -->
	<script src="<?php echo base_url();?>assets/vendor_plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.js"></script>
	
	<!-- Slimscroll -->
	<script src="<?php echo base_url();?>assets/vendor_components/jquery-slimscroll/jquery.slimscroll.js"></script>
	
	<!-- FastClick -->
	<script src="<?php echo base_url();?>assets/vendor_components/fastclick/lib/fastclick.js"></script>
	
	<!-- maximum_admin App -->
	<script src="<?php echo base_url();?>assets/js/template.js"></script>
	
	<!-- maximum_admin dashboard demo (This is only for demo purposes) -->
	<script src="<?php echo base_url();?>assets/js/pages/dashboard.js"></script>
	
	<!-- maximum_admin for demo purposes -->
	<script src="<?php echo base_url();?>assets/js/demo.js"></script>
 <script src="<?php echo base_url('assets/js/plugins/dataTables/jquery.dataTables.js');?>"></script>
    <script src="<?php echo base_url('assets/js/plugins/dataTables/datatables.min.js');?>"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>
    <script src="<?php echo base_url('assets/js/plugins/dataTables/dataTables.select.js');?>"></script>
    <!-- Alertify -->
    <script src="<?php echo base_url('assets/js/plugins/alertify/alertify.min.js');?>"></script>
    <script src="<?php echo base_url('assets/js/custom.js');?>"></script>
    <?php if(isset($js_script)){?>
    <script src="<?php echo base_url('assets/js/pages/');?><?=$js_script?>.js"></script>
    <?php } ?>

  

  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.13.4/af-2.5.3/b-2.3.6/b-colvis-2.3.6/b-html5-2.3.6/b-print-2.3.6/cr-1.6.2/date-1.4.0/fc-4.2.2/fh-3.3.2/kt-2.8.2/r-2.4.1/rg-1.3.1/rr-1.3.3/sc-2.1.1/sb-1.4.2/sp-2.1.2/sl-1.6.2/sr-1.2.2/datatables.min.js"></script>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js"></script>
	<script>
	 $(document).ready(function() {

      var dataTable = $('.dataTables-example').DataTable({
                pageLength: 10,
                lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                    { extend: 'copy'},
                    {extend: 'csv'},
                    {extend: 'excel', title: 'ExampleFile'},
                    {extend: 'pdf', title: 'ExampleFile'},

                    {extend: 'print',
                     customize: function (win){
                            $(win.document.body).addClass('white-bg');
                            $(win.document.body).css('font-size', '10px');

                            $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                    }
                    }
                ]

            });
      dataTable.buttons().container().appendTo( $('.dataTable_btn_group' ) );
	 });
   
			</script>

<script type="text/javascript">
  $('#datetimepicker').datetimepicker({
    format: 'Y-m-d H:i',
  });
</script>

<script>
var sessionValue = "<?php echo $this->session->userdata('message');?>";
if(sessionValue != "") {
  alertify.success(sessionValue);
}
</script>

</body>

</html>

   