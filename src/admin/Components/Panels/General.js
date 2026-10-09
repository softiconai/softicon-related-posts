import { __ } from '@wordpress/i18n';
import { CheckboxControl, BaseControl, RangeControl, SelectControl, ToggleControl } from '@wordpress/components';
import Section from './Section';
import { toOptions } from '../../../utils/functions';

const General = ({ settings, set, data }) => {
	const types = settings.post_types || [];
	const toggleType = (type, on) => set('post_types')(on ? [...types, type] : types.filter(t => t !== type));

	return <>
		<Section title={__('Where related posts appear', 'softicon-related-posts')}>
			<ToggleControl label={__('Show related posts on single posts', 'softicon-related-posts')} checked={!!settings.enable} onChange={v => set('enable')(v ? 1 : 0)} __nextHasNoMarginBottom />

			<BaseControl label={__('Post types', 'softicon-related-posts')} help={__('Pages usually have no categories or tags, so use hand-picked related posts there.', 'softicon-related-posts')} __nextHasNoMarginBottom>
				<div className='alrpChecks alrpChecksInline'>
					{Object.keys(data.postTypes || {}).map(type => <CheckboxControl key={type} label={data.postTypes[type]} checked={types.includes(type)} onChange={on => toggleType(type, on)} __nextHasNoMarginBottom />)}
				</div>
			</BaseControl>

			<SelectControl label={__('Position', 'softicon-related-posts')} value={settings.position} options={toOptions(data.choices.position)} onChange={set('position')} __nextHasNoMarginBottom />
		</Section>

		<Section title={__('How posts are matched', 'softicon-related-posts')}>
			<SelectControl label={__('Related by', 'softicon-related-posts')} value={settings.relation} options={toOptions(data.choices.relation)} onChange={set('relation')} __nextHasNoMarginBottom />

			<SelectControl label={__('Order by', 'softicon-related-posts')} help={'relevance' === settings.orderby ? __('Posts sharing more tags and categories come first. A shared tag counts twice.', 'softicon-related-posts') : ''} value={settings.orderby} options={toOptions(data.choices.orderby)} onChange={set('orderby')} __nextHasNoMarginBottom />

			<RangeControl label={__('Number of posts', 'softicon-related-posts')} value={Number(settings.posts_per_page)} onChange={v => set('posts_per_page')(v || 1)} min={1} max={24} __nextHasNoMarginBottom />
		</Section>

		<Section title={__('RSS feed', 'softicon-related-posts')}>
			<ToggleControl label={__('Add related post links to items in your RSS feed', 'softicon-related-posts')} help={__('Added to the full text of feed items.', 'softicon-related-posts')} checked={!!settings.show_in_feed} onChange={v => set('show_in_feed')(v ? 1 : 0)} __nextHasNoMarginBottom />
		</Section>
	</>;
};

export default General;
