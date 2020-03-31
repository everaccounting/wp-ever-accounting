const SHRINK_TIME = 5000;
import {Component} from "@wordpress/element";
import {withSelect} from '@wordpress/data';

class Notice extends Component {

	constructor(props) {
		super(props);
		this.state = {
			shrunk: false,
			width: 'auto',
		};
	}

	componentWillUnmount() {
		this.stopTimer();
	}

	onClick = () => {
		if ( this.state.shrunk ) {
			this.setState( { shrunk: false } );
		} else {
			this.props.onClear();
		}
	}


	stopTimer() {
		clearTimeout( this.timer );
	}

	startTimer() {
		this.timer = setTimeout( this.onShrink, SHRINK_TIME );
	}

	onShrink = () => {
		this.setState( { shrunk: true } );
	}

	getNotice() {
		const {notices} = this.props;
		if (notices.length > 1) {
			return notices[notices.length - 1] + ' (' + notices.length + ')';
		}

		return notices[0];
	}


	renderNotice(notices) {
		const klasses = 'notice notice-info eaccounting-notice' + (this.state.shrunk ? ' redirection-notice_shrunk' : '');
		const {notices} = this.props;

		return (
			{notices.map((notice, index) => {
				return (
					<div key={index} className={klasses} onClick={this.onClick}>
						<div className="closer">&#10004;</div>
						<p>{ this.state.shrunk ? <span title={ __( 'View notice' ) }>🔔</span> : notice.content }</p>
					</div>
				)
			})}
		)
	}


	render() {
		const {notices} = this.props;

		if (notices.length === 0) {
			return null;
		}

		return this.renderNotice(notices);
	}
}

export default withSelect(select => {
	return {
		notices: select('core/notices').getNotices()
	}
})(Notice);
