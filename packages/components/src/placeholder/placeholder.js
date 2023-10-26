/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * Internal dependencies
 */
import Avatar from './avatar';
import Button from './button';
import Element from './element';
import Image from './image';
import Input from './input';
import Node from './node';
import Paragraph from './paragraph';
import Title from './title';
import './style.scss';

function Placeholder( props ) {
	const { loading, className, style, children, avatar = false, title = true, paragraph = true, active = true, round } = props;

	const getComponentProps = ( prop ) => ( prop && typeof prop === 'object' ? prop : {} );
	const getAvatarBasicProps = ( hasTitle, hasParagraph ) =>
		hasTitle && ! hasParagraph ? { size: 'large', shape: 'square' } : { size: 'large', shape: 'circle' };

	const getTitleBasicProps = ( hasAvatar, hasParagraph ) =>
		! hasAvatar && hasParagraph ? { width: '38%' } : hasAvatar && hasParagraph ? { width: '50%' } : {};

	const getParagraphBasicProps = ( hasAvatar, hasTitle ) => ( {
		width: ! hasAvatar || ! hasTitle ? '61%' : undefined,
		rows: ! hasAvatar && hasTitle ? 3 : 2,
	} );

	if ( loading || ! ( 'loading' in props ) ) {
		const hasAvatar = !! avatar;
		const hasTitle = !! title;
		const hasParagraph = !! paragraph;

		// Avatar
		let avatarNode;
		if ( hasAvatar ) {
			const avatarProps = {
				className: 'eac-placeholder-avatar',
				...getAvatarBasicProps( hasTitle, hasParagraph ),
				...getComponentProps( avatar ),
			};
			avatarNode = (
				<div className="eac-placeholder__header">
					<Element { ...avatarProps } />
				</div>
			);
		}

		let contentNode;
		if ( hasTitle || hasParagraph ) {
			// Title
			let $title;
			if ( hasTitle ) {
				const titleProps = {
					className: 'eac-placeholder-title',
					...getTitleBasicProps( hasAvatar, hasParagraph ),
					...getComponentProps( title ),
				};

				$title = <Title { ...titleProps } />;
			}

			// Paragraph
			let paragraphNode;
			if ( hasParagraph ) {
				const paragraphProps = {
					className: 'eac-placeholder-paragraph',
					...getParagraphBasicProps( hasAvatar, hasTitle ),
					...getComponentProps( paragraph ),
				};

				paragraphNode = <Paragraph { ...paragraphProps } />;
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
			'eac-placeholder--round': !! round,
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
Placeholder.Avatar = Avatar;
Placeholder.Button = Button;
Placeholder.Element = Element;
Placeholder.Input = Input;
Placeholder.Node = Node;
Placeholder.Image = Image;
export default Placeholder;
