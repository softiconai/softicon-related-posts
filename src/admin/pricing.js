import { Checkout } from '@freemius/checkout';

// Buy now opens the Freemius checkout in a modal over the Pricing page.
// Without JavaScript, or if the checkout can't start, the link still opens it in a new tab.
const data = window.alrpCheckout || {};
let checkout = null;

const thanks = () => {
	const box = document.getElementById('alrp-thanks');
	if (box) {
		box.hidden = false;
		box.scrollIntoView({ behavior: 'smooth', block: 'center' });
	}
};

document.addEventListener('click', event => {
	const button = event.target.closest('a[data-alrp-licenses]');
	if (!button || !data.productId) {
		return;
	}
	event.preventDefault();

	const licenses = button.dataset.alrpLicenses;
	try {
		checkout = checkout || new Checkout({ product_id: data.productId, public_key: data.publicKey });
		checkout.open({
			// 'unlimited' is what Freemius expects for the unlimited-sites price
			licenses: 'unlimited' === licenses ? 'unlimited' : parseInt(licenses, 10) || 1,
			billing_cycle: 'lifetime',
			// Freemius joins product and plan names ("… Pro Pro") unless the title is set
			title: data.title,
			purchaseCompleted: thanks,
			success: thanks
		});
	} catch (e) {
		window.open(button.href, '_blank', 'noopener');
	}
});
