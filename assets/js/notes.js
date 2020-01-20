const noteSubmitButton = document.getElementById( 'js-add-note-button' );
const noteTextArea = document.getElementById( 'add_order_note' );
const noteSpinner = document.getElementById( 'js-add-note-spinner' );

if ( ! noteSubmitButton || ! noteTextArea ) {
	return;
}

noteSubmitButton.addEventListener( 'click', () => {
	if ( ! noteTextArea.textLength ) {
		return;
	}

	// fetch(  )
} );
