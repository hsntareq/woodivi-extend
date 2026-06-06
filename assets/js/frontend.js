 (function ($) {
	'use strict';

	var config = window.wooDiviExtended && window.wooDiviExtended.filterablePortfolio
		? window.wooDiviExtended.filterablePortfolio
		: {};

	function getCategorySlug($filter) {
		return $filter.data('category-slug') || $filter.attr('data-category-slug') || $filter.data('filter') || $filter.attr('data-filter') || '';
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
			'category-' + slug
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
function ($) {
	'use strict';
	var config = window.wooDiviExtended && window.wooDiviExtended.filterablePortfolio ? window.wooDiviExtended.filterablePortfolio : {};
	function getCategorySlug($filter) {
		return $filter.data('category-slug') || $filter.attr('data-category-slug') || '';
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
}(jQuery);
!function(o){"use strict";var i=window.wooDiviExtended&&window.wooDiviExtended.filterablePortfolio?window.wooDiviExtended.filterablePortfolio:{};function t(o){return o.data("category-slug")||o.attr("data-category-slug")||""}function e(o,i){return o.hasClass(function(o){return"project_category-"+o}(i))}function r(i){var r=t(i.find(".et_pb_portfolio_filters a.active").first()),n=i.find(".woodivi-portfolio-subfilters input:checked").map(function(){return this.value}).get();i.find(".et_pb_portfolio_item").removeClass("woodivi-subcategory-hidden"),r&&"all"!==r&&n.length&&(i.find(".et_pb_portfolio_item").each(function(){var i=o(this),t=n.some(function(o){return e(i,o)});e(i,r)&&!t&&i.addClass("woodivi-subcategory-hidden")}),o(window).trigger("resize"))}function n(e){var n=t(e.find(".et_pb_portfolio_filters a.active").first());!function(t,e){var r=e&&e.children?e.children:[],n=t.children(".woodivi-portfolio-subfilters");if(n.length||(n=o('<div class="woodivi-portfolio-subfilters" />'),t.find(".et_pb_portfolio_filters").first().after(n)),n.empty(),t.removeClass("woodivi-has-subfilters"),r.length){var l="woodivi-subfilters-"+e.slug,a=i.showLabel&&i.checkboxLabel?i.checkboxLabel:"",s=o("<fieldset />",{class:"woodivi-portfolio-subfilters__fieldset"});a&&(s.attr("aria-label",a),s.append(o("<legend />",{class:"woodivi-portfolio-subfilters__legend",text:a}))),r.forEach(function(t){var r=l+"-"+t.slug,n=t.name;i.showCounts&&void 0!==t.count&&(n+=" ("+t.count+")");var a=o("<input />",{type:"checkbox",id:r,value:t.slug,"data-parent":e.slug}),f=o("<label />",{class:"woodivi-portfolio-subfilters__option",for:r});f.append(a).append(o("<span />",{text:n})),s.append(f)}),n.append(s),t.addClass("woodivi-has-subfilters")}}(e,n&&"all"!==n?function(o){for(var t=i.categories||[],e=0;e<t.length;e++)if(t[e].slug===o)return t[e];return null}(n):null),r(e)}function l(){i.enabled&&Array.isArray(i.categories)&&i.categories.length&&o(".et_pb_filterable_portfolio.woodivi-filterable-portfolio-subcategories").each(function(){var i=o(this);i.data("woodiviSubfiltersReady")?n(i):(i.data("woodiviSubfiltersReady",!0),n(i),i.on("click",".et_pb_portfolio_filters a",function(){window.setTimeout(function(){n(i)},80)}),i.on("change",".woodivi-portfolio-subfilters input",function(){r(i)}))})}o(document).ready(function(){l(),"MutationObserver"in window&&new MutationObserver(function(){l()}).observe(document.body,{childList:!0,subtree:!0})})}(jQuery);
