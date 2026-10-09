import { __ } from '@wordpress/i18n';
import { useCallback, useEffect, useMemo, useState } from '@wordpress/element';
import { Card, CardBody, SnackbarList, TabPanel } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import Header from './Header';
import Preview from './Preview';
import General from './Panels/General';
import Filters from './Panels/Filters';
import Display from './Panels/Display';
import Design from './Panels/Design';

const data = window.alrpSettingsData || {};

const tabs = [
	{ name: 'general', title: __('General', 'softicon-related-posts'), Panel: General },
	{ name: 'filters', title: __('Filters', 'softicon-related-posts'), Panel: Filters },
	{ name: 'display', title: __('Display', 'softicon-related-posts'), Panel: Display },
	{ name: 'design', title: __('Design', 'softicon-related-posts'), Panel: Design }
];

const App = () => {
	const [settings, setSettings] = useState(data.settings || {});
	const [saved, setSaved] = useState(JSON.stringify(data.settings || {}));
	const [saving, setSaving] = useState(false);
	const [notices, setNotices] = useState([]);

	const isDirty = JSON.stringify(settings) !== saved;
	const set = useCallback(key => value => setSettings(prev => ({ ...prev, [key]: value })), []);
	const panelProps = useMemo(() => ({ settings, set, data }), [settings, set]);

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
		<Header {...{ isDirty, saving, save, version: data.version, helpUrl: data.helpUrl }} />

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
