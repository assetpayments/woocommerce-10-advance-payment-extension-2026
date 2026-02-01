/* global wcAssetPaymentsData */
(function () {
	const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
	const { createElement } = window.wp.element;
	const { __ } = window.wp.i18n;

	const data = window.wcAssetPaymentsData || {};
	const label = data.title || 'AssetPayments';
	const description = data.description || '';

	const Content = () => {
		if (!description) return null;
		return createElement('div', null, description);
	};

	registerPaymentMethod({
		name: 'assetpayments',
		label: createElement('span', null, label),
		content: createElement(Content, null),
		edit: createElement(Content, null),
		canMakePayment: () => true,
		ariaLabel: label || __('AssetPayments', 'wc-assetpayments'),
		supports: {
			features: (data.supports && data.supports.features) ? data.supports.features : ['products'],
		},
	});
})();
