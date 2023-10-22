const BreadcrumbSeparator = ({ children }) => {
	return (
		<li className="eac-breadcrumb-separator" aria-hidden="true">
			{children === '' ? children : children || '/'}
		</li>
	);
};

export default BreadcrumbSeparator;
