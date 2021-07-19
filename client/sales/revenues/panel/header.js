/**
 * External dependencies
 */
import { Text } from '@eaccounting/components';
import classnames from 'classnames';

export default function PanelHeader( props ) {
	const { className, menu, subtitle, title } = props;
	const cardClassName = classnames(
		{
			'woocommerce-layout__inbox-panel-header': subtitle,
			'woocommerce-layout__activity-panel-header': ! subtitle,
		},
		className
	);

	return (
		<div className={ cardClassName }>
			<div className="woocommerce-layout__inbox-title">
				<Text variant="title.small">{ title }</Text>
			</div>
			<div className="woocommerce-layout__inbox-subtitle">
				{ subtitle && <Text variant="body.small">{ subtitle }</Text> }
			</div>
			{ menu && (
				<div className="woocommerce-layout__activity-panel-header-menu">
					{ menu }
				</div>
			) }
		</div>
	);
}
