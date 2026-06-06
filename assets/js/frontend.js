(function ($) {
	'use strict';

	var config = window.wooDiviExtended && window.wooDiviExtended.filterablePortfolio
		? window.wooDiviExtended.filterablePortfolio
		: {};

	function getCategorySlug($filter) {
		return $filter.data('category-slug') || $filter.attr('data-category-slug') || '';
		var raw = $filter.data('category-slug') || $filter.attr('data-category-slug') || $filter.data('filter') || $filter.attr('data-filter') || $filter.attr('href') || '';
		raw = String(raw || '');

		if (!raw) {
			return '';
		}

		// Common patterns:
		//  - ".project_category-slug" (filter selector)
		//  - "project_category-slug" (class name without dot)
		//  - "#filter-project_category-slug" (anchor href)
		//  - ".category-slug" or "category-slug"
		var patterns = [
			/project[_-]category[-_]([A-Za-z0-9-_]+)$/,
			/portfolio[_-]category[-_]([A-Za-z0-9-_]+)$/,
			/category[-_]([A-Za-z0-9-_]+)$/,
			/(?:#filter-)?\.?(?:filter-)?([A-Za-z0-9-_]+)$/
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
		return $item.hasClass(categoryClass(slug));
	}

	function renderSubCategoryFilters($portfolio, parentCategory) {
		var children = parentCategory && parentCategory.children ? parentCategory.children : [];
		var $holder = $portfolio.children('.woodivi-portfolio-subfilters');

		if (!$holder.length) {
			$holder = $('<div class="woodivi-portfolio-subfilters" />');
			$portfolio.find('.et_pb_portfolio_filters').first().after($holder);
		}

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
				refreshPortfolio($portfolio);
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

	$(document).ready(function () {
		initFilterablePortfolioSubcategories();

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
