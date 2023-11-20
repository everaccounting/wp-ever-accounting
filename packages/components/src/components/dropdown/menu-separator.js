/**
 * `MenuTitle` is another valid Menu child, but this does not have any accessibility attributes associated
 * (so this should not be used in place of the `DropdownMenu` prop `label`).
 */

const MenuSeparator = () => {
	return <div className="eac-dropdown__separator" />;
};

export default MenuSeparator;
