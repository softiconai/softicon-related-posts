import { __ } from '@wordpress/i18n';
import { applyFilters } from '@wordpress/hooks';
import ProLocked from './ProLocked';

export const isLicensed = data => !!data?.pro?.license?.active;

// Locked previews of Pro features, each shown next to the free settings it extends.
const locked = data => ({
	'general.placement': { icon: 'editor-insertmore', title: __('"Read also" and "Up next"', 'softicon-related-posts'), description: __('Related links inside the article, and a card that slides in as readers finish.', 'softicon-related-posts'), items: [__('Show "Read also" boxes between paragraphs', 'softicon-related-posts'), __('Show the "Up next" slide-in', 'softicon-related-posts'), __('Only show in chosen categories', 'softicon-related-posts'), __('Hide the list on phones', 'softicon-related-posts')] },
	'general.matching': { icon: 'lightbulb', title: __('Smart matching', 'softicon-related-posts'), description: __('Find related posts by topic, not only by tags.', 'softicon-related-posts'), items: [__('Smart order: shared keywords, tags, categories and freshness', 'softicon-related-posts'), __('Choose how much each one counts', 'softicon-related-posts'), __('Boost cornerstone content from Yoast SEO and Rank Math', 'softicon-related-posts'), __('Match by custom fields and ACF (same city, similar price, upcoming events)', 'softicon-related-posts')] },
	'general.woo': data.woo ? { icon: 'cart', title: __('WooCommerce', 'softicon-related-posts'), description: __('Smarter related products and links between products and articles.', 'softicon-related-posts'), items: [__('Smarter related products by brand, category, attributes and price', 'softicon-related-posts'), __('Price, rating and Add to cart on product cards', 'softicon-related-posts'), __('"Shop this post" under blog posts', 'softicon-related-posts'), __('Related articles on product pages', 'softicon-related-posts')] } : null,
	'general.developers': { icon: 'rest-api', title: __('Headless and apps', 'softicon-related-posts'), description: __('Related posts as JSON, with your settings applied.', 'softicon-related-posts'), items: [__('REST endpoint: /wp-json/alrp/v1/related/<id>', 'softicon-related-posts'), __('Add related posts to the WordPress posts API', 'softicon-related-posts')] },
	'display.fields': { icon: 'database', title: __('More on each card', 'softicon-related-posts'), description: __('Show extra details under the title.', 'softicon-related-posts'), items: [__('Reading time ("4 min read")', 'softicon-related-posts'), __('Custom and ACF fields, like city or price', 'softicon-related-posts')] },
	'design.presets': { icon: 'admin-appearance', title: __('Design presets', 'softicon-related-posts'), description: __('Start from a finished look in one click.', 'softicon-related-posts'), items: [__('Clean cards, Magazine, Photo overlay, Carousel and Top list', 'softicon-related-posts')] },
	'design.css': { icon: 'editor-code', title: __('Custom CSS', 'softicon-related-posts'), description: __('Fine-tune the design with your own CSS.', 'softicon-related-posts'), items: [__('Applies to the list, "Read also" and "Up next"', 'softicon-related-posts')] }
});

// Pro fills a slot with its real panel when licensed; otherwise the locked preview stays.
export const Slot = ({ name, ...props }) => {
	const def = locked(props.data)[name];
	const fallback = def && !isLicensed(props.data) ? <ProLocked {...def} url={props.data.pricingUrl} /> : null;
	return applyFilters('alrp.settings.slot', fallback, name, props);
};
