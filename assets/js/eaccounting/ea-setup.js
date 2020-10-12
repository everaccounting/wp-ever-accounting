jQuery( document ).ready( function ( $ ) {
	$('.repeater').repeater({
		initEmpty: false,
		show: function () {
			$(this).slideDown();
			// $('.select2-container').remove();
			$('.ea-select2').each(function () {
				$(this).eaccounting_select2();
			});
			$('.select2-container').css('width','100%');
		},

		// show: function () {
		// 	$(this).slideDown(function(){
		// 		$(this).find('.ea-select2').eaccounting_select2();
		// 	});
		// },
		// ready: function (setIndexes) {
		// 	$(this).find('.ea-select2').eaccounting_select2();
		// },
		// // (Optional)
		// // "show" is called just after an item is added.  The item is hidden
		// // at this point.  If a show callback is not given the item will
		// // have $(this).show() called on it.
		// show: function () {
		// 	$(this).slideDown();
		// },
		// // (Optional)
		// // "hide" is called when a user clicks on a data-repeater-delete
		// // element.  The item is still visible.  "hide" is passed a function
		// // as its first argument which will properly remove the item.
		// // "hide" allows for a confirmation step, to send a delete request
		// // to the server, etc.  If a hide callback is not given the item
		// // will be deleted.
		// hide: function (deleteElement) {
		// 	if(confirm('Are you sure you want to delete this element?')) {
		// 		$(this).slideUp(deleteElement);
		// 	}
		// },
		// // (Optional)
		// // You can use this if you need to manually re-index the list
		// // for example if you are using a drag and drop library to reorder
		// // list items.
		// ready: function (setIndexes) {
		// 	$dragAndDrop.on('drop', setIndexes);
		// },
		// (Optional)
		// Removes the delete button from the first list item,
		// defaults to false.
		isFirstItemUndeletable: true
	});
});
