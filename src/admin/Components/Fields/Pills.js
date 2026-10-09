import { BaseControl } from '@wordpress/components';

// Multi choice shown as pills, for short lists such as post types.
const Pills = ({ label, help, value = [], options, onChange }) => {
	const toggle = key => onChange(value.includes(key) ? value.filter(v => v !== key) : [...value, key]);

	return <BaseControl label={label} help={help} __nextHasNoMarginBottom>
		<div className='alrpPills'>
			{Object.keys(options).map(key => <button key={key} type='button' aria-pressed={value.includes(key)} className={`alrpPill ${value.includes(key) ? 'is-on' : ''}`} onClick={() => toggle(key)}>
				{options[key]}
			</button>)}
		</div>
	</BaseControl>;
};

export default Pills;
