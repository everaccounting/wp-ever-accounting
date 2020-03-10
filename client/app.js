import {Button} from "@wordpress/components";
import {getSetting} from "@eaccounting/settings";

const App = () => {
	console.log(getSetting('currency'));
	return <div style={{ backgroundColor: 'red', height: '200px', display: 'block' }}>Hello JELLO DEMO 1
		<Button isPrimary={true}>Click NEW</Button>
	</div>;
};

export default App;
