import { createRoot } from '@wordpress/element';
import App from './Components/App';
import './settings.scss';

const root = document.getElementById('alrp-settings-app');
if (root) {
	createRoot(root).render(<App />);
}
