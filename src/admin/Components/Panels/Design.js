import { __ } from '@wordpress/i18n';
import { RangeControl, SelectControl, ToggleControl } from '@wordpress/components';
import Section from './Section';
import Segmented from '../Fields/Segmented';
import ColorControl from '../../../Components/Panel/ColorControl';
import { toOptions } from '../../../utils/functions';

const Design = ({ settings, set, data }) => {
	const color = (key, label) => <ColorControl className='alrpColor' label={label} value={settings[key]} palette={data.palette} onChange={v => set(key)(v || '')} />;

	return <>
		<Section title={__('Layout', 'softicon-related-posts')}>
			<Segmented label={__('Layout', 'softicon-related-posts')} help={'minimal' === settings.layout ? __('Minimal shows only titles (and dates if enabled).', 'softicon-related-posts') : ''} value={settings.layout} options={data.choices.layout} onChange={set('layout')} />

			<SelectControl label={__('Image ratio', 'softicon-related-posts')} value={settings.image_ratio} options={toOptions(data.choices.image_ratio)} onChange={set('image_ratio')} __nextHasNoMarginBottom />
		</Section>

		<Section title={__('Cards', 'softicon-related-posts')}>
			<Segmented label={__('Card style', 'softicon-related-posts')} value={settings.card_style} options={data.choices.card_style} onChange={set('card_style')} />

			<RangeControl label={__('Corner radius (px)', 'softicon-related-posts')} value={Number(settings.radius)} onChange={v => set('radius')(v || 0)} min={0} max={40} __nextHasNoMarginBottom />

			<ToggleControl label={__('Zoom image on hover', 'softicon-related-posts')} checked={!!settings.hover_zoom} onChange={v => set('hover_zoom')(v ? 1 : 0)} __nextHasNoMarginBottom />
		</Section>

		<Section title={__('Colors', 'softicon-related-posts')} description={__('Leave colors empty to use your theme colors.', 'softicon-related-posts')}>
			{color('title_color', __('Title', 'softicon-related-posts'))}
			{color('text_color', __('Text', 'softicon-related-posts'))}
			{color('card_bg', __('Card background', 'softicon-related-posts'))}
		</Section>
	</>;
};

export default Design;
