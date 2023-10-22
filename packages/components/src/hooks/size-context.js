/**
 * WordPress dependencies
 */
import { useContext, createContext } from '@wordpress/element';

const SizeContext = createContext(undefined);

export const SizeContextProvider = ({ children, size }) => {
	const originSize = useContext(SizeContext);
	return <SizeContext.Provider value={size || originSize}>{children}</SizeContext.Provider>;
};
export default SizeContext;
