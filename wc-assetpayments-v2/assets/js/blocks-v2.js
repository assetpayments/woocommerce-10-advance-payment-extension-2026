// wcAssetPaymentsV2Data/* global wcAssetPaymentsV2Data */
/* global wcAssetPaymentsV2Data */
(function () {
	const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
	const { createElement } = window.wp.element;
	const { __ } = window.wp.i18n;

	const data = window.wcAssetPaymentsV2Data || {};
	const label = data.title || 'AssetPayments';
	const description = data.description || '';

	const Content = () => {
		if (!description) return null;
		return createElement('div', null, description);
	};

	registerPaymentMethod({
		name: 'assetpayments-v2',
		label: createElement('span', null, label),
		content: createElement(Content, null),
		edit: createElement(Content, null),
		canMakePayment: () => true,
		ariaLabel: label || __('AssetPayments', 'wc-assetpayments-v2'),
		supports: {
			features: (data.supports && data.supports.features) ? data.supports.features : ['products'],
		},
	});
})();
