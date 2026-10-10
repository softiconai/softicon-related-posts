import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import { blockIcon } from '../../utils/icons';

const Header = ({ isDirty, saving, save, version, helpUrl, proUrl, proActive }) => <div className='alrpHeader'>
	<div className='alrpHeaderTitle'>
		<span className='alrpHeaderIcon'>{blockIcon}</span>
		<h1>{__('SoftIcon Related Posts', 'softicon-related-posts')}</h1>
		{version && <span className='alrpVersion'>v{version}</span>}
		{proActive && <span className='alrpProBadgeHead'>PRO</span>}
	</div>

	<div className='alrpHeaderActions'>
		{proUrl && <Button className='alrpProLink' variant='tertiary' href={proUrl}>{__('Get Pro', 'softicon-related-posts')}</Button>}

		<Button variant='tertiary' href={helpUrl}>{__('Help & Videos', 'softicon-related-posts')}</Button>

		<span className='alrpStatus'>{isDirty ? __('Unsaved changes', 'softicon-related-posts') : __('All changes saved', 'softicon-related-posts')}</span>

		<Button variant='primary' onClick={save} isBusy={saving} disabled={!isDirty || saving}>
			{saving ? __('Saving…', 'softicon-related-posts') : __('Save Changes', 'softicon-related-posts')}
		</Button>
	</div>
</div>;

export default Header;
