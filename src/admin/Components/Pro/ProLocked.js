import { Disabled, ToggleControl } from '@wordpress/components';
import Section from '../Panels/Section';
import ProNotice from '../../../Components/ProNotice';

// A Pro feature shown in its place but locked: visible and inert, with the upgrade notice and button under it.
const ProLocked = ({ icon, title, description, items = [], notice, children, url }) => <div className='alrpProLockWrap'>
	<Section icon={icon} title={<>{title}<span className='alrpProTag'>PRO</span></>} description={description}>
		<Disabled className='alrpProLocked'>
			{children || items.map(label => <div key={label} className='alrpToggleRow'>
				<ToggleControl label={label} checked={false} onChange={() => {}} __nextHasNoMarginBottom />
			</div>)}
		</Disabled>

		<ProNotice url={url} text={notice} />
	</Section>
	<a className='alrpProLockCover' href={url} tabIndex={-1} aria-hidden='true' />
</div>;

export default ProLocked;
