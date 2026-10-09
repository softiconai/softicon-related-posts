import domReady from '@wordpress/dom-ready';
import { createRoot } from '@wordpress/element';
import App from './Components/App';
import './settings.scss';

// Rendering on domReady lets add-on scripts loaded after this one register their tabs first.
domReady(() => {
	const root = document.getElementById('alrp-settings-app');
	if (root) {
		createRoot(root).render(<App />);
	}
});
