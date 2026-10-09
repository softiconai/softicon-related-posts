import { BaseControl } from '@wordpress/components';

// Visual single choice: each option is a tile with a small illustration.
const Tiles = ({ label, help, value, options, art = {}, onChange }) => <BaseControl label={label} help={help} __nextHasNoMarginBottom>
	<div className='alrpTiles' role='radiogroup' aria-label={label}>
		{Object.keys(options).map(key => <button key={key} type='button' role='radio' aria-checked={key === value} className={`alrpTile ${key === value ? 'is-selected' : ''}`} onClick={() => onChange(key)}>
			{art[key] && <span className='alrpTileArt'>{art[key]}</span>}
			<span className='alrpTileLabel'>{options[key]}</span>
		</button>)}
	</div>
</BaseControl>;

export default Tiles;
