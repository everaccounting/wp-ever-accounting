import {ConfirmDialog} from '@wordpress/components'

function PopConfirm({onConfirm, onCancel, ...props}) {
    return (
        <ConfirmDialog
            title={title}
            message={message}
            confirmText={confirmText}
            cancelText={cancelText}
            onConfirm={onConfirm}
            onCancel={onCancel}
        />
    )
}
