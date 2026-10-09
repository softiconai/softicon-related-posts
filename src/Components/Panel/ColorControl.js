import { __ } from '@wordpress/i18n';
import { Button, ColorPicker, Dropdown, PanelRow } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import Label from '../../../../bpl-tools/Components/Label/Label';
import '../../../../bpl-tools/Components/ColorControl/ColorControl.scss';

// Same UI as bpl-tools ColorControl, imported directly so the bpl-tools index (and its code editor) stays out of the bundle.
// palette: list of color strings, used outside the block editor where its store isn't loaded
const ColorControl = ({ className = '', label, value = '', onChange, palette }) => {
	const editorColors = useSelect(select => {
		const store = palette ? null : select('core/block-editor');
		return store ? store.getSettings().colors || [] : [];
	}, [palette]);
	const themeColors = palette ? palette.map(color => ({ color })) : editorColors;

	return <PanelRow className={className}>
		<Label className=''>{label}</Label>

		<Dropdown className='bPlDropdownContainer bPlColor' contentClassName='bPlDropdownPopover' popoverProps={{ placement: 'top-end' }}
			renderToggle={({ isOpen, onToggle }) => <>
				<div className='bPlColorButtonContainer'>
					<button type='button' className='bPlColorButton' onClick={onToggle} aria-expanded={isOpen} aria-label={label} style={{ backgroundColor: value || 'transparent' }} />
				</div>

				{value && <Button className='bPlResetVal' icon='image-rotate' label={__('Clear', 'softicon-related-posts')} onClick={() => onChange(undefined)} />}
			</>}
			renderContent={({ onClose }) => <>
				<ColorPicker color={value || ''} onChangeComplete={c => onChange(`rgba(${c.rgb.r}, ${c.rgb.g}, ${c.rgb.b}, ${c.rgb.a})`)} />

				{themeColors.length ? <div className='bPlThemeColors'>
					{themeColors.map(({ color }) => <div key={color} className='bPlColorButtonContainer'>
						<button type='button' className='bPlColorButton' aria-label={color} style={{ backgroundColor: color }} onClick={() => { onChange(color); onClose(); }} />
					</div>)}
				</div> : null}
			</>}
		/>
	</PanelRow>;
};

export default ColorControl;
