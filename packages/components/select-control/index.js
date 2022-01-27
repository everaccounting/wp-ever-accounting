/**
 * Internal dependencies
 */
import AsyncSelect from './async';
import SelectControl from './control';
import CustomerControl from './customer';

import './style.scss';
import VendorControl from './vendor';
import CurrencyControl from './currency';

SelectControl.Async = AsyncSelect;
SelectControl.Customer = CustomerControl;
SelectControl.Vendor = VendorControl;
SelectControl.Currency = CurrencyControl;

export default SelectControl;
