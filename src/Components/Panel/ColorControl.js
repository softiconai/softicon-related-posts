import { __ } from '@wordpress/i18n';
import { Button, ColorPicker, Dropdown, PanelRow } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import './ColorControl.scss';

// palette: list of color strings, used outside the block editor where its store isn't loaded
const ColorControl = ({ className = '', label, value = '', onChange, palette }) => {
	const editorColors = useSelect(select => {
		const store = palette ? null : select('core/block-editor');
		return store ? store.getSettings().colors || [] : [];
	}, [palette]);
	const themeColors = palette ? palette.map(color => ({ color })) : editorColors;

	return <PanelRow className={`alrpColorControl ${className}`}>
		<span className='alrpColorLabel'>{label}</span>

		<Dropdown className='alrpColorDropdown' contentClassName='alrpColorPopover' popoverProps={{ placement: 'top-end' }}
			renderToggle={({ isOpen, onToggle }) => <>
				<button type='button' className='alrpSwatch' onClick={onToggle} aria-expanded={isOpen} aria-label={label} style={{ '--alrp-swatch': value || 'transparent' }} />

				{value && <Button className='alrpColorClear' icon='image-rotate' size='small' label={__('Clear', 'softicon-related-posts')} onClick={() => onChange(undefined)} />}
			</>}
			renderContent={({ onClose }) => <>
				<ColorPicker color={value || ''} onChangeComplete={c => onChange(`rgba(${c.rgb.r}, ${c.rgb.g}, ${c.rgb.b}, ${c.rgb.a})`)} />

				{themeColors.length ? <div className='alrpColorPalette'>
					{themeColors.map(({ color }) => <button key={color} type='button' className='alrpSwatch alrpSwatchSmall' aria-label={color} style={{ '--alrp-swatch': color }} onClick={() => { onChange(color); onClose(); }} />)}
				</div> : null}
			</>}
		/>
	</PanelRow>;
};

export default ColorControl;
