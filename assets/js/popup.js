jQuery(document).ready(function($){
	// modalContainer = $('#popup-container');
	// modalContainer.addClass('show-modal');
	// $('body').addClass('stop-scrolling');
	// $('#popup-container').click(function(){
	// 	modalContainer.removeClass('show-modal');
	// })

	$('body').on( 'updated_cart_totals', function() {
		$.ajax({
			url: popup.url,
			method: 'post',
			data: { action: 'get_cart_details_for_popup' },
			success: function( response ) {
				modalContainer = $('#popup-container');
				
				modalContainer.addClass('show-modal');
			},
			error: function( error ) {
				console.log( error );
			}
		});
     
	});
})