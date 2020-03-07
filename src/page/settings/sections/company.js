import { Component, Fragment } from 'react';
import { translate as __ } from 'lib/locale';
import {Form, TextControl, TextareaControl, DateControl, Icon, CompactCard, Card, Select} from "@eaccounting/components";


export default class Company extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	componentDidCatch(error, info) {
		this.setState({ error: true, stack: error, info });
	}

	render() {
		return (
			<Fragment>
				<CompactCard tagName="h3">{__('Company Settings')}</CompactCard>
				<Card>
					<div className="ea-row">

						<div className="ea-col-6">
							<TextControl
							label={__("Name")}
							before={<Icon icon="id-card-o"/>}
							required/>
						</div>

						<div className="ea-col-6">
							<TextControl
								label={__("Email")}
								before={<Icon icon="envelope"/>}
								type="email"
								required/>
						</div>

						<div className="ea-col-6">
							<TextControl
								label={__("Tax Number")}
								before={<Icon icon="percent"/>}/>
						</div>

						<div className="ea-col-6">
							<TextControl
								label={__("Phone")}
								before={<Icon icon="phone"/>}/>
						</div>



					</div>
				</Card>
			</Fragment>
		);
	}
}
