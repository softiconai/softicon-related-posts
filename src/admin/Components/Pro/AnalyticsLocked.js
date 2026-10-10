import { __ } from '@wordpress/i18n';
import ProLocked from './ProLocked';

// Example numbers only, clearly labelled, so the locked tab shows what the real one looks like.
const example = [[16465, __('Post views', 'softicon-related-posts')], [26888, __('Related posts seen', 'softicon-related-posts')], [2398, __('Clicks', 'softicon-related-posts')], ['8.9%', __('Click rate', 'softicon-related-posts')]];
const bars = [38, 44, 41, 52, 49, 57, 55, 63, 60, 68, 72, 70, 79, 83, 88, 92];

const AnalyticsLocked = ({ data }) => <ProLocked icon='chart-bar' url={data.pricingUrl} title={__('How related posts perform', 'softicon-related-posts')} description={__('Views, clicks and click rate for every placement, counted without cookies. Example data shown.', 'softicon-related-posts')}>
	<div className='alrpExampleKpis'>
		{example.map(([value, label]) => <div key={label}><strong>{'number' === typeof value ? value.toLocaleString() : value}</strong><span>{label}</span></div>)}
	</div>
	<div className='alrpExampleChart' aria-hidden='true'>{bars.map((h, i) => <span key={i} style={{ height: `${h}%` }} />)}</div>
	<ul className='alrpExampleList'>
		<li>{__('Popular and Trending order', 'softicon-related-posts')}</li>
		<li>{__('Internal link report: find posts nothing links to', 'softicon-related-posts')}</li>
	</ul>
</ProLocked>;

export default AnalyticsLocked;
