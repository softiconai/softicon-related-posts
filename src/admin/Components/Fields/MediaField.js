import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { BaseControl, Button } from '@wordpress/components';

// Fallback image picker using the Media Library modal that wp_enqueue_media() loads.
const MediaField = ({ label, help, imageId, initialUrl, onChange }) => {
	const [url, setUrl] = useState(initialUrl || '');

	const open = () => {
		const frame = window.wp.media({
			title: __('Select fallback image', 'softicon-related-posts'),
			button: { text: __('Use this image', 'softicon-related-posts') },
			library: { type: 'image' },
			multiple: false
		});
		frame.on('select', () => {
			const image = frame.state().get('selection').first().toJSON();
			setUrl(image.sizes?.thumbnail?.url || image.url);
			onChange({ id: image.id, url: '' });
		});
		frame.open();
	};

	const remove = () => {
		setUrl('');
		onChange({ id: 0, url: '' });
	};

	return <BaseControl label={label} help={help} __nextHasNoMarginBottom>
		<div className='alrpMedia'>
			{url && <img src={url} alt='' />}
			<div className='alrpMediaActions'>
				<Button variant='secondary' onClick={open}>{imageId || url ? __('Replace image', 'softicon-related-posts') : __('Select image', 'softicon-related-posts')}</Button>
				{(imageId || url) ? <Button variant='link' isDestructive onClick={remove}>{__('Remove', 'softicon-related-posts')}</Button> : null}
			</div>
		</div>
	</BaseControl>;
};

export default MediaField;
