<link rel='stylesheet'  href='<?php echo l_assets_url('css/lotus.css')?>' type='text/css' media='all' />
<div id='embeddedFramework'>
<div class='box <?php echo $class ?>'>
	<?php
	
	echo "<p id='signature'>LotusFramework</p>";	
	echo "<H2>{$title}</H2>";
	echo "{$detail}";

	?>
</div>

<?php 

$backtraces = debug_backtrace();

if(sizeof($backtraces)!=0) :

?>

<h2>Stack Trace : Please check this following file(s)</h2>

<table >
<tr>
	<th>File</th>
	<th>Line</th>
</tr>
<?php
foreach ($backtraces as $backtrace) {
	echo "<tr>";

	if(isset($backtrace['file'])){
		if(__c('framework_debug')==false&&strpos($backtrace['file'],'lotus_core')){
			continue;
		}	
		echo "<td>{$backtrace['file']}</td>";
		echo "<td class='line'>{$backtrace['line']}</td>";
		echo "</tr>";
	}
}
?>
</table>
<?php
	endif;
?>
</div>