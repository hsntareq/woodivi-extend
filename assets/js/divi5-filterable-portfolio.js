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

	function addHideChildCategoriesToggle(attributes) {
		var contentSettings = attributes &&
			attributes.portfolio &&
			attributes.portfolio.settings &&
			attributes.portfolio.settings.content;

		if (!contentSettings) {
			return attributes;
		}

		contentSettings.hideChildCategories = {
			groupType: 'group-item',
			item: {
				groupSlug: 'content',
				attrName: 'portfolio.content.hideChildCategories',
				label: __('Hide Child Categories', 'woodivi-extend'),
				description: __('Hide child categories from the Included Categories list in the module.', 'woodivi-extend'),
				category: 'basic_option',
				priority: 55,
				render: true,
				features: {
					sticky: false,
					responsive: false,
					hover: false,
					preset: []
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

		// If the module metadata already contains includeCategories options, filter client-side
		try {
			var includeCat = contentSettings.includedCategories || contentSettings.categories || null;
			if (includeCat && includeCat.item && includeCat.item.component && includeCat.item.component.props && Array.isArray(includeCat.item.component.props.options)) {
				includeCat.item.component.props.options = includeCat.item.component.props.options.filter(function (opt) {
					// Keep top level terms (parent === 0) or items without parent info
					if (typeof opt.parent !== 'undefined') {
						return parseInt(opt.parent, 10) === 0;
					}
					return true;
				});
			}
		} catch (e) {
			// swallow errors — non-critical
		}

		return attributes;
	}

	hooks.addFilter(
		'divi.moduleLibrary.moduleAttributes.divi.filterable-portfolio',
		'woodivi-extend/filterable-portfolio-subcategory-toggle',
		addSubcategoryToggle,
		20
	);

	hooks.addFilter(
		'divi.moduleLibrary.moduleAttributes.divi.filterable-portfolio',
		'woodivi-extend/filterable-portfolio-hide-child-toggle',
		addHideChildCategoriesToggle,
		21
	);
})();
