import { __ } from '@wordpress/i18n';
import { RangeControl, SelectControl, TextControl } from '@wordpress/components';
import Section from './Section';
import ToggleRow from '../Fields/ToggleRow';
import MediaField from '../Fields/MediaField';

const Display = ({ settings, set, data }) => {
	const sizes = (data.imageSizes || []).map(size => ({ value: size, label: size }));

	return <>
		<Section icon='heading' title={__('Heading and grid', 'softicon-related-posts')}>
			<TextControl label={__('Section title', 'softicon-related-posts')} help={__('Leave empty to hide the heading.', 'softicon-related-posts')} value={settings.title} onChange={set('title')} __nextHasNoMarginBottom />

			<RangeControl label={__('Columns', 'softicon-related-posts')} help={__('Tablets and phones automatically use fewer.', 'softicon-related-posts')} value={Number(settings.columns)} onChange={v => set('columns')(v || 1)} min={1} max={6} __nextHasNoMarginBottom />

			<ToggleRow label={__('Show the current post’s categories', 'softicon-related-posts')} help={__('Shown as chips above the related posts.', 'softicon-related-posts')} checked={settings.show_categories} onChange={set('show_categories')} />
		</Section>

		<Section icon='format-image' title={__('Image', 'softicon-related-posts')}>
			<ToggleRow label={__('Show featured image', 'softicon-related-posts')} checked={settings.show_image} onChange={set('show_image')} />

			{!!settings.show_image && <>
				<SelectControl label={__('Image size', 'softicon-related-posts')} value={settings.image_size} options={sizes} onChange={set('image_size')} __nextHasNoMarginBottom />

				<MediaField label={__('Fallback image', 'softicon-related-posts')} help={__('Used when a post has no featured image. Leave empty to show a placeholder.', 'softicon-related-posts')} imageId={settings.fallback_image_id} initialUrl={data.fallback} onChange={({ id, url }) => { set('fallback_image_id')(id); set('fallback_image')(url); }} />
			</>}
		</Section>

		<Section icon='editor-paragraph' title={__('Card content', 'softicon-related-posts')}>
			<ToggleRow label={__('Excerpt', 'softicon-related-posts')} checked={settings.show_excerpt} onChange={set('show_excerpt')} />

			{!!settings.show_excerpt && <RangeControl label={__('Excerpt length (words)', 'softicon-related-posts')} value={Number(settings.excerpt_length)} onChange={v => set('excerpt_length')(v || 1)} min={1} max={100} __nextHasNoMarginBottom />}

			<ToggleRow label={__('Publish date', 'softicon-related-posts')} checked={settings.show_date} onChange={set('show_date')} />
			<ToggleRow label={__('Author avatar and name', 'softicon-related-posts')} checked={settings.show_author} onChange={set('show_author')} />
			<ToggleRow label={__('Read more link', 'softicon-related-posts')} checked={settings.show_read_more} onChange={set('show_read_more')} />

			{!!settings.show_read_more && <TextControl label={__('Read more text', 'softicon-related-posts')} value={settings.read_more_text} onChange={set('read_more_text')} __nextHasNoMarginBottom />}
		</Section>
	</>;
};

export default Display;
