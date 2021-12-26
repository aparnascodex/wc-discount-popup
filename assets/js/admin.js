jQuery(document).ready(function($){
	$('.type').change(function(){
		console.log($(this).val())
		if( $(this).val() == 2 ){
			$('.field-product').removeClass('hide_field');
			$('.field-condition, .field-value').addClass('hide_field');
		}
		else {
			$('.field-product').addClass('hide_field');
			$('.field-condition, .field-value').removeClass('hide_field');
		}
	});
	console.log(opts.products)
	$('#products').searchableOptionList({
        maxHeight : '250px',
        allowNullSelection : true,
        data : opts.products
    });
})