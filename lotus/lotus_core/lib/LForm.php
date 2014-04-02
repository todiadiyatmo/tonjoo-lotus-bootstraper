<?php  if ( ! defined('L_BASEPATH')) exit('No direct script access allowed');

class LForm  extends LLibrary{




	/*
	 * Print hidden Input field in a form
	 */
	function printFormData($controller,$method='index',$extra_params=array()){


		if($controller)
			echo "<input type='hidden' value='{$controller}' name='controller' >";

		if($method)
			echo "<input type='hidden' value='{$method}' name='method' >";

		foreach ($extra_params as $key => $value) {
			echo "<input type='hidden' value='{$value}' name='{$key}' >";
		}
		global $wp;
		if($wp&&is_admin()){

			if(isset($_GET['page'])){
				echo "<input type='hidden' value='{$_GET['page']}' name='page' >";
			}

		}

	}

	/*  
	*  $options['select_array']
	*  $options['value']
	*  $options['id']
	*  $options['name']
	*  $options['attr']
    */

	function select($options){
		$r ='';
		$p ='';

		$options['attr'] = isset($options['attr']) ? $options['attr'] : '';

		foreach ( $options['select_array'] as $value => $key ) {	

			if ( $options['value'] == $value ) // Make selected first in list
				$p = "<option selected='selected' value='$value'>$key</option>";
			else
				$r .= "<option value='$value'>$key</option>";
		}

		
		if(isset($options['id']) )
			$options['attr'] .=" id='{$options['id']}'";

		if(isset($options['name']) )
			$options['attr'] .=" name='{$options['name']}'";

		$print_select= "<select {$options['attr']} >{$p}{$r}</select>
		";

		return $print_select;
	}


	function radio($checkboxes){
		$r ='';


		$checkboxes['attr'] = isset($checkboxes['attr']) ? $checkboxes['attr'] : '';

		if(isset($checkboxes['id']) )
			$checkboxes['attr'] .=" id='{$checkboxes['id']}'";

		if(isset($checkboxes['name']) )
			$checkboxes['attr'] .=" name='{$checkboxes['name']}'";

		$first_check = $checkboxes['value']=='' ? true : false;

		foreach ( $checkboxes['select_array'] as $key => $value ) {	

			// Make selected first in list				
			if ( $checkboxes['value'] == $value || $first_check ) {
				// $p .= "<span class='radio'>";
				$r .= "<input type='radio' {$checkboxes['attr']} checked  value='$value' >$key";
				// $p .="</span>";
			}
			else
				// $r .= "<span class='radio'>";
				$r .= "<input type='radio' {$checkboxes['attr']} value='$value'>$key";
				// $r .="</span>";

			$first_check=false;
		}
			

		$print_select= "{$r}";
		
		return $print_select;
	}


	function checkboxes($checkboxes){
		$r ='';

		$checkboxes['attr'] = isset($checkboxes['attr']) ? $checkboxes['attr'] : '';

		if(isset($checkboxes['id']) )
			$checkboxes['attr'] .=" id='{$checkboxes['id']}'";

		foreach ( $checkboxes['select_array'] as $key => $value ) {	
			$checked = '';
			if(is_array($checkboxes['value'])){

				if(in_array($key,$checkboxes['value'])){
					
					$checked='checked';

				}
			}

			$r .= "<input type='checkbox' $checked {$checkboxes['attr']} value='$key' name='{$checkboxes['name']}'>$value";
				
		}
			

		$print_select= "{$r}";
		
		return $print_select;
	}
}