<?php

if ( ! function_exists( 'tjl_lf_print_select_option' ) ) :
function tjl_lf_print_select_option($options){
	$r = '';
	$p = '';
	foreach($options['select_array'] as $select) {
		$label = $select['label'];
		$options['attr'] = isset($options['attr']) ? $options['attr'] : '';
		$options['description'] = isset($options['description']) ? $options['description'] : '';
		if ($options['value'] == $select['value']) // Make default first in list
		$p = "<option selected='selected' value='" . esc_attr($select['value']) . "'>$label</option>";
		else $r.= "<option value='" . esc_attr($select['value']) . "'>$label</option>";
	}
	// backward compability with id options
	if (isset($options['id'])) $options['attr'].= " id='{$options['id']}'";
	$print_select = "<tr valign='top' {$options['attr']}>

		<td scope='row'>{$options['label']}</td>

		<td>

		<select name='{$options['name']}'>

		{$p}{$r}

		</select>

		<label class='description' >{$options['description']}</label>

		</td>

	</tr>

	";
	echo $print_select;
}

endif;

if ( ! function_exists( 'tjl_lf_print_text_option' ) ) :
function tjl_lf_print_text_option($options){


	$options['attr'] = isset($options['attr']) ? $options['attr'] : '';
	$options['description'] = isset($options['description']) ? $options['description'] : '';
	if (isset($options['id'])) $options['attr'].= " id='{$options['id']}'";
	if (!isset($options['name'])) $options['name'] = '';
	$print_select = "<tr valign='top' {$options['attr']} >

	<td scope='row'>{$options['label']}</td>

	<td>

	<input type='text' name='{$options['name']}' value='{$options['value']}'>		

	<label class='description'>{$options['description']}</label>

	</td>

	</tr>

	";
	echo $print_select;
}

endif;

if ( ! function_exists( 'tjl_lf_print_text_area_option' ) ) :
function tjl_lf_print_text_area_option($options){


	if(!$options['row'])
		$options['row']=4;
	if(!$options['column'])
		$options['column']=50;


	

	$print_select= "<tr valign='top' id='{$options['id']}' >
						<th scope='row'>{$options['label']}</th>
						<td>
							<textarea  name='{$options['name']}' rows='{$options['row']}' cols='{$options['column']}'>{$options['value']}</textarea>		
						</td>
					</tr>
					";

	echo $print_select;
}

endif;