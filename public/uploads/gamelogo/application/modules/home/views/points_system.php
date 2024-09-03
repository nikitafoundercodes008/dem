<!DOCTYPE html>
<html>
<head>
<style>
html {
    overflow: scroll;
    overflow-x: hidden;
}
::-webkit-scrollbar { display: none; }
tr th {
   background: #eeeeee;
   max-width:100%;
}
table {
   text-align: center;
   position: relative;
   border-collapse: separated;
   width: 100%;
}
th {
   top: 0;
   position: sticky;
   background: white;
}
</style>
</head>
<body>
<section class="sample-text-area">
	<div class="container">
	<table class="table table-bordered">
			    <thead>
			    	<tr>
				        <th width="15%">Title</th>
				        <th width="10%">T10</th>
				        <th width="10%">T20</th>
				        <th width="10%">ODI</th>
				        <th width="10%">Test</th>
				        <th width="45%">Description</th>
			      	</tr>
			    </thead>
			    <tbody>
			    	<?php foreach ($points as $point) {
			    		?>
		    			<tr>
					        <td width="15%"><?php echo $point['title']; ?></td>
					        <td width="10%"><?php echo $point['t10score']; ?></td>
					        <td width="10%"><?php echo $point['t20score']; ?></td>
					        <td width="10%"><?php echo $point['odiscore']; ?></td>
					        <td width="10%"><?php echo $point['testscore']; ?></td>
					        <td width="45%"><?php echo $point['description']; ?></td>
					    </tr>
			    		<?php
			    	} ?>
			    </tbody>
		  </table>
	</div>
</section>
</body>
</html>