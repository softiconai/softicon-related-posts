import { __, sprintf } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import { Spinner } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

// Loads the new preview's images off-screen first (max 1.5s), so switching designs never flashes empty boxes.
const withImages = html => {
	const eager = html.replace(/\sloading="lazy"/g, '');
	const box = document.createElement('div');
	box.innerHTML = eager;
	const images = [...box.querySelectorAll('img')].map(img => img.decode ? img.decode().catch(() => {}) : Promise.resolve());
	return Promise.race([Promise.all(images), new Promise(done => setTimeout(done, 1500))]).then(() => eager);
};

// Renders the unsaved settings on the server, debounced so typing doesn't fire a request per key.
const Preview = ({ settings }) => {
	const [result, setResult] = useState(null);
	const [loading, setLoading] = useState(true);

	useEffect(() => {
		let cancelled = false;
		setLoading(true);
		const timer = setTimeout(() => {
			apiFetch({ path: '/alrp/v1/preview', method: 'POST', data: { settings } })
				.then(res => res.html ? withImages(res.html).then(html => ({ ...res, html })) : res)
				.then(res => !cancelled && setResult(res))
				.catch(() => !cancelled && setResult({ html: '', error: true }))
				.finally(() => !cancelled && setLoading(false));
		}, 450);
		return () => { cancelled = true; clearTimeout(timer); };
	}, [settings]);

	return <aside className='alrpPreview'>
		<div className='alrpPreviewHead'>
			<span className='alrpLiveDot' />
			<strong>{__('Live preview', 'softicon-related-posts')}</strong>
			{loading && <Spinner />}
		</div>

		{result?.post && <p className='alrpPreviewFor'>
			{/* translators: %s: post title */}
			{sprintf(__('Related posts for “%s”', 'softicon-related-posts'), result.post)}
		</p>}

		<div className={`alrpPreviewCanvas ${loading ? 'is-loading' : ''}`}>
			{result?.html
				// HTML comes from the plugin's own templates, which escape every value.
				? <div dangerouslySetInnerHTML={{ __html: result.html }} />
				: !loading && <p className='alrpPreviewEmpty'>{result?.error ? __('The preview could not be loaded.', 'softicon-related-posts') : __('No related posts found with these settings yet.', 'softicon-related-posts')}</p>}
		</div>

		<p className='alrpPreviewNote'>{__('Your theme’s fonts and colors also apply on the site.', 'softicon-related-posts')}</p>
	</aside>;
};

export default Preview;
