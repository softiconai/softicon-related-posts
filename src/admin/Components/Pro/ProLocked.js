import { __ } from '@wordpress/i18n';
import { Disabled, ToggleControl } from '@wordpress/components';
import Section from '../Panels/Section';

// A Pro feature shown in its place but locked: visible, inert, and any click opens the pricing page.
const ProLocked = ({ icon, title, description, items = [], children, url }) => <div className='alrpProLockWrap'>
	<Section icon={icon} title={<>{title}<span className='alrpProTag'>PRO</span></>} description={description}>
		<Disabled className='alrpProLocked'>
			{children || items.map(label => <div key={label} className='alrpToggleRow'>
				<ToggleControl label={label} checked={false} onChange={() => {}} __nextHasNoMarginBottom />
			</div>)}
		</Disabled>
	</Section>
	<a className='alrpProUnlock' href={url}>{__('Unlock with Pro', 'softicon-related-posts')} &rarr;</a>
	<a className='alrpProLockCover' href={url} tabIndex={-1} aria-hidden='true' />
</div>;

export default ProLocked;
