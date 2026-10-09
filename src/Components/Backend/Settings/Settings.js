import { InspectorControls } from '@wordpress/block-editor';
import { TabPanel } from '@wordpress/components';
import General from './General/General';
import Style from './Style/Style';
import { tabs } from '../../../utils/options';

const Settings = ({ attributes, setAttributes }) => {
	return <InspectorControls>
		<TabPanel className='alrpTabs' tabs={tabs}>
			{(tab) => <>
				{'general' === tab.name && <General {...{ attributes, setAttributes }} />}

				{'style' === tab.name && <Style {...{ attributes, setAttributes }} />}
			</>}
		</TabPanel>
	</InspectorControls>;
};

export default Settings;
