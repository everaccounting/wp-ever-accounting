(function ($, window, document, undefined) {
	'use strict';
	/**
	 * A nifty plugin to converting form to serialize object
	 */
	$.fn.serializeObject = function () {
		const o = {};
		const a = this.serializeArray();
		$.each( a, function () {
			if ( o[ this.name ] !== undefined ) {
				if ( ! o[ this.name ].push ) {
					o[ this.name ] = [ o[ this.name ] ];
				}
				o[ this.name ].push( this.value || '' );
			} else {
				o[ this.name ] = this.value || '';
			}
		} );
		return o;
	};

	/**
	 * A plugin for converting form to serializeAssoc
	 *
	 * @return {{}}
	 */
	$.fn.serializeAssoc = function () {
		const data = {};
		$.each( this.serializeArray(), function ( key, obj ) {
			const a = obj.name.match( /(.*?)\[(.*?)\]/ );
			if ( a !== null ) {
				const subName = a[ 1 ];
				let subKey = a[ 2 ];

				if ( ! data[ subName ] ) {
					data[ subName ] = [];
				}

				if ( ! subKey.length ) {
					subKey = data[ subName ].length;
				}

				if ( data[ subName ][ subKey ] ) {
					if ( Array.isArray( data[ subName ][ subKey ] ) ) {
						data[ subName ][ subKey ].push( obj.value );
					} else {
						data[ subName ][ subKey ] = [];
						data[ subName ][ subKey ].push( obj.value );
					}
				} else {
					data[ subName ][ subKey ] = obj.value;
				}
			} else if ( data[ obj.name ] ) {
				if ( Array.isArray( data[ obj.name ] ) ) {
					data[ obj.name ].push( obj.value );
				} else {
					data[ obj.name ] = [];
					data[ obj.name ].push( obj.value );
				}
			} else {
				data[ obj.name ] = obj.value;
			}
		} );
		return data;
	};

}(jQuery, window, document));
