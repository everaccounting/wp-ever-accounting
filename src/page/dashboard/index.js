/**
 * External dependencies
 */

import {Component} from 'react';
import { translate as __ } from 'lib/locale';
import { Icon, TextControl,SelectControl, ReactSelect, TextareaControl, CurrencyControl, ToggleControl} from '@eaccounting/components';
import EditAccount from 'component/edit-account';
import {initialAccount} from 'state/accounts/selection';

/**
 * Internal dependencies
 */
import './style.scss';
export default class Dashboard extends Component {
	constructor( props ) {
		super(props);
		this.state = {};
		window.addEventListener( 'popstate', this.onPageChanged );
	}

	componentDidCatch( error, info ) {
		this.setState( { error: true, stack: error, info } );
	}

	componentWillUnmount() {
		window.removeEventListener( 'popstate', this.onPageChanged );
	}


	render() {
		return (
			<div>
				{/*<EditAccount item={initialAccount}/>*/}

				<TextareaControl
					label="Text field with both affixes"
					help="Text field with both affixes"
					value={'third'}
					required
					onChange={value => setState({ third: value })}
				/>

				<TextControl
					before={<Icon icon={'pencil'}/>}
					after="Suffix"
					label="Text field with both affixes"
					help="Text field with both affixes"
					value={'third'}
					required
					onChange={value => setState({ third: value })}
				/>

				<SelectControl
					before={<Icon icon={'pencil'}/>}
					after="Suffix"
					label="Size"
					value={ '25%' }
					help="Text field with both affixes"
					options={ [
						{ label: 'Big', value: '100%' },
						{ label: 'Medium', value: '50%' },
						{ label: 'Small', value: '25%' },
					] }
					onChange={ ( size ) => { this.setState( { size } ) } }
				/>

				<ReactSelect
					before={<Icon icon={'pencil'}/>}
					after="Suffix"
					label="React Select"
					help="Text field with both affixes"
					options={ [
						{ label: 'Big', value: '100%' },
						{ label: 'Medium', value: '50%' },
						{ label: 'Small', value: '25%' },
					] }
					value={{ label: 'Medium', value: '50%' }}
					required
					onChange={ ( size ) => { this.setState( { size } ) } }
				/>

				<ReactSelect
					before={<Icon icon={'pencil'}/>}
					after="Suffix"
					label="React Select"
					help="Text field with both affixes"
					options={ [
						{ label: 'Big', value: '100%' },
						{ label: 'Medium', value: '50%' },
						{ label: 'Small', value: '25%' },
					] }
					value={{ label: 'Medium', value: '50%' }}
					required
					isMulti={true}
					onChange={ ( size ) => { this.setState( { size } ) } }
				/>

				<CurrencyControl
					before={<Icon icon={'pencil'}/>}
					after="Suffix"
					help="Text field with both affixes"
					label={__('Opening Balance')} required/>

				<ToggleControl
					help="Text field with both affixes"
					label={__('Opening Balance')}
					check={ 'on' }
					value={ 'on1' }
					onChange={ () => setState( state => ( { checked: ! state.checked } ) ) }
				/>

			</div>
		);
	}
}

// function mapDispatchToProps( dispatch ) {
// 	return {}
// }
// function mapStateToProps( state ) {
// 	return {}
// }
//
// export default connect(
// 	mapStateToProps,
// 	mapDispatchToProps,
// )( Dashboard );
