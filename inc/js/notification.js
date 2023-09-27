( function() {
	'use strict';

	const bpCustomAddOn = () => {
		document.querySelector( '#bp-custom-add-on-send-notification' ).addEventListener(
			'click',
			( e ) => {
				e.preventDefault();

				const self = e.currentTarget;
				const formData = new FormData();
				formData.append( 'action', 'bp_custom_add_on_ajax_notify' );

				if ( self.dataset && self.dataset.nonce ) {
					formData.append( 'bp-add-on-nonce', self.dataset.nonce );
				}

				fetch( bpCustomAddOnAjaxURL, {
					method: 'POST',
					body: formData
				} ).then(
					response => response.json()
				).then(
					result => {
						const resultContainer = document.querySelector( '#bp-custom-add-on-notification-result' );
						const {
							data: {
								message
							}
						} = result;

						resultContainer.innerHTML = message;
					}
				);
			}
		);
	};

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', bpCustomAddOn );
	} else {
		bpCustomAddOn();
	}

} )();
