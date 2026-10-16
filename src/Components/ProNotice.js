import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import './ProNotice.scss';

// Under every locked Pro feature: which features Pro adds there, then the upgrade button.
const ProNotice = ({ url, text, newTab = false, compact = false }) => <div className={`alrpProNotice${compact ? ' is-compact' : ''}`}>
	<p className='alrpProNoticeText'>
		<span className='dashicons dashicons-lock' aria-hidden='true' />
		<strong>{text || __('This feature is available in Pro.', 'softicon-related-posts')}</strong>
	</p>

	{url && <Button className='alrpProButton' variant='primary' href={url} {...(newTab ? { target: '_blank', rel: 'noopener noreferrer' } : {})}>
		{__('Upgrade to Pro', 'softicon-related-posts')}
	</Button>}
</div>;

export default ProNotice;
