jQuery( document ).ready( ( $ ) => {
	'use strict';

	$( '#eac-edit-bill' )
		.on('change', ':input[name="contact_id"]', function ( e ) {
			console.log('contact_id changed');
		})
});
