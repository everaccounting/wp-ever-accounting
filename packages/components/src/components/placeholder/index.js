/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * Internal dependencies
 */
import Avatar from './avatar';
import Button from './button';
import Image from './image';
import Input from './input';
import Node from './node';
import Text from './text';
import './style.scss';

function Placeholder( props ) {
	const {
		loading,
		className,
		style,
		children,
		avatar = false,
		title = true,
		paragraph = true,
		active = true,
	} = props;

	if ( loading || ! ( 'loading' in props ) ) {
		const hasAvatar = !! avatar;
		const hasTitle = !! title;
		const hasParagraph = !! paragraph;

		// Avatar
		let avatarNode;
		if ( hasAvatar ) {
			avatarNode = (
				<div className="eac-placeholder__header">
					<span className="eac-placeholder__avatar" />
				</div>
			);
		}

		let contentNode;
		if ( hasTitle || hasParagraph ) {
			// Title
			let $title;
			if ( hasTitle ) {
				const tittleStyle = {};
				if ( ! hasAvatar && hasParagraph ) {
					tittleStyle.width = '38%';
				} else if ( hasAvatar && hasParagraph ) {
					tittleStyle.width = '50%';
				}
				$title = (
					<h3 className="eac-placeholder__title" style={ tittleStyle }>
						&nbsp;
					</h3>
				);
			}

			// Paragraph
			let paragraphNode;
			if ( hasParagraph ) {
				paragraphNode = (
					<ul className="eac-placeholder__paragraph">
						<li />
						<li />
						<li style={ { width: '61%' } } />
					</ul>
				);
			}

			contentNode = (
				<div className="eac-placeholder__content">
					{ $title }
					{ paragraphNode }
				</div>
			);
		}

		const classes = classNames( 'eac-placeholder', className, {
			'eac-placeholder--has-avatar': hasAvatar,
			'eac-placeholder--active': !! active,
		} );

		return (
			<div className={ classes } style={ style }>
				{ avatarNode }
				{ contentNode }
			</div>
		);
	}

	return typeof children !== 'undefined' ? children : null;
}

Placeholder.Text = Text;
Placeholder.Avatar = Avatar;
Placeholder.Button = Button;
Placeholder.Input = Input;
Placeholder.Node = Node;
Placeholder.Image = Image;
export default Placeholder;
