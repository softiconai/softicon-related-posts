import { __ } from '@wordpress/i18n';
import { RangeControl, TextControl } from '@wordpress/components';
import Section from './Section';
import Pills from '../Fields/Pills';

const Filters = ({ settings, set, data }) => {
	const categories = (data.categories || []).reduce((acc, cat) => ({ ...acc, [cat.id]: cat.name }), {});
	const excluded = (settings.exclude_categories || []).map(String);
	const postIds = Array.isArray(settings.exclude_posts) ? settings.exclude_posts.join(', ') : settings.exclude_posts || '';

	return <>
		<Section icon='hidden' title={__('Leave out', 'softicon-related-posts')} description={__('Keep some content out of every related posts list.', 'softicon-related-posts')}>
			<Pills label={__('Exclude categories', 'softicon-related-posts')} help={__('Posts in the highlighted categories never appear as related posts.', 'softicon-related-posts')} value={excluded} options={categories} onChange={ids => set('exclude_categories')(ids.map(Number))} />

			<TextControl label={__('Exclude posts', 'softicon-related-posts')} help={__('Comma separated post IDs, e.g. 12, 45', 'softicon-related-posts')} value={postIds} onChange={set('exclude_posts')} __nextHasNoMarginBottom />
		</Section>

		<Section icon='calendar-alt' title={__('Freshness', 'softicon-related-posts')}>
			<RangeControl label={__('Only posts from the last (months)', 'softicon-related-posts')} help={__('0 shows posts of any age.', 'softicon-related-posts')} value={Number(settings.max_age)} onChange={v => set('max_age')(v || 0)} min={0} max={120} __nextHasNoMarginBottom />
		</Section>
	</>;
};

export default Filters;
