import { __ } from '@wordpress/i18n';
import { BaseControl, CheckboxControl, RangeControl, TextControl } from '@wordpress/components';
import Section from './Section';

const Filters = ({ settings, set, data }) => {
	const excluded = (settings.exclude_categories || []).map(Number);
	const toggleCat = (id, on) => set('exclude_categories')(on ? [...excluded, id] : excluded.filter(c => c !== id));
	const postIds = Array.isArray(settings.exclude_posts) ? settings.exclude_posts.join(', ') : settings.exclude_posts || '';

	return <>
		<Section title={__('Leave out', 'softicon-related-posts')}>
			<BaseControl label={__('Exclude categories', 'softicon-related-posts')} help={__('Posts in these categories never appear as related posts.', 'softicon-related-posts')} __nextHasNoMarginBottom>
				<div className='alrpChecks alrpChecksBox'>
					{(data.categories || []).map(cat => <CheckboxControl key={cat.id} label={cat.name} checked={excluded.includes(cat.id)} onChange={on => toggleCat(cat.id, on)} __nextHasNoMarginBottom />)}
				</div>
			</BaseControl>

			<TextControl label={__('Exclude posts', 'softicon-related-posts')} help={__('Comma separated post IDs, e.g. 12, 45', 'softicon-related-posts')} value={postIds} onChange={set('exclude_posts')} __nextHasNoMarginBottom />
		</Section>

		<Section title={__('Freshness', 'softicon-related-posts')}>
			<RangeControl label={__('Only posts from the last (months)', 'softicon-related-posts')} help={__('0 shows posts of any age.', 'softicon-related-posts')} value={Number(settings.max_age)} onChange={v => set('max_age')(v || 0)} min={0} max={120} __nextHasNoMarginBottom />
		</Section>
	</>;
};

export default Filters;
