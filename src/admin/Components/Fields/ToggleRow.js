import { ToggleControl } from '@wordpress/components';

// One setting per row: text on the left, switch on the right.
const ToggleRow = ({ label, help, checked, onChange }) => <div className={`alrpToggleRow ${checked ? 'is-on' : ''}`}>
	<ToggleControl label={label} help={help} checked={!!checked} onChange={v => onChange(v ? 1 : 0)} __nextHasNoMarginBottom />
</div>;

export default ToggleRow;
