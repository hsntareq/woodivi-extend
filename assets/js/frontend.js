(function ($) {
	'use strict';

	// Defensive: avoid uncaught NotSupportedError from calling play() on media elements with no sources.
	// Some WP/core scripts call `.play()` without checking for sources and the returned promise can
	// reject with NotSupportedError. Monkey-patch play to short-circuit when no source is present
	// and to swallow harmless rejections to avoid breaking the live UI.
	(function () {
		if (typeof HTMLMediaElement === 'undefined') {
			return;
		}

		var origPlay = HTMLMediaElement.prototype.play;

		HTMLMediaElement.prototype.play = function () {
			try {
				var hasSrc = !!(this.currentSrc || (this.querySelector && this.querySelector('source[src]')));
				if (!hasSrc) {
					return Promise.resolve();
				}
				var p = origPlay.call(this);
				if (p && typeof p.catch === 'function') {
					p.catch(function (err) {
						// swallow NotSupportedError or other harmless rejections to avoid uncaught promise errors
						// still log others in debug mode
						if (window.console && window.DEBUG) {
							console.warn('media play rejected', err);
						}
					});
				}
				return p;
			} catch (e) {
				return Promise.resolve();
			}
		};
	})();

	var config = window.wooDiviExtended && window.wooDiviExtended.filterablePortfolio
		? window.wooDiviExtended.filterablePortfolio
		: {};

	function getCategorySlug($filter) {
		var raw = $filter.data('category-slug') || $filter.attr('data-category-slug') || $filter.data('filter') || $filter.attr('data-filter') || $filter.attr('href') || '';
		raw = String(raw || '');

		if (!raw) {
			return '';
		}

		var patterns = [
			/project[_-]category[-_]([A-Za-z0-9-_]+)/i,
			/portfolio[_-]category[-_]([A-Za-z0-9-_]+)/i,
			/category[-_]([A-Za-z0-9-_]+)/i,
			/(?:#filter-)?\.?(?:filter-)?([A-Za-z0-9-_]+)/i
		];

		for (var i = 0; i < patterns.length; i++) {
			var m = raw.match(patterns[i]);
			if (m && m[1]) {
				return m[1];
			}
		}

		// Fallback: strip leading punctuation and return remainder if it looks like a slug
		var fallback = raw.replace(/^\W+/, '');
		if (/^[A-Za-z0-9-_]+$/.test(fallback)) {
			return fallback;
		}

		return '';
	}

	function getParentBySlug(slug) {
		var categories = config.categories || [];

		for (var i = 0; i < categories.length; i++) {
			if (categories[i].slug === slug) {
				return categories[i];
			}
		}

		return null;
	}

	function categoryClass(slug) {
		return 'project_category-' + slug;
	}

	function itemHasCategory($item, slug) {
		if (!slug) {
			return false;
		}

		var candidates = [
			'project_category-' + slug,
			'project-category-' + slug,
			'portfolio_category-' + slug,
			'portfolio-category-' + slug,
			'category-' + slug,
			slug
		];

		for (var i = 0; i < candidates.length; i++) {
			if ($item.hasClass(candidates[i])) {
				return true;
			}
		}

		// Fallback: check data attributes that may contain term slugs
		var dataCats = $item.data('categories') || $item.data('category') || $item.data('term') || $item.attr('data-categories') || '';
		if (dataCats) {
			try {
				if (Array.isArray(dataCats)) {
					if (dataCats.indexOf(slug) !== -1) {
						return true;
					}
				} else {
					var parts = String(dataCats).split(/\s+|,|\|/);
					if (parts.indexOf(slug) !== -1) {
						return true;
					}
				}
			} catch (e) {
				// ignore
			}
		}

		return false;
	}

	function renderSubCategoryFilters($portfolio, parentCategory) {
		var children = parentCategory && parentCategory.children ? parentCategory.children : [];
		var $holder = $portfolio.children('.woodivi-portfolio-subfilters');

		if (!$holder.length) {
			$holder = $('<div class="woodivi-portfolio-subfilters" />');
			$portfolio.find('.et_pb_portfolio_filters').first().after($holder);
		}

		// Apply shape modifier from config: 'circle' (default) or 'square'
		var shape = (config.shape || 'circle').toString().toLowerCase();
		// normalize value
		if (shape !== 'square') {
			shape = 'circle';
		}
		$holder.removeClass('woodivi-portfolio-subfilters--circle woodivi-portfolio-subfilters--square');
		$holder.addClass('woodivi-portfolio-subfilters--' + shape);

		$holder.empty();
		$portfolio.removeClass('woodivi-has-subfilters');

		if (!children.length) {
			return;
		}

		var groupId = 'woodivi-subfilters-' + parentCategory.slug;
		var groupLabel = config.showLabel && config.checkboxLabel ? config.checkboxLabel : '';
		var $fieldset = $('<fieldset />', {
			'class': 'woodivi-portfolio-subfilters__fieldset'
		});

		if (groupLabel) {
			$fieldset.attr('aria-label', groupLabel);
			$fieldset.append(
				$('<legend />', {
					'class': 'woodivi-portfolio-subfilters__legend',
					'text': groupLabel
				})
			);
		}

		children.forEach(function (child) {
			var inputId = groupId + '-' + child.slug;
			var labelText = child.name;

			if (config.showCounts && typeof child.count !== 'undefined') {
				labelText += ' (' + child.count + ')';
			}

			var $input = $('<input />', {
				'type': 'checkbox',
				'id': inputId,
				'value': child.slug,
				'data-parent': parentCategory.slug
			});

			var $label = $('<label />', {
				'class': 'woodivi-portfolio-subfilters__option',
				'for': inputId
			});

			$label.append($input).append($('<span />', { 'text': labelText }));
			$fieldset.append($label);
		});

		$holder.append($fieldset);
		$portfolio.addClass('woodivi-has-subfilters');
	}

	function applySubCategoryFilter($portfolio) {
		var activeParentSlug = getCategorySlug($portfolio.find('.et_pb_portfolio_filters a.active').first());
		var checkedSlugs = $portfolio.find('.woodivi-portfolio-subfilters input:checked').map(function () {
			return this.value;
		}).get();

		$portfolio.find('.et_pb_portfolio_item').removeClass('woodivi-subcategory-hidden');

		if (!activeParentSlug || activeParentSlug === 'all' || !checkedSlugs.length) {
			return;
		}

		$portfolio.find('.et_pb_portfolio_item').each(function () {
			var $item = $(this);
			var matchesChild = checkedSlugs.some(function (slug) {
				return itemHasCategory($item, slug);
			});

			if (itemHasCategory($item, activeParentSlug) && !matchesChild) {
				$item.addClass('woodivi-subcategory-hidden');
			}
		});

		$(window).trigger('resize');
	}

	function refreshPortfolio($portfolio) {
		var activeSlug = getCategorySlug($portfolio.find('.et_pb_portfolio_filters a.active').first());
		var parentCategory = activeSlug && activeSlug !== 'all' ? getParentBySlug(activeSlug) : null;

		renderSubCategoryFilters($portfolio, parentCategory);
		applySubCategoryFilter($portfolio);
	}

	function initFilterablePortfolioSubcategories() {
		if (!config.enabled || !Array.isArray(config.categories) || !config.categories.length) {
			return;
		}

		$('.et_pb_filterable_portfolio.woodivi-filterable-portfolio-subcategories').each(function () {
			var $portfolio = $(this);

			if ($portfolio.data('woodiviSubfiltersReady')) {
				return;
			}

			$portfolio.data('woodiviSubfiltersReady', true);
			refreshPortfolio($portfolio);

			$portfolio.on('click', '.et_pb_portfolio_filters a', function () {
				window.setTimeout(function () {
					refreshPortfolio($portfolio);
				}, 80);
			});

			$portfolio.on('change', '.woodivi-portfolio-subfilters input', function () {
				applySubCategoryFilter($portfolio);
			});
		});
	}

	function initAccordionMouseover() {
		$('body').on('mouseenter', '.et_pb_accordion.woodivi-accordion-mouseover .et_pb_accordion_item', function () {
			var $item = $(this);

			// Guard: do not trigger in Visual Builder
			if ($item.data('id') || $item.closest('.et_pb_accordion').data('id')) {
				return;
			}

			// If already open or currently toggling, do nothing
			if ($item.hasClass('et_pb_toggle_open') || $item.closest('.et_pb_accordion').hasClass('et_pb_accordion_toggling')) {
				return;
			}

			// Call the Divi core function to expand the accordion item
			if (typeof window.et_pb_accordion_item_expand === 'function') {
				window.et_pb_accordion_item_expand($item);
			} else {
				// Fallback if the core function is not available
				var $title = $item.find('.et_pb_toggle_title').first();
				if ($title.length) {
					$title.trigger('click');
				}
			}
		});
	}

	$(document).ready(function () {
		initFilterablePortfolioSubcategories();
		initAccordionMouseover();

		if ('MutationObserver' in window) {
			var observer = new MutationObserver(function () {
				initFilterablePortfolioSubcategories();
			});

			observer.observe(document.body, {
				childList: true,
				subtree: true
			});
		}
	});

})(jQuery);
