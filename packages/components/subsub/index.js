import {useState} from "@wordpress/element";
import PropTypes from 'prop-types';
import classNames from "classnames";
import './style.scss';


const SubItem = (props) => {
	const {item, isCurrent, onClick, isLast} = props;
	const clicker = (ev) => {
		ev.preventDefault();
		onClick(item.value);
	};

	return (
		<li className={item.value}>
			<a className={classNames({ 'current':  isCurrent})} href='#' onClick={clicker}>
				{item.name}
			</a> {!isLast && '|'}&nbsp;
		</li>
	);
}

const SubSub = (props) => {
	const {items = [], current = '', onClick, isDisabled = false} = props;
	const [active, setActive] = useState(current);

	if (items.length < 2) {
		return null;
	}


	const HandleClick = (active) => {
		setActive(active);
		onClick(active);
	}

	return (
		<ul className="subsubsub ea-subsubsub">
			{items.map((item, pos) => (
				<SubItem
					key={pos}
					item={item}
					isDisabled={isDisabled}
					isCurrent={active === item.value}
					isLast={pos === items.length - 1}
					onClick={HandleClick}
				/>
			))}
		</ul>
	);
}

SubSub.propTypes = {
	onClick: PropTypes.func,
	isDisabled: PropTypes.string,
	current: PropTypes.string,
	items: PropTypes.arrayOf(PropTypes.shape({
		name: PropTypes.string,
		value: PropTypes.string,
	}))
}

SubSub.defaultProps = {
	onClick: () => {
	},
	current: '',
	isDisabled: false,
	items: []
}

export default SubSub;
