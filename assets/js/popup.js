jQuery(document).ready(function($){
	
	console.log(popup)
	if( popup.display == 1) {
		modalContainer = $('#popup-container');
		modalContainer.addClass('show-modal');
		
	}
	$('.close-modal').click(function(){
		modalContainer.removeClass('show-modal');
		
	})

	$('.apply-code').click(function(){
		modalContainer.removeClass('show-modal');
		$.ajax({
			url: popup.url,
			method: 'post',
			data: { 
				action: 'apply_discount',
				nonce: popup.popup_nonce 
			},
			success: function( response ) {
				location.reload();
			},
			error: function( error ) {
				console.log( error );
			}
		});
	})


	$('body').on( 'updated_cart_totals', function() {
		$.ajax({
			url: popup.url,
			method: 'post',
			data: { 
				action: 'get_cart_details_for_popup',
				nonce: popup.popup_nonce
			},
			success: function( response ) {
				if( response == 1 ) {
					modalContainer = $('#popup-container');
					modalContainer.addClass('show-modal');	
				}
				else {
					location.reload();
				}
			},
			error: function( error ) {
				console.log( error );
			}
		});
     
	});


})