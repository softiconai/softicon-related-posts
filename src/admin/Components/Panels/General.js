import { __ } from '@wordpress/i18n';
import { RangeControl, SelectControl } from '@wordpress/components';
import Section from './Section';
import ToggleRow from '../Fields/ToggleRow';
import Pills from '../Fields/Pills';
import { toOptions } from '../../../utils/functions';
import { Slot } from '../Pro/slots';

// Pro orders are listed but disabled until Pro is active.
const orderOptions = choices => {
	const options = toOptions(choices);
	if (!choices.smart) {
		options.push(
			{ value: 'pro-smart', label: __('Smart: keywords, terms and freshness (Pro)', 'softicon-related-posts'), disabled: true },
			{ value: 'pro-popular', label: __('Popular: most read in 30 days (Pro)', 'softicon-related-posts'), disabled: true },
			{ value: 'pro-trending', label: __('Trending: most read in 7 days (Pro)', 'softicon-related-posts'), disabled: true }
		);
	}
	return options;
};

const General = props => {
	const { settings, set, data } = props;

	return <>
	<Section icon='visibility' title={__('Where related posts appear', 'softicon-related-posts')}>
		<ToggleRow label={__('Show related posts automatically', 'softicon-related-posts')} help={__('Adds them to every single post of the chosen types.', 'softicon-related-posts')} checked={settings.enable} onChange={set('enable')} />

		<Pills label={__('Post types', 'softicon-related-posts')} help={__('Pages usually have no categories or tags, so use hand-picked related posts there.', 'softicon-related-posts')} value={settings.post_types || []} options={data.postTypes || {}} onChange={set('post_types')} />

		<SelectControl label={__('Position', 'softicon-related-posts')} value={settings.position} options={toOptions(data.choices.position)} onChange={set('position')} __nextHasNoMarginBottom />
	</Section>

	<Slot name='general.placement' {...props} />

	<Section icon='randomize' title={__('How posts are matched', 'softicon-related-posts')}>
		<SelectControl label={__('Related by', 'softicon-related-posts')} value={settings.relation} options={toOptions(data.choices.relation)} onChange={set('relation')} __nextHasNoMarginBottom />

		<SelectControl label={__('Order by', 'softicon-related-posts')} help={'relevance' === settings.orderby ? __('Posts sharing more tags and categories come first. A shared tag counts twice.', 'softicon-related-posts') : ''} value={settings.orderby} options={orderOptions(data.choices.orderby)} onChange={set('orderby')} __nextHasNoMarginBottom />

		<RangeControl label={__('Number of posts', 'softicon-related-posts')} value={Number(settings.posts_per_page)} onChange={v => set('posts_per_page')(v || 1)} min={1} max={24} __nextHasNoMarginBottom />
	</Section>

	<Slot name='general.matching' {...props} />

	<Section icon='rss' title={__('RSS feed', 'softicon-related-posts')}>
		<ToggleRow label={__('Add related links to feed items', 'softicon-related-posts')} help={__('Added to the full text of each item in your RSS feed.', 'softicon-related-posts')} checked={settings.show_in_feed} onChange={set('show_in_feed')} />
	</Section>

	<Slot name='general.woo' {...props} />
	<Slot name='general.developers' {...props} />
	</>;
};

export default General;
