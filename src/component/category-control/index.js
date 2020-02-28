import {Component, Fragment} from "react";
import {translate as __} from 'lib/locale';
import {apiRequest, eAccountingApi} from "lib/api";
import {AsyncSelect} from '@eaccounting/components'
import PropTypes from "prop-types";

export default class CategoryControl extends Component {
	static propTypes = {
		label: PropTypes.string,
		placeholder: PropTypes.string,
		isMulti: PropTypes.bool,
		onChange: PropTypes.func,
		before: PropTypes.node,
		after: PropTypes.node,
		selected: PropTypes.any
	};

	constructor(props) {
		super(props);
		this.state = {
			defaultOptions: []
		};
	}

	componentDidMount() {
		const {selected} = this.props;

		selected && selected.length && this.getCategory({include: selected}, (options) => {
			this.setState({
				value: options
			})
		});

		this.getCategory({}, (options) => {
			this.setState({
				defaultOptions: options
			})
		});
	}

	getCategory = (params, callback) => {
		apiRequest(eAccountingApi.categories.list(params)).then((res) => {
			callback(res.data.map(item => {
				return {
					label: `${item.name}`,
					value: item.id,
				};
			}))
		});
	};

	onChange = (value) => {
		this.setState({
			value
		});
		this.props.onChange && this.props.onChange(value);
	};

	render() {
		const {value, defaultOptions} = this.state;
		return (
			<Fragment>
				<AsyncSelect
					placeholder={__('Select Category')}
					defaultOptions={defaultOptions}
					value={value}
					onChange={this.onChange}
					noOptionsMessage={() => {
						__('No items')
					}}
					loadOptions={(search, callback) => {
						this.getCategory({search}, callback);
					}}
					{...this.props}
				/>
			</Fragment>
		)
	}
}
