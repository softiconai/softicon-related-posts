import { __ } from '@wordpress/i18n';
import { Button, PanelBody, RangeControl, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import { blockData, getValue, toOptions } from '../../../../utils/functions';

const General = ({ attributes, setAttributes }) => {
	const { choices } = blockData();
	const value = key => getValue(attributes, key);
	const set = key => val => setAttributes({ [key]: val });

	const toggles = [
		['showCategories', __('Show current post categories', 'softicon-related-posts')],
		['showImage', __('Show featured image', 'softicon-related-posts')],
		['showExcerpt', __('Show excerpt', 'softicon-related-posts')],
		['showDate', __('Show publish date', 'softicon-related-posts')],
		['showAuthor', __('Show author', 'softicon-related-posts')],
		['showReadMore', __('Show read more link', 'softicon-related-posts')]
	];

	// Unset every attribute so the block follows the settings page again.
	const resetAll = () => setAttributes(Object.keys(attributes).reduce((acc, key) => ({ ...acc, [key]: undefined }), {}));

	return <>
		<PanelBody className='bPlPanelBody' title={__('Query', 'softicon-related-posts')} initialOpen={true}>
			<RangeControl label={__('Number of posts', 'softicon-related-posts')} value={value('posts')} onChange={set('posts')} min={1} max={24} __nextHasNoMarginBottom />

			<SelectControl className='mt20' label={__('Related by', 'softicon-related-posts')} value={value('relation')} options={toOptions(choices.relation)} onChange={set('relation')} __nextHasNoMarginBottom />

			<SelectControl className='mt20' label={__('Order by', 'softicon-related-posts')} value={value('orderby')} options={toOptions(choices.orderby)} onChange={set('orderby')} __nextHasNoMarginBottom />
		</PanelBody>

		<PanelBody className='bPlPanelBody' title={__('Display', 'softicon-related-posts')} initialOpen={false}>
			<TextControl label={__('Section title', 'softicon-related-posts')} value={value('title')} onChange={set('title')} __nextHasNoMarginBottom />

			<RangeControl className='mt20' label={__('Columns', 'softicon-related-posts')} value={value('columns')} onChange={set('columns')} min={1} max={6} __nextHasNoMarginBottom />

			{toggles.map(([key, label]) => <ToggleControl key={key} className='mt20' label={label} checked={!!value(key)} onChange={set(key)} __nextHasNoMarginBottom />)}
		</PanelBody>

		<PanelBody className='bPlPanelBody' title={__('Defaults', 'softicon-related-posts')} initialOpen={false}>
			<p>{__('Options you have not changed here follow Related Posts → Settings.', 'softicon-related-posts')}</p>

			<Button className='alrpResetBtn' variant='secondary' onClick={resetAll}>{__('Reset to settings page values', 'softicon-related-posts')}</Button>
		</PanelBody>
	</>;
};

export default General;
