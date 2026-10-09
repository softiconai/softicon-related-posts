import { __ } from '@wordpress/i18n';
import { Button, PanelBody, RangeControl, SelectControl, ToggleControl } from '@wordpress/components';
import ColorControl from '../../../Panel/ColorControl';
import { blockData, getValue, toOptions } from '../../../../utils/functions';

const Style = ({ attributes, setAttributes }) => {
	const { choices } = blockData();
	const value = key => getValue(attributes, key);
	const set = key => val => setAttributes({ [key]: val });

	return <>
		<PanelBody title={__('Layout', 'softicon-related-posts')} initialOpen={true}>
			<SelectControl label={__('Layout', 'softicon-related-posts')} value={value('layout')} options={toOptions(choices.layout)} onChange={set('layout')} __nextHasNoMarginBottom />

			<SelectControl className='alrpMt' label={__('Image ratio', 'softicon-related-posts')} value={value('imageRatio')} options={toOptions(choices.image_ratio)} onChange={set('imageRatio')} __nextHasNoMarginBottom />
		</PanelBody>

		<PanelBody title={__('Card', 'softicon-related-posts')} initialOpen={false}>
			<SelectControl label={__('Card style', 'softicon-related-posts')} value={value('cardStyle')} options={toOptions(choices.card_style)} onChange={set('cardStyle')} __nextHasNoMarginBottom />

			<RangeControl className='alrpMt' label={__('Corner radius (px)', 'softicon-related-posts')} value={value('radius')} onChange={set('radius')} min={0} max={40} __nextHasNoMarginBottom />

			<ToggleControl className='alrpMt' label={__('Zoom image on hover', 'softicon-related-posts')} checked={!!value('hoverZoom')} onChange={set('hoverZoom')} __nextHasNoMarginBottom />
		</PanelBody>

		<PanelBody title={__('Colors', 'softicon-related-posts')} initialOpen={false}>
			<ColorControl label={__('Title', 'softicon-related-posts')} value={value('titleColor')} onChange={set('titleColor')} />

			<ColorControl className='alrpMt' label={__('Text', 'softicon-related-posts')} value={value('textColor')} onChange={set('textColor')} />

			<ColorControl className='alrpMt' label={__('Card background', 'softicon-related-posts')} value={value('cardBg')} onChange={set('cardBg')} />

			<p className='alrpMt description'>{__('Leave colors empty to use your theme colors.', 'softicon-related-posts')}</p>

			{/* ColorControl has no clear button, unset the colors so the settings page applies again. */}
			<Button className='alrpResetBtn' variant='secondary' onClick={() => setAttributes({ titleColor: undefined, textColor: undefined, cardBg: undefined })}>{__('Reset colors', 'softicon-related-posts')}</Button>
		</PanelBody>
	</>;
};

export default Style;
