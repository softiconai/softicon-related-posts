import { __ } from '@wordpress/i18n';
import { Dashicon } from '@wordpress/components';
import Section from './Section';

// Shown only while the Pro add-on is not installed; every card opens the pricing page.
const features = () => [
	{ icon: 'editor-insertmore', title: __('"Read also" and "Up next"', 'softicon-related-posts'), text: __('Related links inside the article and a slide-in as readers finish.', 'softicon-related-posts') },
	{ icon: 'images-alt2', title: __('Carousel and more layouts', 'softicon-related-posts'), text: __('Carousel, photo overlay, magazine and numbered lists, plus presets.', 'softicon-related-posts') },
	{ icon: 'chart-bar', title: __('Click analytics', 'softicon-related-posts'), text: __('Views, clicks and click rate without cookies, Popular and Trending.', 'softicon-related-posts') },
	{ icon: 'lightbulb', title: __('Smart matching', 'softicon-related-posts'), text: __('Keyword matching, adjustable weights and cornerstone posts first.', 'softicon-related-posts') },
	{ icon: 'cart', title: __('WooCommerce', 'softicon-related-posts'), text: __('Smarter related products, product cards and "Shop this post".', 'softicon-related-posts') },
	{ icon: 'database', title: __('Custom fields and REST', 'softicon-related-posts'), text: __('ACF rules, fields on cards and JSON for headless sites.', 'softicon-related-posts') }
];

const ProTeaser = ({ data }) => <Section icon='star-filled' title={__('Do more with Pro', 'softicon-related-posts')} description={__('An optional add-on. Everything you use today stays free.', 'softicon-related-posts')}>
	<div className='alrpTeaser'>
		{features().map(f => <a key={f.title} className='alrpTeaserCard' href={data.pricingUrl}>
			<span className='alrpTeaserIcon'><Dashicon icon={f.icon} /></span>
			<strong>{f.title}<span className='alrpProTag'>PRO</span></strong>
			<span>{f.text}</span>
		</a>)}
	</div>
	<a className='components-button is-primary alrpTeaserCta' href={data.proUrl}>{__('Compare Free and Pro', 'softicon-related-posts')}</a>
</Section>;

export default ProTeaser;
