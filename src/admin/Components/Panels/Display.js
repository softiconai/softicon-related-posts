import { __ } from '@wordpress/i18n';
import { RangeControl, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import Section from './Section';
import MediaField from '../Fields/MediaField';

const Display = ({ settings, set, data }) => {
	const toggle = (key, label, help) => <ToggleControl label={label} help={help} checked={!!settings[key]} onChange={v => set(key)(v ? 1 : 0)} __nextHasNoMarginBottom />;
	const sizes = (data.imageSizes || []).map(size => ({ value: size, label: size }));

	return <>
		<Section title={__('Heading and grid', 'softicon-related-posts')}>
			<TextControl label={__('Section title', 'softicon-related-posts')} help={__('Leave empty to hide the heading.', 'softicon-related-posts')} value={settings.title} onChange={set('title')} __nextHasNoMarginBottom />

			<RangeControl label={__('Columns', 'softicon-related-posts')} help={__('Tablets and phones automatically use fewer.', 'softicon-related-posts')} value={Number(settings.columns)} onChange={v => set('columns')(v || 1)} min={1} max={6} __nextHasNoMarginBottom />

			{toggle('show_categories', __('Show current post categories above related posts', 'softicon-related-posts'))}
		</Section>

		<Section title={__('Image', 'softicon-related-posts')}>
			{toggle('show_image', __('Show featured image', 'softicon-related-posts'))}

			{!!settings.show_image && <>
				<SelectControl label={__('Image size', 'softicon-related-posts')} value={settings.image_size} options={sizes} onChange={set('image_size')} __nextHasNoMarginBottom />

				<MediaField label={__('Fallback image', 'softicon-related-posts')} help={__('Used when a post has no featured image. Leave empty to show a placeholder.', 'softicon-related-posts')} imageId={settings.fallback_image_id} initialUrl={data.fallback} onChange={({ id, url }) => { set('fallback_image_id')(id); set('fallback_image')(url); }} />
			</>}
		</Section>

		<Section title={__('Card content', 'softicon-related-posts')}>
			{toggle('show_excerpt', __('Show excerpt', 'softicon-related-posts'))}

			{!!settings.show_excerpt && <RangeControl label={__('Excerpt length (words)', 'softicon-related-posts')} value={Number(settings.excerpt_length)} onChange={v => set('excerpt_length')(v || 1)} min={1} max={100} __nextHasNoMarginBottom />}

			{toggle('show_date', __('Show publish date', 'softicon-related-posts'))}
			{toggle('show_author', __('Show author avatar and name', 'softicon-related-posts'))}
			{toggle('show_read_more', __('Show read more link', 'softicon-related-posts'))}

			{!!settings.show_read_more && <TextControl label={__('Read more text', 'softicon-related-posts')} value={settings.read_more_text} onChange={set('read_more_text')} __nextHasNoMarginBottom />}
		</Section>
	</>;
};

export default Display;
