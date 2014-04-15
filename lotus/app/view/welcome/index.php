<link rel="stylesheet"  href="<?php echo l_assets_url('css/lotus.css') ?>" />

<div id='embeddedFramework'>
	<?php
	
	echo "<p id='signature'>Hello !</p>";	
	echo "<H2>Welcome to Lotus Framework</H2>";
	echo "<p id='copyright'>copyright @todiadiyatmo 2013</p>";

	$elapsed_time = l_bench_stop();

	echo "<p id='time'>Page rendered in $elapsed_time  second </p>";
	
	?>
</div>