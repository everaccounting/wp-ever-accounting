import $ from "jquery" ;

/**
 * Block UI
 * @param params
 */
export function blockUI(params){
	const {el, background = '#fff', opacity = 0.6 } = params;
	$(el).block({
		message: null,
		overlayCSS: {
			background: background,
			opacity: opacity,
		},
	});
}

/**
 * Block UI
 * @param params
 */
export function unBlockUI(params){
	const {el} = params;
	$(el).unblock();
}
