/* global wcAssetPaymentsV3Data */
(function () {
	const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
	const { createElement } = window.wp.element;
	const { __ } = window.wp.i18n;

	const data = window.wcAssetPaymentsV3Data || {};
	const label = data.title || 'AssetPayments V3';
	const description = data.description || '';

	const Content = () => {
		if (!description) return null;
		return createElement('div', null, description);
	};

	registerPaymentMethod({
		name: 'assetpayments-v3',
		label: createElement('span', null, label),
		content: createElement(Content, null),
		edit: createElement(Content, null),
		canMakePayment: () => true,
		ariaLabel: label || __('AssetPayments V3', 'wc-assetpayments-v3'),
		supports: {
			features: (data.supports && data.supports.features) ? data.supports.features : ['products'],
		},
	});
})();
