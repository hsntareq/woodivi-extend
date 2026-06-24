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

	function addToggleTriggerSelect(attributes) {
		var advancedSettings = attributes &&
			attributes.module &&
			attributes.module.settings &&
			attributes.module.settings.advanced;

		if (!advancedSettings) {
			return attributes;
		}

		advancedSettings.toggleTrigger = {
			groupType: 'group-item',
			item: {
				groupSlug: 'contentElements',
				attrName: 'module.advanced.toggleTrigger',
				label: __('Toggle Trigger', 'woodivi-extend'),
				description: __('Choose whether to toggle accordion items by click or mouseover.', 'woodivi-extend'),
				category: 'configuration',
				priority: 10,
				render: true,
				features: {
					sticky: false,
					responsive: false,
					hover: false,
					preset: ['html']
				},
				defaultAttr: {
					desktop: {
						value: 'click'
					}
				},
				component: {
					type: 'field',
					name: 'divi/select',
					props: {
						options: {
							click: {
								label: __('Click', 'woodivi-extend')
							},
							mouseover: {
								label: __('Mouseover', 'woodivi-extend')
							}
						}
					}
				}
			}
		};

		return attributes;
	}

	hooks.addFilter(
		'divi.moduleLibrary.moduleAttributes.divi.accordion',
		'woodivi-extend/accordion-toggle-trigger',
		addToggleTriggerSelect,
		20
	);
})();
