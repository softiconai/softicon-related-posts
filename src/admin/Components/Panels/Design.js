import { __ } from '@wordpress/i18n';
import { RangeControl } from '@wordpress/components';
import { applyFilters } from '@wordpress/hooks';
import Section from './Section';
import Tiles from '../Fields/Tiles';
import ToggleRow from '../Fields/ToggleRow';
import { layoutArt, ratioArt, cardArt, proLayoutArt } from '../Fields/illustrations';
import ColorControl from '../../../Components/Panel/ColorControl';
import { Slot } from '../Pro/slots';

const Design = props => {
	const { settings, set, data } = props;
	const color = (key, label) => <ColorControl className='alrpColor' label={label} value={settings[key]} palette={data.palette} onChange={v => set(key)(v || '')} />;
	// Add-ons add layouts (label and tile drawing) through these two filters.
	const layouts = applyFilters('alrp.settings.layouts', { grid: __('Grid', 'softicon-related-posts'), list: __('List', 'softicon-related-posts'), minimal: __('Minimal', 'softicon-related-posts') }, data);
	const layoutTiles = applyFilters('alrp.settings.layoutArt', layoutArt, data);
	const ratios = { fixed: __('Fixed', 'softicon-related-posts'), '16-9': '16:9', '4-3': '4:3', '3-2': '3:2', '1-1': '1:1' };

	return <>
		<Section icon='layout' title={__('Layout', 'softicon-related-posts')}>
			<Tiles label={__('Layout', 'softicon-related-posts')} help={'minimal' === settings.layout ? __('Minimal shows only titles (and dates if enabled).', 'softicon-related-posts') : ''} value={settings.layout} options={layouts} art={layoutTiles} onChange={set('layout')} />

			{!layouts.carousel && data.pricingUrl && <div className='alrpProTiles'>
				<span className='alrpProTilesLabel'>{__('More layouts', 'softicon-related-posts')}<span className='alrpProTag'>PRO</span></span>
				<div className='alrpTiles'>
					{[['carousel', __('Carousel', 'softicon-related-posts')], ['overlay', __('Overlay', 'softicon-related-posts')], ['magazine', __('Magazine', 'softicon-related-posts')], ['numbered', __('Numbered', 'softicon-related-posts')]].map(([key, label]) => <a key={key} className='alrpTile is-locked' href={data.pricingUrl}>
						<span className='alrpTileArt'>{proLayoutArt[key]}</span>
						<span className='alrpTileLabel'>{label}<span className='dashicons dashicons-lock' aria-hidden='true' /></span>
					</a>)}
				</div>
			</div>}

			<Tiles label={__('Image ratio', 'softicon-related-posts')} help={'fixed' === settings.image_ratio ? __('Fixed keeps every image 200px tall.', 'softicon-related-posts') : ''} value={settings.image_ratio} options={ratios} art={ratioArt} onChange={set('image_ratio')} />
		</Section>

		<Slot name='design.presets' {...props} />

		<Section icon='admin-appearance' title={__('Cards', 'softicon-related-posts')}>
			<Tiles label={__('Card style', 'softicon-related-posts')} value={settings.card_style} options={data.choices.card_style} art={cardArt} onChange={set('card_style')} />

			<RangeControl label={__('Corner radius (px)', 'softicon-related-posts')} value={Number(settings.radius)} onChange={v => set('radius')(v || 0)} min={0} max={40} __nextHasNoMarginBottom />

			<ToggleRow label={__('Zoom image on hover', 'softicon-related-posts')} checked={settings.hover_zoom} onChange={set('hover_zoom')} />
		</Section>

		<Section icon='art' title={__('Colors', 'softicon-related-posts')} description={__('Leave colors empty to use your theme colors.', 'softicon-related-posts')}>
			<div className='alrpColors'>
				{color('title_color', __('Title', 'softicon-related-posts'))}
				{color('text_color', __('Text', 'softicon-related-posts'))}
				{color('card_bg', __('Card background', 'softicon-related-posts'))}
			</div>
		</Section>

		<Slot name='design.css' {...props} />
	</>;
};

export default Design;
