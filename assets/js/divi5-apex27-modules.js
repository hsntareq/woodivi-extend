/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "@divi/module"
/*!******************************!*\
  !*** external "divi.module" ***!
  \******************************/
(module) {

module.exports = divi.module;

/***/ },

/***/ "@divi/module-library"
/*!*************************************!*\
  !*** external "divi.moduleLibrary" ***!
  \*************************************/
(module) {

module.exports = divi.moduleLibrary;

/***/ },

/***/ "react"
/*!************************!*\
  !*** external "React" ***!
  \************************/
(module) {

module.exports = window["React"];

/***/ },

/***/ "./modules/apex27-listings/module.json"
/*!*********************************************!*\
  !*** ./modules/apex27-listings/module.json ***!
  \*********************************************/
(module) {

module.exports = /*#__PURE__*/JSON.parse('{"name":"woodivi/apex27-listings","version":"1.0.0","apiVersion":2,"title":"Apex27 Listings","titles":"Apex27 Listings","moduleIcon":"divi/module-portfolio","moduleClassName":"woodivi-apex27-listings-module","moduleOrderClassName":"woodivi_apex27_listings","category":"module","visualBuilderScript":"woodivi-extend-divi5-apex27","attributes":{"module":{"type":"object","selector":"{{selector}}","settings":{"meta":{"meta":{}},"advanced":{"html":{},"link":{},"text":{},"loop":{}},"decoration":{"animation":{},"attributes":{},"background":{},"border":{},"boxShadow":{},"conditions":{},"disabledOn":{},"filters":{},"interactions":{},"overflow":{},"order":{},"position":{},"scroll":{},"sizing":{},"spacing":{},"sticky":{},"transform":{},"transition":{},"zIndex":{}}}},"apex27":{"type":"object","selector":"{{selector}}","default":{"advanced":{"itemType":{"desktop":{"value":"listings"}},"customEndpoint":{"desktop":{"value":""}},"staticQuery":{"desktop":{"value":"includeImages=1"}},"limit":{"desktop":{"value":"9"}},"columns":{"desktop":{"value":"3"}},"emptyText":{"desktop":{"value":"No Apex27 properties found."}}}},"settings":{"advanced":{"itemType":{"groupType":"group-item","item":{"groupSlug":"contentMain","attrName":"apex27.advanced.itemType","priority":10,"render":true,"label":"Items to Display","description":"Choose whether this module displays listings, sales, or both.","category":"configuration","features":{"sticky":false,"preset":"content"},"defaultAttr":{"desktop":{"value":"listings"}},"component":{"name":"divi/select","type":"field","props":{"options":{"listings":{"label":"Listings"},"sales":{"label":"Sales"},"both":{"label":"Listings and Sales"},"custom":{"label":"Custom Endpoint"}}}}}},"customEndpoint":{"groupType":"group-item","item":{"groupSlug":"contentMain","attrName":"apex27.advanced.customEndpoint","priority":15,"render":true,"label":"Custom Endpoint","description":"Only used when Items to Display is set to Custom Endpoint. Example: /api/listings.","category":"configuration","features":{"sticky":false,"preset":"content"},"defaultAttr":{"desktop":{"value":"includeImages=1"}},"component":{"name":"divi/text","type":"field"}}},"staticQuery":{"groupType":"group-item","item":{"groupSlug":"contentMain","attrName":"apex27.advanced.staticQuery","priority":18,"render":true,"label":"Static Query Params","description":"Optional query string without ? e.g. branch=ragdon&department=sales","category":"configuration","features":{"sticky":false,"preset":"content"},"defaultAttr":{"desktop":{"value":""}},"component":{"name":"divi/text","type":"field"}}},"limit":{"groupType":"group-item","item":{"groupSlug":"contentMain","attrName":"apex27.advanced.limit","priority":20,"render":true,"label":"Item Limit","description":"Set the maximum number of items to display.","category":"configuration","features":{"sticky":false,"preset":"content"},"defaultAttr":{"desktop":{"value":"9"}},"component":{"name":"divi/text","type":"field"}}},"columns":{"groupType":"group-item","item":{"groupSlug":"contentMain","attrName":"apex27.advanced.columns","priority":30,"render":true,"label":"Columns","description":"Choose the desktop grid columns.","category":"layout","features":{"sticky":false,"preset":"content"},"defaultAttr":{"desktop":{"value":"3"}},"component":{"name":"divi/select","type":"field","props":{"options":{"2":{"label":"2 Columns"},"3":{"label":"3 Columns"},"4":{"label":"4 Columns"}}}}}},"emptyText":{"groupType":"group-item","item":{"groupSlug":"contentMain","attrName":"apex27.advanced.emptyText","priority":40,"render":true,"label":"Empty State Text","description":"Text shown when no Apex27 items match the filters.","category":"basic_option","features":{"sticky":false,"preset":"content"},"defaultAttr":{"desktop":{"value":"No Apex27 properties found."}},"component":{"name":"divi/text","type":"field"}}}}}}},"customCssFields":{},"settings":{"content":"auto","design":"auto","advanced":"auto","groups":{"contentMain":{"panel":"content","priority":10,"groupName":"mainContent","component":{"name":"divi/composite","props":{"groupLabel":"Apex27"}}}}}}');

/***/ },

/***/ "./modules/apex27-search-form/module.json"
/*!************************************************!*\
  !*** ./modules/apex27-search-form/module.json ***!
  \************************************************/
(module) {

module.exports = /*#__PURE__*/JSON.parse('{"name":"woodivi/apex27-search-form","version":"1.0.0","apiVersion":2,"title":"Apex27 Search Form","titles":"Apex27 Search Forms","moduleIcon":"divi/module-search","moduleClassName":"woodivi-apex27-search-module","moduleOrderClassName":"woodivi_apex27_search_form","category":"module","visualBuilderScript":"woodivi-extend-divi5-apex27","attributes":{"module":{"type":"object","selector":"{{selector}}","settings":{"meta":{"meta":{}},"advanced":{"html":{},"link":{},"text":{},"loop":{}},"decoration":{"animation":{},"attributes":{},"background":{},"border":{},"boxShadow":{},"conditions":{},"disabledOn":{},"filters":{},"interactions":{},"overflow":{},"order":{},"position":{},"scroll":{},"sizing":{},"spacing":{},"sticky":{},"transform":{},"transition":{},"zIndex":{}}}},"apex27":{"type":"object","selector":"{{selector}}","default":{"advanced":{"buttonText":{"desktop":{"value":"Search"}},"showStatus":{"desktop":{"value":"on"}},"showPrices":{"desktop":{"value":"on"}},"showBedrooms":{"desktop":{"value":"on"}}}},"settings":{"advanced":{"buttonText":{"groupType":"group-item","item":{"groupSlug":"contentFields","attrName":"apex27.advanced.buttonText","priority":10,"render":true,"label":"Button Text","description":"Set the text for the search button.","category":"basic_option","features":{"sticky":false,"preset":"content"},"defaultAttr":{"desktop":{"value":"Search"}},"component":{"name":"divi/text","type":"field"}}},"showStatus":{"groupType":"group-item","item":{"groupSlug":"contentFields","attrName":"apex27.advanced.showStatus","priority":20,"render":true,"label":"Show Status Field","description":"Turn this on to display the status dropdown.","category":"configuration","features":{"sticky":false,"preset":"content"},"defaultAttr":{"desktop":{"value":"on"}},"component":{"name":"divi/toggle","type":"field"}}},"showPrices":{"groupType":"group-item","item":{"groupSlug":"contentFields","attrName":"apex27.advanced.showPrices","priority":30,"render":true,"label":"Show Price Fields","description":"Turn this on to display min and max price fields.","category":"configuration","features":{"sticky":false,"preset":"content"},"defaultAttr":{"desktop":{"value":"on"}},"component":{"name":"divi/toggle","type":"field"}}},"showBedrooms":{"groupType":"group-item","item":{"groupSlug":"contentFields","attrName":"apex27.advanced.showBedrooms","priority":40,"render":true,"label":"Show Bedrooms Field","description":"Turn this on to display the bedrooms field.","category":"configuration","features":{"sticky":false,"preset":"content"},"defaultAttr":{"desktop":{"value":"on"}},"component":{"name":"divi/toggle","type":"field"}}}}}}},"customCssFields":{},"settings":{"content":"auto","design":"auto","advanced":"auto","groups":{"contentFields":{"panel":"content","priority":10,"groupName":"mainContent","component":{"name":"divi/composite","props":{"groupLabel":"Search Fields"}}}}}}');

/***/ }

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		if (!(moduleId in __webpack_modules__)) {
/******/ 			delete __webpack_module_cache__[moduleId];
/******/ 			var e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
/******/ 		}
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!************************************************!*\
  !*** ./assets/src/js/divi5-apex27-modules.jsx ***!
  \************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _divi_module_library__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @divi/module-library */ "@divi/module-library");
/* harmony import */ var _divi_module_library__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_divi_module_library__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _divi_module__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @divi/module */ "@divi/module");
/* harmony import */ var _divi_module__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_divi_module__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _modules_apex27_listings_module_json__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../modules/apex27-listings/module.json */ "./modules/apex27-listings/module.json");
/* harmony import */ var _modules_apex27_search_form_module_json__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../modules/apex27-search-form/module.json */ "./modules/apex27-search-form/module.json");




// Import metadata from module.json files


const hooks = window.vendor?.wp?.hooks;
if (hooks && hooks.addAction) {
  hooks.addAction('divi.moduleLibrary.registerModuleLibraryStore.after', 'woodivi-extend/apex27-modules', () => {
    // Register Listings Module
    (0,_divi_module_library__WEBPACK_IMPORTED_MODULE_1__.registerModule)(_modules_apex27_listings_module_json__WEBPACK_IMPORTED_MODULE_3__, {
      renderers: {
        edit: props => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(_divi_module__WEBPACK_IMPORTED_MODULE_2__.ServerRenderedModule, props)
      }
    });

    // Register Search Form Module
    (0,_divi_module_library__WEBPACK_IMPORTED_MODULE_1__.registerModule)(_modules_apex27_search_form_module_json__WEBPACK_IMPORTED_MODULE_4__, {
      renderers: {
        edit: props => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default().createElement(_divi_module__WEBPACK_IMPORTED_MODULE_2__.ServerRenderedModule, props)
      }
    });
    console.log('WooDivi Extend: Apex27 Divi 5 modules successfully registered via registerModule API with metadata.');
  });
}
})();

/******/ })()
;
//# sourceMappingURL=divi5-apex27-modules.js.map