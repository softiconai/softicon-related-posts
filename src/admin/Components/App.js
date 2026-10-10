import { __ } from '@wordpress/i18n';
import { useCallback, useEffect, useMemo, useState } from '@wordpress/element';
import { Card, CardBody, SnackbarList, TabPanel } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { applyFilters } from '@wordpress/hooks';
import Header from './Header';
import Preview from './Preview';
import General from './Panels/General';
import Filters from './Panels/Filters';
import Display from './Panels/Display';
import Design from './Panels/Design';
import AnalyticsLocked from './Pro/AnalyticsLocked';
import { isLicensed } from './Pro/slots';
import Section from './Panels/Section';
import ToggleRow from './Fields/ToggleRow';
import Tiles from './Fields/Tiles';
import Pills from './Fields/Pills';
import MediaField from './Fields/MediaField';
import ColorControl from '../../Components/Panel/ColorControl';

const data = window.alrpSettingsData || {};

// Shared with add-on tabs, so their panels look like the built-in ones.
const ui = { Section, ToggleRow, Tiles, Pills, MediaField, ColorControl };

// className picks the dashicon shown before each tab title (settings.scss)
const baseTabs = [
	{ name: 'general', title: __('General', 'softicon-related-posts'), className: 'alrpTab-general', Panel: General },
	{ name: 'filters', title: __('Filters', 'softicon-related-posts'), className: 'alrpTab-filters', Panel: Filters },
	{ name: 'display', title: __('Display', 'softicon-related-posts'), className: 'alrpTab-display', Panel: Display },
	{ name: 'design', title: __('Design', 'softicon-related-posts'), className: 'alrpTab-design', Panel: Design }
];

const App = () => {
	const [settings, setSettings] = useState(data.settings || {});
	const [saved, setSaved] = useState(JSON.stringify(data.settings || {}));
	const [saving, setSaving] = useState(false);
	const [notices, setNotices] = useState([]);

	const isDirty = JSON.stringify(settings) !== saved;
	const set = useCallback(key => value => setSettings(prev => ({ ...prev, [key]: value })), []);
	const panelProps = useMemo(() => ({ settings, set, data, ui }), [settings, set]);

	// Add-ons add tabs as { name, title, className, Panel }; Panel gets the same props as the built-in panels.
	// without an active Pro license the Analytics tab is a locked preview; Pro replaces it with the real one
	const tabs = useMemo(() => applyFilters('alrp.settings.tabs', isLicensed(data) ? baseTabs : [...baseTabs, { name: 'analytics', title: __('Analytics', 'softicon-related-posts'), className: 'alrpTab-analytics', Panel: AnalyticsLocked }], data), []);

	const notify = (content, status = 'success') => setNotices(list => [...list, { id: Date.now(), content, status }]);

	const save = useCallback(async () => {
		setSaving(true);
		try {
			const result = await apiFetch({ path: '/alrp/v1/settings', method: 'POST', data: { settings } });
			setSettings(result);
			setSaved(JSON.stringify(result));
			notify(__('Settings saved.', 'softicon-related-posts'));
		} catch (error) {
			notify(error?.message || __('Could not save the settings.', 'softicon-related-posts'), 'error');
		}
		setSaving(false);
	}, [settings]);

	// Ctrl/Cmd+S saves, like the block editor.
	useEffect(() => {
		const onKey = e => {
			if ((e.metaKey || e.ctrlKey) && 's' === e.key.toLowerCase()) {
				e.preventDefault();
				isDirty && !saving && save();
			}
		};
		document.addEventListener('keydown', onKey);
		return () => document.removeEventListener('keydown', onKey);
	}, [isDirty, saving, save]);

	// Warn before leaving with unsaved changes.
	useEffect(() => {
		const onLeave = e => {
			if (isDirty) {
				e.preventDefault();
				e.returnValue = '';
			}
		};
		window.addEventListener('beforeunload', onLeave);
		return () => window.removeEventListener('beforeunload', onLeave);
	}, [isDirty]);

	return <div className='alrpSettings'>
		<Header {...{ isDirty, saving, save, version: data.version, helpUrl: data.helpUrl, proUrl: isLicensed(data) ? '' : data.proUrl, proActive: isLicensed(data) }} />

		<div className='alrpSettingsBody'>
			<Card className='alrpSettingsMain'>
				<CardBody>
					<TabPanel className='alrpTabs' activeClass='is-active' tabs={tabs}>
						{tab => {
							const { Panel } = tabs.find(t => t.name === tab.name);
							return <Panel {...panelProps} />;
						}}
					</TabPanel>
				</CardBody>
			</Card>

			<Preview settings={settings} />
		</div>

		<SnackbarList className='alrpSnackbars' notices={notices} onRemove={id => setNotices(list => list.filter(n => n.id !== id))} />
	</div>;
};

export default App;
