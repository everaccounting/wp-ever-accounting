// eslint-disable-next-line no-undef

const { logo_url } = eaccountingi10n;

export default function SetupWizardHeader( { steps, currentStep } ) {
	return (
		<>
			<h1 className="ea-setup__logo">
				<a
					href="https://wpeveraccounting.com/"
					target="_blank"
					rel="noreferrer"
				>
					<img
						src={ logo_url }
						alt="Ever Accounting"
						width={ 300 }
						height={ 66 }
					/>
				</a>
			</h1>

			<ol className="ea-setup__steps">
				{ steps.map( ( step ) => {
					return <li key={ step.key }>{ step.label }</li>;
				} ) }
			</ol>
		</>
	);
}
