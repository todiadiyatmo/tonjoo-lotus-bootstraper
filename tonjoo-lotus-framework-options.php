<?php

add_action( 'admin_menu', 'tonjoo_lotus_framework_options' );
add_action( 'admin_init', 'tonjoo_lf_options_init' );

function tonjoo_lf_options_init(){


	//enqueue script
	$plugin_url = plugin_dir_url(dirname(__FILE__));

	//register css
    $css = $plugin_url . 'tonjoo-lotus-bootstraper/assets/css/tonjoo-lotus-bootstraper.css';

	wp_register_style('tonjoo-lotus-bootstraper', $css);
    wp_enqueue_style('tonjoo-lotus-bootstraper');

    //register js
    wp_enqueue_script('jquery');

    $js_sortable = $plugin_url . 'tonjoo-lotus-bootstraper/assets/js/tonjoo-lotus-bootstraper.js';
    wp_register_script('tonjoo-lotus-bootstraper',$js_sortable);
    wp_enqueue_script('tonjoo-lotus-bootstraper');

    
	register_setting( 'tonjoo_lf_options', 'lf_settings' );
}

function tonjoo_lotus_framework_options(){
   add_menu_page( 'Lotus Framework Options', // Page Title
    			   'Lotus Framework', //Menu Title 
    			   'manage_options',  // capability
    			   'lotus-framework-options', // menu slug
    			    'tonjoo_lotus_framework_options_do_page',  // function page name
    			    plugins_url( 'tonjoo-lotus-bootstraper/lf_icon.png' ),
    			    "65.1"
    			    );
    
  // add_submenu_page( 'lotus-framework-options', 'Riders', 'Riders Management', 'manage_options', 'lotus-framework-options-2','testing_sss');


}

function lotus_framework_enable_backend_options(){
   //backend menu

   $options = get_option('lf_settings'); 
   $options = tonjoo_lf_load_default($options);

   if(trim($options['page_title'])!=''&&$options['enable_backend']=='true'){



   		$options['menu_title'] = trim($options['menu_title'])=='' ? $options['menu_title'] : $options['page_title'];

   		$options['position'] = is_numeric($options['position']) ? $options['position'] : "65.2";

   		//force position to not overlab with other icon
   		if($options['position'] % 5 ==0)
   			$options['position'] = "{$options['position']}.1";

   		if($options['position']=='65.1')
			$options['position'] = "65.2";
   		

   		$options['image'] = trim($options['image'])=='' ?   plugins_url( 'tonjoo-lotus-bootstraper/lightning.png' ) : plugins_url( "tonjoo-lotus-bootstraper/{$options['image'] }" ) ;// icon 

   		$slug = sanitize_title_with_dashes($options['page_title']);

   		add_menu_page( $options['page_title'], // Page Title
					   $options['menu_title'], //Menu Title 
					   'manage_options',  // capability
					    $slug, // menu slug
					    'tonjoo_lotus_bootstraper_do_backend',  // function page name
					    $options['image'] , // icon
					    $options['position']
					    );

   		add_submenu_page( $options['page_title'], // Page Title
					   $options['menu_title'], //Menu Title 
					   'manage_options',  // capability
					    $slug, // menu slug
					    'tonjoo_lotus_bootstraper_do_backend'
					    );

   }
}

add_action( 'admin_menu', 'tonjoo_lotus_framework_options' );

add_action( 'admin_menu', 'lotus_framework_enable_backend_options' );




function tonjoo_lotus_framework_options_do_page(){

	if (!current_user_can('manage_options')) {  
		wp_die('You do not have sufficient permissions to access this page.');  
	}  


	

	global $select_options, $radio_options;
			

	$options = get_option('lf_settings'); 

	$options = tonjoo_lf_load_default($options);

	global $wpdb;

	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;




	?>


	
	<style>
	label{
		vertical-align: top
	}

	.form-table input{
		width: 275px;
	}

	.form-table h3{
		margin :0px;
	}

	.backend_opt.disabled{
		display :none;
	}
	

	</style>

	<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery('#backend_hook select').change(function(){
				cur_val = jQuery(this).val()

				if(cur_val=='true'){
					jQuery('.backend_opt').show()
				}
				else{
					jQuery('.backend_opt').hide()
				}
			})
		})
		

	</script>
	<div class="wrap">
		<?php screen_icon();
		echo "<h2>Lotus Framework Options Page</h2>";

		if($_REQUEST['settings-updated']==='true'){
			echo "<div id='setting-error-settings_updated' class='updated settings-error'> <p><strong>Settings saved.</strong></p></div>";

			$post = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}posts where post_name='{$options['frontpage_hook']}' ");

		    $hook_id =  $post->ID;

		    $slug = $post->post_name ; 

		    
	        global $wp_rewrite;
	   		$wp_rewrite->flush_rules();
		    //rewrite
	        add_rewrite_tag('%lotus_controller%','([^&]+)');
	        add_rewrite_tag('%lotus_function%','([^&]+)');
	        add_rewrite_tag('%lotus_params%','([^&]+)');
	        add_rewrite_tag('%lotus_params2%','([^&]+)');
	        add_rewrite_rule(''.$slug.'/([^/]+)/?$','index.php?page_id='.$hook_id.'&lotus_controller=$matches[1]','top');
	        add_rewrite_rule(''.$slug.'/([^/]+)/?([^/]*)/?$','index.php?page_id='.$hook_id.'&lotus_controller=$matches[1]&lotus_function=$matches[2]','top');
	        add_rewrite_rule(''.$slug.'/([^/]+)/?([^/]*)/?([^/]*)/?$','index.php?page_id='.$hook_id.'&lotus_controller=$matches[1]&lotus_function=$matches[2]&lotus_params=$matches[3]','top');
	        add_rewrite_rule(''.$slug.'/([^/]+)/?([^/]*)/?([^/]*)/?([^/]*)/?$','index.php?page_id='.$hook_id.'&lotus_controller=$matches[1]&lotus_function=$matches[2]&lotus_params=$matches[3]&lotus_params2=$matches[4]','top');

	}
		?>
		<form method="post" action="options.php">
			
			<?php settings_fields('tonjoo_lf_options'); ?>

			<table class="form-table">

				<tr>
					<th><h3>Front Page Hook</h3></th>
				</tr>

				<?php

					

					//get all page that is publish

					$results = $wpdb->get_results( "
						SELECT * FROM {$wpdb->prefix}posts where post_type = 'page' and post_status='publish' order by post_title asc
					");

					$select ='';
					
					if(sizeof($results)==0){
						$select ="<option value='false'>No page to hook</option>";
					}

					else{
						foreach ($results as $post) {
							
							$selected = '';

							if($options['frontpage_hook']==$post->post_name)
								$selected = 'selected';

							$select .="<option value='$post->post_name' $selected>$post->post_title</option>";
						}
					}


				?>
					<tr>
						<td>Page Hook</td>
						<td><select  name='lf_settings[frontpage_hook]'>
							<?php echo $select ?>
							</select>
						</td>
					</tr>

					<tr>
						<th><h3>Backend Hook</h3></th>
					</tr>
					<?php 

					$yes_no = array(
					'0' => array(
						'value' =>	'true',
						'label' =>  "Yes"
						),
					'1' => array(
						'value' =>	'false',
						'label' =>  "No"
						)
					);

					$backend_hook = array(
						"name"=>"lf_settings[enable_backend]",
						"description" => "",
						"label" => "Backend Hook Enable",
						"value" => $options['enable_backend'],
						"select_array" => $yes_no,
						'attr'=>"id='backend_hook'"
						);

					echo tjl_lf_print_select_option($backend_hook);


					$display = 'disabled';

					if($options['enable_backend']=='true'){
						$display='';
					}

					// add_menu_page( 'Lotus Framework Options', // Page Title
			    	// 		   'Lotus Framework', //Menu Title 
			    	// 		   'manage_options',  // capability
			    	// 		   'lotus_framework_options', // menu slug
			    	// 		    'tonjoo_lotus_framework_options_do_page',  // function page name
			    	// 		    plugins_url( 'tonjoo-lotus-bootstraper/lf_icon.png' ), // icon
			    	// 		    65
			    	// 		    );

					?>



					<tr class='backend_opt <?php echo $display ?>'>
						<td scope='row'>Page Title</td>
						<td>
							<input name='lf_settings[page_title]' type='text' value='<?php echo $options['page_title'] ?>'>
						</td>
					</tr>
					<tr class='backend_opt <?php echo $display ?>'>
						<td>Menu Title</td>
						<td><input name='lf_settings[menu_title]' type='text' value='<?php echo $options['menu_title'] ?>'></td>
					</tr>
					<tr class='backend_opt <?php echo $display ?>'>
						<td>Menu Image (PNG 20x20) if any</td>
						<td><input name='lf_settings[image]' type='text' value='<?php echo $options['image'] ?>'><label>Relative path to the LF plugin</label></td>
					</tr>
					<tr class='backend_opt <?php echo $display ?>'>
						<td>Plugin menu position</td>
						<td><input name='lf_settings[position]' type='text' value='<?php echo $options['position'] ?>'></td>
					</tr>
					<tr class='backend_opt <?php echo $display ?>'>
						<th style='padding-bottom:0px'><h4 style='margin:0px'>Backend Submenu Hook</h4></th>
					</tr>
					<tr class='backend_opt <?php echo $display ?>'>

						<?php
							$submenu_page = $options['submenu_page'];

							$submenu_page_copy = $submenu_page;

	
							//resort $submenu_page
							$resort_index = 0;
							foreach ($submenu_page_copy as $key => $value) {
								$submenu_page[$resort_index] = $submenu_page_copy[$key];

								$resort_index = $resort_index+1;
							}

							$max =  sizeof($submenu_page_copy)==0 ? 1:  sizeof($submenu_page_copy) ;

							if(sizeof($submenu_page)==0){
								 $submenu_page[0]['file'] = '';
								 $submenu_page[0]['name'] = '';
							}
							
							?>
							<!-- List size for javascript parsing -->
							<script type="text/javascript">
							var tonjoo_lf_submenu = <?php echo $max ?>
							</script>
						<td colspan='2'>
						<ul class="tonjoo-lf-submenu list">
						<?php for($i=0;$i<$max;$i++) { 

								$submenu_page[$i]['menu_name'] = isset($submenu_page[$i]['menu_name']) ? $submenu_page[$i]['menu_name'] : '';
								$submenu_page[$i]['controller_name'] = isset($submenu_page[$i]['controller_name']) ? $submenu_page[$i]['controller_name'] : '';
								$submenu_page[$i]['method'] = isset($submenu_page[$i]['method']) ? $submenu_page[$i]['method'] : 'index';

							?>
						<li><div class='number column'><?php echo $i+1 ?></div>
							<div class='column'>
								<div class='first row'>
									<label>Submenu Name</label><input type="text" name='lf_settings[submenu_page][<?php echo $i ?>][menu_name]' value="<?php echo $submenu_page[$i]['menu_name'] ?>">
								</div>
								<div class='second row'>
									<label>Controller</label><input type="text" name='lf_settings[submenu_page][<?php echo $i ?>][controller_name]' value="<?php echo $submenu_page[$i]['controller_name'] ?>">
								</div>
								<div class='third row'>
									<label>Method</label><input type="text" name='lf_settings[submenu_page][<?php echo $i ?>][method]' value="<?php echo $submenu_page[$i]['method'] ?>"><a href='' tonjoo-lf-submenu-remove class='button'>Remove</a>
								</div>
							</div>
						</li>
						<?php } ?>
						
					</ul>	
	
						<a href='#' tonjoo-lf-submenu-add-row class='button button'>Add Template</a>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" value="Save Options" />
				</p>


			</form>

			<div id='tonjoo-lf-submenu-list-content'>
			<li><div class='number column'>#number#</div>
				<div class='column'>
					<div class='first row'>
						<label>Submenu Name</label><input type="text" name='lf_settings[submenu_page][#input_number#][menu_name]'>
					</div>
					<div class='second row'>
						<label>Controller</label><input type="text" name='lf_settings[submenu_page][#input_number#][controller_name]'>
					</div>
					<div class='third row'>
						<label>Method</label><input type="text" name='lf_settings[submenu_page][#input_number#][method]'><a href='#' class='button button-danger' tonjoo-lf-submenu-remove >Remove</a>
					</div>
				</div>
			</li>
			</div>  
		</div>
		<?php
	}



// Register Lotus framework submenu

add_action('admin_menu', 'tonjoo_lf_options_backend_submenu');

function tonjoo_lf_options_backend_submenu(){
	//get dynamic menu generation
	$options = get_option('lf_settings'); 


	$page_title = isset($options['page_title']) ?  $options['page_title'] : false ;

	//quit if no hook
	if(!$page_title)
		return;

	$submenu_page= isset($options['submenu_page']) ?  $options['submenu_page'] : array() ;


	//get slug for page title
	$page_title = sanitize_title_with_dashes($page_title);

	foreach ($submenu_page as $submenu) {



		$menu_name = isset($submenu['menu_name']) ? $submenu['menu_name'] : false;
		$controller_name = isset($submenu['controller_name']) ? $submenu['controller_name'] : false;
		$method = isset($submenu['method']) ? $submenu['method'] : 'index';

		if(!$menu_name&&!$controller_name)
			continue;

		

		add_submenu_page( $page_title, $menu_name, $menu_name, 'manage_options', "{$controller_name}X__X{$method}",function(){});
	}

	
}

// Change LF Submenu to have proper URL

add_action( 'admin_init', 'tonjoo_lf_options_backend_submenu_restore_url' );

function tonjoo_lf_options_backend_submenu_restore_url() {
	global $submenu;
	global $menu;


	//check if LF Bootstraper admin is set

	$options = get_option('lf_settings'); 

	$options = tonjoo_lf_load_default($options);

	if(trim($options['page_title'])!=''&&$options['enable_backend']=='true'){

		$slug = sanitize_title_with_dashes($options['page_title']);


		$submenu_page= $options['submenu_page'];
		$submenu_page_search = array();

		//break if no child
		if(!isset($submenu[$slug]))
			return;
		
		foreach ($submenu[$slug] as $key1 => $menu_submenu) {

			//find the same contrrolerX___Xmethod pattern
			if(strrpos($menu_submenu[2],'X__X')){
					
				//find controller and method
				$arr = explode('X__X',$submenu[$slug][$key1][2]);

				$submenu[$slug][$key1][2] = admin_url("admin.php?page=$slug&controller={$arr[0]}&method={$arr[1]}");
					

			}
		}



	}

	
}

add_action('admin_head', 'tonjoo_lf_admin_url');

function tonjoo_lf_admin_url(){



echo '<script>lf_ajax_url="'.admin_url().'admin.php"</script>';

}