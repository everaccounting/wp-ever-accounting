// import {Select} from '@eac/components';
import Select from './select';
const App = () => {

	return (
		<>
		Hello World!
			<Select
				// isMulti
			options={[
				{ value: 'chocolate', label: 'Chocolate' },
				{ value: 'strawberry', label: 'Strawberry' },
				{ value: 'vanilla', label: 'Vanilla' },
			]}
			/>
		</>
	)
};

export default App;
