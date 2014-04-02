jQuery(document).ready(function($){
	$('.form_date_date_join').datetimepicker({

        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0
    });

    $('[btn-confirm]').click(function(e){
		e.preventDefault()
		return confirm_delete($(this))

	})

	$('[data-chosen]').chosen();

	jQuery('[data-select-rider]').each(function(){

		$(this).chosen({width:'125px'})

		$(this).change(function(){
			select_data  = $(this)

			current_val = select_data.val();

			//do nothing if select no rider
			if(current_val=='select_rider'){
		
				return;
			}

			//do nothing if selected value = previous value
			if(current_val==select_data.parents('td').first().data('rider')){
				return;
			}


			rider_name = rider_array[current_val]

			//open dialog
			var conf = confirm("Are you want to asign the order to "+rider_name+" ?");

		    if(conf == true){

		    	//Send ajax to asign da rider :D

		    	$.ajax({
					  url: lf_ajax_url+"?page=riders-order&controller=riderOrder&method=updateRider",
					  data:{
					  	ID:select_data.parents('td').first().data('order-id'),
					  	rider_tag:current_val
					  },
					  dataType:'JSON',
					  type:'POST'
					}).done(function(data) {
					  	
						//update def value
						if(data.result==true){

							$('.message-bar').html("<div class='alert alert-success  fade in'><button type='button' class='close' data-dismiss='alert'>×</button><strong>Rider sucesfully assigned</strong></div>");

			    			select_data.parents('td').first().data('rider',current_val)

			    			console.log(select_data.parents('td').first().data('rider'))

			    			select_data.trigger("chosen:updated")
			    		}
			    		else{
			    			$('.message-bar').html("<div class='alert alert-danger  fade in'><button type='button' class='close' data-dismiss='alert'>×</button><strong>Fail to assign rider</strong></div>");
			    		
			    			//revert to def value
					    	last_rider = select_data.parents('td').first().data('rider')

					    	select_data.attr('value',last_rider)

					    	select_data.trigger("chosen:updated");
			    		}

					}).fail(function(){
						$('.message-bar').html("<div class='alert alert-danger  fade in'><button type='button' class='close' data-dismiss='alert'>×</button><strong>Fail to assign rider</strong></div>");
					
						//revert to def value
				    	last_rider = select_data.parents('td').first().data('rider')

				    	select_data.attr('value',last_rider)

				    	select_data.trigger("chosen:updated");
					})

		    	
		    }
		    else{
		    	//revert to def value
		    	last_rider = select_data.parents('td').first().data('rider')

		    	select_data.attr('value',last_rider)

		    	select_data.trigger("chosen:updated");
		    }

		});

	})


	
})

function confirm_delete(button){

		text = "Are you sure want to delete ?"

		if(button.data('text'))
			text = "Are you sure want to "+button.data('text')+" ?"

		if(confirm(text)){
			if(button.attr('href'))
				window.location = button.attr('href')

			return true;
		}

		return false;
		
}