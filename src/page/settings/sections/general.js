import { Component, Fragment } from 'react';
import { translate as __ } from 'lib/locale';
import {Form, DateControl, Icon, CompactCard, Card, Select} from "@eaccounting/components";



export default class General extends Component {
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
				<CompactCard tagName="h3">{__('General Settings')}</CompactCard>
				<Card>
					<div className="ea-row">
						<div className="ea-col-6">
							<DateControl
								label={"Financial Year Start"}
								before={<Icon icon="calendar-check-o"/>}
								required
							/>
						</div>
						<div className="ea-col-6">
							<Select
								label={"Date Format"}
								options={[
									{
										label:"1",
										value:"1"
									}
								]}
								before={<Icon icon="calendar"/>}
							/>
						</div>
						<div className="ea-col-6">
							<Select
								label={"Date Separator"}
								options={[
									{
										label:"1",
										value:"1"
									}
								]}
								before={<Icon icon="minus"/>}
							/>
						</div>
						<div className="ea-col-6">
							<Select
								label={"Percent (%) Position"}
								options={[
									{
										label:"1",
										value:"1"
									}
								]}
								before={<Icon icon="percent"/>}
							/>
						</div>


					</div>
				</Card>
			</Fragment>
		);
	}
}
