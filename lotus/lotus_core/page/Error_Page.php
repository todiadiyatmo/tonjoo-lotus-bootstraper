<style type="text/css">
	#embeddedFramework{
		background-color:#eceaea;
		padding:20px;
		font-family :'PT Sans' !important;
		color:#84c770;
	}

	#embeddedFramework p#signature{
		margin:0px ;
		padding:0px ;
		font-size:17px ;

	}

	#embeddedFramework h2{
		margin:0px ;
		padding:0px ;
}
</style>

<div id='embeddedFramework' class='<?php echo $class ?>'>
	<?php
	
	echo "<p id='signature'>LotusFramework</p>";	
	echo "<H2>{$title}</H2>";
	echo "<p>{$detail}</p>";

	?>
</div>