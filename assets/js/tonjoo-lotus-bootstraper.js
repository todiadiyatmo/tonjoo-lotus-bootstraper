jQuery(document).ready(function($){



	$('[tonjoo-lf-submenu-add-row]').click(function(){
		
		content = $('#tonjoo-lf-submenu-list-content').html();

		content = content.replace(/#input_number#/g,tonjoo_lf_submenu) 
		tonjoo_lf_submenu = tonjoo_lf_submenu + 1
		content = content.replace(/#number#/g,tonjoo_lf_submenu) 
			
	
		$("ul.tonjoo-lf-submenu.list").append(content);


	})

	$("ul.tonjoo-lf-submenu.list").delegate('a','click',function(event){
	
		$(this).parents('li').remove();

		return false;

	})

	// $('.wp-has-current-submenu').children('ul').children('.current').removeClass('current')

	// $('.wp-has-current-submenu').children('ul').children('li a').each(function(){
	// 	li_href = $(this).attr('href')

	// 	console.log(li_href);
	// })

})