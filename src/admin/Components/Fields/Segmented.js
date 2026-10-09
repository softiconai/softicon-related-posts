import { BaseControl, Button } from '@wordpress/components';

// Button-style choice for short option lists such as layout and card style.
const Segmented = ({ label, help, value, options, onChange }) => <BaseControl label={label} help={help} __nextHasNoMarginBottom>
	<div className='alrpSegmented' role='group' aria-label={label}>
		{Object.keys(options).map(key => <Button key={key} variant={key === value ? 'primary' : 'secondary'} isPressed={key === value} onClick={() => onChange(key)}>
			{options[key]}
		</Button>)}
	</div>
</BaseControl>;

export default Segmented;
