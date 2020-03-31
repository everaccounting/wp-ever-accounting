import {Component} from '@wordpress/element';
import './style.scss';
import {__} from "@wordpress/i18n";

const notices = [
	{
		type:'error',
		message:'আমার চুপ থাকাটা আমার দুর্বলতা না এটা আমার নবীর শিক্ষা ,আমার ভদ্রতা ইনশাহআল্লাহ হাশরের মাঠে সব পাওনা মিটায়ে দিবে আমার রব । আমি ছাড় দিব না ঐখানে । দুনীয়াতে সব ছাড় তোমার। আলহামদুলিল্লাহ্‌।'
	},
	{
		type:'success',
		message:'আমার চুপ থাকাটা আমার দুর্বলতা না এটা আমার নবীর শিক্ষা ,আমার ভদ্রতা ইনশাহআল্লাহ হাশরের মাঠে সব পাওনা মিটায়ে দিবে আমার রব । আমি ছাড় দিব না ঐখানে । দুনীয়াতে সব ছাড় তোমার। আলহামদুলিল্লাহ্‌।'
	}
];

export default class Notice extends Component {
	constructor( props ) {
		super( props );

		this.state = {
			shrunk: false,
			width: 'auto',
		};
	}

	onClick = () => {
		if ( this.state.shrunk ) {
			this.setState( { shrunk: false } );
		}
	};

	componentDidUpdate( prevProps ) {
		if ( this.props.notices !== prevProps.notices ) {
			this.stopTimer();
			this.setState( { shrunk: false } );
			this.startTimer();
		}

		return null;
	}

	componentWillUnmount() {
		this.stopTimer();
	}

	stopTimer() {
		clearTimeout( this.timer );
	}

	startTimer() {
		this.timer = setTimeout( this.onShrink, SHRINK_TIME );
	}

	onShrink = () => {
		this.setState( { shrunk: true } );
	};

	getNotice( notices ) {
		if ( notices.length > 1 ) {
			return notices[ notices.length - 1 ] + ' (' + notices.length + ')';
		}

		return notices[ 0 ];
	}

	renderNotice( notice, index ) {
		const {type = 'info', message} = notice;
		const klasses = 'notice notice-'+type+' redirection-notice' + ( this.state.shrunk ? ' redirection-notice_shrunk' : '' );
		return (
			<div key={index} className={ klasses } onClick={ this.onClick }>
				<div className="closer">&#10004;</div>
				<p>{ message }</p>
			</div>
		);
	}

	render() {
		if ( notices.length === 0 ) {
			return null;
		}
		return (
			notices.map((notice, index)=> {
				return this.renderNotice( notice, index );
			})
		);

	}
}
