import { push } from 'connected-react-router'
import {connect} from "react-redux";

export const Link = (props) => {
	const {href, className ='', children, push} = props;
	const handleClick = (e, link) => {
		e.preventDefault();
		push(link);
	};

	return (
		<a href="#" className={className} onClick={(e) => {
			handleClick(e, href)
		}}>{children}</a>
	)
};

export default connect(null, { push })(Link);

