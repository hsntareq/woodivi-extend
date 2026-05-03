(function () {
	'use strict';

	var hooks = window.vendor && window.vendor.wp && window.vendor.wp.hooks;
	var i18n = window.vendor && window.vendor.wp && window.vendor.wp.i18n;
	var __ = i18n && i18n.__ ? i18n.__ : function (text) {
		return text;
	};

	if (!hooks || !hooks.addFilter) {
		return;
	}

	function addSubcategoryToggle(attributes) {
		var advancedSettings = attributes &&
			attributes.portfolio &&
			attributes.portfolio.settings &&
			attributes.portfolio.settings.advanced;

		if (!advancedSettings) {
			return attributes;
		}

		advancedSettings.showSubcategories = {
			groupType: 'group-item',
			item: {
				groupSlug: 'contentElements',
				attrName: 'portfolio.advanced.showSubcategories',
				label: __('Show Sub Categories', 'woodivi-extend'),
				description: __('Show inline child category checkboxes under parent category tabs.', 'woodivi-extend'),
				category: 'configuration',
				priority: 12,
				render: true,
				features: {
					sticky: false,
					responsive: false,
					hover: false,
					preset: ['html']
				},
				defaultAttr: {
					desktop: {
						value: 'off'
					}
				},
				component: {
					type: 'field',
					name: 'divi/toggle'
				}
			}
		};

		return attributes;
	}

	hooks.addFilter(
		'divi.moduleLibrary.moduleAttributes.divi.filterable-portfolio',
		'woodivi-extend/filterable-portfolio-subcategory-toggle',
		addSubcategoryToggle,
		20
	);
})();
