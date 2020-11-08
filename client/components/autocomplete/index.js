import {useState, useRef} from "@wordpress/element";
import {Dashicon} from "@wordpress/components";
import './style.scss';

function AutoComplete({children, placeholder, value}) {
	const [search, setSearch] = useState(value);

	const handleSearch = (e) => {

	}

	const click = (e) =>{

	}

	return (
		<div className="ea-autocomplete">
			<div className="ea-autocomplete__input-wrap">
				<input
					value={search}
					onChange={handleSearch}
					type="text"
					className='ea-autocomplete__input'
					placeholder={placeholder}
					autocomplete='off'/>
			</div>
			<button className="ea-autocomplete__btn">
				{/*<Dashicon icon='search' size={20}/>*/}
				<Dashicon icon='no-alt' size={22}/>
			</button>

			{children && children}
		</div>
	)
}

export default AutoComplete;
