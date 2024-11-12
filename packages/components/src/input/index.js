/**
 * Internal dependencies
 */
import Input from './input';
import Radio from './radio';
import Checkbox from './checkbox';
import Textarea from './textarea';
import Switch from './switch';
import Amount from './amount';
import Date from './date';
import Autocomplete from './autocomplete';
import './style.scss';

Input.Text = Input;
Input.Checkbox = Checkbox;
Input.Radio = Radio;
Input.Textarea = Textarea;
Input.Switch = Switch;
Input.Amount = Amount;
Input.Date = Date;
Input.Autocomplete = Autocomplete;

export default Input;
