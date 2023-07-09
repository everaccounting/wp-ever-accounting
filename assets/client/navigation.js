/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./client/packages/navigation/filters.js":
/*!***********************************************!*\
  !*** ./client/packages/navigation/filters.js ***!
  \***********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   flattenFilters: function() { return /* binding */ flattenFilters; },
/* harmony export */   getActiveFiltersFromQuery: function() { return /* binding */ getActiveFiltersFromQuery; },
/* harmony export */   getDefaultOptionValue: function() { return /* binding */ getDefaultOptionValue; },
/* harmony export */   getQueryFromActiveFilters: function() { return /* binding */ getQueryFromActiveFilters; },
/* harmony export */   getUrlKey: function() { return /* binding */ getUrlKey; }
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_2__);


function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0,_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * External dependencies
 */


/**
 * Collapse an array of filter values with subFilters into a 1-dimensional array.
 *
 * @param {Array} filters Set of filters with possible subfilters.
 * @return {Array} Flattened array of all filters.
 */
function flattenFilters(filters) {
  var allFilters = [];
  filters.forEach(function (f) {
    if (!f.subFilters) {
      allFilters.push(f);
    } else {
      allFilters.push((0,lodash__WEBPACK_IMPORTED_MODULE_2__.omit)(f, 'subFilters'));
      var subFilters = flattenFilters(f.subFilters);
      allFilters.push.apply(allFilters, (0,_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_1__["default"])(subFilters));
    }
  });
  return allFilters;
}

/**
 * Describe activeFilter object.
 *
 * @typedef {Object} activeFilter
 * @property {string} key - filter key.
 * @property {string} [rule] - a modifying rule for a filter, eg 'includes' or 'is_not'.
 * @property {string} value - filter value(s).
 */

/**
 * Given a query object, return an array of activeFilters, if any.
 *
 * @param {Object} query - query oject
 * @param {Object} filters - filters object
 * @return {Array} - array of activeFilters
 */
function getActiveFiltersFromQuery(query, filters) {
  return Object.keys(filters).reduce(function (activeFilters, configKey) {
    var filter = filters[configKey];
    if (filter.rules) {
      // Get all rules found in the query string.
      var matches = filter.rules.filter(function (rule) {
        return query.hasOwnProperty(getUrlKey(configKey, rule.value));
      });
      if (matches.length) {
        if (filter.allowMultiple) {
          // If rules were found in the query string, and this filter supports
          // multiple instances, add all matches to the active filters array.
          matches.forEach(function (match) {
            var value = query[getUrlKey(configKey, match.value)];
            value.forEach(function (filterValue) {
              activeFilters.push({
                key: configKey,
                rule: match.value,
                value: filterValue
              });
            });
          });
        } else {
          // If the filter is a single instance, just process the first rule match.
          var value = query[getUrlKey(configKey, matches[0].value)];
          activeFilters.push({
            key: configKey,
            rule: matches[0].value,
            value: value
          });
        }
      }
    } else if (query[configKey]) {
      // If the filter doesn't have rules, but allows multiples.
      if (filter.allowMultiple) {
        var _value = query[configKey];
        _value.forEach(function (filterValue) {
          activeFilters.push({
            key: configKey,
            value: filterValue
          });
        });
      } else {
        // Filter with no rules and only one instance.
        activeFilters.push({
          key: configKey,
          value: query[configKey]
        });
      }
    }
    return (0,lodash__WEBPACK_IMPORTED_MODULE_2__.uniqWith)(activeFilters, lodash__WEBPACK_IMPORTED_MODULE_2__.isEqual);
  }, []);
}

/**
 * Get the default option's value from the configuration object for a given filter. The first
 * option is used as default if no `defaultOption` is provided.
 *
 * @param {Object} filter - a filter config object.
 * @param {Array} options - select options.
 * @return {string|undefined}  - the value of the default option.
 */
function getDefaultOptionValue(filter, options) {
  var defaultOption = filter.input.defaultOption;
  if (filter.input.defaultOption) {
    var option = (0,lodash__WEBPACK_IMPORTED_MODULE_2__.find)(options, {
      value: defaultOption
    });
    if (!option) {
      /* eslint-disable no-console */
      console.warn("invalid defaultOption ".concat(defaultOption, " supplied to ").concat(filter.labels.add));
      /* eslint-enable */
      return undefined;
    }
    return option.value;
  }
  return (0,lodash__WEBPACK_IMPORTED_MODULE_2__.get)(options, [0, 'value']);
}

/**
 * Given activeFilters, create a new query object to update the url. Use previousFilters to
 * Remove unused params.
 *
 * @param {Array} activeFilters - Array of activeFilters shown in the UI
 * @param {Object} query - the current url query object
 * @param {Object} filters - config object
 * @return {Object} - query object representing the new parameters
 */
function getQueryFromActiveFilters() {
  var activeFilters = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var query = arguments.length > 1 ? arguments[1] : undefined;
  var filters = arguments.length > 2 ? arguments[2] : undefined;
  var previousFilters = getActiveFiltersFromQuery(query, filters);
  var previousData = previousFilters.reduce(function (data, filter) {
    data[getUrlKey(filter.key, filter.rule)] = undefined;
    return data;
  }, {});
  var nextData = activeFilters.reduce(function (data, filter) {
    if (filter.rule === 'between' && (!Array.isArray(filter.value) || filter.value.some(function (value) {
      return !value;
    }))) {
      return data;
    }
    if (filter.value) {
      var urlKey = getUrlKey(filter.key, filter.rule);
      if (filters[filter.key] && filters[filter.key].allowMultiple) {
        if (!data.hasOwnProperty(urlKey)) {
          data[urlKey] = [];
        }
        data[urlKey].push(filter.value);
      } else {
        data[urlKey] = filter.value;
      }
    }
    return data;
  }, {});
  return _objectSpread(_objectSpread({}, previousData), nextData);
}

/**
 * Get the url query key from the filter key and rule.
 *
 * @param {string} key - filter key.
 * @param {string} rule - filter rule.
 * @return {string} - url query key.
 */
function getUrlKey(key, rule) {
  if (rule && rule.length) {
    return "".concat(key, "_").concat(rule);
  }
  return key;
}

/***/ }),

/***/ "./client/packages/navigation/history.js":
/*!***********************************************!*\
  !*** ./client/packages/navigation/history.js ***!
  \***********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getHistory: function() { return /* binding */ getHistory; }
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0,_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * External dependencies
 */
var _require = __webpack_require__(/*! history */ "./node_modules/history/index.js"),
  createBrowserHistory = _require.createBrowserHistory;
var _require2 = __webpack_require__(/*! qs */ "./node_modules/qs/lib/index.js"),
  parse = _require2.parse;
var _history;
function getHistory() {
  if (!_history) {
    var browserHistory = createBrowserHistory();
    _history = {
      get action() {
        return browserHistory.action;
      },
      get location() {
        var location = browserHistory.location;
        var query = parse(location.search.substring(1));
        var pathname;
        if (query && typeof query.path === 'string') {
          pathname = query.path;
        } else if (query && query.path && typeof query.path !== 'string') {
          console.warn("Query path parameter should be a string but instead was: ".concat(query.path, ", undefined behaviour may occur."));
          pathname = query.path;
        } else {
          pathname = '/';
        }
        return _objectSpread(_objectSpread({}, location), {}, {
          pathname: pathname
        });
      },
      createHref: browserHistory.createHref,
      push: browserHistory.push,
      replace: browserHistory.replace,
      go: browserHistory.go,
      back: browserHistory.back,
      forward: browserHistory.forward,
      block: browserHistory.block,
      listen: function listen(listener) {
        var _this = this;
        return browserHistory.listen(function () {
          listener({
            action: _this.action,
            location: _this.location
          });
        });
      }
    };
  }
  return _history;
}


/***/ }),

/***/ "./client/packages/navigation/index.js":
/*!*********************************************!*\
  !*** ./client/packages/navigation/index.js ***!
  \*********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   addHistoryListener: function() { return /* binding */ addHistoryListener; },
/* harmony export */   flattenFilters: function() { return /* reexport safe */ _filters__WEBPACK_IMPORTED_MODULE_7__.flattenFilters; },
/* harmony export */   generatePath: function() { return /* binding */ generatePath; },
/* harmony export */   getActiveFiltersFromQuery: function() { return /* reexport safe */ _filters__WEBPACK_IMPORTED_MODULE_7__.getActiveFiltersFromQuery; },
/* harmony export */   getDefaultOptionValue: function() { return /* reexport safe */ _filters__WEBPACK_IMPORTED_MODULE_7__.getDefaultOptionValue; },
/* harmony export */   getHistory: function() { return /* reexport safe */ _history__WEBPACK_IMPORTED_MODULE_5__.getHistory; },
/* harmony export */   getIdFromQuery: function() { return /* binding */ getIdFromQuery; },
/* harmony export */   getIdsFromQuery: function() { return /* binding */ getIdsFromQuery; },
/* harmony export */   getPage: function() { return /* binding */ getPage; },
/* harmony export */   getPath: function() { return /* binding */ getPath; },
/* harmony export */   getQuery: function() { return /* binding */ getQuery; },
/* harmony export */   getQueryFromActiveFilters: function() { return /* reexport safe */ _filters__WEBPACK_IMPORTED_MODULE_7__.getQueryFromActiveFilters; },
/* harmony export */   getScreenFromPath: function() { return /* binding */ getScreenFromPath; },
/* harmony export */   getSearchWords: function() { return /* binding */ getSearchWords; },
/* harmony export */   getTableQuery: function() { return /* binding */ getTableQuery; },
/* harmony export */   getUrlKey: function() { return /* reexport safe */ _filters__WEBPACK_IMPORTED_MODULE_7__.getUrlKey; },
/* harmony export */   onQueryChange: function() { return /* binding */ onQueryChange; },
/* harmony export */   removeQueryArgs: function() { return /* binding */ removeQueryArgs; },
/* harmony export */   updateQueryString: function() { return /* binding */ updateQueryString; }
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "./node_modules/@babel/runtime/helpers/esm/typeof.js");
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/url */ "@wordpress/url");
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var qs__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! qs */ "./node_modules/qs/lib/index.js");
/* harmony import */ var qs__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(qs__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _history__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./history */ "./client/packages/navigation/history.js");
/* harmony import */ var _index__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./index */ "./client/packages/navigation/index.js");
/* harmony import */ var _filters__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./filters */ "./client/packages/navigation/filters.js");


function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0,_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


// For the above, import the module into itself. Functions consumed from this import can be mocked in tests.

// Expose history so all uses get the same history object.


/**
 * Get the current path from history.
 *
 * @return {string}  Current path.
 */
var getPath = function getPath() {
  return (0,_history__WEBPACK_IMPORTED_MODULE_5__.getHistory)().location.pathname;
};

/**
 * Get the page from history.
 *
 * @return {string} Query String
 */
var getPage = function getPage() {
  var search = (0,_history__WEBPACK_IMPORTED_MODULE_5__.getHistory)().location.search;
  if (search.length) {
    var query = (0,qs__WEBPACK_IMPORTED_MODULE_3__.parse)(search.substring(1)) || {};
    var page = query.page;
    return page;
  }
  return null;
};

/**
 * Retrieve a string 'name' representing the current screen
 *
 * @param {Object} path Path to resolve, default to current
 * @return {string} Screen name
 */
var getScreenFromPath = function getScreenFromPath() {
  var path = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : getPath();
  return path === '/' ? 'overview' : path.replace('/eaccounting', '').replace('/', '');
};

/**
 * Get an array of IDs from a comma-separated query parameter.
 *
 * @param {string} queryString string value extracted from URL.
 * @return {Array} List of IDs converted to numbers.
 */
function getIdsFromQuery() {
  var queryString = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  return (0,lodash__WEBPACK_IMPORTED_MODULE_4__.uniq)(queryString.split(',').map(function (id) {
    return parseInt(id, 10);
  }).filter(Boolean));
}

/**
 * Get an ID from a query parameter.
 *
 * @return {number} List of IDs converted to numbers.
 */
function getIdFromQuery() {
  var key = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'id';
  var query = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : getQuery();
  return parseInt(query[key] || 0, 10);
}

/**
 * Get an array of searched words given a query.
 *
 * @param {Object} query Query object.
 * @return {Array} List of search words.
 */
function getSearchWords() {
  var query = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : _index__WEBPACK_IMPORTED_MODULE_6__.getQuery();
  if ((0,_babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_1__["default"])(query) !== 'object') {
    throw new Error('Invalid parameter passed to getSearchWords, it expects an object or no parameters.');
  }
  var search = query.search;
  if (!search) {
    return [];
  }
  if (typeof search !== 'string') {
    throw new Error("Invalid 'search' type. getSearchWords expects query's 'search' property to be a string.");
  }
  return search.split(',').map(function (searchWord) {
    return searchWord.replace('%2C', ',');
  });
}

/**
 * Return a URL with set query parameters.
 *
 * @param {Object} query object of params to be updated.
 * @param {string} path Relative path (defaults to current path).
 * @param {Object} currentQuery object of current query params (defaults to current querystring).
 * @return {string}  Updated URL merging query params into existing params.
 */
function generatePath(query) {
  var path = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : getPath();
  var currentQuery = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : getQuery();
  var page = getPage();
  var args = {
    page: page
  };
  if (path !== '/') {
    args.path = path;
  }
  return (0,_wordpress_url__WEBPACK_IMPORTED_MODULE_2__.addQueryArgs)('admin.php', (0,lodash__WEBPACK_IMPORTED_MODULE_4__.pickBy)(_objectSpread(_objectSpread(_objectSpread({}, args), currentQuery), query), lodash__WEBPACK_IMPORTED_MODULE_4__.identity));
}

/**
 * Get the current query string, parsed into an object, from history.
 *
 * @return {Object}  Current query object, defaults to empty object.
 */
function getQuery() {
  var search = (0,_history__WEBPACK_IMPORTED_MODULE_5__.getHistory)().location.search;
  if (search.length) {
    return (0,lodash__WEBPACK_IMPORTED_MODULE_4__.omit)((0,qs__WEBPACK_IMPORTED_MODULE_3__.parse)(search.substring(1)) || {}, ['page', 'path']);
  }
  return {};
}

/**
 * Get table query.
 *
 * @param {Array|Object} whitelists Extra params.
 * @param {Object} defaults Extra params.
 * @param {Function} filter Extra params.
 * @param {Object} query Extra params.
 * @return {{}} query.
 */
function getTableQuery() {
  var whitelists = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var defaults = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var filter = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : function (x) {
    return x;
  };
  var query = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : getQuery();
  if ((0,lodash__WEBPACK_IMPORTED_MODULE_4__.isArray)(whitelists)) {
    whitelists = whitelists.reduce(function (acc, whitelist) {
      // eslint-disable-next-line no-unused-vars
      return _objectSpread(_objectSpread({}, acc), {}, (0,_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])({}, whitelist, function (x, query) {
        return x;
      }));
    }, {});
  }
  defaults = _objectSpread(_objectSpread({}, defaults), {}, {
    orderby: 'id',
    order: 'desc',
    per_page: 20,
    paged: 1
  });
  whitelists = _objectSpread(_objectSpread({}, whitelists), {}, {
    search: function search(_search, query) {
      return query.search || '';
    },
    paged: function paged(_paged, query) {
      return parseInt(query.paged, 10) || 1;
    },
    orderby: function orderby(_orderby, query) {
      return query.orderby || defaults.orderby;
    },
    order: function order(_order, query) {
      return query.order === 'asc' ? 'asc' : defaults.order;
    }
  });
  query = Object.keys(query).reduce(function (acc, queryKey) {
    if ((0,lodash__WEBPACK_IMPORTED_MODULE_4__.has)(whitelists, [queryKey])) {
      var queryValue = whitelists[queryKey](query[queryKey], query);
      if ((0,lodash__WEBPACK_IMPORTED_MODULE_4__.has)(defaults, [queryKey]) && (0,lodash__WEBPACK_IMPORTED_MODULE_4__.isEqual)(defaults[queryKey], queryValue)) {
        return acc;
      }
      acc = _objectSpread(_objectSpread({}, acc), {}, (0,_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])({}, queryKey, queryValue));
    }
    return acc;
  }, {});
  return filter((0,lodash__WEBPACK_IMPORTED_MODULE_4__.pickBy)(query, lodash__WEBPACK_IMPORTED_MODULE_4__.identity));
}

/**
 * This function returns an event handler for the given `param`
 *
 * @param {string} param The parameter in the querystring which should be updated (ex `page`, `per_page`)
 * @param {string} path Relative path (defaults to current path).
 * @param {string} query object of current query params (defaults to current querystring).
 * @return {Function} A callback which will update `param` to the passed value when called.
 */
function onQueryChange(param) {
  var path = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : getPath();
  var query = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : getQuery();
  switch (param) {
    case 'sort':
      return function (sort) {
        return updateQueryString(sort, path, query);
      };
    default:
      return function (value) {
        return updateQueryString((0,_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])({}, param, value), path, query);
      };
  }
}

/**
 * Updates the query parameters of the current page.
 *
 * @param {Object} query object of params to be updated.
 * @param {string} path Relative path (defaults to current path).
 * @param {Object} currentQuery object of current query params (defaults to current querystring).
 */
function updateQueryString(query) {
  var path = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : getPath();
  var currentQuery = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : getQuery();
  var newPath = generatePath(query, path, currentQuery);
  (0,_history__WEBPACK_IMPORTED_MODULE_5__.getHistory)().push(newPath);
}

/**
 * Remove query ags
 *
 * @param {string | Array}key
 * @param {Object} query
 */
function removeQueryArgs(key) {
  var query = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : getQuery();
  return (0,lodash__WEBPACK_IMPORTED_MODULE_4__.omit)(query, Array.isArray(key) ? key : [key]);
}

/**
 * Adds a listener that runs on history change.
 *
 * @param {Function} listener Listener to add on history change.
 * @return {Function} Function to remove listeners.
 */
var addHistoryListener = function addHistoryListener(listener) {
  // Monkey patch pushState to allow trigger the pushstate event listener.
  if (window.wcNavigation && !window.wcNavigation.historyPatched) {
    (function (history) {
      /* global CustomEvent */
      var pushState = history.pushState;
      var replaceState = history.replaceState;
      history.pushState = function (state) {
        var pushStateEvent = new CustomEvent('pushstate', {
          state: state
        });
        window.dispatchEvent(pushStateEvent);
        return pushState.apply(history, arguments);
      };
      history.replaceState = function (state) {
        var replaceStateEvent = new CustomEvent('replacestate', {
          state: state
        });
        window.dispatchEvent(replaceStateEvent);
        return replaceState.apply(history, arguments);
      };
      window.wcNavigation.historyPatched = true;
    })(window.history);
  }
  /*eslint-disable @wordpress/no-global-event-listener */
  window.addEventListener('popstate', listener);
  window.addEventListener('pushstate', listener);
  window.addEventListener('replacestate', listener);
  return function () {
    window.removeEventListener('popstate', listener);
    window.removeEventListener('pushstate', listener);
    window.removeEventListener('replacestate', listener);
  };

  /* eslint-enable @wordpress/no-global-event-listener */
};



/***/ }),

/***/ "./node_modules/history/index.js":
/*!***************************************!*\
  !*** ./node_modules/history/index.js ***!
  \***************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Action: function() { return /* binding */ Action; },
/* harmony export */   createBrowserHistory: function() { return /* binding */ createBrowserHistory; },
/* harmony export */   createHashHistory: function() { return /* binding */ createHashHistory; },
/* harmony export */   createMemoryHistory: function() { return /* binding */ createMemoryHistory; },
/* harmony export */   createPath: function() { return /* binding */ createPath; },
/* harmony export */   parsePath: function() { return /* binding */ parsePath; }
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "./node_modules/@babel/runtime/helpers/esm/extends.js");


/**
 * Actions represent the type of change to a location value.
 *
 * @see https://github.com/remix-run/history/tree/main/docs/api-reference.md#action
 */
var Action;

(function (Action) {
  /**
   * A POP indicates a change to an arbitrary index in the history stack, such
   * as a back or forward navigation. It does not describe the direction of the
   * navigation, only that the current index changed.
   *
   * Note: This is the default action for newly created history objects.
   */
  Action["Pop"] = "POP";
  /**
   * A PUSH indicates a new entry being added to the history stack, such as when
   * a link is clicked and a new page loads. When this happens, all subsequent
   * entries in the stack are lost.
   */

  Action["Push"] = "PUSH";
  /**
   * A REPLACE indicates the entry at the current index in the history stack
   * being replaced by a new one.
   */

  Action["Replace"] = "REPLACE";
})(Action || (Action = {}));

var readOnly =  true ? function (obj) {
  return Object.freeze(obj);
} : 0;

function warning(cond, message) {
  if (!cond) {
    // eslint-disable-next-line no-console
    if (typeof console !== 'undefined') console.warn(message);

    try {
      // Welcome to debugging history!
      //
      // This error is thrown as a convenience so you can more easily
      // find the source for a warning that appears in the console by
      // enabling "pause on exceptions" in your JavaScript debugger.
      throw new Error(message); // eslint-disable-next-line no-empty
    } catch (e) {}
  }
}

var BeforeUnloadEventType = 'beforeunload';
var HashChangeEventType = 'hashchange';
var PopStateEventType = 'popstate';
/**
 * Browser history stores the location in regular URLs. This is the standard for
 * most web apps, but it requires some configuration on the server to ensure you
 * serve the same app at multiple URLs.
 *
 * @see https://github.com/remix-run/history/tree/main/docs/api-reference.md#createbrowserhistory
 */

function createBrowserHistory(options) {
  if (options === void 0) {
    options = {};
  }

  var _options = options,
      _options$window = _options.window,
      window = _options$window === void 0 ? document.defaultView : _options$window;
  var globalHistory = window.history;

  function getIndexAndLocation() {
    var _window$location = window.location,
        pathname = _window$location.pathname,
        search = _window$location.search,
        hash = _window$location.hash;
    var state = globalHistory.state || {};
    return [state.idx, readOnly({
      pathname: pathname,
      search: search,
      hash: hash,
      state: state.usr || null,
      key: state.key || 'default'
    })];
  }

  var blockedPopTx = null;

  function handlePop() {
    if (blockedPopTx) {
      blockers.call(blockedPopTx);
      blockedPopTx = null;
    } else {
      var nextAction = Action.Pop;

      var _getIndexAndLocation = getIndexAndLocation(),
          nextIndex = _getIndexAndLocation[0],
          nextLocation = _getIndexAndLocation[1];

      if (blockers.length) {
        if (nextIndex != null) {
          var delta = index - nextIndex;

          if (delta) {
            // Revert the POP
            blockedPopTx = {
              action: nextAction,
              location: nextLocation,
              retry: function retry() {
                go(delta * -1);
              }
            };
            go(delta);
          }
        } else {
          // Trying to POP to a location with no index. We did not create
          // this location, so we can't effectively block the navigation.
           true ? warning(false, // TODO: Write up a doc that explains our blocking strategy in
          // detail and link to it here so people can understand better what
          // is going on and how to avoid it.
          "You are trying to block a POP navigation to a location that was not " + "created by the history library. The block will fail silently in " + "production, but in general you should do all navigation with the " + "history library (instead of using window.history.pushState directly) " + "to avoid this situation.") : 0;
        }
      } else {
        applyTx(nextAction);
      }
    }
  }

  window.addEventListener(PopStateEventType, handlePop);
  var action = Action.Pop;

  var _getIndexAndLocation2 = getIndexAndLocation(),
      index = _getIndexAndLocation2[0],
      location = _getIndexAndLocation2[1];

  var listeners = createEvents();
  var blockers = createEvents();

  if (index == null) {
    index = 0;
    globalHistory.replaceState((0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({}, globalHistory.state, {
      idx: index
    }), '');
  }

  function createHref(to) {
    return typeof to === 'string' ? to : createPath(to);
  } // state defaults to `null` because `window.history.state` does


  function getNextLocation(to, state) {
    if (state === void 0) {
      state = null;
    }

    return readOnly((0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
      pathname: location.pathname,
      hash: '',
      search: ''
    }, typeof to === 'string' ? parsePath(to) : to, {
      state: state,
      key: createKey()
    }));
  }

  function getHistoryStateAndUrl(nextLocation, index) {
    return [{
      usr: nextLocation.state,
      key: nextLocation.key,
      idx: index
    }, createHref(nextLocation)];
  }

  function allowTx(action, location, retry) {
    return !blockers.length || (blockers.call({
      action: action,
      location: location,
      retry: retry
    }), false);
  }

  function applyTx(nextAction) {
    action = nextAction;

    var _getIndexAndLocation3 = getIndexAndLocation();

    index = _getIndexAndLocation3[0];
    location = _getIndexAndLocation3[1];
    listeners.call({
      action: action,
      location: location
    });
  }

  function push(to, state) {
    var nextAction = Action.Push;
    var nextLocation = getNextLocation(to, state);

    function retry() {
      push(to, state);
    }

    if (allowTx(nextAction, nextLocation, retry)) {
      var _getHistoryStateAndUr = getHistoryStateAndUrl(nextLocation, index + 1),
          historyState = _getHistoryStateAndUr[0],
          url = _getHistoryStateAndUr[1]; // TODO: Support forced reloading
      // try...catch because iOS limits us to 100 pushState calls :/


      try {
        globalHistory.pushState(historyState, '', url);
      } catch (error) {
        // They are going to lose state here, but there is no real
        // way to warn them about it since the page will refresh...
        window.location.assign(url);
      }

      applyTx(nextAction);
    }
  }

  function replace(to, state) {
    var nextAction = Action.Replace;
    var nextLocation = getNextLocation(to, state);

    function retry() {
      replace(to, state);
    }

    if (allowTx(nextAction, nextLocation, retry)) {
      var _getHistoryStateAndUr2 = getHistoryStateAndUrl(nextLocation, index),
          historyState = _getHistoryStateAndUr2[0],
          url = _getHistoryStateAndUr2[1]; // TODO: Support forced reloading


      globalHistory.replaceState(historyState, '', url);
      applyTx(nextAction);
    }
  }

  function go(delta) {
    globalHistory.go(delta);
  }

  var history = {
    get action() {
      return action;
    },

    get location() {
      return location;
    },

    createHref: createHref,
    push: push,
    replace: replace,
    go: go,
    back: function back() {
      go(-1);
    },
    forward: function forward() {
      go(1);
    },
    listen: function listen(listener) {
      return listeners.push(listener);
    },
    block: function block(blocker) {
      var unblock = blockers.push(blocker);

      if (blockers.length === 1) {
        window.addEventListener(BeforeUnloadEventType, promptBeforeUnload);
      }

      return function () {
        unblock(); // Remove the beforeunload listener so the document may
        // still be salvageable in the pagehide event.
        // See https://html.spec.whatwg.org/#unloading-documents

        if (!blockers.length) {
          window.removeEventListener(BeforeUnloadEventType, promptBeforeUnload);
        }
      };
    }
  };
  return history;
}
/**
 * Hash history stores the location in window.location.hash. This makes it ideal
 * for situations where you don't want to send the location to the server for
 * some reason, either because you do cannot configure it or the URL space is
 * reserved for something else.
 *
 * @see https://github.com/remix-run/history/tree/main/docs/api-reference.md#createhashhistory
 */

function createHashHistory(options) {
  if (options === void 0) {
    options = {};
  }

  var _options2 = options,
      _options2$window = _options2.window,
      window = _options2$window === void 0 ? document.defaultView : _options2$window;
  var globalHistory = window.history;

  function getIndexAndLocation() {
    var _parsePath = parsePath(window.location.hash.substr(1)),
        _parsePath$pathname = _parsePath.pathname,
        pathname = _parsePath$pathname === void 0 ? '/' : _parsePath$pathname,
        _parsePath$search = _parsePath.search,
        search = _parsePath$search === void 0 ? '' : _parsePath$search,
        _parsePath$hash = _parsePath.hash,
        hash = _parsePath$hash === void 0 ? '' : _parsePath$hash;

    var state = globalHistory.state || {};
    return [state.idx, readOnly({
      pathname: pathname,
      search: search,
      hash: hash,
      state: state.usr || null,
      key: state.key || 'default'
    })];
  }

  var blockedPopTx = null;

  function handlePop() {
    if (blockedPopTx) {
      blockers.call(blockedPopTx);
      blockedPopTx = null;
    } else {
      var nextAction = Action.Pop;

      var _getIndexAndLocation4 = getIndexAndLocation(),
          nextIndex = _getIndexAndLocation4[0],
          nextLocation = _getIndexAndLocation4[1];

      if (blockers.length) {
        if (nextIndex != null) {
          var delta = index - nextIndex;

          if (delta) {
            // Revert the POP
            blockedPopTx = {
              action: nextAction,
              location: nextLocation,
              retry: function retry() {
                go(delta * -1);
              }
            };
            go(delta);
          }
        } else {
          // Trying to POP to a location with no index. We did not create
          // this location, so we can't effectively block the navigation.
           true ? warning(false, // TODO: Write up a doc that explains our blocking strategy in
          // detail and link to it here so people can understand better
          // what is going on and how to avoid it.
          "You are trying to block a POP navigation to a location that was not " + "created by the history library. The block will fail silently in " + "production, but in general you should do all navigation with the " + "history library (instead of using window.history.pushState directly) " + "to avoid this situation.") : 0;
        }
      } else {
        applyTx(nextAction);
      }
    }
  }

  window.addEventListener(PopStateEventType, handlePop); // popstate does not fire on hashchange in IE 11 and old (trident) Edge
  // https://developer.mozilla.org/de/docs/Web/API/Window/popstate_event

  window.addEventListener(HashChangeEventType, function () {
    var _getIndexAndLocation5 = getIndexAndLocation(),
        nextLocation = _getIndexAndLocation5[1]; // Ignore extraneous hashchange events.


    if (createPath(nextLocation) !== createPath(location)) {
      handlePop();
    }
  });
  var action = Action.Pop;

  var _getIndexAndLocation6 = getIndexAndLocation(),
      index = _getIndexAndLocation6[0],
      location = _getIndexAndLocation6[1];

  var listeners = createEvents();
  var blockers = createEvents();

  if (index == null) {
    index = 0;
    globalHistory.replaceState((0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({}, globalHistory.state, {
      idx: index
    }), '');
  }

  function getBaseHref() {
    var base = document.querySelector('base');
    var href = '';

    if (base && base.getAttribute('href')) {
      var url = window.location.href;
      var hashIndex = url.indexOf('#');
      href = hashIndex === -1 ? url : url.slice(0, hashIndex);
    }

    return href;
  }

  function createHref(to) {
    return getBaseHref() + '#' + (typeof to === 'string' ? to : createPath(to));
  }

  function getNextLocation(to, state) {
    if (state === void 0) {
      state = null;
    }

    return readOnly((0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
      pathname: location.pathname,
      hash: '',
      search: ''
    }, typeof to === 'string' ? parsePath(to) : to, {
      state: state,
      key: createKey()
    }));
  }

  function getHistoryStateAndUrl(nextLocation, index) {
    return [{
      usr: nextLocation.state,
      key: nextLocation.key,
      idx: index
    }, createHref(nextLocation)];
  }

  function allowTx(action, location, retry) {
    return !blockers.length || (blockers.call({
      action: action,
      location: location,
      retry: retry
    }), false);
  }

  function applyTx(nextAction) {
    action = nextAction;

    var _getIndexAndLocation7 = getIndexAndLocation();

    index = _getIndexAndLocation7[0];
    location = _getIndexAndLocation7[1];
    listeners.call({
      action: action,
      location: location
    });
  }

  function push(to, state) {
    var nextAction = Action.Push;
    var nextLocation = getNextLocation(to, state);

    function retry() {
      push(to, state);
    }

     true ? warning(nextLocation.pathname.charAt(0) === '/', "Relative pathnames are not supported in hash history.push(" + JSON.stringify(to) + ")") : 0;

    if (allowTx(nextAction, nextLocation, retry)) {
      var _getHistoryStateAndUr3 = getHistoryStateAndUrl(nextLocation, index + 1),
          historyState = _getHistoryStateAndUr3[0],
          url = _getHistoryStateAndUr3[1]; // TODO: Support forced reloading
      // try...catch because iOS limits us to 100 pushState calls :/


      try {
        globalHistory.pushState(historyState, '', url);
      } catch (error) {
        // They are going to lose state here, but there is no real
        // way to warn them about it since the page will refresh...
        window.location.assign(url);
      }

      applyTx(nextAction);
    }
  }

  function replace(to, state) {
    var nextAction = Action.Replace;
    var nextLocation = getNextLocation(to, state);

    function retry() {
      replace(to, state);
    }

     true ? warning(nextLocation.pathname.charAt(0) === '/', "Relative pathnames are not supported in hash history.replace(" + JSON.stringify(to) + ")") : 0;

    if (allowTx(nextAction, nextLocation, retry)) {
      var _getHistoryStateAndUr4 = getHistoryStateAndUrl(nextLocation, index),
          historyState = _getHistoryStateAndUr4[0],
          url = _getHistoryStateAndUr4[1]; // TODO: Support forced reloading


      globalHistory.replaceState(historyState, '', url);
      applyTx(nextAction);
    }
  }

  function go(delta) {
    globalHistory.go(delta);
  }

  var history = {
    get action() {
      return action;
    },

    get location() {
      return location;
    },

    createHref: createHref,
    push: push,
    replace: replace,
    go: go,
    back: function back() {
      go(-1);
    },
    forward: function forward() {
      go(1);
    },
    listen: function listen(listener) {
      return listeners.push(listener);
    },
    block: function block(blocker) {
      var unblock = blockers.push(blocker);

      if (blockers.length === 1) {
        window.addEventListener(BeforeUnloadEventType, promptBeforeUnload);
      }

      return function () {
        unblock(); // Remove the beforeunload listener so the document may
        // still be salvageable in the pagehide event.
        // See https://html.spec.whatwg.org/#unloading-documents

        if (!blockers.length) {
          window.removeEventListener(BeforeUnloadEventType, promptBeforeUnload);
        }
      };
    }
  };
  return history;
}
/**
 * Memory history stores the current location in memory. It is designed for use
 * in stateful non-browser environments like tests and React Native.
 *
 * @see https://github.com/remix-run/history/tree/main/docs/api-reference.md#creatememoryhistory
 */

function createMemoryHistory(options) {
  if (options === void 0) {
    options = {};
  }

  var _options3 = options,
      _options3$initialEntr = _options3.initialEntries,
      initialEntries = _options3$initialEntr === void 0 ? ['/'] : _options3$initialEntr,
      initialIndex = _options3.initialIndex;
  var entries = initialEntries.map(function (entry) {
    var location = readOnly((0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
      pathname: '/',
      search: '',
      hash: '',
      state: null,
      key: createKey()
    }, typeof entry === 'string' ? parsePath(entry) : entry));
     true ? warning(location.pathname.charAt(0) === '/', "Relative pathnames are not supported in createMemoryHistory({ initialEntries }) (invalid entry: " + JSON.stringify(entry) + ")") : 0;
    return location;
  });
  var index = clamp(initialIndex == null ? entries.length - 1 : initialIndex, 0, entries.length - 1);
  var action = Action.Pop;
  var location = entries[index];
  var listeners = createEvents();
  var blockers = createEvents();

  function createHref(to) {
    return typeof to === 'string' ? to : createPath(to);
  }

  function getNextLocation(to, state) {
    if (state === void 0) {
      state = null;
    }

    return readOnly((0,_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
      pathname: location.pathname,
      search: '',
      hash: ''
    }, typeof to === 'string' ? parsePath(to) : to, {
      state: state,
      key: createKey()
    }));
  }

  function allowTx(action, location, retry) {
    return !blockers.length || (blockers.call({
      action: action,
      location: location,
      retry: retry
    }), false);
  }

  function applyTx(nextAction, nextLocation) {
    action = nextAction;
    location = nextLocation;
    listeners.call({
      action: action,
      location: location
    });
  }

  function push(to, state) {
    var nextAction = Action.Push;
    var nextLocation = getNextLocation(to, state);

    function retry() {
      push(to, state);
    }

     true ? warning(location.pathname.charAt(0) === '/', "Relative pathnames are not supported in memory history.push(" + JSON.stringify(to) + ")") : 0;

    if (allowTx(nextAction, nextLocation, retry)) {
      index += 1;
      entries.splice(index, entries.length, nextLocation);
      applyTx(nextAction, nextLocation);
    }
  }

  function replace(to, state) {
    var nextAction = Action.Replace;
    var nextLocation = getNextLocation(to, state);

    function retry() {
      replace(to, state);
    }

     true ? warning(location.pathname.charAt(0) === '/', "Relative pathnames are not supported in memory history.replace(" + JSON.stringify(to) + ")") : 0;

    if (allowTx(nextAction, nextLocation, retry)) {
      entries[index] = nextLocation;
      applyTx(nextAction, nextLocation);
    }
  }

  function go(delta) {
    var nextIndex = clamp(index + delta, 0, entries.length - 1);
    var nextAction = Action.Pop;
    var nextLocation = entries[nextIndex];

    function retry() {
      go(delta);
    }

    if (allowTx(nextAction, nextLocation, retry)) {
      index = nextIndex;
      applyTx(nextAction, nextLocation);
    }
  }

  var history = {
    get index() {
      return index;
    },

    get action() {
      return action;
    },

    get location() {
      return location;
    },

    createHref: createHref,
    push: push,
    replace: replace,
    go: go,
    back: function back() {
      go(-1);
    },
    forward: function forward() {
      go(1);
    },
    listen: function listen(listener) {
      return listeners.push(listener);
    },
    block: function block(blocker) {
      return blockers.push(blocker);
    }
  };
  return history;
} ////////////////////////////////////////////////////////////////////////////////
// UTILS
////////////////////////////////////////////////////////////////////////////////

function clamp(n, lowerBound, upperBound) {
  return Math.min(Math.max(n, lowerBound), upperBound);
}

function promptBeforeUnload(event) {
  // Cancel the event.
  event.preventDefault(); // Chrome (and legacy IE) requires returnValue to be set.

  event.returnValue = '';
}

function createEvents() {
  var handlers = [];
  return {
    get length() {
      return handlers.length;
    },

    push: function push(fn) {
      handlers.push(fn);
      return function () {
        handlers = handlers.filter(function (handler) {
          return handler !== fn;
        });
      };
    },
    call: function call(arg) {
      handlers.forEach(function (fn) {
        return fn && fn(arg);
      });
    }
  };
}

function createKey() {
  return Math.random().toString(36).substr(2, 8);
}
/**
 * Creates a string URL path from the given pathname, search, and hash components.
 *
 * @see https://github.com/remix-run/history/tree/main/docs/api-reference.md#createpath
 */


function createPath(_ref) {
  var _ref$pathname = _ref.pathname,
      pathname = _ref$pathname === void 0 ? '/' : _ref$pathname,
      _ref$search = _ref.search,
      search = _ref$search === void 0 ? '' : _ref$search,
      _ref$hash = _ref.hash,
      hash = _ref$hash === void 0 ? '' : _ref$hash;
  if (search && search !== '?') pathname += search.charAt(0) === '?' ? search : '?' + search;
  if (hash && hash !== '#') pathname += hash.charAt(0) === '#' ? hash : '#' + hash;
  return pathname;
}
/**
 * Parses a string URL path into its separate pathname, search, and hash components.
 *
 * @see https://github.com/remix-run/history/tree/main/docs/api-reference.md#parsepath
 */

function parsePath(path) {
  var parsedPath = {};

  if (path) {
    var hashIndex = path.indexOf('#');

    if (hashIndex >= 0) {
      parsedPath.hash = path.substr(hashIndex);
      path = path.substr(0, hashIndex);
    }

    var searchIndex = path.indexOf('?');

    if (searchIndex >= 0) {
      parsedPath.search = path.substr(searchIndex);
      path = path.substr(0, searchIndex);
    }

    if (path) {
      parsedPath.pathname = path;
    }
  }

  return parsedPath;
}


//# sourceMappingURL=index.js.map


/***/ }),

/***/ "./node_modules/qs/lib/formats.js":
/*!****************************************!*\
  !*** ./node_modules/qs/lib/formats.js ***!
  \****************************************/
/***/ (function(module) {



var replace = String.prototype.replace;
var percentTwenties = /%20/g;

module.exports = {
    'default': 'RFC3986',
    formatters: {
        RFC1738: function (value) {
            return replace.call(value, percentTwenties, '+');
        },
        RFC3986: function (value) {
            return String(value);
        }
    },
    RFC1738: 'RFC1738',
    RFC3986: 'RFC3986'
};


/***/ }),

/***/ "./node_modules/qs/lib/index.js":
/*!**************************************!*\
  !*** ./node_modules/qs/lib/index.js ***!
  \**************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {



var stringify = __webpack_require__(/*! ./stringify */ "./node_modules/qs/lib/stringify.js");
var parse = __webpack_require__(/*! ./parse */ "./node_modules/qs/lib/parse.js");
var formats = __webpack_require__(/*! ./formats */ "./node_modules/qs/lib/formats.js");

module.exports = {
    formats: formats,
    parse: parse,
    stringify: stringify
};


/***/ }),

/***/ "./node_modules/qs/lib/parse.js":
/*!**************************************!*\
  !*** ./node_modules/qs/lib/parse.js ***!
  \**************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {



var utils = __webpack_require__(/*! ./utils */ "./node_modules/qs/lib/utils.js");

var has = Object.prototype.hasOwnProperty;

var defaults = {
    allowDots: false,
    allowPrototypes: false,
    arrayLimit: 20,
    decoder: utils.decode,
    delimiter: '&',
    depth: 5,
    parameterLimit: 1000,
    plainObjects: false,
    strictNullHandling: false
};

var parseValues = function parseQueryStringValues(str, options) {
    var obj = {};
    var cleanStr = options.ignoreQueryPrefix ? str.replace(/^\?/, '') : str;
    var limit = options.parameterLimit === Infinity ? undefined : options.parameterLimit;
    var parts = cleanStr.split(options.delimiter, limit);

    for (var i = 0; i < parts.length; ++i) {
        var part = parts[i];

        var bracketEqualsPos = part.indexOf(']=');
        var pos = bracketEqualsPos === -1 ? part.indexOf('=') : bracketEqualsPos + 1;

        var key, val;
        if (pos === -1) {
            key = options.decoder(part, defaults.decoder);
            val = options.strictNullHandling ? null : '';
        } else {
            key = options.decoder(part.slice(0, pos), defaults.decoder);
            val = options.decoder(part.slice(pos + 1), defaults.decoder);
        }
        if (has.call(obj, key)) {
            obj[key] = [].concat(obj[key]).concat(val);
        } else {
            obj[key] = val;
        }
    }

    return obj;
};

var parseObject = function (chain, val, options) {
    var leaf = val;

    for (var i = chain.length - 1; i >= 0; --i) {
        var obj;
        var root = chain[i];

        if (root === '[]' && options.parseArrays) {
            obj = [].concat(leaf);
        } else {
            obj = options.plainObjects ? Object.create(null) : {};
            var cleanRoot = root.charAt(0) === '[' && root.charAt(root.length - 1) === ']' ? root.slice(1, -1) : root;
            var index = parseInt(cleanRoot, 10);
            if (!options.parseArrays && cleanRoot === '') {
                obj = { 0: leaf };
            } else if (
                !isNaN(index)
                && root !== cleanRoot
                && String(index) === cleanRoot
                && index >= 0
                && (options.parseArrays && index <= options.arrayLimit)
            ) {
                obj = [];
                obj[index] = leaf;
            } else if (cleanRoot !== '__proto__') {
                obj[cleanRoot] = leaf;
            }
        }

        leaf = obj;
    }

    return leaf;
};

var parseKeys = function parseQueryStringKeys(givenKey, val, options) {
    if (!givenKey) {
        return;
    }

    // Transform dot notation to bracket notation
    var key = options.allowDots ? givenKey.replace(/\.([^.[]+)/g, '[$1]') : givenKey;

    // The regex chunks

    var brackets = /(\[[^[\]]*])/;
    var child = /(\[[^[\]]*])/g;

    // Get the parent

    var segment = brackets.exec(key);
    var parent = segment ? key.slice(0, segment.index) : key;

    // Stash the parent if it exists

    var keys = [];
    if (parent) {
        // If we aren't using plain objects, optionally prefix keys
        // that would overwrite object prototype properties
        if (!options.plainObjects && has.call(Object.prototype, parent)) {
            if (!options.allowPrototypes) {
                return;
            }
        }

        keys.push(parent);
    }

    // Loop through children appending to the array until we hit depth

    var i = 0;
    while ((segment = child.exec(key)) !== null && i < options.depth) {
        i += 1;
        if (!options.plainObjects && has.call(Object.prototype, segment[1].slice(1, -1))) {
            if (!options.allowPrototypes) {
                return;
            }
        }
        keys.push(segment[1]);
    }

    // If there's a remainder, just add whatever is left

    if (segment) {
        keys.push('[' + key.slice(segment.index) + ']');
    }

    return parseObject(keys, val, options);
};

module.exports = function (str, opts) {
    var options = opts ? utils.assign({}, opts) : {};

    if (options.decoder !== null && options.decoder !== undefined && typeof options.decoder !== 'function') {
        throw new TypeError('Decoder has to be a function.');
    }

    options.ignoreQueryPrefix = options.ignoreQueryPrefix === true;
    options.delimiter = typeof options.delimiter === 'string' || utils.isRegExp(options.delimiter) ? options.delimiter : defaults.delimiter;
    options.depth = typeof options.depth === 'number' ? options.depth : defaults.depth;
    options.arrayLimit = typeof options.arrayLimit === 'number' ? options.arrayLimit : defaults.arrayLimit;
    options.parseArrays = options.parseArrays !== false;
    options.decoder = typeof options.decoder === 'function' ? options.decoder : defaults.decoder;
    options.allowDots = typeof options.allowDots === 'boolean' ? options.allowDots : defaults.allowDots;
    options.plainObjects = typeof options.plainObjects === 'boolean' ? options.plainObjects : defaults.plainObjects;
    options.allowPrototypes = typeof options.allowPrototypes === 'boolean' ? options.allowPrototypes : defaults.allowPrototypes;
    options.parameterLimit = typeof options.parameterLimit === 'number' ? options.parameterLimit : defaults.parameterLimit;
    options.strictNullHandling = typeof options.strictNullHandling === 'boolean' ? options.strictNullHandling : defaults.strictNullHandling;

    if (str === '' || str === null || typeof str === 'undefined') {
        return options.plainObjects ? Object.create(null) : {};
    }

    var tempObj = typeof str === 'string' ? parseValues(str, options) : str;
    var obj = options.plainObjects ? Object.create(null) : {};

    // Iterate over the keys and setup the new object

    var keys = Object.keys(tempObj);
    for (var i = 0; i < keys.length; ++i) {
        var key = keys[i];
        var newObj = parseKeys(key, tempObj[key], options);
        obj = utils.merge(obj, newObj, options);
    }

    return utils.compact(obj);
};


/***/ }),

/***/ "./node_modules/qs/lib/stringify.js":
/*!******************************************!*\
  !*** ./node_modules/qs/lib/stringify.js ***!
  \******************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {



var utils = __webpack_require__(/*! ./utils */ "./node_modules/qs/lib/utils.js");
var formats = __webpack_require__(/*! ./formats */ "./node_modules/qs/lib/formats.js");

var arrayPrefixGenerators = {
    brackets: function brackets(prefix) {
        return prefix + '[]';
    },
    indices: function indices(prefix, key) {
        return prefix + '[' + key + ']';
    },
    repeat: function repeat(prefix) {
        return prefix;
    }
};

var isArray = Array.isArray;
var push = Array.prototype.push;
var pushToArray = function (arr, valueOrArray) {
    push.apply(arr, isArray(valueOrArray) ? valueOrArray : [valueOrArray]);
};

var toISO = Date.prototype.toISOString;

var defaults = {
    delimiter: '&',
    encode: true,
    encoder: utils.encode,
    encodeValuesOnly: false,
    serializeDate: function serializeDate(date) {
        return toISO.call(date);
    },
    skipNulls: false,
    strictNullHandling: false
};

var stringify = function stringify(
    object,
    prefix,
    generateArrayPrefix,
    strictNullHandling,
    skipNulls,
    encoder,
    filter,
    sort,
    allowDots,
    serializeDate,
    formatter,
    encodeValuesOnly
) {
    var obj = object;
    if (typeof filter === 'function') {
        obj = filter(prefix, obj);
    } else if (obj instanceof Date) {
        obj = serializeDate(obj);
    }

    if (obj === null) {
        if (strictNullHandling) {
            return encoder && !encodeValuesOnly ? encoder(prefix, defaults.encoder) : prefix;
        }

        obj = '';
    }

    if (typeof obj === 'string' || typeof obj === 'number' || typeof obj === 'boolean' || utils.isBuffer(obj)) {
        if (encoder) {
            var keyValue = encodeValuesOnly ? prefix : encoder(prefix, defaults.encoder);
            return [formatter(keyValue) + '=' + formatter(encoder(obj, defaults.encoder))];
        }
        return [formatter(prefix) + '=' + formatter(String(obj))];
    }

    var values = [];

    if (typeof obj === 'undefined') {
        return values;
    }

    var objKeys;
    if (isArray(filter)) {
        objKeys = filter;
    } else {
        var keys = Object.keys(obj);
        objKeys = sort ? keys.sort(sort) : keys;
    }

    for (var i = 0; i < objKeys.length; ++i) {
        var key = objKeys[i];

        if (skipNulls && obj[key] === null) {
            continue;
        }

        if (isArray(obj)) {
            pushToArray(values, stringify(
                obj[key],
                generateArrayPrefix(prefix, key),
                generateArrayPrefix,
                strictNullHandling,
                skipNulls,
                encoder,
                filter,
                sort,
                allowDots,
                serializeDate,
                formatter,
                encodeValuesOnly
            ));
        } else {
            pushToArray(values, stringify(
                obj[key],
                prefix + (allowDots ? '.' + key : '[' + key + ']'),
                generateArrayPrefix,
                strictNullHandling,
                skipNulls,
                encoder,
                filter,
                sort,
                allowDots,
                serializeDate,
                formatter,
                encodeValuesOnly
            ));
        }
    }

    return values;
};

module.exports = function (object, opts) {
    var obj = object;
    var options = opts ? utils.assign({}, opts) : {};

    if (options.encoder !== null && typeof options.encoder !== 'undefined' && typeof options.encoder !== 'function') {
        throw new TypeError('Encoder has to be a function.');
    }

    var delimiter = typeof options.delimiter === 'undefined' ? defaults.delimiter : options.delimiter;
    var strictNullHandling = typeof options.strictNullHandling === 'boolean' ? options.strictNullHandling : defaults.strictNullHandling;
    var skipNulls = typeof options.skipNulls === 'boolean' ? options.skipNulls : defaults.skipNulls;
    var encode = typeof options.encode === 'boolean' ? options.encode : defaults.encode;
    var encoder = typeof options.encoder === 'function' ? options.encoder : defaults.encoder;
    var sort = typeof options.sort === 'function' ? options.sort : null;
    var allowDots = typeof options.allowDots === 'undefined' ? false : options.allowDots;
    var serializeDate = typeof options.serializeDate === 'function' ? options.serializeDate : defaults.serializeDate;
    var encodeValuesOnly = typeof options.encodeValuesOnly === 'boolean' ? options.encodeValuesOnly : defaults.encodeValuesOnly;
    if (typeof options.format === 'undefined') {
        options.format = formats['default'];
    } else if (!Object.prototype.hasOwnProperty.call(formats.formatters, options.format)) {
        throw new TypeError('Unknown format option provided.');
    }
    var formatter = formats.formatters[options.format];
    var objKeys;
    var filter;

    if (typeof options.filter === 'function') {
        filter = options.filter;
        obj = filter('', obj);
    } else if (isArray(options.filter)) {
        filter = options.filter;
        objKeys = filter;
    }

    var keys = [];

    if (typeof obj !== 'object' || obj === null) {
        return '';
    }

    var arrayFormat;
    if (options.arrayFormat in arrayPrefixGenerators) {
        arrayFormat = options.arrayFormat;
    } else if ('indices' in options) {
        arrayFormat = options.indices ? 'indices' : 'repeat';
    } else {
        arrayFormat = 'indices';
    }

    var generateArrayPrefix = arrayPrefixGenerators[arrayFormat];

    if (!objKeys) {
        objKeys = Object.keys(obj);
    }

    if (sort) {
        objKeys.sort(sort);
    }

    for (var i = 0; i < objKeys.length; ++i) {
        var key = objKeys[i];

        if (skipNulls && obj[key] === null) {
            continue;
        }
        pushToArray(keys, stringify(
            obj[key],
            key,
            generateArrayPrefix,
            strictNullHandling,
            skipNulls,
            encode ? encoder : null,
            filter,
            sort,
            allowDots,
            serializeDate,
            formatter,
            encodeValuesOnly
        ));
    }

    var joined = keys.join(delimiter);
    var prefix = options.addQueryPrefix === true ? '?' : '';

    return joined.length > 0 ? prefix + joined : '';
};


/***/ }),

/***/ "./node_modules/qs/lib/utils.js":
/*!**************************************!*\
  !*** ./node_modules/qs/lib/utils.js ***!
  \**************************************/
/***/ (function(module) {



var has = Object.prototype.hasOwnProperty;

var hexTable = (function () {
    var array = [];
    for (var i = 0; i < 256; ++i) {
        array.push('%' + ((i < 16 ? '0' : '') + i.toString(16)).toUpperCase());
    }

    return array;
}());

var compactQueue = function compactQueue(queue) {
    var obj;

    while (queue.length) {
        var item = queue.pop();
        obj = item.obj[item.prop];

        if (Array.isArray(obj)) {
            var compacted = [];

            for (var j = 0; j < obj.length; ++j) {
                if (typeof obj[j] !== 'undefined') {
                    compacted.push(obj[j]);
                }
            }

            item.obj[item.prop] = compacted;
        }
    }

    return obj;
};

var arrayToObject = function arrayToObject(source, options) {
    var obj = options && options.plainObjects ? Object.create(null) : {};
    for (var i = 0; i < source.length; ++i) {
        if (typeof source[i] !== 'undefined') {
            obj[i] = source[i];
        }
    }

    return obj;
};

var merge = function merge(target, source, options) {
    if (!source) {
        return target;
    }

    if (typeof source !== 'object') {
        if (Array.isArray(target)) {
            target.push(source);
        } else if (target && typeof target === 'object') {
            if ((options && (options.plainObjects || options.allowPrototypes)) || !has.call(Object.prototype, source)) {
                target[source] = true;
            }
        } else {
            return [target, source];
        }

        return target;
    }

    if (!target || typeof target !== 'object') {
        return [target].concat(source);
    }

    var mergeTarget = target;
    if (Array.isArray(target) && !Array.isArray(source)) {
        mergeTarget = arrayToObject(target, options);
    }

    if (Array.isArray(target) && Array.isArray(source)) {
        source.forEach(function (item, i) {
            if (has.call(target, i)) {
                var targetItem = target[i];
                if (targetItem && typeof targetItem === 'object' && item && typeof item === 'object') {
                    target[i] = merge(targetItem, item, options);
                } else {
                    target.push(item);
                }
            } else {
                target[i] = item;
            }
        });
        return target;
    }

    return Object.keys(source).reduce(function (acc, key) {
        var value = source[key];

        if (has.call(acc, key)) {
            acc[key] = merge(acc[key], value, options);
        } else {
            acc[key] = value;
        }
        return acc;
    }, mergeTarget);
};

var assign = function assignSingleSource(target, source) {
    return Object.keys(source).reduce(function (acc, key) {
        acc[key] = source[key];
        return acc;
    }, target);
};

var decode = function (str) {
    try {
        return decodeURIComponent(str.replace(/\+/g, ' '));
    } catch (e) {
        return str;
    }
};

var encode = function encode(str) {
    // This code was originally written by Brian White (mscdex) for the io.js core querystring library.
    // It has been adapted here for stricter adherence to RFC 3986
    if (str.length === 0) {
        return str;
    }

    var string = typeof str === 'string' ? str : String(str);

    var out = '';
    for (var i = 0; i < string.length; ++i) {
        var c = string.charCodeAt(i);

        if (
            c === 0x2D // -
            || c === 0x2E // .
            || c === 0x5F // _
            || c === 0x7E // ~
            || (c >= 0x30 && c <= 0x39) // 0-9
            || (c >= 0x41 && c <= 0x5A) // a-z
            || (c >= 0x61 && c <= 0x7A) // A-Z
        ) {
            out += string.charAt(i);
            continue;
        }

        if (c < 0x80) {
            out = out + hexTable[c];
            continue;
        }

        if (c < 0x800) {
            out = out + (hexTable[0xC0 | (c >> 6)] + hexTable[0x80 | (c & 0x3F)]);
            continue;
        }

        if (c < 0xD800 || c >= 0xE000) {
            out = out + (hexTable[0xE0 | (c >> 12)] + hexTable[0x80 | ((c >> 6) & 0x3F)] + hexTable[0x80 | (c & 0x3F)]);
            continue;
        }

        i += 1;
        c = 0x10000 + (((c & 0x3FF) << 10) | (string.charCodeAt(i) & 0x3FF));
        /* eslint operator-linebreak: [2, "before"] */
        out += hexTable[0xF0 | (c >> 18)]
            + hexTable[0x80 | ((c >> 12) & 0x3F)]
            + hexTable[0x80 | ((c >> 6) & 0x3F)]
            + hexTable[0x80 | (c & 0x3F)];
    }

    return out;
};

var compact = function compact(value) {
    var queue = [{ obj: { o: value }, prop: 'o' }];
    var refs = [];

    for (var i = 0; i < queue.length; ++i) {
        var item = queue[i];
        var obj = item.obj[item.prop];

        var keys = Object.keys(obj);
        for (var j = 0; j < keys.length; ++j) {
            var key = keys[j];
            var val = obj[key];
            if (typeof val === 'object' && val !== null && refs.indexOf(val) === -1) {
                queue.push({ obj: obj, prop: key });
                refs.push(val);
            }
        }
    }

    return compactQueue(queue);
};

var isRegExp = function isRegExp(obj) {
    return Object.prototype.toString.call(obj) === '[object RegExp]';
};

var isBuffer = function isBuffer(obj) {
    if (obj === null || typeof obj === 'undefined') {
        return false;
    }

    return !!(obj.constructor && obj.constructor.isBuffer && obj.constructor.isBuffer(obj));
};

module.exports = {
    arrayToObject: arrayToObject,
    assign: assign,
    compact: compact,
    decode: decode,
    encode: encode,
    isBuffer: isBuffer,
    isRegExp: isRegExp,
    merge: merge
};


/***/ }),

/***/ "lodash":
/*!*************************!*\
  !*** external "lodash" ***!
  \*************************/
/***/ (function(module) {

module.exports = window["lodash"];

/***/ }),

/***/ "@wordpress/url":
/*!*****************************!*\
  !*** external ["wp","url"] ***!
  \*****************************/
/***/ (function(module) {

module.exports = window["wp"]["url"];

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js ***!
  \*********************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _arrayLikeToArray; }
/* harmony export */ });
function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;
  for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i];
  return arr2;
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js ***!
  \**********************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _arrayWithoutHoles; }
/* harmony export */ });
/* harmony import */ var _arrayLikeToArray_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./arrayLikeToArray.js */ "./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js");

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) return (0,_arrayLikeToArray_js__WEBPACK_IMPORTED_MODULE_0__["default"])(arr);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/defineProperty.js ***!
  \*******************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _defineProperty; }
/* harmony export */ });
/* harmony import */ var _toPropertyKey_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./toPropertyKey.js */ "./node_modules/@babel/runtime/helpers/esm/toPropertyKey.js");

function _defineProperty(obj, key, value) {
  key = (0,_toPropertyKey_js__WEBPACK_IMPORTED_MODULE_0__["default"])(key);
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }
  return obj;
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/extends.js":
/*!************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/extends.js ***!
  \************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _extends; }
/* harmony export */ });
function _extends() {
  _extends = Object.assign ? Object.assign.bind() : function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];
      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }
    return target;
  };
  return _extends.apply(this, arguments);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/iterableToArray.js":
/*!********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js ***!
  \********************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _iterableToArray; }
/* harmony export */ });
function _iterableToArray(iter) {
  if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js ***!
  \**********************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _nonIterableSpread; }
/* harmony export */ });
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js ***!
  \**********************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _toConsumableArray; }
/* harmony export */ });
/* harmony import */ var _arrayWithoutHoles_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./arrayWithoutHoles.js */ "./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js");
/* harmony import */ var _iterableToArray_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./iterableToArray.js */ "./node_modules/@babel/runtime/helpers/esm/iterableToArray.js");
/* harmony import */ var _unsupportedIterableToArray_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./unsupportedIterableToArray.js */ "./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js");
/* harmony import */ var _nonIterableSpread_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./nonIterableSpread.js */ "./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js");




function _toConsumableArray(arr) {
  return (0,_arrayWithoutHoles_js__WEBPACK_IMPORTED_MODULE_0__["default"])(arr) || (0,_iterableToArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(arr) || (0,_unsupportedIterableToArray_js__WEBPACK_IMPORTED_MODULE_2__["default"])(arr) || (0,_nonIterableSpread_js__WEBPACK_IMPORTED_MODULE_3__["default"])();
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/toPrimitive.js":
/*!****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/toPrimitive.js ***!
  \****************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _toPrimitive; }
/* harmony export */ });
/* harmony import */ var _typeof_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./typeof.js */ "./node_modules/@babel/runtime/helpers/esm/typeof.js");

function _toPrimitive(input, hint) {
  if ((0,_typeof_js__WEBPACK_IMPORTED_MODULE_0__["default"])(input) !== "object" || input === null) return input;
  var prim = input[Symbol.toPrimitive];
  if (prim !== undefined) {
    var res = prim.call(input, hint || "default");
    if ((0,_typeof_js__WEBPACK_IMPORTED_MODULE_0__["default"])(res) !== "object") return res;
    throw new TypeError("@@toPrimitive must return a primitive value.");
  }
  return (hint === "string" ? String : Number)(input);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/toPropertyKey.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/toPropertyKey.js ***!
  \******************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _toPropertyKey; }
/* harmony export */ });
/* harmony import */ var _typeof_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./typeof.js */ "./node_modules/@babel/runtime/helpers/esm/typeof.js");
/* harmony import */ var _toPrimitive_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./toPrimitive.js */ "./node_modules/@babel/runtime/helpers/esm/toPrimitive.js");


function _toPropertyKey(arg) {
  var key = (0,_toPrimitive_js__WEBPACK_IMPORTED_MODULE_1__["default"])(arg, "string");
  return (0,_typeof_js__WEBPACK_IMPORTED_MODULE_0__["default"])(key) === "symbol" ? key : String(key);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/typeof.js":
/*!***********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/typeof.js ***!
  \***********************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _typeof; }
/* harmony export */ });
function _typeof(obj) {
  "@babel/helpers - typeof";

  return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) {
    return typeof obj;
  } : function (obj) {
    return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
  }, _typeof(obj);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _unsupportedIterableToArray; }
/* harmony export */ });
/* harmony import */ var _arrayLikeToArray_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./arrayLikeToArray.js */ "./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js");

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return (0,_arrayLikeToArray_js__WEBPACK_IMPORTED_MODULE_0__["default"])(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return (0,_arrayLikeToArray_js__WEBPACK_IMPORTED_MODULE_0__["default"])(o, minLen);
}

/***/ })

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
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module is referenced by other modules so it can't be inlined
/******/ 	var __webpack_exports__ = __webpack_require__("./client/packages/navigation/index.js");
/******/ 	(window.eac = window.eac || {}).navigation = __webpack_exports__;
/******/ 	
/******/ })()
;
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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QTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEtBQUs7QUFDTDs7QUFFQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLE1BQU07QUFDTjs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxVQUFVO0FBQ1Y7QUFDQTtBQUNBLFVBQVUsS0FBcUM7QUFDL0M7QUFDQTtBQUNBLHNVQUFzVSxDQUFNO0FBQzVVO0FBQ0EsUUFBUTtBQUNSO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBLCtCQUErQiw4RUFBUSxHQUFHO0FBQzFDO0FBQ0EsS0FBSztBQUNMOztBQUVBO0FBQ0E7QUFDQSxJQUFJOzs7QUFHSjtBQUNBO0FBQ0E7QUFDQTs7QUFFQSxvQkFBb0IsOEVBQVE7QUFDNUI7QUFDQTtBQUNBO0FBQ0EsS0FBSztBQUNMO0FBQ0E7QUFDQSxLQUFLO0FBQ0w7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEtBQUs7QUFDTDs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsS0FBSztBQUNMOztBQUVBO0FBQ0E7O0FBRUE7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEtBQUs7QUFDTDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLDBDQUEwQztBQUMxQzs7O0FBR0E7QUFDQTtBQUNBLFFBQVE7QUFDUjtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSwyQ0FBMkM7OztBQUczQztBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsS0FBSzs7QUFFTDtBQUNBO0FBQ0EsS0FBSzs7QUFFTDtBQUNBO0FBQ0E7QUFDQTtB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QUNBO0FBQ0E7QUFDQSxLQUFLO0FBQ0w7QUFDQTtBQUNBLEtBQUs7QUFDTDs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsS0FBSztBQUNMOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEtBQUs7QUFDTDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBLElBQUksS0FBcUMsNklBQTZJLENBQU07O0FBRTVMO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBLElBQUksS0FBcUMsZ0pBQWdKLENBQU07O0FBRS9MO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsS0FBSzs7QUFFTDtBQUNBO0FBQ0EsS0FBSzs7QUFFTDtBQUNBO0FBQ0EsS0FBSzs7QUFFTDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxLQUFLO0FBQ0w7QUFDQTtBQUNBLEtBQUs7QUFDTDtBQUNBO0FBQ0EsS0FBSztBQUNMO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxFQUFFO0FBQ0Y7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBLDBCQUEwQjs7QUFFMUI7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsS0FBSzs7QUFFTDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsU0FBUztBQUNUO0FBQ0EsS0FBSztBQUNMO0FBQ0E7QUFDQTtBQUNBLE9BQU87QUFDUDtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FB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REFBdUQsaUJBQWlCO1dBQ3hFO1dBQ0EsZ0RBQWdELGFBQWE7V0FDN0Q7Ozs7O1VFTkE7VUFDQTtVQUNBO1VBQ0EiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9fX2V2ZXJBY2NvdW50aW5nX3dlYnBhY2tKc29ucC8uL2NsaWVudC9wYWNrYWdlcy9uYXZpZ2F0aW9uL2ZpbHRlcnMuanMiLCJ3ZWJwYWNrOi8vX19ldmVyQWNjb3VudGluZ193ZWJwYWNrSnNvbnAvLi9jbGllbnQvcGFja2FnZXMvbmF2aWdhdGlvbi9oaXN0b3J5LmpzIiwid2VicGFjazovL19fZXZlckFjY291bnRpbmdfd2VicGFja0pzb25wLy4vY2xpZW50L3BhY2thZ2VzL25hdmlnYXRpb24vaW5kZXguanMiLCJ3ZWJwYWNrOi8vX19ldmVyQWNjb3VudGluZ193ZWJwYWNrSnNvbnAvLi9ub2RlX21vZHVsZXMvaGlzdG9yeS9pbmRleC5qcyIsIndlYnBhY2s6Ly9fX2V2ZXJBY2NvdW50aW5nX3dlYnBhY2tKc29ucC8uL25vZGVfbW9kdWxlcy9xcy9saWIvZm9ybWF0cy5qcyIsIndlYnBhY2s6Ly9fX2V2ZXJBY2NvdW50aW5nX3dlYnBhY2tKc29ucC8uL25vZGVfbW9kdWxlcy9xcy9saWIvaW5kZXguanMiLCJ3ZWJwYWNrOi8vX19ldmVyQWNjb3VudGluZ193ZWJwYWNrSnNvbnAvLi9ub2RlX21vZHVsZXMvcXMvbGliL3BhcnNlLmpzIiwid2VicGFjazovL19fZXZlckFjY291bnRpbmdfd2VicGFja0pzb25wLy4vbm9kZV9tb2R1bGVzL3FzL2xpYi9zdHJpbmdpZnkuanMiLCJ3ZWJwYWNrOi8vX19ldmVyQWNjb3VudGluZ193ZWJwYWNrSnNvbnAvLi9ub2RlX21vZHVsZXMvcXMvbGliL3V0aWxzLmpzIiwid2VicGFjazovL19fZXZlckFjY291bnRpbmdfd2VicGFja0pzb25wL2V4dGVybmFsIHdpbmRvdyBcImxvZGFzaFwiIiwid2VicGFjazovL19fZXZlckFjY291bnRpbmdfd2VicGFja0pzb25wL2V4dGVybmFsIHdpbmRvdyBbXCJ3cFwiLFwidXJsXCJdIiwid2VicGFjazovL19fZXZlckFjY291bnRpbmdfd2VicGFja0pzb25wLy4vbm9kZV9tb2R1bGVzL0BiYWJlbC9ydW50aW1lL2hlbHBlcnMvZXNtL2FycmF5TGlrZVRvQXJyYXkuanMiLCJ3ZWJwYWNrOi8vX19ldmVyQWNjb3VudGluZ193ZWJwYWNrSnNvbnAvLi9ub2RlX21vZHVsZXMvQGJhYmVsL3J1bnRpbWUvaGVscGVycy9lc20vYXJyYXlXaXRob3V0SG9sZXMuanMiLCJ3ZWJwYWNrOi8vX19ldmVyQWNjb3VudGluZ193ZWJwYWNrSnNvbnAvLi9ub2RlX21vZHVsZXMvQGJhYmVsL3J1bnRpbWUvaGVscGVycy9lc20vZGVmaW5lUHJvcGVydHkuanMiLCJ3ZWJwYWNrOi8vX19ldmVyQWNjb3VudGluZ193ZWJwYWNrSnNvbnAvLi9ub2RlX21vZHVsZXMvQGJhYmVsL3J1bnRpbWUvaGVscGVycy9lc20vZXh0ZW5kcy5qcyIsIndlYnBhY2s6Ly9fX2V2ZXJBY2NvdW50aW5nX3dlYnBhY2tKc29ucC8uL25vZGVfbW9kdWxlcy9AYmFiZWwvcnVudGltZS9oZWxwZXJzL2VzbS9pdGVyYWJsZVRvQXJyYXkuanMiLCJ3ZWJwYWNrOi8vX19ldmVyQWNjb3VudGluZ193ZWJwYWNrSnNvbnAvLi9ub2RlX21vZHVsZXMvQGJhYmVsL3J1bnRpbWUvaGVscGVycy9lc20vbm9uSXRlcmFibGVTcHJlYWQuanMiLCJ3ZWJwYWNrOi8vX19ldmVyQWNjb3VudGluZ193ZWJwYWNrSnNvbnAvLi9ub2RlX21vZHVsZXMvQGJhYmVsL3J1bnRpbWUvaGVscGVycy9lc20vdG9Db25zdW1hYmxlQXJyYXkuanMiLCJ3ZWJwYWNrOi8vX19ldmVyQWNjb3VudGluZ193ZWJwYWNrSnNvbnAvLi9ub2RlX21vZHVsZXMvQGJhYmVsL3J1bnRpbWUvaGVscGVycy9lc20vdG9QcmltaXRpdmUuanMiLCJ3ZWJwYWNrOi8vX19ldmVyQWNjb3VudGluZ193ZWJwYWNrSnNvbnAvLi9ub2RlX21vZHVsZXMvQGJhYmVsL3J1bnRpbWUvaGVscGVycy9lc20vdG9Qcm9wZXJ0eUtleS5qcyIsIndlYnBhY2s6Ly9fX2V2ZXJBY2NvdW50aW5nX3dlYnBhY2tKc29ucC8uL25vZGVfbW9kdWxlcy9AYmFiZWwvcnVudGltZS9oZWxwZXJzL2VzbS90eXBlb2YuanMiLCJ3ZWJwYWNrOi8vX19ldmVyQWNjb3VudGluZ193ZWJwYWNrSnNvbnAvLi9ub2RlX21vZHVsZXMvQGJhYmVsL3J1bnRpbWUvaGVscGVycy9lc20vdW5zdXBwb3J0ZWRJdGVyYWJsZVRvQXJyYXkuanMiLCJ3ZWJwYWNrOi8vX19ldmVyQWNjb3VudGluZ193ZWJwYWNrSnNvbnAvd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vX19ldmVyQWNjb3VudGluZ193ZWJwYWNrSnNvbnAvd2VicGFjay9ydW50aW1lL2NvbXBhdCBnZXQgZGVmYXVsdCBleHBvcnQiLCJ3ZWJwYWNrOi8vX19ldmVyQWNjb3VudGluZ193ZWJwYWNrSnNvbnAvd2VicGFjay9ydW50aW1lL2RlZmluZSBwcm9wZXJ0eSBnZXR0ZXJzIiwid2VicGFjazovL19fZXZlckFjY291bnRpbmdfd2VicGFja0pzb25wL3dlYnBhY2svcnVudGltZS9oYXNPd25Qcm9wZXJ0eSBzaG9ydGhhbmQiLCJ3ZWJwYWNrOi8vX19ldmVyQWNjb3VudGluZ193ZWJwYWNrSnNvbnAvd2VicGFjay9ydW50aW1lL21ha2UgbmFtZXNwYWNlIG9iamVjdCIsIndlYnBhY2s6Ly9fX2V2ZXJBY2NvdW50aW5nX3dlYnBhY2tKc29ucC93ZWJwYWNrL2JlZm9yZS1zdGFydHVwIiwid2VicGFjazovL19fZXZlckFjY291bnRpbmdfd2VicGFja0pzb25wL3dlYnBhY2svc3RhcnR1cCIsIndlYnBhY2s6Ly9fX2V2ZXJBY2NvdW50aW5nX3dlYnBhY2tKc29ucC93ZWJwYWNrL2FmdGVyLXN0YXJ0dXAiXSwic291cmNlc0NvbnRlbnQiOlsiLyoqXG4gKiBFeHRlcm5hbCBkZXBlbmRlbmNpZXNcbiAqL1xuaW1wb3J0IHsgZmluZCwgZ2V0LCBvbWl0LCB1bmlxV2l0aCwgaXNFcXVhbCB9IGZyb20gJ2xvZGFzaCc7XG5cbi8qKlxuICogQ29sbGFwc2UgYW4gYXJyYXkgb2YgZmlsdGVyIHZhbHVlcyB3aXRoIHN1YkZpbHRlcnMgaW50byBhIDEtZGltZW5zaW9uYWwgYXJyYXkuXG4gKlxuICogQHBhcmFtIHtBcnJheX0gZmlsdGVycyBTZXQgb2YgZmlsdGVycyB3aXRoIHBvc3NpYmxlIHN1YmZpbHRlcnMuXG4gKiBAcmV0dXJuIHtBcnJheX0gRmxhdHRlbmVkIGFycmF5IG9mIGFsbCBmaWx0ZXJzLlxuICovXG5leHBvcnQgZnVuY3Rpb24gZmxhdHRlbkZpbHRlcnMoZmlsdGVycykge1xuXHRjb25zdCBhbGxGaWx0ZXJzID0gW107XG5cdGZpbHRlcnMuZm9yRWFjaCgoZikgPT4ge1xuXHRcdGlmICghZi5zdWJGaWx0ZXJzKSB7XG5cdFx0XHRhbGxGaWx0ZXJzLnB1c2goZik7XG5cdFx0fSBlbHNlIHtcblx0XHRcdGFsbEZpbHRlcnMucHVzaChvbWl0KGYsICdzdWJGaWx0ZXJzJykpO1xuXHRcdFx0Y29uc3Qgc3ViRmlsdGVycyA9IGZsYXR0ZW5GaWx0ZXJzKGYuc3ViRmlsdGVycyk7XG5cdFx0XHRhbGxGaWx0ZXJzLnB1c2goLi4uc3ViRmlsdGVycyk7XG5cdFx0fVxuXHR9KTtcblx0cmV0dXJuIGFsbEZpbHRlcnM7XG59XG5cbi8qKlxuICogRGVzY3JpYmUgYWN0aXZlRmlsdGVyIG9iamVjdC5cbiAqXG4gKiBAdHlwZWRlZiB7T2JqZWN0fSBhY3RpdmVGaWx0ZXJcbiAqIEBwcm9wZXJ0eSB7c3RyaW5nfSBrZXkgLSBmaWx0ZXIga2V5LlxuICogQHByb3BlcnR5IHtzdHJpbmd9IFtydWxlXSAtIGEgbW9kaWZ5aW5nIHJ1bGUgZm9yIGEgZmlsdGVyLCBlZyAnaW5jbHVkZXMnIG9yICdpc19ub3QnLlxuICogQHByb3BlcnR5IHtzdHJpbmd9IHZhbHVlIC0gZmlsdGVyIHZhbHVlKHMpLlxuICovXG5cbi8qKlxuICogR2l2ZW4gYSBxdWVyeSBvYmplY3QsIHJldHVybiBhbiBhcnJheSBvZiBhY3RpdmVGaWx0ZXJzLCBpZiBhbnkuXG4gKlxuICogQHBhcmFtIHtPYmplY3R9IHF1ZXJ5IC0gcXVlcnkgb2plY3RcbiAqIEBwYXJhbSB7T2JqZWN0fSBmaWx0ZXJzIC0gZmlsdGVycyBvYmplY3RcbiAqIEByZXR1cm4ge0FycmF5fSAtIGFycmF5IG9mIGFjdGl2ZUZpbHRlcnNcbiAqL1xuZXhwb3J0IGZ1bmN0aW9uIGdldEFjdGl2ZUZpbHRlcnNGcm9tUXVlcnkocXVlcnksIGZpbHRlcnMpIHtcblx0cmV0dXJuIE9iamVjdC5rZXlzKGZpbHRlcnMpLnJlZHVjZSgoYWN0aXZlRmlsdGVycywgY29uZmlnS2V5KSA9PiB7XG5cdFx0Y29uc3QgZmlsdGVyID0gZmlsdGVyc1tjb25maWdLZXldO1xuXHRcdGlmIChmaWx0ZXIucnVsZXMpIHtcblx0XHRcdC8vIEdldCBhbGwgcnVsZXMgZm91bmQgaW4gdGhlIHF1ZXJ5IHN0cmluZy5cblx0XHRcdGNvbnN0IG1hdGNoZXMgPSBmaWx0ZXIucnVsZXMuZmlsdGVyKChydWxlKSA9PlxuXHRcdFx0XHRxdWVyeS5oYXNPd25Qcm9wZXJ0eShnZXRVcmxLZXkoY29uZmlnS2V5LCBydWxlLnZhbHVlKSlcblx0XHRcdCk7XG5cblx0XHRcdGlmIChtYXRjaGVzLmxlbmd0aCkge1xuXHRcdFx0XHRpZiAoZmlsdGVyLmFsbG93TXVsdGlwbGUpIHtcblx0XHRcdFx0XHQvLyBJZiBydWxlcyB3ZXJlIGZvdW5kIGluIHRoZSBxdWVyeSBzdHJpbmcsIGFuZCB0aGlzIGZpbHRlciBzdXBwb3J0c1xuXHRcdFx0XHRcdC8vIG11bHRpcGxlIGluc3RhbmNlcywgYWRkIGFsbCBtYXRjaGVzIHRvIHRoZSBhY3RpdmUgZmlsdGVycyBhcnJheS5cblx0XHRcdFx0XHRtYXRjaGVzLmZvckVhY2goKG1hdGNoKSA9PiB7XG5cdFx0XHRcdFx0XHRjb25zdCB2YWx1ZSA9IHF1ZXJ5W2dldFVybEtleShjb25maWdLZXksIG1hdGNoLnZhbHVlKV07XG5cblx0XHRcdFx0XHRcdHZhbHVlLmZvckVhY2goKGZpbHRlclZhbHVlKSA9PiB7XG5cdFx0XHRcdFx0XHRcdGFjdGl2ZUZpbHRlcnMucHVzaCh7XG5cdFx0XHRcdFx0XHRcdFx0a2V5OiBjb25maWdLZXksXG5cdFx0XHRcdFx0XHRcdFx0cnVsZTogbWF0Y2gudmFsdWUsXG5cdFx0XHRcdFx0XHRcdFx0dmFsdWU6IGZpbHRlclZhbHVlLFxuXHRcdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdH0pO1xuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdC8vIElmIHRoZSBmaWx0ZXIgaXMgYSBzaW5nbGUgaW5zdGFuY2UsIGp1c3QgcHJvY2VzcyB0aGUgZmlyc3QgcnVsZSBtYXRjaC5cblx0XHRcdFx0XHRjb25zdCB2YWx1ZSA9IHF1ZXJ5W2dldFVybEtleShjb25maWdLZXksIG1hdGNoZXNbMF0udmFsdWUpXTtcblx0XHRcdFx0XHRhY3RpdmVGaWx0ZXJzLnB1c2goe1xuXHRcdFx0XHRcdFx0a2V5OiBjb25maWdLZXksXG5cdFx0XHRcdFx0XHRydWxlOiBtYXRjaGVzWzBdLnZhbHVlLFxuXHRcdFx0XHRcdFx0dmFsdWUsXG5cdFx0XHRcdFx0fSk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHR9IGVsc2UgaWYgKHF1ZXJ5W2NvbmZpZ0tleV0pIHtcblx0XHRcdC8vIElmIHRoZSBmaWx0ZXIgZG9lc24ndCBoYXZlIHJ1bGVzLCBidXQgYWxsb3dzIG11bHRpcGxlcy5cblx0XHRcdGlmIChmaWx0ZXIuYWxsb3dNdWx0aXBsZSkge1xuXHRcdFx0XHRjb25zdCB2YWx1ZSA9IHF1ZXJ5W2NvbmZpZ0tleV07XG5cdFx0XHRcdHZhbHVlLmZvckVhY2goKGZpbHRlclZhbHVlKSA9PiB7XG5cdFx0XHRcdFx0YWN0aXZlRmlsdGVycy5wdXNoKHtcblx0XHRcdFx0XHRcdGtleTogY29uZmlnS2V5LFxuXHRcdFx0XHRcdFx0dmFsdWU6IGZpbHRlclZhbHVlLFxuXHRcdFx0XHRcdH0pO1xuXHRcdFx0XHR9KTtcblx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdC8vIEZpbHRlciB3aXRoIG5vIHJ1bGVzIGFuZCBvbmx5IG9uZSBpbnN0YW5jZS5cblx0XHRcdFx0YWN0aXZlRmlsdGVycy5wdXNoKHtcblx0XHRcdFx0XHRrZXk6IGNvbmZpZ0tleSxcblx0XHRcdFx0XHR2YWx1ZTogcXVlcnlbY29uZmlnS2V5XSxcblx0XHRcdFx0fSk7XG5cdFx0XHR9XG5cdFx0fVxuXG5cdFx0cmV0dXJuIHVuaXFXaXRoKGFjdGl2ZUZpbHRlcnMsIGlzRXF1YWwpO1xuXHR9LCBbXSk7XG59XG5cbi8qKlxuICogR2V0IHRoZSBkZWZhdWx0IG9wdGlvbidzIHZhbHVlIGZyb20gdGhlIGNvbmZpZ3VyYXRpb24gb2JqZWN0IGZvciBhIGdpdmVuIGZpbHRlci4gVGhlIGZpcnN0XG4gKiBvcHRpb24gaXMgdXNlZCBhcyBkZWZhdWx0IGlmIG5vIGBkZWZhdWx0T3B0aW9uYCBpcyBwcm92aWRlZC5cbiAqXG4gKiBAcGFyYW0ge09iamVjdH0gZmlsdGVyIC0gYSBmaWx0ZXIgY29uZmlnIG9iamVjdC5cbiAqIEBwYXJhbSB7QXJyYXl9IG9wdGlvbnMgLSBzZWxlY3Qgb3B0aW9ucy5cbiAqIEByZXR1cm4ge3N0cmluZ3x1bmRlZmluZWR9ICAtIHRoZSB2YWx1ZSBvZiB0aGUgZGVmYXVsdCBvcHRpb24uXG4gKi9cbmV4cG9ydCBmdW5jdGlvbiBnZXREZWZhdWx0T3B0aW9uVmFsdWUoZmlsdGVyLCBvcHRpb25zKSB7XG5cdGNvbnN0IHsgZGVmYXVsdE9wdGlvbiB9ID0gZmlsdGVyLmlucHV0O1xuXHRpZiAoZmlsdGVyLmlucHV0LmRlZmF1bHRPcHRpb24pIHtcblx0XHRjb25zdCBvcHRpb24gPSBmaW5kKG9wdGlvbnMsIHsgdmFsdWU6IGRlZmF1bHRPcHRpb24gfSk7XG5cdFx0aWYgKCFvcHRpb24pIHtcblx0XHRcdC8qIGVzbGludC1kaXNhYmxlIG5vLWNvbnNvbGUgKi9cblx0XHRcdGNvbnNvbGUud2Fybihcblx0XHRcdFx0YGludmFsaWQgZGVmYXVsdE9wdGlvbiAke2RlZmF1bHRPcHRpb259IHN1cHBsaWVkIHRvICR7ZmlsdGVyLmxhYmVscy5hZGR9YFxuXHRcdFx0KTtcblx0XHRcdC8qIGVzbGludC1lbmFibGUgKi9cblx0XHRcdHJldHVybiB1bmRlZmluZWQ7XG5cdFx0fVxuXHRcdHJldHVybiBvcHRpb24udmFsdWU7XG5cdH1cblx0cmV0dXJuIGdldChvcHRpb25zLCBbMCwgJ3ZhbHVlJ10pO1xufVxuXG4vKipcbiAqIEdpdmVuIGFjdGl2ZUZpbHRlcnMsIGNyZWF0ZSBhIG5ldyBxdWVyeSBvYmplY3QgdG8gdXBkYXRlIHRoZSB1cmwuIFVzZSBwcmV2aW91c0ZpbHRlcnMgdG9cbiAqIFJlbW92ZSB1bnVzZWQgcGFyYW1zLlxuICpcbiAqIEBwYXJhbSB7QXJyYXl9IGFjdGl2ZUZpbHRlcnMgLSBBcnJheSBvZiBhY3RpdmVGaWx0ZXJzIHNob3duIGluIHRoZSBVSVxuICogQHBhcmFtIHtPYmplY3R9IHF1ZXJ5IC0gdGhlIGN1cnJlbnQgdXJsIHF1ZXJ5IG9iamVjdFxuICogQHBhcmFtIHtPYmplY3R9IGZpbHRlcnMgLSBjb25maWcgb2JqZWN0XG4gKiBAcmV0dXJuIHtPYmplY3R9IC0gcXVlcnkgb2JqZWN0IHJlcHJlc2VudGluZyB0aGUgbmV3IHBhcmFtZXRlcnNcbiAqL1xuZXhwb3J0IGZ1bmN0aW9uIGdldFF1ZXJ5RnJvbUFjdGl2ZUZpbHRlcnMoYWN0aXZlRmlsdGVycyA9IFtdLCBxdWVyeSwgZmlsdGVycykge1xuXHRjb25zdCBwcmV2aW91c0ZpbHRlcnMgPSBnZXRBY3RpdmVGaWx0ZXJzRnJvbVF1ZXJ5KHF1ZXJ5LCBmaWx0ZXJzKTtcblx0Y29uc3QgcHJldmlvdXNEYXRhID0gcHJldmlvdXNGaWx0ZXJzLnJlZHVjZSgoZGF0YSwgZmlsdGVyKSA9PiB7XG5cdFx0ZGF0YVtnZXRVcmxLZXkoZmlsdGVyLmtleSwgZmlsdGVyLnJ1bGUpXSA9IHVuZGVmaW5lZDtcblx0XHRyZXR1cm4gZGF0YTtcblx0fSwge30pO1xuXHRjb25zdCBuZXh0RGF0YSA9IGFjdGl2ZUZpbHRlcnMucmVkdWNlKChkYXRhLCBmaWx0ZXIpID0+IHtcblx0XHRpZiAoXG5cdFx0XHRmaWx0ZXIucnVsZSA9PT0gJ2JldHdlZW4nICYmXG5cdFx0XHQoIUFycmF5LmlzQXJyYXkoZmlsdGVyLnZhbHVlKSB8fFxuXHRcdFx0XHRmaWx0ZXIudmFsdWUuc29tZSgodmFsdWUpID0+ICF2YWx1ZSkpXG5cdFx0KSB7XG5cdFx0XHRyZXR1cm4gZGF0YTtcblx0XHR9XG5cblx0XHRpZiAoZmlsdGVyLnZhbHVlKSB7XG5cdFx0XHRjb25zdCB1cmxLZXkgPSBnZXRVcmxLZXkoZmlsdGVyLmtleSwgZmlsdGVyLnJ1bGUpO1xuXG5cdFx0XHRpZiAoZmlsdGVyc1tmaWx0ZXIua2V5XSAmJiBmaWx0ZXJzW2ZpbHRlci5rZXldLmFsbG93TXVsdGlwbGUpIHtcblx0XHRcdFx0aWYgKCFkYXRhLmhhc093blByb3BlcnR5KHVybEtleSkpIHtcblx0XHRcdFx0XHRkYXRhW3VybEtleV0gPSBbXTtcblx0XHRcdFx0fVxuXHRcdFx0XHRkYXRhW3VybEtleV0ucHVzaChmaWx0ZXIudmFsdWUpO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0ZGF0YVt1cmxLZXldID0gZmlsdGVyLnZhbHVlO1xuXHRcdFx0fVxuXHRcdH1cblx0XHRyZXR1cm4gZGF0YTtcblx0fSwge30pO1xuXG5cdHJldHVybiB7IC4uLnByZXZpb3VzRGF0YSwgLi4ubmV4dERhdGEgfTtcbn1cblxuLyoqXG4gKiBHZXQgdGhlIHVybCBxdWVyeSBrZXkgZnJvbSB0aGUgZmlsdGVyIGtleSBhbmQgcnVsZS5cbiAqXG4gKiBAcGFyYW0ge3N0cmluZ30ga2V5IC0gZmlsdGVyIGtleS5cbiAqIEBwYXJhbSB7c3RyaW5nfSBydWxlIC0gZmlsdGVyIHJ1bGUuXG4gKiBAcmV0dXJuIHtzdHJpbmd9IC0gdXJsIHF1ZXJ5IGtleS5cbiAqL1xuZXhwb3J0IGZ1bmN0aW9uIGdldFVybEtleShrZXksIHJ1bGUpIHtcblx0aWYgKHJ1bGUgJiYgcnVsZS5sZW5ndGgpIHtcblx0XHRyZXR1cm4gYCR7a2V5fV8ke3J1bGV9YDtcblx0fVxuXHRyZXR1cm4ga2V5O1xufVxuIiwiLyoqXG4gKiBFeHRlcm5hbCBkZXBlbmRlbmNpZXNcbiAqL1xuY29uc3QgeyBjcmVhdGVCcm93c2VySGlzdG9yeSB9ID0gcmVxdWlyZSgnaGlzdG9yeScpO1xuY29uc3QgeyBwYXJzZSB9ID0gcmVxdWlyZSgncXMnKTtcblxubGV0IF9oaXN0b3J5O1xuXG5mdW5jdGlvbiBnZXRIaXN0b3J5KCkge1xuXHRpZiAoIV9oaXN0b3J5KSB7XG5cdFx0Y29uc3QgYnJvd3Nlckhpc3RvcnkgPSBjcmVhdGVCcm93c2VySGlzdG9yeSgpO1xuXHRcdF9oaXN0b3J5ID0ge1xuXHRcdFx0Z2V0IGFjdGlvbigpIHtcblx0XHRcdFx0cmV0dXJuIGJyb3dzZXJIaXN0b3J5LmFjdGlvbjtcblx0XHRcdH0sXG5cdFx0XHRnZXQgbG9jYXRpb24oKSB7XG5cdFx0XHRcdGNvbnN0IHsgbG9jYXRpb24gfSA9IGJyb3dzZXJIaXN0b3J5O1xuXHRcdFx0XHRjb25zdCBxdWVyeSA9IHBhcnNlKGxvY2F0aW9uLnNlYXJjaC5zdWJzdHJpbmcoMSkpO1xuXHRcdFx0XHRsZXQgcGF0aG5hbWU7XG5cblx0XHRcdFx0aWYgKHF1ZXJ5ICYmIHR5cGVvZiBxdWVyeS5wYXRoID09PSAnc3RyaW5nJykge1xuXHRcdFx0XHRcdHBhdGhuYW1lID0gcXVlcnkucGF0aDtcblx0XHRcdFx0fSBlbHNlIGlmIChcblx0XHRcdFx0XHRxdWVyeSAmJlxuXHRcdFx0XHRcdHF1ZXJ5LnBhdGggJiZcblx0XHRcdFx0XHR0eXBlb2YgcXVlcnkucGF0aCAhPT0gJ3N0cmluZydcblx0XHRcdFx0KSB7XG5cdFx0XHRcdFx0Y29uc29sZS53YXJuKFxuXHRcdFx0XHRcdFx0YFF1ZXJ5IHBhdGggcGFyYW1ldGVyIHNob3VsZCBiZSBhIHN0cmluZyBidXQgaW5zdGVhZCB3YXM6ICR7cXVlcnkucGF0aH0sIHVuZGVmaW5lZCBiZWhhdmlvdXIgbWF5IG9jY3VyLmBcblx0XHRcdFx0XHQpO1xuXHRcdFx0XHRcdHBhdGhuYW1lID0gcXVlcnkucGF0aDtcblx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRwYXRobmFtZSA9ICcvJztcblx0XHRcdFx0fVxuXHRcdFx0XHRyZXR1cm4ge1xuXHRcdFx0XHRcdC4uLmxvY2F0aW9uLFxuXHRcdFx0XHRcdHBhdGhuYW1lLFxuXHRcdFx0XHR9O1xuXHRcdFx0fSxcblx0XHRcdGNyZWF0ZUhyZWY6IGJyb3dzZXJIaXN0b3J5LmNyZWF0ZUhyZWYsXG5cdFx0XHRwdXNoOiBicm93c2VySGlzdG9yeS5wdXNoLFxuXHRcdFx0cmVwbGFjZTogYnJvd3Nlckhpc3RvcnkucmVwbGFjZSxcblx0XHRcdGdvOiBicm93c2VySGlzdG9yeS5nbyxcblx0XHRcdGJhY2s6IGJyb3dzZXJIaXN0b3J5LmJhY2ssXG5cdFx0XHRmb3J3YXJkOiBicm93c2VySGlzdG9yeS5mb3J3YXJkLFxuXHRcdFx0YmxvY2s6IGJyb3dzZXJIaXN0b3J5LmJsb2NrLFxuXHRcdFx0bGlzdGVuKGxpc3RlbmVyKSB7XG5cdFx0XHRcdHJldHVybiBicm93c2VySGlzdG9yeS5saXN0ZW4oKCkgPT4ge1xuXHRcdFx0XHRcdGxpc3RlbmVyKHtcblx0XHRcdFx0XHRcdGFjdGlvbjogdGhpcy5hY3Rpb24sXG5cdFx0XHRcdFx0XHRsb2NhdGlvbjogdGhpcy5sb2NhdGlvbixcblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0fSk7XG5cdFx0XHR9LFxuXHRcdH07XG5cdH1cblx0cmV0dXJuIF9oaXN0b3J5O1xufVxuXG5leHBvcnQgeyBnZXRIaXN0b3J5IH07XG4iLCIvKipcbiAqIEV4dGVybmFsIGRlcGVuZGVuY2llc1xuICovXG4vKipcbiAqIFdvcmRQcmVzcyBkZXBlbmRlbmNpZXNcbiAqL1xuaW1wb3J0IHsgYWRkUXVlcnlBcmdzIH0gZnJvbSAnQHdvcmRwcmVzcy91cmwnO1xuaW1wb3J0IHsgcGFyc2UgfSBmcm9tICdxcyc7XG5pbXBvcnQgeyBpZGVudGl0eSwgb21pdCwgcGlja0J5LCB1bmlxLCBpc0FycmF5LCBoYXMsIGlzRXF1YWwgfSBmcm9tICdsb2Rhc2gnO1xuXG4vKipcbiAqIEludGVybmFsIGRlcGVuZGVuY2llc1xuICovXG5pbXBvcnQgeyBnZXRIaXN0b3J5IH0gZnJvbSAnLi9oaXN0b3J5JztcbmltcG9ydCAqIGFzIG5hdlV0aWxzIGZyb20gJy4vaW5kZXgnO1xuLy8gRm9yIHRoZSBhYm92ZSwgaW1wb3J0IHRoZSBtb2R1bGUgaW50byBpdHNlbGYuIEZ1bmN0aW9ucyBjb25zdW1lZCBmcm9tIHRoaXMgaW1wb3J0IGNhbiBiZSBtb2NrZWQgaW4gdGVzdHMuXG5cbi8vIEV4cG9zZSBoaXN0b3J5IHNvIGFsbCB1c2VzIGdldCB0aGUgc2FtZSBoaXN0b3J5IG9iamVjdC5cbmV4cG9ydCB7IGdldEhpc3RvcnkgfTtcblxuLyoqXG4gKiBHZXQgdGhlIGN1cnJlbnQgcGF0aCBmcm9tIGhpc3RvcnkuXG4gKlxuICogQHJldHVybiB7c3RyaW5nfSAgQ3VycmVudCBwYXRoLlxuICovXG5leHBvcnQgY29uc3QgZ2V0UGF0aCA9ICgpID0+IGdldEhpc3RvcnkoKS5sb2NhdGlvbi5wYXRobmFtZTtcblxuLyoqXG4gKiBHZXQgdGhlIHBhZ2UgZnJvbSBoaXN0b3J5LlxuICpcbiAqIEByZXR1cm4ge3N0cmluZ30gUXVlcnkgU3RyaW5nXG4gKi9cbmV4cG9ydCBjb25zdCBnZXRQYWdlID0gKCkgPT4ge1xuXHRjb25zdCBzZWFyY2ggPSBnZXRIaXN0b3J5KCkubG9jYXRpb24uc2VhcmNoO1xuXHRpZiAoc2VhcmNoLmxlbmd0aCkge1xuXHRcdGNvbnN0IHF1ZXJ5ID0gcGFyc2Uoc2VhcmNoLnN1YnN0cmluZygxKSkgfHwge307XG5cdFx0Y29uc3QgeyBwYWdlIH0gPSBxdWVyeTtcblx0XHRyZXR1cm4gcGFnZTtcblx0fVxuXHRyZXR1cm4gbnVsbDtcbn07XG5cbi8qKlxuICogUmV0cmlldmUgYSBzdHJpbmcgJ25hbWUnIHJlcHJlc2VudGluZyB0aGUgY3VycmVudCBzY3JlZW5cbiAqXG4gKiBAcGFyYW0ge09iamVjdH0gcGF0aCBQYXRoIHRvIHJlc29sdmUsIGRlZmF1bHQgdG8gY3VycmVudFxuICogQHJldHVybiB7c3RyaW5nfSBTY3JlZW4gbmFtZVxuICovXG5leHBvcnQgY29uc3QgZ2V0U2NyZWVuRnJvbVBhdGggPSAocGF0aCA9IGdldFBhdGgoKSkgPT4ge1xuXHRyZXR1cm4gcGF0aCA9PT0gJy8nXG5cdFx0PyAnb3ZlcnZpZXcnXG5cdFx0OiBwYXRoLnJlcGxhY2UoJy9lYWNjb3VudGluZycsICcnKS5yZXBsYWNlKCcvJywgJycpO1xufTtcblxuLyoqXG4gKiBHZXQgYW4gYXJyYXkgb2YgSURzIGZyb20gYSBjb21tYS1zZXBhcmF0ZWQgcXVlcnkgcGFyYW1ldGVyLlxuICpcbiAqIEBwYXJhbSB7c3RyaW5nfSBxdWVyeVN0cmluZyBzdHJpbmcgdmFsdWUgZXh0cmFjdGVkIGZyb20gVVJMLlxuICogQHJldHVybiB7QXJyYXl9IExpc3Qgb2YgSURzIGNvbnZlcnRlZCB0byBudW1iZXJzLlxuICovXG5leHBvcnQgZnVuY3Rpb24gZ2V0SWRzRnJvbVF1ZXJ5KHF1ZXJ5U3RyaW5nID0gJycpIHtcblx0cmV0dXJuIHVuaXEoXG5cdFx0cXVlcnlTdHJpbmdcblx0XHRcdC5zcGxpdCgnLCcpXG5cdFx0XHQubWFwKChpZCkgPT4gcGFyc2VJbnQoaWQsIDEwKSlcblx0XHRcdC5maWx0ZXIoQm9vbGVhbilcblx0KTtcbn1cblxuLyoqXG4gKiBHZXQgYW4gSUQgZnJvbSBhIHF1ZXJ5IHBhcmFtZXRlci5cbiAqXG4gKiBAcmV0dXJuIHtudW1iZXJ9IExpc3Qgb2YgSURzIGNvbnZlcnRlZCB0byBudW1iZXJzLlxuICovXG5leHBvcnQgZnVuY3Rpb24gZ2V0SWRGcm9tUXVlcnkoa2V5ID0gJ2lkJywgcXVlcnkgPSBnZXRRdWVyeSgpKSB7XG5cdHJldHVybiBwYXJzZUludChxdWVyeVtrZXldIHx8IDAsIDEwKTtcbn1cblxuLyoqXG4gKiBHZXQgYW4gYXJyYXkgb2Ygc2VhcmNoZWQgd29yZHMgZ2l2ZW4gYSBxdWVyeS5cbiAqXG4gKiBAcGFyYW0ge09iamVjdH0gcXVlcnkgUXVlcnkgb2JqZWN0LlxuICogQHJldHVybiB7QXJyYXl9IExpc3Qgb2Ygc2VhcmNoIHdvcmRzLlxuICovXG5leHBvcnQgZnVuY3Rpb24gZ2V0U2VhcmNoV29yZHMocXVlcnkgPSBuYXZVdGlscy5nZXRRdWVyeSgpKSB7XG5cdGlmICh0eXBlb2YgcXVlcnkgIT09ICdvYmplY3QnKSB7XG5cdFx0dGhyb3cgbmV3IEVycm9yKFxuXHRcdFx0J0ludmFsaWQgcGFyYW1ldGVyIHBhc3NlZCB0byBnZXRTZWFyY2hXb3JkcywgaXQgZXhwZWN0cyBhbiBvYmplY3Qgb3Igbm8gcGFyYW1ldGVycy4nXG5cdFx0KTtcblx0fVxuXHRjb25zdCB7IHNlYXJjaCB9ID0gcXVlcnk7XG5cdGlmICghc2VhcmNoKSB7XG5cdFx0cmV0dXJuIFtdO1xuXHR9XG5cdGlmICh0eXBlb2Ygc2VhcmNoICE9PSAnc3RyaW5nJykge1xuXHRcdHRocm93IG5ldyBFcnJvcihcblx0XHRcdFwiSW52YWxpZCAnc2VhcmNoJyB0eXBlLiBnZXRTZWFyY2hXb3JkcyBleHBlY3RzIHF1ZXJ5J3MgJ3NlYXJjaCcgcHJvcGVydHkgdG8gYmUgYSBzdHJpbmcuXCJcblx0XHQpO1xuXHR9XG5cdHJldHVybiBzZWFyY2hcblx0XHQuc3BsaXQoJywnKVxuXHRcdC5tYXAoKHNlYXJjaFdvcmQpID0+IHNlYXJjaFdvcmQucmVwbGFjZSgnJTJDJywgJywnKSk7XG59XG5cbi8qKlxuICogUmV0dXJuIGEgVVJMIHdpdGggc2V0IHF1ZXJ5IHBhcmFtZXRlcnMuXG4gKlxuICogQHBhcmFtIHtPYmplY3R9IHF1ZXJ5IG9iamVjdCBvZiBwYXJhbXMgdG8gYmUgdXBkYXRlZC5cbiAqIEBwYXJhbSB7c3RyaW5nfSBwYXRoIFJlbGF0aXZlIHBhdGggKGRlZmF1bHRzIHRvIGN1cnJlbnQgcGF0aCkuXG4gKiBAcGFyYW0ge09iamVjdH0gY3VycmVudFF1ZXJ5IG9iamVjdCBvZiBjdXJyZW50IHF1ZXJ5IHBhcmFtcyAoZGVmYXVsdHMgdG8gY3VycmVudCBxdWVyeXN0cmluZykuXG4gKiBAcmV0dXJuIHtzdHJpbmd9ICBVcGRhdGVkIFVSTCBtZXJnaW5nIHF1ZXJ5IHBhcmFtcyBpbnRvIGV4aXN0aW5nIHBhcmFtcy5cbiAqL1xuZXhwb3J0IGZ1bmN0aW9uIGdlbmVyYXRlUGF0aChcblx0cXVlcnksXG5cdHBhdGggPSBnZXRQYXRoKCksXG5cdGN1cnJlbnRRdWVyeSA9IGdldFF1ZXJ5KClcbikge1xuXHRjb25zdCBwYWdlID0gZ2V0UGFnZSgpO1xuXHRjb25zdCBhcmdzID0geyBwYWdlIH07XG5cdGlmIChwYXRoICE9PSAnLycpIHtcblx0XHRhcmdzLnBhdGggPSBwYXRoO1xuXHR9XG5cblx0cmV0dXJuIGFkZFF1ZXJ5QXJncyhcblx0XHQnYWRtaW4ucGhwJyxcblx0XHRwaWNrQnkoeyAuLi5hcmdzLCAuLi5jdXJyZW50UXVlcnksIC4uLnF1ZXJ5IH0sIGlkZW50aXR5KVxuXHQpO1xufVxuXG4vKipcbiAqIEdldCB0aGUgY3VycmVudCBxdWVyeSBzdHJpbmcsIHBhcnNlZCBpbnRvIGFuIG9iamVjdCwgZnJvbSBoaXN0b3J5LlxuICpcbiAqIEByZXR1cm4ge09iamVjdH0gIEN1cnJlbnQgcXVlcnkgb2JqZWN0LCBkZWZhdWx0cyB0byBlbXB0eSBvYmplY3QuXG4gKi9cbmV4cG9ydCBmdW5jdGlvbiBnZXRRdWVyeSgpIHtcblx0Y29uc3Qgc2VhcmNoID0gZ2V0SGlzdG9yeSgpLmxvY2F0aW9uLnNlYXJjaDtcblx0aWYgKHNlYXJjaC5sZW5ndGgpIHtcblx0XHRyZXR1cm4gb21pdChwYXJzZShzZWFyY2guc3Vic3RyaW5nKDEpKSB8fCB7fSwgWydwYWdlJywgJ3BhdGgnXSk7XG5cdH1cblx0cmV0dXJuIHt9O1xufVxuXG4vKipcbiAqIEdldCB0YWJsZSBxdWVyeS5cbiAqXG4gKiBAcGFyYW0ge0FycmF5fE9iamVjdH0gd2hpdGVsaXN0cyBFeHRyYSBwYXJhbXMuXG4gKiBAcGFyYW0ge09iamVjdH0gZGVmYXVsdHMgRXh0cmEgcGFyYW1zLlxuICogQHBhcmFtIHtGdW5jdGlvbn0gZmlsdGVyIEV4dHJhIHBhcmFtcy5cbiAqIEBwYXJhbSB7T2JqZWN0fSBxdWVyeSBFeHRyYSBwYXJhbXMuXG4gKiBAcmV0dXJuIHt7fX0gcXVlcnkuXG4gKi9cbmV4cG9ydCBmdW5jdGlvbiBnZXRUYWJsZVF1ZXJ5KFxuXHR3aGl0ZWxpc3RzID0ge30sXG5cdGRlZmF1bHRzID0ge30sXG5cdGZpbHRlciA9ICh4KSA9PiB4LFxuXHRxdWVyeSA9IGdldFF1ZXJ5KClcbikge1xuXHRpZiAoaXNBcnJheSh3aGl0ZWxpc3RzKSkge1xuXHRcdHdoaXRlbGlzdHMgPSB3aGl0ZWxpc3RzLnJlZHVjZSgoYWNjLCB3aGl0ZWxpc3QpID0+IHtcblx0XHRcdC8vIGVzbGludC1kaXNhYmxlLW5leHQtbGluZSBuby11bnVzZWQtdmFyc1xuXHRcdFx0cmV0dXJuIHsgLi4uYWNjLCBbd2hpdGVsaXN0XTogKHgsIHF1ZXJ5KSA9PiB4IH07XG5cdFx0fSwge30pO1xuXHR9XG5cblx0ZGVmYXVsdHMgPSB7XG5cdFx0Li4uZGVmYXVsdHMsXG5cdFx0b3JkZXJieTogJ2lkJyxcblx0XHRvcmRlcjogJ2Rlc2MnLFxuXHRcdHBlcl9wYWdlOiAyMCxcblx0XHRwYWdlZDogMSxcblx0fTtcblxuXHR3aGl0ZWxpc3RzID0ge1xuXHRcdC4uLndoaXRlbGlzdHMsXG5cdFx0c2VhcmNoOiAoc2VhcmNoLCBxdWVyeSkgPT4gcXVlcnkuc2VhcmNoIHx8ICcnLFxuXHRcdHBhZ2VkOiAocGFnZWQsIHF1ZXJ5KSA9PiBwYXJzZUludChxdWVyeS5wYWdlZCwgMTApIHx8IDEsXG5cdFx0b3JkZXJieTogKG9yZGVyYnksIHF1ZXJ5KSA9PiBxdWVyeS5vcmRlcmJ5IHx8IGRlZmF1bHRzLm9yZGVyYnksXG5cdFx0b3JkZXI6IChvcmRlciwgcXVlcnkpID0+XG5cdFx0XHRxdWVyeS5vcmRlciA9PT0gJ2FzYycgPyAnYXNjJyA6IGRlZmF1bHRzLm9yZGVyLFxuXHR9O1xuXHRxdWVyeSA9IE9iamVjdC5rZXlzKHF1ZXJ5KS5yZWR1Y2UoKGFjYywgcXVlcnlLZXkpID0+IHtcblx0XHRpZiAoaGFzKHdoaXRlbGlzdHMsIFtxdWVyeUtleV0pKSB7XG5cdFx0XHRjb25zdCBxdWVyeVZhbHVlID0gd2hpdGVsaXN0c1txdWVyeUtleV0ocXVlcnlbcXVlcnlLZXldLCBxdWVyeSk7XG5cdFx0XHRpZiAoXG5cdFx0XHRcdGhhcyhkZWZhdWx0cywgW3F1ZXJ5S2V5XSkgJiZcblx0XHRcdFx0aXNFcXVhbChkZWZhdWx0c1txdWVyeUtleV0sIHF1ZXJ5VmFsdWUpXG5cdFx0XHQpIHtcblx0XHRcdFx0cmV0dXJuIGFjYztcblx0XHRcdH1cblx0XHRcdGFjYyA9IHtcblx0XHRcdFx0Li4uYWNjLFxuXHRcdFx0XHRbcXVlcnlLZXldOiBxdWVyeVZhbHVlLFxuXHRcdFx0fTtcblx0XHR9XG5cblx0XHRyZXR1cm4gYWNjO1xuXHR9LCB7fSk7XG5cdHJldHVybiBmaWx0ZXIocGlja0J5KHF1ZXJ5LCBpZGVudGl0eSkpO1xufVxuXG4vKipcbiAqIFRoaXMgZnVuY3Rpb24gcmV0dXJucyBhbiBldmVudCBoYW5kbGVyIGZvciB0aGUgZ2l2ZW4gYHBhcmFtYFxuICpcbiAqIEBwYXJhbSB7c3RyaW5nfSBwYXJhbSBUaGUgcGFyYW1ldGVyIGluIHRoZSBxdWVyeXN0cmluZyB3aGljaCBzaG91bGQgYmUgdXBkYXRlZCAoZXggYHBhZ2VgLCBgcGVyX3BhZ2VgKVxuICogQHBhcmFtIHtzdHJpbmd9IHBhdGggUmVsYXRpdmUgcGF0aCAoZGVmYXVsdHMgdG8gY3VycmVudCBwYXRoKS5cbiAqIEBwYXJhbSB7c3RyaW5nfSBxdWVyeSBvYmplY3Qgb2YgY3VycmVudCBxdWVyeSBwYXJhbXMgKGRlZmF1bHRzIHRvIGN1cnJlbnQgcXVlcnlzdHJpbmcpLlxuICogQHJldHVybiB7RnVuY3Rpb259IEEgY2FsbGJhY2sgd2hpY2ggd2lsbCB1cGRhdGUgYHBhcmFtYCB0byB0aGUgcGFzc2VkIHZhbHVlIHdoZW4gY2FsbGVkLlxuICovXG5leHBvcnQgZnVuY3Rpb24gb25RdWVyeUNoYW5nZShwYXJhbSwgcGF0aCA9IGdldFBhdGgoKSwgcXVlcnkgPSBnZXRRdWVyeSgpKSB7XG5cdHN3aXRjaCAocGFyYW0pIHtcblx0XHRjYXNlICdzb3J0Jzpcblx0XHRcdHJldHVybiAoc29ydCkgPT4gdXBkYXRlUXVlcnlTdHJpbmcoc29ydCwgcGF0aCwgcXVlcnkpO1xuXHRcdGRlZmF1bHQ6XG5cdFx0XHRyZXR1cm4gKHZhbHVlKSA9PlxuXHRcdFx0XHR1cGRhdGVRdWVyeVN0cmluZyh7IFtwYXJhbV06IHZhbHVlIH0sIHBhdGgsIHF1ZXJ5KTtcblx0fVxufVxuXG4vKipcbiAqIFVwZGF0ZXMgdGhlIHF1ZXJ5IHBhcmFtZXRlcnMgb2YgdGhlIGN1cnJlbnQgcGFnZS5cbiAqXG4gKiBAcGFyYW0ge09iamVjdH0gcXVlcnkgb2JqZWN0IG9mIHBhcmFtcyB0byBiZSB1cGRhdGVkLlxuICogQHBhcmFtIHtzdHJpbmd9IHBhdGggUmVsYXRpdmUgcGF0aCAoZGVmYXVsdHMgdG8gY3VycmVudCBwYXRoKS5cbiAqIEBwYXJhbSB7T2JqZWN0fSBjdXJyZW50UXVlcnkgb2JqZWN0IG9mIGN1cnJlbnQgcXVlcnkgcGFyYW1zIChkZWZhdWx0cyB0byBjdXJyZW50IHF1ZXJ5c3RyaW5nKS5cbiAqL1xuZXhwb3J0IGZ1bmN0aW9uIHVwZGF0ZVF1ZXJ5U3RyaW5nKFxuXHRxdWVyeSxcblx0cGF0aCA9IGdldFBhdGgoKSxcblx0Y3VycmVudFF1ZXJ5ID0gZ2V0UXVlcnkoKVxuKSB7XG5cdGNvbnN0IG5ld1BhdGggPSBnZW5lcmF0ZVBhdGgocXVlcnksIHBhdGgsIGN1cnJlbnRRdWVyeSk7XG5cdGdldEhpc3RvcnkoKS5wdXNoKG5ld1BhdGgpO1xufVxuXG4vKipcbiAqIFJlbW92ZSBxdWVyeSBhZ3NcbiAqXG4gKiBAcGFyYW0ge3N0cmluZyB8IEFycmF5fWtleVxuICogQHBhcmFtIHtPYmplY3R9IHF1ZXJ5XG4gKi9cbmV4cG9ydCBmdW5jdGlvbiByZW1vdmVRdWVyeUFyZ3Moa2V5LCBxdWVyeSA9IGdldFF1ZXJ5KCkpIHtcblx0cmV0dXJuIG9taXQocXVlcnksIEFycmF5LmlzQXJyYXkoa2V5KSA/IGtleSA6IFtrZXldKTtcbn1cblxuLyoqXG4gKiBBZGRzIGEgbGlzdGVuZXIgdGhhdCBydW5zIG9uIGhpc3RvcnkgY2hhbmdlLlxuICpcbiAqIEBwYXJhbSB7RnVuY3Rpb259IGxpc3RlbmVyIExpc3RlbmVyIHRvIGFkZCBvbiBoaXN0b3J5IGNoYW5nZS5cbiAqIEByZXR1cm4ge0Z1bmN0aW9ufSBGdW5jdGlvbiB0byByZW1vdmUgbGlzdGVuZXJzLlxuICovXG5leHBvcnQgY29uc3QgYWRkSGlzdG9yeUxpc3RlbmVyID0gKGxpc3RlbmVyKSA9PiB7XG5cdC8vIE1vbmtleSBwYXRjaCBwdXNoU3RhdGUgdG8gYWxsb3cgdHJpZ2dlciB0aGUgcHVzaHN0YXRlIGV2ZW50IGxpc3RlbmVyLlxuXHRpZiAod2luZG93LndjTmF2aWdhdGlvbiAmJiAhd2luZG93LndjTmF2aWdhdGlvbi5oaXN0b3J5UGF0Y2hlZCkge1xuXHRcdCgoaGlzdG9yeSkgPT4ge1xuXHRcdFx0LyogZ2xvYmFsIEN1c3RvbUV2ZW50ICovXG5cdFx0XHRjb25zdCBwdXNoU3RhdGUgPSBoaXN0b3J5LnB1c2hTdGF0ZTtcblx0XHRcdGNvbnN0IHJlcGxhY2VTdGF0ZSA9IGhpc3RvcnkucmVwbGFjZVN0YXRlO1xuXHRcdFx0aGlzdG9yeS5wdXNoU3RhdGUgPSBmdW5jdGlvbiAoc3RhdGUpIHtcblx0XHRcdFx0Y29uc3QgcHVzaFN0YXRlRXZlbnQgPSBuZXcgQ3VzdG9tRXZlbnQoJ3B1c2hzdGF0ZScsIHtcblx0XHRcdFx0XHRzdGF0ZSxcblx0XHRcdFx0fSk7XG5cdFx0XHRcdHdpbmRvdy5kaXNwYXRjaEV2ZW50KHB1c2hTdGF0ZUV2ZW50KTtcblx0XHRcdFx0cmV0dXJuIHB1c2hTdGF0ZS5hcHBseShoaXN0b3J5LCBhcmd1bWVudHMpO1xuXHRcdFx0fTtcblx0XHRcdGhpc3RvcnkucmVwbGFjZVN0YXRlID0gZnVuY3Rpb24gKHN0YXRlKSB7XG5cdFx0XHRcdGNvbnN0IHJlcGxhY2VTdGF0ZUV2ZW50ID0gbmV3IEN1c3RvbUV2ZW50KCdyZXBsYWNlc3RhdGUnLCB7XG5cdFx0XHRcdFx0c3RhdGUsXG5cdFx0XHRcdH0pO1xuXHRcdFx0XHR3aW5kb3cuZGlzcGF0Y2hFdmVudChyZXBsYWNlU3RhdGVFdmVudCk7XG5cdFx0XHRcdHJldHVybiByZXBsYWNlU3RhdGUuYXBwbHkoaGlzdG9yeSwgYXJndW1lbnRzKTtcblx0XHRcdH07XG5cdFx0XHR3aW5kb3cud2NOYXZpZ2F0aW9uLmhpc3RvcnlQYXRjaGVkID0gdHJ1ZTtcblx0XHR9KSh3aW5kb3cuaGlzdG9yeSk7XG5cdH1cblx0Lyplc2xpbnQtZGlzYWJsZSBAd29yZHByZXNzL25vLWdsb2JhbC1ldmVudC1saXN0ZW5lciAqL1xuXHR3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lcigncG9wc3RhdGUnLCBsaXN0ZW5lcik7XG5cdHdpbmRvdy5hZGRFdmVudExpc3RlbmVyKCdwdXNoc3RhdGUnLCBsaXN0ZW5lcik7XG5cdHdpbmRvdy5hZGRFdmVudExpc3RlbmVyKCdyZXBsYWNlc3RhdGUnLCBsaXN0ZW5lcik7XG5cblx0cmV0dXJuICgpID0+IHtcblx0XHR3aW5kb3cucmVtb3ZlRXZlbnRMaXN0ZW5lcigncG9wc3RhdGUnLCBsaXN0ZW5lcik7XG5cdFx0d2luZG93LnJlbW92ZUV2ZW50TGlzdGVuZXIoJ3B1c2hzdGF0ZScsIGxpc3RlbmVyKTtcblx0XHR3aW5kb3cucmVtb3ZlRXZlbnRMaXN0ZW5lcigncmVwbGFjZXN0YXRlJywgbGlzdGVuZXIpO1xuXHR9O1xuXG5cdC8qIGVzbGludC1lbmFibGUgQHdvcmRwcmVzcy9uby1nbG9iYWwtZXZlbnQtbGlzdGVuZXIgKi9cbn07XG5cbmV4cG9ydCAqIGZyb20gJy4vZmlsdGVycyc7XG4iLCJpbXBvcnQgX2V4dGVuZHMgZnJvbSAnQGJhYmVsL3J1bnRpbWUvaGVscGVycy9lc20vZXh0ZW5kcyc7XG5cbi8qKlxyXG4gKiBBY3Rpb25zIHJlcHJlc2VudCB0aGUgdHlwZSBvZiBjaGFuZ2UgdG8gYSBsb2NhdGlvbiB2YWx1ZS5cclxuICpcclxuICogQHNlZSBodHRwczovL2dpdGh1Yi5jb20vcmVtaXgtcnVuL2hpc3RvcnkvdHJlZS9tYWluL2RvY3MvYXBpLXJlZmVyZW5jZS5tZCNhY3Rpb25cclxuICovXG52YXIgQWN0aW9uO1xuXG4oZnVuY3Rpb24gKEFjdGlvbikge1xuICAvKipcclxuICAgKiBBIFBPUCBpbmRpY2F0ZXMgYSBjaGFuZ2UgdG8gYW4gYXJiaXRyYXJ5IGluZGV4IGluIHRoZSBoaXN0b3J5IHN0YWNrLCBzdWNoXHJcbiAgICogYXMgYSBiYWNrIG9yIGZvcndhcmQgbmF2aWdhdGlvbi4gSXQgZG9lcyBub3QgZGVzY3JpYmUgdGhlIGRpcmVjdGlvbiBvZiB0aGVcclxuICAgKiBuYXZpZ2F0aW9uLCBvbmx5IHRoYXQgdGhlIGN1cnJlbnQgaW5kZXggY2hhbmdlZC5cclxuICAgKlxyXG4gICAqIE5vdGU6IFRoaXMgaXMgdGhlIGRlZmF1bHQgYWN0aW9uIGZvciBuZXdseSBjcmVhdGVkIGhpc3Rvcnkgb2JqZWN0cy5cclxuICAgKi9cbiAgQWN0aW9uW1wiUG9wXCJdID0gXCJQT1BcIjtcbiAgLyoqXHJcbiAgICogQSBQVVNIIGluZGljYXRlcyBhIG5ldyBlbnRyeSBiZWluZyBhZGRlZCB0byB0aGUgaGlzdG9yeSBzdGFjaywgc3VjaCBhcyB3aGVuXHJcbiAgICogYSBsaW5rIGlzIGNsaWNrZWQgYW5kIGEgbmV3IHBhZ2UgbG9hZHMuIFdoZW4gdGhpcyBoYXBwZW5zLCBhbGwgc3Vic2VxdWVudFxyXG4gICAqIGVudHJpZXMgaW4gdGhlIHN0YWNrIGFyZSBsb3N0LlxyXG4gICAqL1xuXG4gIEFjdGlvbltcIlB1c2hcIl0gPSBcIlBVU0hcIjtcbiAgLyoqXHJcbiAgICogQSBSRVBMQUNFIGluZGljYXRlcyB0aGUgZW50cnkgYXQgdGhlIGN1cnJlbnQgaW5kZXggaW4gdGhlIGhpc3Rvcnkgc3RhY2tcclxuICAgKiBiZWluZyByZXBsYWNlZCBieSBhIG5ldyBvbmUuXHJcbiAgICovXG5cbiAgQWN0aW9uW1wiUmVwbGFjZVwiXSA9IFwiUkVQTEFDRVwiO1xufSkoQWN0aW9uIHx8IChBY3Rpb24gPSB7fSkpO1xuXG52YXIgcmVhZE9ubHkgPSBwcm9jZXNzLmVudi5OT0RFX0VOViAhPT0gXCJwcm9kdWN0aW9uXCIgPyBmdW5jdGlvbiAob2JqKSB7XG4gIHJldHVybiBPYmplY3QuZnJlZXplKG9iaik7XG59IDogZnVuY3Rpb24gKG9iaikge1xuICByZXR1cm4gb2JqO1xufTtcblxuZnVuY3Rpb24gd2FybmluZyhjb25kLCBtZXNzYWdlKSB7XG4gIGlmICghY29uZCkge1xuICAgIC8vIGVzbGludC1kaXNhYmxlLW5leHQtbGluZSBuby1jb25zb2xlXG4gICAgaWYgKHR5cGVvZiBjb25zb2xlICE9PSAndW5kZWZpbmVkJykgY29uc29sZS53YXJuKG1lc3NhZ2UpO1xuXG4gICAgdHJ5IHtcbiAgICAgIC8vIFdlbGNvbWUgdG8gZGVidWdnaW5nIGhpc3RvcnkhXG4gICAgICAvL1xuICAgICAgLy8gVGhpcyBlcnJvciBpcyB0aHJvd24gYXMgYSBjb252ZW5pZW5jZSBzbyB5b3UgY2FuIG1vcmUgZWFzaWx5XG4gICAgICAvLyBmaW5kIHRoZSBzb3VyY2UgZm9yIGEgd2FybmluZyB0aGF0IGFwcGVhcnMgaW4gdGhlIGNvbnNvbGUgYnlcbiAgICAgIC8vIGVuYWJsaW5nIFwicGF1c2Ugb24gZXhjZXB0aW9uc1wiIGluIHlvdXIgSmF2YVNjcmlwdCBkZWJ1Z2dlci5cbiAgICAgIHRocm93IG5ldyBFcnJvcihtZXNzYWdlKTsgLy8gZXNsaW50LWRpc2FibGUtbmV4dC1saW5lIG5vLWVtcHR5XG4gICAgfSBjYXRjaCAoZSkge31cbiAgfVxufVxuXG52YXIgQmVmb3JlVW5sb2FkRXZlbnRUeXBlID0gJ2JlZm9yZXVubG9hZCc7XG52YXIgSGFzaENoYW5nZUV2ZW50VHlwZSA9ICdoYXNoY2hhbmdlJztcbnZhciBQb3BTdGF0ZUV2ZW50VHlwZSA9ICdwb3BzdGF0ZSc7XG4vKipcclxuICogQnJvd3NlciBoaXN0b3J5IHN0b3JlcyB0aGUgbG9jYXRpb24gaW4gcmVndWxhciBVUkxzLiBUaGlzIGlzIHRoZSBzdGFuZGFyZCBmb3JcclxuICogbW9zdCB3ZWIgYXBwcywgYnV0IGl0IHJlcXVpcmVzIHNvbWUgY29uZmlndXJhdGlvbiBvbiB0aGUgc2VydmVyIHRvIGVuc3VyZSB5b3VcclxuICogc2VydmUgdGhlIHNhbWUgYXBwIGF0IG11bHRpcGxlIFVSTHMuXHJcbiAqXHJcbiAqIEBzZWUgaHR0cHM6Ly9naXRodWIuY29tL3JlbWl4LXJ1bi9oaXN0b3J5L3RyZWUvbWFpbi9kb2NzL2FwaS1yZWZlcmVuY2UubWQjY3JlYXRlYnJvd3Nlcmhpc3RvcnlcclxuICovXG5cbmZ1bmN0aW9uIGNyZWF0ZUJyb3dzZXJIaXN0b3J5KG9wdGlvbnMpIHtcbiAgaWYgKG9wdGlvbnMgPT09IHZvaWQgMCkge1xuICAgIG9wdGlvbnMgPSB7fTtcbiAgfVxuXG4gIHZhciBfb3B0aW9ucyA9IG9wdGlvbnMsXG4gICAgICBfb3B0aW9ucyR3aW5kb3cgPSBfb3B0aW9ucy53aW5kb3csXG4gICAgICB3aW5kb3cgPSBfb3B0aW9ucyR3aW5kb3cgPT09IHZvaWQgMCA/IGRvY3VtZW50LmRlZmF1bHRWaWV3IDogX29wdGlvbnMkd2luZG93O1xuICB2YXIgZ2xvYmFsSGlzdG9yeSA9IHdpbmRvdy5oaXN0b3J5O1xuXG4gIGZ1bmN0aW9uIGdldEluZGV4QW5kTG9jYXRpb24oKSB7XG4gICAgdmFyIF93aW5kb3ckbG9jYXRpb24gPSB3aW5kb3cubG9jYXRpb24sXG4gICAgICAgIHBhdGhuYW1lID0gX3dpbmRvdyRsb2NhdGlvbi5wYXRobmFtZSxcbiAgICAgICAgc2VhcmNoID0gX3dpbmRvdyRsb2NhdGlvbi5zZWFyY2gsXG4gICAgICAgIGhhc2ggPSBfd2luZG93JGxvY2F0aW9uLmhhc2g7XG4gICAgdmFyIHN0YXRlID0gZ2xvYmFsSGlzdG9yeS5zdGF0ZSB8fCB7fTtcbiAgICByZXR1cm4gW3N0YXRlLmlkeCwgcmVhZE9ubHkoe1xuICAgICAgcGF0aG5hbWU6IHBhdGhuYW1lLFxuICAgICAgc2VhcmNoOiBzZWFyY2gsXG4gICAgICBoYXNoOiBoYXNoLFxuICAgICAgc3RhdGU6IHN0YXRlLnVzciB8fCBudWxsLFxuICAgICAga2V5OiBzdGF0ZS5rZXkgfHwgJ2RlZmF1bHQnXG4gICAgfSldO1xuICB9XG5cbiAgdmFyIGJsb2NrZWRQb3BUeCA9IG51bGw7XG5cbiAgZnVuY3Rpb24gaGFuZGxlUG9wKCkge1xuICAgIGlmIChibG9ja2VkUG9wVHgpIHtcbiAgICAgIGJsb2NrZXJzLmNhbGwoYmxvY2tlZFBvcFR4KTtcbiAgICAgIGJsb2NrZWRQb3BUeCA9IG51bGw7XG4gICAgfSBlbHNlIHtcbiAgICAgIHZhciBuZXh0QWN0aW9uID0gQWN0aW9uLlBvcDtcblxuICAgICAgdmFyIF9nZXRJbmRleEFuZExvY2F0aW9uID0gZ2V0SW5kZXhBbmRMb2NhdGlvbigpLFxuICAgICAgICAgIG5leHRJbmRleCA9IF9nZXRJbmRleEFuZExvY2F0aW9uWzBdLFxuICAgICAgICAgIG5leHRMb2NhdGlvbiA9IF9nZXRJbmRleEFuZExvY2F0aW9uWzFdO1xuXG4gICAgICBpZiAoYmxvY2tlcnMubGVuZ3RoKSB7XG4gICAgICAgIGlmIChuZXh0SW5kZXggIT0gbnVsbCkge1xuICAgICAgICAgIHZhciBkZWx0YSA9IGluZGV4IC0gbmV4dEluZGV4O1xuXG4gICAgICAgICAgaWYgKGRlbHRhKSB7XG4gICAgICAgICAgICAvLyBSZXZlcnQgdGhlIFBPUFxuICAgICAgICAgICAgYmxvY2tlZFBvcFR4ID0ge1xuICAgICAgICAgICAgICBhY3Rpb246IG5leHRBY3Rpb24sXG4gICAgICAgICAgICAgIGxvY2F0aW9uOiBuZXh0TG9jYXRpb24sXG4gICAgICAgICAgICAgIHJldHJ5OiBmdW5jdGlvbiByZXRyeSgpIHtcbiAgICAgICAgICAgICAgICBnbyhkZWx0YSAqIC0xKTtcbiAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfTtcbiAgICAgICAgICAgIGdvKGRlbHRhKTtcbiAgICAgICAgICB9XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgLy8gVHJ5aW5nIHRvIFBPUCB0byBhIGxvY2F0aW9uIHdpdGggbm8gaW5kZXguIFdlIGRpZCBub3QgY3JlYXRlXG4gICAgICAgICAgLy8gdGhpcyBsb2NhdGlvbiwgc28gd2UgY2FuJ3QgZWZmZWN0aXZlbHkgYmxvY2sgdGhlIG5hdmlnYXRpb24uXG4gICAgICAgICAgcHJvY2Vzcy5lbnYuTk9ERV9FTlYgIT09IFwicHJvZHVjdGlvblwiID8gd2FybmluZyhmYWxzZSwgLy8gVE9ETzogV3JpdGUgdXAgYSBkb2MgdGhhdCBleHBsYWlucyBvdXIgYmxvY2tpbmcgc3RyYXRlZ3kgaW5cbiAgICAgICAgICAvLyBkZXRhaWwgYW5kIGxpbmsgdG8gaXQgaGVyZSBzbyBwZW9wbGUgY2FuIHVuZGVyc3RhbmQgYmV0dGVyIHdoYXRcbiAgICAgICAgICAvLyBpcyBnb2luZyBvbiBhbmQgaG93IHRvIGF2b2lkIGl0LlxuICAgICAgICAgIFwiWW91IGFyZSB0cnlpbmcgdG8gYmxvY2sgYSBQT1AgbmF2aWdhdGlvbiB0byBhIGxvY2F0aW9uIHRoYXQgd2FzIG5vdCBcIiArIFwiY3JlYXRlZCBieSB0aGUgaGlzdG9yeSBsaWJyYXJ5LiBUaGUgYmxvY2sgd2lsbCBmYWlsIHNpbGVudGx5IGluIFwiICsgXCJwcm9kdWN0aW9uLCBidXQgaW4gZ2VuZXJhbCB5b3Ugc2hvdWxkIGRvIGFsbCBuYXZpZ2F0aW9uIHdpdGggdGhlIFwiICsgXCJoaXN0b3J5IGxpYnJhcnkgKGluc3RlYWQgb2YgdXNpbmcgd2luZG93Lmhpc3RvcnkucHVzaFN0YXRlIGRpcmVjdGx5KSBcIiArIFwidG8gYXZvaWQgdGhpcyBzaXR1YXRpb24uXCIpIDogdm9pZCAwO1xuICAgICAgICB9XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBhcHBseVR4KG5leHRBY3Rpb24pO1xuICAgICAgfVxuICAgIH1cbiAgfVxuXG4gIHdpbmRvdy5hZGRFdmVudExpc3RlbmVyKFBvcFN0YXRlRXZlbnRUeXBlLCBoYW5kbGVQb3ApO1xuICB2YXIgYWN0aW9uID0gQWN0aW9uLlBvcDtcblxuICB2YXIgX2dldEluZGV4QW5kTG9jYXRpb24yID0gZ2V0SW5kZXhBbmRMb2NhdGlvbigpLFxuICAgICAgaW5kZXggPSBfZ2V0SW5kZXhBbmRMb2NhdGlvbjJbMF0sXG4gICAgICBsb2NhdGlvbiA9IF9nZXRJbmRleEFuZExvY2F0aW9uMlsxXTtcblxuICB2YXIgbGlzdGVuZXJzID0gY3JlYXRlRXZlbnRzKCk7XG4gIHZhciBibG9ja2VycyA9IGNyZWF0ZUV2ZW50cygpO1xuXG4gIGlmIChpbmRleCA9PSBudWxsKSB7XG4gICAgaW5kZXggPSAwO1xuICAgIGdsb2JhbEhpc3RvcnkucmVwbGFjZVN0YXRlKF9leHRlbmRzKHt9LCBnbG9iYWxIaXN0b3J5LnN0YXRlLCB7XG4gICAgICBpZHg6IGluZGV4XG4gICAgfSksICcnKTtcbiAgfVxuXG4gIGZ1bmN0aW9uIGNyZWF0ZUhyZWYodG8pIHtcbiAgICByZXR1cm4gdHlwZW9mIHRvID09PSAnc3RyaW5nJyA/IHRvIDogY3JlYXRlUGF0aCh0byk7XG4gIH0gLy8gc3RhdGUgZGVmYXVsdHMgdG8gYG51bGxgIGJlY2F1c2UgYHdpbmRvdy5oaXN0b3J5LnN0YXRlYCBkb2VzXG5cblxuICBmdW5jdGlvbiBnZXROZXh0TG9jYXRpb24odG8sIHN0YXRlKSB7XG4gICAgaWYgKHN0YXRlID09PSB2b2lkIDApIHtcbiAgICAgIHN0YXRlID0gbnVsbDtcbiAgICB9XG5cbiAgICByZXR1cm4gcmVhZE9ubHkoX2V4dGVuZHMoe1xuICAgICAgcGF0aG5hbWU6IGxvY2F0aW9uLnBhdGhuYW1lLFxuICAgICAgaGFzaDogJycsXG4gICAgICBzZWFyY2g6ICcnXG4gICAgfSwgdHlwZW9mIHRvID09PSAnc3RyaW5nJyA/IHBhcnNlUGF0aCh0bykgOiB0bywge1xuICAgICAgc3RhdGU6IHN0YXRlLFxuICAgICAga2V5OiBjcmVhdGVLZXkoKVxuICAgIH0pKTtcbiAgfVxuXG4gIGZ1bmN0aW9uIGdldEhpc3RvcnlTdGF0ZUFuZFVybChuZXh0TG9jYXRpb24sIGluZGV4KSB7XG4gICAgcmV0dXJuIFt7XG4gICAgICB1c3I6IG5leHRMb2NhdGlvbi5zdGF0ZSxcbiAgICAgIGtleTogbmV4dExvY2F0aW9uLmtleSxcbiAgICAgIGlkeDogaW5kZXhcbiAgICB9LCBjcmVhdGVIcmVmKG5leHRMb2NhdGlvbildO1xuICB9XG5cbiAgZnVuY3Rpb24gYWxsb3dUeChhY3Rpb24sIGxvY2F0aW9uLCByZXRyeSkge1xuICAgIHJldHVybiAhYmxvY2tlcnMubGVuZ3RoIHx8IChibG9ja2Vycy5jYWxsKHtcbiAgICAgIGFjdGlvbjogYWN0aW9uLFxuICAgICAgbG9jYXRpb246IGxvY2F0aW9uLFxuICAgICAgcmV0cnk6IHJldHJ5XG4gICAgfSksIGZhbHNlKTtcbiAgfVxuXG4gIGZ1bmN0aW9uIGFwcGx5VHgobmV4dEFjdGlvbikge1xuICAgIGFjdGlvbiA9IG5leHRBY3Rpb247XG5cbiAgICB2YXIgX2dldEluZGV4QW5kTG9jYXRpb24zID0gZ2V0SW5kZXhBbmRMb2NhdGlvbigpO1xuXG4gICAgaW5kZXggPSBfZ2V0SW5kZXhBbmRMb2NhdGlvbjNbMF07XG4gICAgbG9jYXRpb24gPSBfZ2V0SW5kZXhBbmRMb2NhdGlvbjNbMV07XG4gICAgbGlzdGVuZXJzLmNhbGwoe1xuICAgICAgYWN0aW9uOiBhY3Rpb24sXG4gICAgICBsb2NhdGlvbjogbG9jYXRpb25cbiAgICB9KTtcbiAgfVxuXG4gIGZ1bmN0aW9uIHB1c2godG8sIHN0YXRlKSB7XG4gICAgdmFyIG5leHRBY3Rpb24gPSBBY3Rpb24uUHVzaDtcbiAgICB2YXIgbmV4dExvY2F0aW9uID0gZ2V0TmV4dExvY2F0aW9uKHRvLCBzdGF0ZSk7XG5cbiAgICBmdW5jdGlvbiByZXRyeSgpIHtcbiAgICAgIHB1c2godG8sIHN0YXRlKTtcbiAgICB9XG5cbiAgICBpZiAoYWxsb3dUeChuZXh0QWN0aW9uLCBuZXh0TG9jYXRpb24sIHJldHJ5KSkge1xuICAgICAgdmFyIF9nZXRIaXN0b3J5U3RhdGVBbmRVciA9IGdldEhpc3RvcnlTdGF0ZUFuZFVybChuZXh0TG9jYXRpb24sIGluZGV4ICsgMSksXG4gICAgICAgICAgaGlzdG9yeVN0YXRlID0gX2dldEhpc3RvcnlTdGF0ZUFuZFVyWzBdLFxuICAgICAgICAgIHVybCA9IF9nZXRIaXN0b3J5U3RhdGVBbmRVclsxXTsgLy8gVE9ETzogU3VwcG9ydCBmb3JjZWQgcmVsb2FkaW5nXG4gICAgICAvLyB0cnkuLi5jYXRjaCBiZWNhdXNlIGlPUyBsaW1pdHMgdXMgdG8gMTAwIHB1c2hTdGF0ZSBjYWxscyA6L1xuXG5cbiAgICAgIHRyeSB7XG4gICAgICAgIGdsb2JhbEhpc3RvcnkucHVzaFN0YXRlKGhpc3RvcnlTdGF0ZSwgJycsIHVybCk7XG4gICAgICB9IGNhdGNoIChlcnJvcikge1xuICAgICAgICAvLyBUaGV5IGFyZSBnb2luZyB0byBsb3NlIHN0YXRlIGhlcmUsIGJ1dCB0aGVyZSBpcyBubyByZWFsXG4gICAgICAgIC8vIHdheSB0byB3YXJuIHRoZW0gYWJvdXQgaXQgc2luY2UgdGhlIHBhZ2Ugd2lsbCByZWZyZXNoLi4uXG4gICAgICAgIHdpbmRvdy5sb2NhdGlvbi5hc3NpZ24odXJsKTtcbiAgICAgIH1cblxuICAgICAgYXBwbHlUeChuZXh0QWN0aW9uKTtcbiAgICB9XG4gIH1cblxuICBmdW5jdGlvbiByZXBsYWNlKHRvLCBzdGF0ZSkge1xuICAgIHZhciBuZXh0QWN0aW9uID0gQWN0aW9uLlJlcGxhY2U7XG4gICAgdmFyIG5leHRMb2NhdGlvbiA9IGdldE5leHRMb2NhdGlvbih0bywgc3RhdGUpO1xuXG4gICAgZnVuY3Rpb24gcmV0cnkoKSB7XG4gICAgICByZXBsYWNlKHRvLCBzdGF0ZSk7XG4gICAgfVxuXG4gICAgaWYgKGFsbG93VHgobmV4dEFjdGlvbiwgbmV4dExvY2F0aW9uLCByZXRyeSkpIHtcbiAgICAgIHZhciBfZ2V0SGlzdG9yeVN0YXRlQW5kVXIyID0gZ2V0SGlzdG9yeVN0YXRlQW5kVXJsKG5leHRMb2NhdGlvbiwgaW5kZXgpLFxuICAgICAgICAgIGhpc3RvcnlTdGF0ZSA9IF9nZXRIaXN0b3J5U3RhdGVBbmRVcjJbMF0sXG4gICAgICAgICAgdXJsID0gX2dldEhpc3RvcnlTdGF0ZUFuZFVyMlsxXTsgLy8gVE9ETzogU3VwcG9ydCBmb3JjZWQgcmVsb2FkaW5nXG5cblxuICAgICAgZ2xvYmFsSGlzdG9yeS5yZXBsYWNlU3RhdGUoaGlzdG9yeVN0YXRlLCAnJywgdXJsKTtcbiAgICAgIGFwcGx5VHgobmV4dEFjdGlvbik7XG4gICAgfVxuICB9XG5cbiAgZnVuY3Rpb24gZ28oZGVsdGEpIHtcbiAgICBnbG9iYWxIaXN0b3J5LmdvKGRlbHRhKTtcbiAgfVxuXG4gIHZhciBoaXN0b3J5ID0ge1xuICAgIGdldCBhY3Rpb24oKSB7XG4gICAgICByZXR1cm4gYWN0aW9uO1xuICAgIH0sXG5cbiAgICBnZXQgbG9jYXRpb24oKSB7XG4gICAgICByZXR1cm4gbG9jYXRpb247XG4gICAgfSxcblxuICAgIGNyZWF0ZUhyZWY6IGNyZWF0ZUhyZWYsXG4gICAgcHVzaDogcHVzaCxcbiAgICByZXBsYWNlOiByZXBsYWNlLFxuICAgIGdvOiBnbyxcbiAgICBiYWNrOiBmdW5jdGlvbiBiYWNrKCkge1xuICAgICAgZ28oLTEpO1xuICAgIH0sXG4gICAgZm9yd2FyZDogZnVuY3Rpb24gZm9yd2FyZCgpIHtcbiAgICAgIGdvKDEpO1xuICAgIH0sXG4gICAgbGlzdGVuOiBmdW5jdGlvbiBsaXN0ZW4obGlzdGVuZXIpIHtcbiAgICAgIHJldHVybiBsaXN0ZW5lcnMucHVzaChsaXN0ZW5lcik7XG4gICAgfSxcbiAgICBibG9jazogZnVuY3Rpb24gYmxvY2soYmxvY2tlcikge1xuICAgICAgdmFyIHVuYmxvY2sgPSBibG9ja2Vycy5wdXNoKGJsb2NrZXIpO1xuXG4gICAgICBpZiAoYmxvY2tlcnMubGVuZ3RoID09PSAxKSB7XG4gICAgICAgIHdpbmRvdy5hZGRFdmVudExpc3RlbmVyKEJlZm9yZVVubG9hZEV2ZW50VHlwZSwgcHJvbXB0QmVmb3JlVW5sb2FkKTtcbiAgICAgIH1cblxuICAgICAgcmV0dXJuIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgdW5ibG9jaygpOyAvLyBSZW1vdmUgdGhlIGJlZm9yZXVubG9hZCBsaXN0ZW5lciBzbyB0aGUgZG9jdW1lbnQgbWF5XG4gICAgICAgIC8vIHN0aWxsIGJlIHNhbHZhZ2VhYmxlIGluIHRoZSBwYWdlaGlkZSBldmVudC5cbiAgICAgICAgLy8gU2VlIGh0dHBzOi8vaHRtbC5zcGVjLndoYXR3Zy5vcmcvI3VubG9hZGluZy1kb2N1bWVudHNcblxuICAgICAgICBpZiAoIWJsb2NrZXJzLmxlbmd0aCkge1xuICAgICAgICAgIHdpbmRvdy5yZW1vdmVFdmVudExpc3RlbmVyKEJlZm9yZVVubG9hZEV2ZW50VHlwZSwgcHJvbXB0QmVmb3JlVW5sb2FkKTtcbiAgICAgICAgfVxuICAgICAgfTtcbiAgICB9XG4gIH07XG4gIHJldHVybiBoaXN0b3J5O1xufVxuLyoqXHJcbiAqIEhhc2ggaGlzdG9yeSBzdG9yZXMgdGhlIGxvY2F0aW9uIGluIHdpbmRvdy5sb2NhdGlvbi5oYXNoLiBUaGlzIG1ha2VzIGl0IGlkZWFsXHJcbiAqIGZvciBzaXR1YXRpb25zIHdoZXJlIHlvdSBkb24ndCB3YW50IHRvIHNlbmQgdGhlIGxvY2F0aW9uIHRvIHRoZSBzZXJ2ZXIgZm9yXHJcbiAqIHNvbWUgcmVhc29uLCBlaXRoZXIgYmVjYXVzZSB5b3UgZG8gY2Fubm90IGNvbmZpZ3VyZSBpdCBvciB0aGUgVVJMIHNwYWNlIGlzXHJcbiAqIHJlc2VydmVkIGZvciBzb21ldGhpbmcgZWxzZS5cclxuICpcclxuICogQHNlZSBodHRwczovL2dpdGh1Yi5jb20vcmVtaXgtcnVuL2hpc3RvcnkvdHJlZS9tYWluL2RvY3MvYXBpLXJlZmVyZW5jZS5tZCNjcmVhdGVoYXNoaGlzdG9yeVxyXG4gKi9cblxuZnVuY3Rpb24gY3JlYXRlSGFzaEhpc3Rvcnkob3B0aW9ucykge1xuICBpZiAob3B0aW9ucyA9PT0gdm9pZCAwKSB7XG4gICAgb3B0aW9ucyA9IHt9O1xuICB9XG5cbiAgdmFyIF9vcHRpb25zMiA9IG9wdGlvbnMsXG4gICAgICBfb3B0aW9uczIkd2luZG93ID0gX29wdGlvbnMyLndpbmRvdyxcbiAgICAgIHdpbmRvdyA9IF9vcHRpb25zMiR3aW5kb3cgPT09IHZvaWQgMCA/IGRvY3VtZW50LmRlZmF1bHRWaWV3IDogX29wdGlvbnMyJHdpbmRvdztcbiAgdmFyIGdsb2JhbEhpc3RvcnkgPSB3aW5kb3cuaGlzdG9yeTtcblxuICBmdW5jdGlvbiBnZXRJbmRleEFuZExvY2F0aW9uKCkge1xuICAgIHZhciBfcGFyc2VQYXRoID0gcGFyc2VQYXRoKHdpbmRvdy5sb2NhdGlvbi5oYXNoLnN1YnN0cigxKSksXG4gICAgICAgIF9wYXJzZVBhdGgkcGF0aG5hbWUgPSBfcGFyc2VQYXRoLnBhdGhuYW1lLFxuICAgICAgICBwYXRobmFtZSA9IF9wYXJzZVBhdGgkcGF0aG5hbWUgPT09IHZvaWQgMCA/ICcvJyA6IF9wYXJzZVBhdGgkcGF0aG5hbWUsXG4gICAgICAgIF9wYXJzZVBhdGgkc2VhcmNoID0gX3BhcnNlUGF0aC5zZWFyY2gsXG4gICAgICAgIHNlYXJjaCA9IF9wYXJzZVBhdGgkc2VhcmNoID09PSB2b2lkIDAgPyAnJyA6IF9wYXJzZVBhdGgkc2VhcmNoLFxuICAgICAgICBfcGFyc2VQYXRoJGhhc2ggPSBfcGFyc2VQYXRoLmhhc2gsXG4gICAgICAgIGhhc2ggPSBfcGFyc2VQYXRoJGhhc2ggPT09IHZvaWQgMCA/ICcnIDogX3BhcnNlUGF0aCRoYXNoO1xuXG4gICAgdmFyIHN0YXRlID0gZ2xvYmFsSGlzdG9yeS5zdGF0ZSB8fCB7fTtcbiAgICByZXR1cm4gW3N0YXRlLmlkeCwgcmVhZE9ubHkoe1xuICAgICAgcGF0aG5hbWU6IHBhdGhuYW1lLFxuICAgICAgc2VhcmNoOiBzZWFyY2gsXG4gICAgICBoYXNoOiBoYXNoLFxuICAgICAgc3RhdGU6IHN0YXRlLnVzciB8fCBudWxsLFxuICAgICAga2V5OiBzdGF0ZS5rZXkgfHwgJ2RlZmF1bHQnXG4gICAgfSldO1xuICB9XG5cbiAgdmFyIGJsb2NrZWRQb3BUeCA9IG51bGw7XG5cbiAgZnVuY3Rpb24gaGFuZGxlUG9wKCkge1xuICAgIGlmIChibG9ja2VkUG9wVHgpIHtcbiAgICAgIGJsb2NrZXJzLmNhbGwoYmxvY2tlZFBvcFR4KTtcbiAgICAgIGJsb2NrZWRQb3BUeCA9IG51bGw7XG4gICAgfSBlbHNlIHtcbiAgICAgIHZhciBuZXh0QWN0aW9uID0gQWN0aW9uLlBvcDtcblxuICAgICAgdmFyIF9nZXRJbmRleEFuZExvY2F0aW9uNCA9IGdldEluZGV4QW5kTG9jYXRpb24oKSxcbiAgICAgICAgICBuZXh0SW5kZXggPSBfZ2V0SW5kZXhBbmRMb2NhdGlvbjRbMF0sXG4gICAgICAgICAgbmV4dExvY2F0aW9uID0gX2dldEluZGV4QW5kTG9jYXRpb240WzFdO1xuXG4gICAgICBpZiAoYmxvY2tlcnMubGVuZ3RoKSB7XG4gICAgICAgIGlmIChuZXh0SW5kZXggIT0gbnVsbCkge1xuICAgICAgICAgIHZhciBkZWx0YSA9IGluZGV4IC0gbmV4dEluZGV4O1xuXG4gICAgICAgICAgaWYgKGRlbHRhKSB7XG4gICAgICAgICAgICAvLyBSZXZlcnQgdGhlIFBPUFxuICAgICAgICAgICAgYmxvY2tlZFBvcFR4ID0ge1xuICAgICAgICAgICAgICBhY3Rpb246IG5leHRBY3Rpb24sXG4gICAgICAgICAgICAgIGxvY2F0aW9uOiBuZXh0TG9jYXRpb24sXG4gICAgICAgICAgICAgIHJldHJ5OiBmdW5jdGlvbiByZXRyeSgpIHtcbiAgICAgICAgICAgICAgICBnbyhkZWx0YSAqIC0xKTtcbiAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfTtcbiAgICAgICAgICAgIGdvKGRlbHRhKTtcbiAgICAgICAgICB9XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgLy8gVHJ5aW5nIHRvIFBPUCB0byBhIGxvY2F0aW9uIHdpdGggbm8gaW5kZXguIFdlIGRpZCBub3QgY3JlYXRlXG4gICAgICAgICAgLy8gdGhpcyBsb2NhdGlvbiwgc28gd2UgY2FuJ3QgZWZmZWN0aXZlbHkgYmxvY2sgdGhlIG5hdmlnYXRpb24uXG4gICAgICAgICAgcHJvY2Vzcy5lbnYuTk9ERV9FTlYgIT09IFwicHJvZHVjdGlvblwiID8gd2FybmluZyhmYWxzZSwgLy8gVE9ETzogV3JpdGUgdXAgYSBkb2MgdGhhdCBleHBsYWlucyBvdXIgYmxvY2tpbmcgc3RyYXRlZ3kgaW5cbiAgICAgICAgICAvLyBkZXRhaWwgYW5kIGxpbmsgdG8gaXQgaGVyZSBzbyBwZW9wbGUgY2FuIHVuZGVyc3RhbmQgYmV0dGVyXG4gICAgICAgICAgLy8gd2hhdCBpcyBnb2luZyBvbiBhbmQgaG93IHRvIGF2b2lkIGl0LlxuICAgICAgICAgIFwiWW91IGFyZSB0cnlpbmcgdG8gYmxvY2sgYSBQT1AgbmF2aWdhdGlvbiB0byBhIGxvY2F0aW9uIHRoYXQgd2FzIG5vdCBcIiArIFwiY3JlYXRlZCBieSB0aGUgaGlzdG9yeSBsaWJyYXJ5LiBUaGUgYmxvY2sgd2lsbCBmYWlsIHNpbGVudGx5IGluIFwiICsgXCJwcm9kdWN0aW9uLCBidXQgaW4gZ2VuZXJhbCB5b3Ugc2hvdWxkIGRvIGFsbCBuYXZpZ2F0aW9uIHdpdGggdGhlIFwiICsgXCJoaXN0b3J5IGxpYnJhcnkgKGluc3RlYWQgb2YgdXNpbmcgd2luZG93Lmhpc3RvcnkucHVzaFN0YXRlIGRpcmVjdGx5KSBcIiArIFwidG8gYXZvaWQgdGhpcyBzaXR1YXRpb24uXCIpIDogdm9pZCAwO1xuICAgICAgICB9XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBhcHBseVR4KG5leHRBY3Rpb24pO1xuICAgICAgfVxuICAgIH1cbiAgfVxuXG4gIHdpbmRvdy5hZGRFdmVudExpc3RlbmVyKFBvcFN0YXRlRXZlbnRUeXBlLCBoYW5kbGVQb3ApOyAvLyBwb3BzdGF0ZSBkb2VzIG5vdCBmaXJlIG9uIGhhc2hjaGFuZ2UgaW4gSUUgMTEgYW5kIG9sZCAodHJpZGVudCkgRWRnZVxuICAvLyBodHRwczovL2RldmVsb3Blci5tb3ppbGxhLm9yZy9kZS9kb2NzL1dlYi9BUEkvV2luZG93L3BvcHN0YXRlX2V2ZW50XG5cbiAgd2luZG93LmFkZEV2ZW50TGlzdGVuZXIoSGFzaENoYW5nZUV2ZW50VHlwZSwgZnVuY3Rpb24gKCkge1xuICAgIHZhciBfZ2V0SW5kZXhBbmRMb2NhdGlvbjUgPSBnZXRJbmRleEFuZExvY2F0aW9uKCksXG4gICAgICAgIG5leHRMb2NhdGlvbiA9IF9nZXRJbmRleEFuZExvY2F0aW9uNVsxXTsgLy8gSWdub3JlIGV4dHJhbmVvdXMgaGFzaGNoYW5nZSBldmVudHMuXG5cblxuICAgIGlmIChjcmVhdGVQYXRoKG5leHRMb2NhdGlvbikgIT09IGNyZWF0ZVBhdGgobG9jYXRpb24pKSB7XG4gICAgICBoYW5kbGVQb3AoKTtcbiAgICB9XG4gIH0pO1xuICB2YXIgYWN0aW9uID0gQWN0aW9uLlBvcDtcblxuICB2YXIgX2dldEluZGV4QW5kTG9jYXRpb242ID0gZ2V0SW5kZXhBbmRMb2NhdGlvbigpLFxuICAgICAgaW5kZXggPSBfZ2V0SW5kZXhBbmRMb2NhdGlvbjZbMF0sXG4gICAgICBsb2NhdGlvbiA9IF9nZXRJbmRleEFuZExvY2F0aW9uNlsxXTtcblxuICB2YXIgbGlzdGVuZXJzID0gY3JlYXRlRXZlbnRzKCk7XG4gIHZhciBibG9ja2VycyA9IGNyZWF0ZUV2ZW50cygpO1xuXG4gIGlmIChpbmRleCA9PSBudWxsKSB7XG4gICAgaW5kZXggPSAwO1xuICAgIGdsb2JhbEhpc3RvcnkucmVwbGFjZVN0YXRlKF9leHRlbmRzKHt9LCBnbG9iYWxIaXN0b3J5LnN0YXRlLCB7XG4gICAgICBpZHg6IGluZGV4XG4gICAgfSksICcnKTtcbiAgfVxuXG4gIGZ1bmN0aW9uIGdldEJhc2VIcmVmKCkge1xuICAgIHZhciBiYXNlID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignYmFzZScpO1xuICAgIHZhciBocmVmID0gJyc7XG5cbiAgICBpZiAoYmFzZSAmJiBiYXNlLmdldEF0dHJpYnV0ZSgnaHJlZicpKSB7XG4gICAgICB2YXIgdXJsID0gd2luZG93LmxvY2F0aW9uLmhyZWY7XG4gICAgICB2YXIgaGFzaEluZGV4ID0gdXJsLmluZGV4T2YoJyMnKTtcbiAgICAgIGhyZWYgPSBoYXNoSW5kZXggPT09IC0xID8gdXJsIDogdXJsLnNsaWNlKDAsIGhhc2hJbmRleCk7XG4gICAgfVxuXG4gICAgcmV0dXJuIGhyZWY7XG4gIH1cblxuICBmdW5jdGlvbiBjcmVhdGVIcmVmKHRvKSB7XG4gICAgcmV0dXJuIGdldEJhc2VIcmVmKCkgKyAnIycgKyAodHlwZW9mIHRvID09PSAnc3RyaW5nJyA/IHRvIDogY3JlYXRlUGF0aCh0bykpO1xuICB9XG5cbiAgZnVuY3Rpb24gZ2V0TmV4dExvY2F0aW9uKHRvLCBzdGF0ZSkge1xuICAgIGlmIChzdGF0ZSA9PT0gdm9pZCAwKSB7XG4gICAgICBzdGF0ZSA9IG51bGw7XG4gICAgfVxuXG4gICAgcmV0dXJuIHJlYWRPbmx5KF9leHRlbmRzKHtcbiAgICAgIHBhdGhuYW1lOiBsb2NhdGlvbi5wYXRobmFtZSxcbiAgICAgIGhhc2g6ICcnLFxuICAgICAgc2VhcmNoOiAnJ1xuICAgIH0sIHR5cGVvZiB0byA9PT0gJ3N0cmluZycgPyBwYXJzZVBhdGgodG8pIDogdG8sIHtcbiAgICAgIHN0YXRlOiBzdGF0ZSxcbiAgICAgIGtleTogY3JlYXRlS2V5KClcbiAgICB9KSk7XG4gIH1cblxuICBmdW5jdGlvbiBnZXRIaXN0b3J5U3RhdGVBbmRVcmwobmV4dExvY2F0aW9uLCBpbmRleCkge1xuICAgIHJldHVybiBbe1xuICAgICAgdXNyOiBuZXh0TG9jYXRpb24uc3RhdGUsXG4gICAgICBrZXk6IG5leHRMb2NhdGlvbi5rZXksXG4gICAgICBpZHg6IGluZGV4XG4gICAgfSwgY3JlYXRlSHJlZihuZXh0TG9jYXRpb24pXTtcbiAgfVxuXG4gIGZ1bmN0aW9uIGFsbG93VHgoYWN0aW9uLCBsb2NhdGlvbiwgcmV0cnkpIHtcbiAgICByZXR1cm4gIWJsb2NrZXJzLmxlbmd0aCB8fCAoYmxvY2tlcnMuY2FsbCh7XG4gICAgICBhY3Rpb246IGFjdGlvbixcbiAgICAgIGxvY2F0aW9uOiBsb2NhdGlvbixcbiAgICAgIHJldHJ5OiByZXRyeVxuICAgIH0pLCBmYWxzZSk7XG4gIH1cblxuICBmdW5jdGlvbiBhcHBseVR4KG5leHRBY3Rpb24pIHtcbiAgICBhY3Rpb24gPSBuZXh0QWN0aW9uO1xuXG4gICAgdmFyIF9nZXRJbmRleEFuZExvY2F0aW9uNyA9IGdldEluZGV4QW5kTG9jYXRpb24oKTtcblxuICAgIGluZGV4ID0gX2dldEluZGV4QW5kTG9jYXRpb243WzBdO1xuICAgIGxvY2F0aW9uID0gX2dldEluZGV4QW5kTG9jYXRpb243WzFdO1xuICAgIGxpc3RlbmVycy5jYWxsKHtcbiAgICAgIGFjdGlvbjogYWN0aW9uLFxuICAgICAgbG9jYXRpb246IGxvY2F0aW9uXG4gICAgfSk7XG4gIH1cblxuICBmdW5jdGlvbiBwdXNoKHRvLCBzdGF0ZSkge1xuICAgIHZhciBuZXh0QWN0aW9uID0gQWN0aW9uLlB1c2g7XG4gICAgdmFyIG5leHRMb2NhdGlvbiA9IGdldE5leHRMb2NhdGlvbih0bywgc3RhdGUpO1xuXG4gICAgZnVuY3Rpb24gcmV0cnkoKSB7XG4gICAgICBwdXNoKHRvLCBzdGF0ZSk7XG4gICAgfVxuXG4gICAgcHJvY2Vzcy5lbnYuTk9ERV9FTlYgIT09IFwicHJvZHVjdGlvblwiID8gd2FybmluZyhuZXh0TG9jYXRpb24ucGF0aG5hbWUuY2hhckF0KDApID09PSAnLycsIFwiUmVsYXRpdmUgcGF0aG5hbWVzIGFyZSBub3Qgc3VwcG9ydGVkIGluIGhhc2ggaGlzdG9yeS5wdXNoKFwiICsgSlNPTi5zdHJpbmdpZnkodG8pICsgXCIpXCIpIDogdm9pZCAwO1xuXG4gICAgaWYgKGFsbG93VHgobmV4dEFjdGlvbiwgbmV4dExvY2F0aW9uLCByZXRyeSkpIHtcbiAgICAgIHZhciBfZ2V0SGlzdG9yeVN0YXRlQW5kVXIzID0gZ2V0SGlzdG9yeVN0YXRlQW5kVXJsKG5leHRMb2NhdGlvbiwgaW5kZXggKyAxKSxcbiAgICAgICAgICBoaXN0b3J5U3RhdGUgPSBfZ2V0SGlzdG9yeVN0YXRlQW5kVXIzWzBdLFxuICAgICAgICAgIHVybCA9IF9nZXRIaXN0b3J5U3RhdGVBbmRVcjNbMV07IC8vIFRPRE86IFN1cHBvcnQgZm9yY2VkIHJlbG9hZGluZ1xuICAgICAgLy8gdHJ5Li4uY2F0Y2ggYmVjYXVzZSBpT1MgbGltaXRzIHVzIHRvIDEwMCBwdXNoU3RhdGUgY2FsbHMgOi9cblxuXG4gICAgICB0cnkge1xuICAgICAgICBnbG9iYWxIaXN0b3J5LnB1c2hTdGF0ZShoaXN0b3J5U3RhdGUsICcnLCB1cmwpO1xuICAgICAgfSBjYXRjaCAoZXJyb3IpIHtcbiAgICAgICAgLy8gVGhleSBhcmUgZ29pbmcgdG8gbG9zZSBzdGF0ZSBoZXJlLCBidXQgdGhlcmUgaXMgbm8gcmVhbFxuICAgICAgICAvLyB3YXkgdG8gd2FybiB0aGVtIGFib3V0IGl0IHNpbmNlIHRoZSBwYWdlIHdpbGwgcmVmcmVzaC4uLlxuICAgICAgICB3aW5kb3cubG9jYXRpb24uYXNzaWduKHVybCk7XG4gICAgICB9XG5cbiAgICAgIGFwcGx5VHgobmV4dEFjdGlvbik7XG4gICAgfVxuICB9XG5cbiAgZnVuY3Rpb24gcmVwbGFjZSh0bywgc3RhdGUpIHtcbiAgICB2YXIgbmV4dEFjdGlvbiA9IEFjdGlvbi5SZXBsYWNlO1xuICAgIHZhciBuZXh0TG9jYXRpb24gPSBnZXROZXh0TG9jYXRpb24odG8sIHN0YXRlKTtcblxuICAgIGZ1bmN0aW9uIHJldHJ5KCkge1xuICAgICAgcmVwbGFjZSh0bywgc3RhdGUpO1xuICAgIH1cblxuICAgIHByb2Nlc3MuZW52Lk5PREVfRU5WICE9PSBcInByb2R1Y3Rpb25cIiA/IHdhcm5pbmcobmV4dExvY2F0aW9uLnBhdGhuYW1lLmNoYXJBdCgwKSA9PT0gJy8nLCBcIlJlbGF0aXZlIHBhdGhuYW1lcyBhcmUgbm90IHN1cHBvcnRlZCBpbiBoYXNoIGhpc3RvcnkucmVwbGFjZShcIiArIEpTT04uc3RyaW5naWZ5KHRvKSArIFwiKVwiKSA6IHZvaWQgMDtcblxuICAgIGlmIChhbGxvd1R4KG5leHRBY3Rpb24sIG5leHRMb2NhdGlvbiwgcmV0cnkpKSB7XG4gICAgICB2YXIgX2dldEhpc3RvcnlTdGF0ZUFuZFVyNCA9IGdldEhpc3RvcnlTdGF0ZUFuZFVybChuZXh0TG9jYXRpb24sIGluZGV4KSxcbiAgICAgICAgICBoaXN0b3J5U3RhdGUgPSBfZ2V0SGlzdG9yeVN0YXRlQW5kVXI0WzBdLFxuICAgICAgICAgIHVybCA9IF9nZXRIaXN0b3J5U3RhdGVBbmRVcjRbMV07IC8vIFRPRE86IFN1cHBvcnQgZm9yY2VkIHJlbG9hZGluZ1xuXG5cbiAgICAgIGdsb2JhbEhpc3RvcnkucmVwbGFjZVN0YXRlKGhpc3RvcnlTdGF0ZSwgJycsIHVybCk7XG4gICAgICBhcHBseVR4KG5leHRBY3Rpb24pO1xuICAgIH1cbiAgfVxuXG4gIGZ1bmN0aW9uIGdvKGRlbHRhKSB7XG4gICAgZ2xvYmFsSGlzdG9yeS5nbyhkZWx0YSk7XG4gIH1cblxuICB2YXIgaGlzdG9yeSA9IHtcbiAgICBnZXQgYWN0aW9uKCkge1xuICAgICAgcmV0dXJuIGFjdGlvbjtcbiAgICB9LFxuXG4gICAgZ2V0IGxvY2F0aW9uKCkge1xuICAgICAgcmV0dXJuIGxvY2F0aW9uO1xuICAgIH0sXG5cbiAgICBjcmVhdGVIcmVmOiBjcmVhdGVIcmVmLFxuICAgIHB1c2g6IHB1c2gsXG4gICAgcmVwbGFjZTogcmVwbGFjZSxcbiAgICBnbzogZ28sXG4gICAgYmFjazogZnVuY3Rpb24gYmFjaygpIHtcbiAgICAgIGdvKC0xKTtcbiAgICB9LFxuICAgIGZvcndhcmQ6IGZ1bmN0aW9uIGZvcndhcmQoKSB7XG4gICAgICBnbygxKTtcbiAgICB9LFxuICAgIGxpc3RlbjogZnVuY3Rpb24gbGlzdGVuKGxpc3RlbmVyKSB7XG4gICAgICByZXR1cm4gbGlzdGVuZXJzLnB1c2gobGlzdGVuZXIpO1xuICAgIH0sXG4gICAgYmxvY2s6IGZ1bmN0aW9uIGJsb2NrKGJsb2NrZXIpIHtcbiAgICAgIHZhciB1bmJsb2NrID0gYmxvY2tlcnMucHVzaChibG9ja2VyKTtcblxuICAgICAgaWYgKGJsb2NrZXJzLmxlbmd0aCA9PT0gMSkge1xuICAgICAgICB3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lcihCZWZvcmVVbmxvYWRFdmVudFR5cGUsIHByb21wdEJlZm9yZVVubG9hZCk7XG4gICAgICB9XG5cbiAgICAgIHJldHVybiBmdW5jdGlvbiAoKSB7XG4gICAgICAgIHVuYmxvY2soKTsgLy8gUmVtb3ZlIHRoZSBiZWZvcmV1bmxvYWQgbGlzdGVuZXIgc28gdGhlIGRvY3VtZW50IG1heVxuICAgICAgICAvLyBzdGlsbCBiZSBzYWx2YWdlYWJsZSBpbiB0aGUgcGFnZWhpZGUgZXZlbnQuXG4gICAgICAgIC8vIFNlZSBodHRwczovL2h0bWwuc3BlYy53aGF0d2cub3JnLyN1bmxvYWRpbmctZG9jdW1lbnRzXG5cbiAgICAgICAgaWYgKCFibG9ja2Vycy5sZW5ndGgpIHtcbiAgICAgICAgICB3aW5kb3cucmVtb3ZlRXZlbnRMaXN0ZW5lcihCZWZvcmVVbmxvYWRFdmVudFR5cGUsIHByb21wdEJlZm9yZVVubG9hZCk7XG4gICAgICAgIH1cbiAgICAgIH07XG4gICAgfVxuICB9O1xuICByZXR1cm4gaGlzdG9yeTtcbn1cbi8qKlxyXG4gKiBNZW1vcnkgaGlzdG9yeSBzdG9yZXMgdGhlIGN1cnJlbnQgbG9jYXRpb24gaW4gbWVtb3J5LiBJdCBpcyBkZXNpZ25lZCBmb3IgdXNlXHJcbiAqIGluIHN0YXRlZnVsIG5vbi1icm93c2VyIGVudmlyb25tZW50cyBsaWtlIHRlc3RzIGFuZCBSZWFjdCBOYXRpdmUuXHJcbiAqXHJcbiAqIEBzZWUgaHR0cHM6Ly9naXRodWIuY29tL3JlbWl4LXJ1bi9oaXN0b3J5L3RyZWUvbWFpbi9kb2NzL2FwaS1yZWZlcmVuY2UubWQjY3JlYXRlbWVtb3J5aGlzdG9yeVxyXG4gKi9cblxuZnVuY3Rpb24gY3JlYXRlTWVtb3J5SGlzdG9yeShvcHRpb25zKSB7XG4gIGlmIChvcHRpb25zID09PSB2b2lkIDApIHtcbiAgICBvcHRpb25zID0ge307XG4gIH1cblxuICB2YXIgX29wdGlvbnMzID0gb3B0aW9ucyxcbiAgICAgIF9vcHRpb25zMyRpbml0aWFsRW50ciA9IF9vcHRpb25zMy5pbml0aWFsRW50cmllcyxcbiAgICAgIGluaXRpYWxFbnRyaWVzID0gX29wdGlvbnMzJGluaXRpYWxFbnRyID09PSB2b2lkIDAgPyBbJy8nXSA6IF9vcHRpb25zMyRpbml0aWFsRW50cixcbiAgICAgIGluaXRpYWxJbmRleCA9IF9vcHRpb25zMy5pbml0aWFsSW5kZXg7XG4gIHZhciBlbnRyaWVzID0gaW5pdGlhbEVudHJpZXMubWFwKGZ1bmN0aW9uIChlbnRyeSkge1xuICAgIHZhciBsb2NhdGlvbiA9IHJlYWRPbmx5KF9leHRlbmRzKHtcbiAgICAgIHBhdGhuYW1lOiAnLycsXG4gICAgICBzZWFyY2g6ICcnLFxuICAgICAgaGFzaDogJycsXG4gICAgICBzdGF0ZTogbnVsbCxcbiAgICAgIGtleTogY3JlYXRlS2V5KClcbiAgICB9LCB0eXBlb2YgZW50cnkgPT09ICdzdHJpbmcnID8gcGFyc2VQYXRoKGVudHJ5KSA6IGVudHJ5KSk7XG4gICAgcHJvY2Vzcy5lbnYuTk9ERV9FTlYgIT09IFwicHJvZHVjdGlvblwiID8gd2FybmluZyhsb2NhdGlvbi5wYXRobmFtZS5jaGFyQXQoMCkgPT09ICcvJywgXCJSZWxhdGl2ZSBwYXRobmFtZXMgYXJlIG5vdCBzdXBwb3J0ZWQgaW4gY3JlYXRlTWVtb3J5SGlzdG9yeSh7IGluaXRpYWxFbnRyaWVzIH0pIChpbnZhbGlkIGVudHJ5OiBcIiArIEpTT04uc3RyaW5naWZ5KGVudHJ5KSArIFwiKVwiKSA6IHZvaWQgMDtcbiAgICByZXR1cm4gbG9jYXRpb247XG4gIH0pO1xuICB2YXIgaW5kZXggPSBjbGFtcChpbml0aWFsSW5kZXggPT0gbnVsbCA/IGVudHJpZXMubGVuZ3RoIC0gMSA6IGluaXRpYWxJbmRleCwgMCwgZW50cmllcy5sZW5ndGggLSAxKTtcbiAgdmFyIGFjdGlvbiA9IEFjdGlvbi5Qb3A7XG4gIHZhciBsb2NhdGlvbiA9IGVudHJpZXNbaW5kZXhdO1xuICB2YXIgbGlzdGVuZXJzID0gY3JlYXRlRXZlbnRzKCk7XG4gIHZhciBibG9ja2VycyA9IGNyZWF0ZUV2ZW50cygpO1xuXG4gIGZ1bmN0aW9uIGNyZWF0ZUhyZWYodG8pIHtcbiAgICByZXR1cm4gdHlwZW9mIHRvID09PSAnc3RyaW5nJyA/IHRvIDogY3JlYXRlUGF0aCh0byk7XG4gIH1cblxuICBmdW5jdGlvbiBnZXROZXh0TG9jYXRpb24odG8sIHN0YXRlKSB7XG4gICAgaWYgKHN0YXRlID09PSB2b2lkIDApIHtcbiAgICAgIHN0YXRlID0gbnVsbDtcbiAgICB9XG5cbiAgICByZXR1cm4gcmVhZE9ubHkoX2V4dGVuZHMoe1xuICAgICAgcGF0aG5hbWU6IGxvY2F0aW9uLnBhdGhuYW1lLFxuICAgICAgc2VhcmNoOiAnJyxcbiAgICAgIGhhc2g6ICcnXG4gICAgfSwgdHlwZW9mIHRvID09PSAnc3RyaW5nJyA/IHBhcnNlUGF0aCh0bykgOiB0bywge1xuICAgICAgc3RhdGU6IHN0YXRlLFxuICAgICAga2V5OiBjcmVhdGVLZXkoKVxuICAgIH0pKTtcbiAgfVxuXG4gIGZ1bmN0aW9uIGFsbG93VHgoYWN0aW9uLCBsb2NhdGlvbiwgcmV0cnkpIHtcbiAgICByZXR1cm4gIWJsb2NrZXJzLmxlbmd0aCB8fCAoYmxvY2tlcnMuY2FsbCh7XG4gICAgICBhY3Rpb246IGFjdGlvbixcbiAgICAgIGxvY2F0aW9uOiBsb2NhdGlvbixcbiAgICAgIHJldHJ5OiByZXRyeVxuICAgIH0pLCBmYWxzZSk7XG4gIH1cblxuICBmdW5jdGlvbiBhcHBseVR4KG5leHRBY3Rpb24sIG5leHRMb2NhdGlvbikge1xuICAgIGFjdGlvbiA9IG5leHRBY3Rpb247XG4gICAgbG9jYXRpb24gPSBuZXh0TG9jYXRpb247XG4gICAgbGlzdGVuZXJzLmNhbGwoe1xuICAgICAgYWN0aW9uOiBhY3Rpb24sXG4gICAgICBsb2NhdGlvbjogbG9jYXRpb25cbiAgICB9KTtcbiAgfVxuXG4gIGZ1bmN0aW9uIHB1c2godG8sIHN0YXRlKSB7XG4gICAgdmFyIG5leHRBY3Rpb24gPSBBY3Rpb24uUHVzaDtcbiAgICB2YXIgbmV4dExvY2F0aW9uID0gZ2V0TmV4dExvY2F0aW9uKHRvLCBzdGF0ZSk7XG5cbiAgICBmdW5jdGlvbiByZXRyeSgpIHtcbiAgICAgIHB1c2godG8sIHN0YXRlKTtcbiAgICB9XG5cbiAgICBwcm9jZXNzLmVudi5OT0RFX0VOViAhPT0gXCJwcm9kdWN0aW9uXCIgPyB3YXJuaW5nKGxvY2F0aW9uLnBhdGhuYW1lLmNoYXJBdCgwKSA9PT0gJy8nLCBcIlJlbGF0aXZlIHBhdGhuYW1lcyBhcmUgbm90IHN1cHBvcnRlZCBpbiBtZW1vcnkgaGlzdG9yeS5wdXNoKFwiICsgSlNPTi5zdHJpbmdpZnkodG8pICsgXCIpXCIpIDogdm9pZCAwO1xuXG4gICAgaWYgKGFsbG93VHgobmV4dEFjdGlvbiwgbmV4dExvY2F0aW9uLCByZXRyeSkpIHtcbiAgICAgIGluZGV4ICs9IDE7XG4gICAgICBlbnRyaWVzLnNwbGljZShpbmRleCwgZW50cmllcy5sZW5ndGgsIG5leHRMb2NhdGlvbik7XG4gICAgICBhcHBseVR4KG5leHRBY3Rpb24sIG5leHRMb2NhdGlvbik7XG4gICAgfVxuICB9XG5cbiAgZnVuY3Rpb24gcmVwbGFjZSh0bywgc3RhdGUpIHtcbiAgICB2YXIgbmV4dEFjdGlvbiA9IEFjdGlvbi5SZXBsYWNlO1xuICAgIHZhciBuZXh0TG9jYXRpb24gPSBnZXROZXh0TG9jYXRpb24odG8sIHN0YXRlKTtcblxuICAgIGZ1bmN0aW9uIHJldHJ5KCkge1xuICAgICAgcmVwbGFjZSh0bywgc3RhdGUpO1xuICAgIH1cblxuICAgIHByb2Nlc3MuZW52Lk5PREVfRU5WICE9PSBcInByb2R1Y3Rpb25cIiA/IHdhcm5pbmcobG9jYXRpb24ucGF0aG5hbWUuY2hhckF0KDApID09PSAnLycsIFwiUmVsYXRpdmUgcGF0aG5hbWVzIGFyZSBub3Qgc3VwcG9ydGVkIGluIG1lbW9yeSBoaXN0b3J5LnJlcGxhY2UoXCIgKyBKU09OLnN0cmluZ2lmeSh0bykgKyBcIilcIikgOiB2b2lkIDA7XG5cbiAgICBpZiAoYWxsb3dUeChuZXh0QWN0aW9uLCBuZXh0TG9jYXRpb24sIHJldHJ5KSkge1xuICAgICAgZW50cmllc1tpbmRleF0gPSBuZXh0TG9jYXRpb247XG4gICAgICBhcHBseVR4KG5leHRBY3Rpb24sIG5leHRMb2NhdGlvbik7XG4gICAgfVxuICB9XG5cbiAgZnVuY3Rpb24gZ28oZGVsdGEpIHtcbiAgICB2YXIgbmV4dEluZGV4ID0gY2xhbXAoaW5kZXggKyBkZWx0YSwgMCwgZW50cmllcy5sZW5ndGggLSAxKTtcbiAgICB2YXIgbmV4dEFjdGlvbiA9IEFjdGlvbi5Qb3A7XG4gICAgdmFyIG5leHRMb2NhdGlvbiA9IGVudHJpZXNbbmV4dEluZGV4XTtcblxuICAgIGZ1bmN0aW9uIHJldHJ5KCkge1xuICAgICAgZ28oZGVsdGEpO1xuICAgIH1cblxuICAgIGlmIChhbGxvd1R4KG5leHRBY3Rpb24sIG5leHRMb2NhdGlvbiwgcmV0cnkpKSB7XG4gICAgICBpbmRleCA9IG5leHRJbmRleDtcbiAgICAgIGFwcGx5VHgobmV4dEFjdGlvbiwgbmV4dExvY2F0aW9uKTtcbiAgICB9XG4gIH1cblxuICB2YXIgaGlzdG9yeSA9IHtcbiAgICBnZXQgaW5kZXgoKSB7XG4gICAgICByZXR1cm4gaW5kZXg7XG4gICAgfSxcblxuICAgIGdldCBhY3Rpb24oKSB7XG4gICAgICByZXR1cm4gYWN0aW9uO1xuICAgIH0sXG5cbiAgICBnZXQgbG9jYXRpb24oKSB7XG4gICAgICByZXR1cm4gbG9jYXRpb247XG4gICAgfSxcblxuICAgIGNyZWF0ZUhyZWY6IGNyZWF0ZUhyZWYsXG4gICAgcHVzaDogcHVzaCxcbiAgICByZXBsYWNlOiByZXBsYWNlLFxuICAgIGdvOiBnbyxcbiAgICBiYWNrOiBmdW5jdGlvbiBiYWNrKCkge1xuICAgICAgZ28oLTEpO1xuICAgIH0sXG4gICAgZm9yd2FyZDogZnVuY3Rpb24gZm9yd2FyZCgpIHtcbiAgICAgIGdvKDEpO1xuICAgIH0sXG4gICAgbGlzdGVuOiBmdW5jdGlvbiBsaXN0ZW4obGlzdGVuZXIpIHtcbiAgICAgIHJldHVybiBsaXN0ZW5lcnMucHVzaChsaXN0ZW5lcik7XG4gICAgfSxcbiAgICBibG9jazogZnVuY3Rpb24gYmxvY2soYmxvY2tlcikge1xuICAgICAgcmV0dXJuIGJsb2NrZXJzLnB1c2goYmxvY2tlcik7XG4gICAgfVxuICB9O1xuICByZXR1cm4gaGlzdG9yeTtcbn0gLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFVUSUxTXG4vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vL1xuXG5mdW5jdGlvbiBjbGFtcChuLCBsb3dlckJvdW5kLCB1cHBlckJvdW5kKSB7XG4gIHJldHVybiBNYXRoLm1pbihNYXRoLm1heChuLCBsb3dlckJvdW5kKSwgdXBwZXJCb3VuZCk7XG59XG5cbmZ1bmN0aW9uIHByb21wdEJlZm9yZVVubG9hZChldmVudCkge1xuICAvLyBDYW5jZWwgdGhlIGV2ZW50LlxuICBldmVudC5wcmV2ZW50RGVmYXVsdCgpOyAvLyBDaHJvbWUgKGFuZCBsZWdhY3kgSUUpIHJlcXVpcmVzIHJldHVyblZhbHVlIHRvIGJlIHNldC5cblxuICBldmVudC5yZXR1cm5WYWx1ZSA9ICcnO1xufVxuXG5mdW5jdGlvbiBjcmVhdGVFdmVudHMoKSB7XG4gIHZhciBoYW5kbGVycyA9IFtdO1xuICByZXR1cm4ge1xuICAgIGdldCBsZW5ndGgoKSB7XG4gICAgICByZXR1cm4gaGFuZGxlcnMubGVuZ3RoO1xuICAgIH0sXG5cbiAgICBwdXNoOiBmdW5jdGlvbiBwdXNoKGZuKSB7XG4gICAgICBoYW5kbGVycy5wdXNoKGZuKTtcbiAgICAgIHJldHVybiBmdW5jdGlvbiAoKSB7XG4gICAgICAgIGhhbmRsZXJzID0gaGFuZGxlcnMuZmlsdGVyKGZ1bmN0aW9uIChoYW5kbGVyKSB7XG4gICAgICAgICAgcmV0dXJuIGhhbmRsZXIgIT09IGZuO1xuICAgICAgICB9KTtcbiAgICAgIH07XG4gICAgfSxcbiAgICBjYWxsOiBmdW5jdGlvbiBjYWxsKGFyZykge1xuICAgICAgaGFuZGxlcnMuZm9yRWFjaChmdW5jdGlvbiAoZm4pIHtcbiAgICAgICAgcmV0dXJuIGZuICYmIGZuKGFyZyk7XG4gICAgICB9KTtcbiAgICB9XG4gIH07XG59XG5cbmZ1bmN0aW9uIGNyZWF0ZUtleSgpIHtcbiAgcmV0dXJuIE1hdGgucmFuZG9tKCkudG9TdHJpbmcoMzYpLnN1YnN0cigyLCA4KTtcbn1cbi8qKlxyXG4gKiBDcmVhdGVzIGEgc3RyaW5nIFVSTCBwYXRoIGZyb20gdGhlIGdpdmVuIHBhdGhuYW1lLCBzZWFyY2gsIGFuZCBoYXNoIGNvbXBvbmVudHMuXHJcbiAqXHJcbiAqIEBzZWUgaHR0cHM6Ly9naXRodWIuY29tL3JlbWl4LXJ1bi9oaXN0b3J5L3RyZWUvbWFpbi9kb2NzL2FwaS1yZWZlcmVuY2UubWQjY3JlYXRlcGF0aFxyXG4gKi9cblxuXG5mdW5jdGlvbiBjcmVhdGVQYXRoKF9yZWYpIHtcbiAgdmFyIF9yZWYkcGF0aG5hbWUgPSBfcmVmLnBhdGhuYW1lLFxuICAgICAgcGF0aG5hbWUgPSBfcmVmJHBhdGhuYW1lID09PSB2b2lkIDAgPyAnLycgOiBfcmVmJHBhdGhuYW1lLFxuICAgICAgX3JlZiRzZWFyY2ggPSBfcmVmLnNlYXJjaCxcbiAgICAgIHNlYXJjaCA9IF9yZWYkc2VhcmNoID09PSB2b2lkIDAgPyAnJyA6IF9yZWYkc2VhcmNoLFxuICAgICAgX3JlZiRoYXNoID0gX3JlZi5oYXNoLFxuICAgICAgaGFzaCA9IF9yZWYkaGFzaCA9PT0gdm9pZCAwID8gJycgOiBfcmVmJGhhc2g7XG4gIGlmIChzZWFyY2ggJiYgc2VhcmNoICE9PSAnPycpIHBhdGhuYW1lICs9IHNlYXJjaC5jaGFyQXQoMCkgPT09ICc/JyA/IHNlYXJjaCA6ICc/JyArIHNlYXJjaDtcbiAgaWYgKGhhc2ggJiYgaGFzaCAhPT0gJyMnKSBwYXRobmFtZSArPSBoYXNoLmNoYXJBdCgwKSA9PT0gJyMnID8gaGFzaCA6ICcjJyArIGhhc2g7XG4gIHJldHVybiBwYXRobmFtZTtcbn1cbi8qKlxyXG4gKiBQYXJzZXMgYSBzdHJpbmcgVVJMIHBhdGggaW50byBpdHMgc2VwYXJhdGUgcGF0aG5hbWUsIHNlYXJjaCwgYW5kIGhhc2ggY29tcG9uZW50cy5cclxuICpcclxuICogQHNlZSBodHRwczovL2dpdGh1Yi5jb20vcmVtaXgtcnVuL2hpc3RvcnkvdHJlZS9tYWluL2RvY3MvYXBpLXJlZmVyZW5jZS5tZCNwYXJzZXBhdGhcclxuICovXG5cbmZ1bmN0aW9uIHBhcnNlUGF0aChwYXRoKSB7XG4gIHZhciBwYXJzZWRQYXRoID0ge307XG5cbiAgaWYgKHBhdGgpIHtcbiAgICB2YXIgaGFzaEluZGV4ID0gcGF0aC5pbmRleE9mKCcjJyk7XG5cbiAgICBpZiAoaGFzaEluZGV4ID49IDApIHtcbiAgICAgIHBhcnNlZFBhdGguaGFzaCA9IHBhdGguc3Vic3RyKGhhc2hJbmRleCk7XG4gICAgICBwYXRoID0gcGF0aC5zdWJzdHIoMCwgaGFzaEluZGV4KTtcbiAgICB9XG5cbiAgICB2YXIgc2VhcmNoSW5kZXggPSBwYXRoLmluZGV4T2YoJz8nKTtcblxuICAgIGlmIChzZWFyY2hJbmRleCA+PSAwKSB7XG4gICAgICBwYXJzZWRQYXRoLnNlYXJjaCA9IHBhdGguc3Vic3RyKHNlYXJjaEluZGV4KTtcbiAgICAgIHBhdGggPSBwYXRoLnN1YnN0cigwLCBzZWFyY2hJbmRleCk7XG4gICAgfVxuXG4gICAgaWYgKHBhdGgpIHtcbiAgICAgIHBhcnNlZFBhdGgucGF0aG5hbWUgPSBwYXRoO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiBwYXJzZWRQYXRoO1xufVxuXG5leHBvcnQgeyBBY3Rpb24sIGNyZWF0ZUJyb3dzZXJIaXN0b3J5LCBjcmVhdGVIYXNoSGlzdG9yeSwgY3JlYXRlTWVtb3J5SGlzdG9yeSwgY3JlYXRlUGF0aCwgcGFyc2VQYXRoIH07XG4vLyMgc291cmNlTWFwcGluZ1VSTD1pbmRleC5qcy5tYXBcbiIsIid1c2Ugc3RyaWN0JztcblxudmFyIHJlcGxhY2UgPSBTdHJpbmcucHJvdG90eXBlLnJlcGxhY2U7XG52YXIgcGVyY2VudFR3ZW50aWVzID0gLyUyMC9nO1xuXG5tb2R1bGUuZXhwb3J0cyA9IHtcbiAgICAnZGVmYXVsdCc6ICdSRkMzOTg2JyxcbiAgICBmb3JtYXR0ZXJzOiB7XG4gICAgICAgIFJGQzE3Mzg6IGZ1bmN0aW9uICh2YWx1ZSkge1xuICAgICAgICAgICAgcmV0dXJuIHJlcGxhY2UuY2FsbCh2YWx1ZSwgcGVyY2VudFR3ZW50aWVzLCAnKycpO1xuICAgICAgICB9LFxuICAgICAgICBSRkMzOTg2OiBmdW5jdGlvbiAodmFsdWUpIHtcbiAgICAgICAgICAgIHJldHVybiBTdHJpbmcodmFsdWUpO1xuICAgICAgICB9XG4gICAgfSxcbiAgICBSRkMxNzM4OiAnUkZDMTczOCcsXG4gICAgUkZDMzk4NjogJ1JGQzM5ODYnXG59O1xuIiwiJ3VzZSBzdHJpY3QnO1xuXG52YXIgc3RyaW5naWZ5ID0gcmVxdWlyZSgnLi9zdHJpbmdpZnknKTtcbnZhciBwYXJzZSA9IHJlcXVpcmUoJy4vcGFyc2UnKTtcbnZhciBmb3JtYXRzID0gcmVxdWlyZSgnLi9mb3JtYXRzJyk7XG5cbm1vZHVsZS5leHBvcnRzID0ge1xuICAgIGZvcm1hdHM6IGZvcm1hdHMsXG4gICAgcGFyc2U6IHBhcnNlLFxuICAgIHN0cmluZ2lmeTogc3RyaW5naWZ5XG59O1xuIiwiJ3VzZSBzdHJpY3QnO1xuXG52YXIgdXRpbHMgPSByZXF1aXJlKCcuL3V0aWxzJyk7XG5cbnZhciBoYXMgPSBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5O1xuXG52YXIgZGVmYXVsdHMgPSB7XG4gICAgYWxsb3dEb3RzOiBmYWxzZSxcbiAgICBhbGxvd1Byb3RvdHlwZXM6IGZhbHNlLFxuICAgIGFycmF5TGltaXQ6IDIwLFxuICAgIGRlY29kZXI6IHV0aWxzLmRlY29kZSxcbiAgICBkZWxpbWl0ZXI6ICcmJyxcbiAgICBkZXB0aDogNSxcbiAgICBwYXJhbWV0ZXJMaW1pdDogMTAwMCxcbiAgICBwbGFpbk9iamVjdHM6IGZhbHNlLFxuICAgIHN0cmljdE51bGxIYW5kbGluZzogZmFsc2Vcbn07XG5cbnZhciBwYXJzZVZhbHVlcyA9IGZ1bmN0aW9uIHBhcnNlUXVlcnlTdHJpbmdWYWx1ZXMoc3RyLCBvcHRpb25zKSB7XG4gICAgdmFyIG9iaiA9IHt9O1xuICAgIHZhciBjbGVhblN0ciA9IG9wdGlvbnMuaWdub3JlUXVlcnlQcmVmaXggPyBzdHIucmVwbGFjZSgvXlxcPy8sICcnKSA6IHN0cjtcbiAgICB2YXIgbGltaXQgPSBvcHRpb25zLnBhcmFtZXRlckxpbWl0ID09PSBJbmZpbml0eSA/IHVuZGVmaW5lZCA6IG9wdGlvbnMucGFyYW1ldGVyTGltaXQ7XG4gICAgdmFyIHBhcnRzID0gY2xlYW5TdHIuc3BsaXQob3B0aW9ucy5kZWxpbWl0ZXIsIGxpbWl0KTtcblxuICAgIGZvciAodmFyIGkgPSAwOyBpIDwgcGFydHMubGVuZ3RoOyArK2kpIHtcbiAgICAgICAgdmFyIHBhcnQgPSBwYXJ0c1tpXTtcblxuICAgICAgICB2YXIgYnJhY2tldEVxdWFsc1BvcyA9IHBhcnQuaW5kZXhPZignXT0nKTtcbiAgICAgICAgdmFyIHBvcyA9IGJyYWNrZXRFcXVhbHNQb3MgPT09IC0xID8gcGFydC5pbmRleE9mKCc9JykgOiBicmFja2V0RXF1YWxzUG9zICsgMTtcblxuICAgICAgICB2YXIga2V5LCB2YWw7XG4gICAgICAgIGlmIChwb3MgPT09IC0xKSB7XG4gICAgICAgICAgICBrZXkgPSBvcHRpb25zLmRlY29kZXIocGFydCwgZGVmYXVsdHMuZGVjb2Rlcik7XG4gICAgICAgICAgICB2YWwgPSBvcHRpb25zLnN0cmljdE51bGxIYW5kbGluZyA/IG51bGwgOiAnJztcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGtleSA9IG9wdGlvbnMuZGVjb2RlcihwYXJ0LnNsaWNlKDAsIHBvcyksIGRlZmF1bHRzLmRlY29kZXIpO1xuICAgICAgICAgICAgdmFsID0gb3B0aW9ucy5kZWNvZGVyKHBhcnQuc2xpY2UocG9zICsgMSksIGRlZmF1bHRzLmRlY29kZXIpO1xuICAgICAgICB9XG4gICAgICAgIGlmIChoYXMuY2FsbChvYmosIGtleSkpIHtcbiAgICAgICAgICAgIG9ialtrZXldID0gW10uY29uY2F0KG9ialtrZXldKS5jb25jYXQodmFsKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIG9ialtrZXldID0gdmFsO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgcmV0dXJuIG9iajtcbn07XG5cbnZhciBwYXJzZU9iamVjdCA9IGZ1bmN0aW9uIChjaGFpbiwgdmFsLCBvcHRpb25zKSB7XG4gICAgdmFyIGxlYWYgPSB2YWw7XG5cbiAgICBmb3IgKHZhciBpID0gY2hhaW4ubGVuZ3RoIC0gMTsgaSA+PSAwOyAtLWkpIHtcbiAgICAgICAgdmFyIG9iajtcbiAgICAgICAgdmFyIHJvb3QgPSBjaGFpbltpXTtcblxuICAgICAgICBpZiAocm9vdCA9PT0gJ1tdJyAmJiBvcHRpb25zLnBhcnNlQXJyYXlzKSB7XG4gICAgICAgICAgICBvYmogPSBbXS5jb25jYXQobGVhZik7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBvYmogPSBvcHRpb25zLnBsYWluT2JqZWN0cyA/IE9iamVjdC5jcmVhdGUobnVsbCkgOiB7fTtcbiAgICAgICAgICAgIHZhciBjbGVhblJvb3QgPSByb290LmNoYXJBdCgwKSA9PT0gJ1snICYmIHJvb3QuY2hhckF0KHJvb3QubGVuZ3RoIC0gMSkgPT09ICddJyA/IHJvb3Quc2xpY2UoMSwgLTEpIDogcm9vdDtcbiAgICAgICAgICAgIHZhciBpbmRleCA9IHBhcnNlSW50KGNsZWFuUm9vdCwgMTApO1xuICAgICAgICAgICAgaWYgKCFvcHRpb25zLnBhcnNlQXJyYXlzICYmIGNsZWFuUm9vdCA9PT0gJycpIHtcbiAgICAgICAgICAgICAgICBvYmogPSB7IDA6IGxlYWYgfTtcbiAgICAgICAgICAgIH0gZWxzZSBpZiAoXG4gICAgICAgICAgICAgICAgIWlzTmFOKGluZGV4KVxuICAgICAgICAgICAgICAgICYmIHJvb3QgIT09IGNsZWFuUm9vdFxuICAgICAgICAgICAgICAgICYmIFN0cmluZyhpbmRleCkgPT09IGNsZWFuUm9vdFxuICAgICAgICAgICAgICAgICYmIGluZGV4ID49IDBcbiAgICAgICAgICAgICAgICAmJiAob3B0aW9ucy5wYXJzZUFycmF5cyAmJiBpbmRleCA8PSBvcHRpb25zLmFycmF5TGltaXQpXG4gICAgICAgICAgICApIHtcbiAgICAgICAgICAgICAgICBvYmogPSBbXTtcbiAgICAgICAgICAgICAgICBvYmpbaW5kZXhdID0gbGVhZjtcbiAgICAgICAgICAgIH0gZWxzZSBpZiAoY2xlYW5Sb290ICE9PSAnX19wcm90b19fJykge1xuICAgICAgICAgICAgICAgIG9ialtjbGVhblJvb3RdID0gbGVhZjtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIGxlYWYgPSBvYmo7XG4gICAgfVxuXG4gICAgcmV0dXJuIGxlYWY7XG59O1xuXG52YXIgcGFyc2VLZXlzID0gZnVuY3Rpb24gcGFyc2VRdWVyeVN0cmluZ0tleXMoZ2l2ZW5LZXksIHZhbCwgb3B0aW9ucykge1xuICAgIGlmICghZ2l2ZW5LZXkpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIC8vIFRyYW5zZm9ybSBkb3Qgbm90YXRpb24gdG8gYnJhY2tldCBub3RhdGlvblxuICAgIHZhciBrZXkgPSBvcHRpb25zLmFsbG93RG90cyA/IGdpdmVuS2V5LnJlcGxhY2UoL1xcLihbXi5bXSspL2csICdbJDFdJykgOiBnaXZlbktleTtcblxuICAgIC8vIFRoZSByZWdleCBjaHVua3NcblxuICAgIHZhciBicmFja2V0cyA9IC8oXFxbW15bXFxdXSpdKS87XG4gICAgdmFyIGNoaWxkID0gLyhcXFtbXltcXF1dKl0pL2c7XG5cbiAgICAvLyBHZXQgdGhlIHBhcmVudFxuXG4gICAgdmFyIHNlZ21lbnQgPSBicmFja2V0cy5leGVjKGtleSk7XG4gICAgdmFyIHBhcmVudCA9IHNlZ21lbnQgPyBrZXkuc2xpY2UoMCwgc2VnbWVudC5pbmRleCkgOiBrZXk7XG5cbiAgICAvLyBTdGFzaCB0aGUgcGFyZW50IGlmIGl0IGV4aXN0c1xuXG4gICAgdmFyIGtleXMgPSBbXTtcbiAgICBpZiAocGFyZW50KSB7XG4gICAgICAgIC8vIElmIHdlIGFyZW4ndCB1c2luZyBwbGFpbiBvYmplY3RzLCBvcHRpb25hbGx5IHByZWZpeCBrZXlzXG4gICAgICAgIC8vIHRoYXQgd291bGQgb3ZlcndyaXRlIG9iamVjdCBwcm90b3R5cGUgcHJvcGVydGllc1xuICAgICAgICBpZiAoIW9wdGlvbnMucGxhaW5PYmplY3RzICYmIGhhcy5jYWxsKE9iamVjdC5wcm90b3R5cGUsIHBhcmVudCkpIHtcbiAgICAgICAgICAgIGlmICghb3B0aW9ucy5hbGxvd1Byb3RvdHlwZXMpIHtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBrZXlzLnB1c2gocGFyZW50KTtcbiAgICB9XG5cbiAgICAvLyBMb29wIHRocm91Z2ggY2hpbGRyZW4gYXBwZW5kaW5nIHRvIHRoZSBhcnJheSB1bnRpbCB3ZSBoaXQgZGVwdGhcblxuICAgIHZhciBpID0gMDtcbiAgICB3aGlsZSAoKHNlZ21lbnQgPSBjaGlsZC5leGVjKGtleSkpICE9PSBudWxsICYmIGkgPCBvcHRpb25zLmRlcHRoKSB7XG4gICAgICAgIGkgKz0gMTtcbiAgICAgICAgaWYgKCFvcHRpb25zLnBsYWluT2JqZWN0cyAmJiBoYXMuY2FsbChPYmplY3QucHJvdG90eXBlLCBzZWdtZW50WzFdLnNsaWNlKDEsIC0xKSkpIHtcbiAgICAgICAgICAgIGlmICghb3B0aW9ucy5hbGxvd1Byb3RvdHlwZXMpIHtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgICAga2V5cy5wdXNoKHNlZ21lbnRbMV0pO1xuICAgIH1cblxuICAgIC8vIElmIHRoZXJlJ3MgYSByZW1haW5kZXIsIGp1c3QgYWRkIHdoYXRldmVyIGlzIGxlZnRcblxuICAgIGlmIChzZWdtZW50KSB7XG4gICAgICAgIGtleXMucHVzaCgnWycgKyBrZXkuc2xpY2Uoc2VnbWVudC5pbmRleCkgKyAnXScpO1xuICAgIH1cblxuICAgIHJldHVybiBwYXJzZU9iamVjdChrZXlzLCB2YWwsIG9wdGlvbnMpO1xufTtcblxubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbiAoc3RyLCBvcHRzKSB7XG4gICAgdmFyIG9wdGlvbnMgPSBvcHRzID8gdXRpbHMuYXNzaWduKHt9LCBvcHRzKSA6IHt9O1xuXG4gICAgaWYgKG9wdGlvbnMuZGVjb2RlciAhPT0gbnVsbCAmJiBvcHRpb25zLmRlY29kZXIgIT09IHVuZGVmaW5lZCAmJiB0eXBlb2Ygb3B0aW9ucy5kZWNvZGVyICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgIHRocm93IG5ldyBUeXBlRXJyb3IoJ0RlY29kZXIgaGFzIHRvIGJlIGEgZnVuY3Rpb24uJyk7XG4gICAgfVxuXG4gICAgb3B0aW9ucy5pZ25vcmVRdWVyeVByZWZpeCA9IG9wdGlvbnMuaWdub3JlUXVlcnlQcmVmaXggPT09IHRydWU7XG4gICAgb3B0aW9ucy5kZWxpbWl0ZXIgPSB0eXBlb2Ygb3B0aW9ucy5kZWxpbWl0ZXIgPT09ICdzdHJpbmcnIHx8IHV0aWxzLmlzUmVnRXhwKG9wdGlvbnMuZGVsaW1pdGVyKSA/IG9wdGlvbnMuZGVsaW1pdGVyIDogZGVmYXVsdHMuZGVsaW1pdGVyO1xuICAgIG9wdGlvbnMuZGVwdGggPSB0eXBlb2Ygb3B0aW9ucy5kZXB0aCA9PT0gJ251bWJlcicgPyBvcHRpb25zLmRlcHRoIDogZGVmYXVsdHMuZGVwdGg7XG4gICAgb3B0aW9ucy5hcnJheUxpbWl0ID0gdHlwZW9mIG9wdGlvbnMuYXJyYXlMaW1pdCA9PT0gJ251bWJlcicgPyBvcHRpb25zLmFycmF5TGltaXQgOiBkZWZhdWx0cy5hcnJheUxpbWl0O1xuICAgIG9wdGlvbnMucGFyc2VBcnJheXMgPSBvcHRpb25zLnBhcnNlQXJyYXlzICE9PSBmYWxzZTtcbiAgICBvcHRpb25zLmRlY29kZXIgPSB0eXBlb2Ygb3B0aW9ucy5kZWNvZGVyID09PSAnZnVuY3Rpb24nID8gb3B0aW9ucy5kZWNvZGVyIDogZGVmYXVsdHMuZGVjb2RlcjtcbiAgICBvcHRpb25zLmFsbG93RG90cyA9IHR5cGVvZiBvcHRpb25zLmFsbG93RG90cyA9PT0gJ2Jvb2xlYW4nID8gb3B0aW9ucy5hbGxvd0RvdHMgOiBkZWZhdWx0cy5hbGxvd0RvdHM7XG4gICAgb3B0aW9ucy5wbGFpbk9iamVjdHMgPSB0eXBlb2Ygb3B0aW9ucy5wbGFpbk9iamVjdHMgPT09ICdib29sZWFuJyA/IG9wdGlvbnMucGxhaW5PYmplY3RzIDogZGVmYXVsdHMucGxhaW5PYmplY3RzO1xuICAgIG9wdGlvbnMuYWxsb3dQcm90b3R5cGVzID0gdHlwZW9mIG9wdGlvbnMuYWxsb3dQcm90b3R5cGVzID09PSAnYm9vbGVhbicgPyBvcHRpb25zLmFsbG93UHJvdG90eXBlcyA6IGRlZmF1bHRzLmFsbG93UHJvdG90eXBlcztcbiAgICBvcHRpb25zLnBhcmFtZXRlckxpbWl0ID0gdHlwZW9mIG9wdGlvbnMucGFyYW1ldGVyTGltaXQgPT09ICdudW1iZXInID8gb3B0aW9ucy5wYXJhbWV0ZXJMaW1pdCA6IGRlZmF1bHRzLnBhcmFtZXRlckxpbWl0O1xuICAgIG9wdGlvbnMuc3RyaWN0TnVsbEhhbmRsaW5nID0gdHlwZW9mIG9wdGlvbnMuc3RyaWN0TnVsbEhhbmRsaW5nID09PSAnYm9vbGVhbicgPyBvcHRpb25zLnN0cmljdE51bGxIYW5kbGluZyA6IGRlZmF1bHRzLnN0cmljdE51bGxIYW5kbGluZztcblxuICAgIGlmIChzdHIgPT09ICcnIHx8IHN0ciA9PT0gbnVsbCB8fCB0eXBlb2Ygc3RyID09PSAndW5kZWZpbmVkJykge1xuICAgICAgICByZXR1cm4gb3B0aW9ucy5wbGFpbk9iamVjdHMgPyBPYmplY3QuY3JlYXRlKG51bGwpIDoge307XG4gICAgfVxuXG4gICAgdmFyIHRlbXBPYmogPSB0eXBlb2Ygc3RyID09PSAnc3RyaW5nJyA/IHBhcnNlVmFsdWVzKHN0ciwgb3B0aW9ucykgOiBzdHI7XG4gICAgdmFyIG9iaiA9IG9wdGlvbnMucGxhaW5PYmplY3RzID8gT2JqZWN0LmNyZWF0ZShudWxsKSA6IHt9O1xuXG4gICAgLy8gSXRlcmF0ZSBvdmVyIHRoZSBrZXlzIGFuZCBzZXR1cCB0aGUgbmV3IG9iamVjdFxuXG4gICAgdmFyIGtleXMgPSBPYmplY3Qua2V5cyh0ZW1wT2JqKTtcbiAgICBmb3IgKHZhciBpID0gMDsgaSA8IGtleXMubGVuZ3RoOyArK2kpIHtcbiAgICAgICAgdmFyIGtleSA9IGtleXNbaV07XG4gICAgICAgIHZhciBuZXdPYmogPSBwYXJzZUtleXMoa2V5LCB0ZW1wT2JqW2tleV0sIG9wdGlvbnMpO1xuICAgICAgICBvYmogPSB1dGlscy5tZXJnZShvYmosIG5ld09iaiwgb3B0aW9ucyk7XG4gICAgfVxuXG4gICAgcmV0dXJuIHV0aWxzLmNvbXBhY3Qob2JqKTtcbn07XG4iLCIndXNlIHN0cmljdCc7XG5cbnZhciB1dGlscyA9IHJlcXVpcmUoJy4vdXRpbHMnKTtcbnZhciBmb3JtYXRzID0gcmVxdWlyZSgnLi9mb3JtYXRzJyk7XG5cbnZhciBhcnJheVByZWZpeEdlbmVyYXRvcnMgPSB7XG4gICAgYnJhY2tldHM6IGZ1bmN0aW9uIGJyYWNrZXRzKHByZWZpeCkge1xuICAgICAgICByZXR1cm4gcHJlZml4ICsgJ1tdJztcbiAgICB9LFxuICAgIGluZGljZXM6IGZ1bmN0aW9uIGluZGljZXMocHJlZml4LCBrZXkpIHtcbiAgICAgICAgcmV0dXJuIHByZWZpeCArICdbJyArIGtleSArICddJztcbiAgICB9LFxuICAgIHJlcGVhdDogZnVuY3Rpb24gcmVwZWF0KHByZWZpeCkge1xuICAgICAgICByZXR1cm4gcHJlZml4O1xuICAgIH1cbn07XG5cbnZhciBpc0FycmF5ID0gQXJyYXkuaXNBcnJheTtcbnZhciBwdXNoID0gQXJyYXkucHJvdG90eXBlLnB1c2g7XG52YXIgcHVzaFRvQXJyYXkgPSBmdW5jdGlvbiAoYXJyLCB2YWx1ZU9yQXJyYXkpIHtcbiAgICBwdXNoLmFwcGx5KGFyciwgaXNBcnJheSh2YWx1ZU9yQXJyYXkpID8gdmFsdWVPckFycmF5IDogW3ZhbHVlT3JBcnJheV0pO1xufTtcblxudmFyIHRvSVNPID0gRGF0ZS5wcm90b3R5cGUudG9JU09TdHJpbmc7XG5cbnZhciBkZWZhdWx0cyA9IHtcbiAgICBkZWxpbWl0ZXI6ICcmJyxcbiAgICBlbmNvZGU6IHRydWUsXG4gICAgZW5jb2RlcjogdXRpbHMuZW5jb2RlLFxuICAgIGVuY29kZVZhbHVlc09ubHk6IGZhbHNlLFxuICAgIHNlcmlhbGl6ZURhdGU6IGZ1bmN0aW9uIHNlcmlhbGl6ZURhdGUoZGF0ZSkge1xuICAgICAgICByZXR1cm4gdG9JU08uY2FsbChkYXRlKTtcbiAgICB9LFxuICAgIHNraXBOdWxsczogZmFsc2UsXG4gICAgc3RyaWN0TnVsbEhhbmRsaW5nOiBmYWxzZVxufTtcblxudmFyIHN0cmluZ2lmeSA9IGZ1bmN0aW9uIHN0cmluZ2lmeShcbiAgICBvYmplY3QsXG4gICAgcHJlZml4LFxuICAgIGdlbmVyYXRlQXJyYXlQcmVmaXgsXG4gICAgc3RyaWN0TnVsbEhhbmRsaW5nLFxuICAgIHNraXBOdWxscyxcbiAgICBlbmNvZGVyLFxuICAgIGZpbHRlcixcbiAgICBzb3J0LFxuICAgIGFsbG93RG90cyxcbiAgICBzZXJpYWxpemVEYXRlLFxuICAgIGZvcm1hdHRlcixcbiAgICBlbmNvZGVWYWx1ZXNPbmx5XG4pIHtcbiAgICB2YXIgb2JqID0gb2JqZWN0O1xuICAgIGlmICh0eXBlb2YgZmlsdGVyID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgIG9iaiA9IGZpbHRlcihwcmVmaXgsIG9iaik7XG4gICAgfSBlbHNlIGlmIChvYmogaW5zdGFuY2VvZiBEYXRlKSB7XG4gICAgICAgIG9iaiA9IHNlcmlhbGl6ZURhdGUob2JqKTtcbiAgICB9XG5cbiAgICBpZiAob2JqID09PSBudWxsKSB7XG4gICAgICAgIGlmIChzdHJpY3ROdWxsSGFuZGxpbmcpIHtcbiAgICAgICAgICAgIHJldHVybiBlbmNvZGVyICYmICFlbmNvZGVWYWx1ZXNPbmx5ID8gZW5jb2RlcihwcmVmaXgsIGRlZmF1bHRzLmVuY29kZXIpIDogcHJlZml4O1xuICAgICAgICB9XG5cbiAgICAgICAgb2JqID0gJyc7XG4gICAgfVxuXG4gICAgaWYgKHR5cGVvZiBvYmogPT09ICdzdHJpbmcnIHx8IHR5cGVvZiBvYmogPT09ICdudW1iZXInIHx8IHR5cGVvZiBvYmogPT09ICdib29sZWFuJyB8fCB1dGlscy5pc0J1ZmZlcihvYmopKSB7XG4gICAgICAgIGlmIChlbmNvZGVyKSB7XG4gICAgICAgICAgICB2YXIga2V5VmFsdWUgPSBlbmNvZGVWYWx1ZXNPbmx5ID8gcHJlZml4IDogZW5jb2RlcihwcmVmaXgsIGRlZmF1bHRzLmVuY29kZXIpO1xuICAgICAgICAgICAgcmV0dXJuIFtmb3JtYXR0ZXIoa2V5VmFsdWUpICsgJz0nICsgZm9ybWF0dGVyKGVuY29kZXIob2JqLCBkZWZhdWx0cy5lbmNvZGVyKSldO1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiBbZm9ybWF0dGVyKHByZWZpeCkgKyAnPScgKyBmb3JtYXR0ZXIoU3RyaW5nKG9iaikpXTtcbiAgICB9XG5cbiAgICB2YXIgdmFsdWVzID0gW107XG5cbiAgICBpZiAodHlwZW9mIG9iaiA9PT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgcmV0dXJuIHZhbHVlcztcbiAgICB9XG5cbiAgICB2YXIgb2JqS2V5cztcbiAgICBpZiAoaXNBcnJheShmaWx0ZXIpKSB7XG4gICAgICAgIG9iaktleXMgPSBmaWx0ZXI7XG4gICAgfSBlbHNlIHtcbiAgICAgICAgdmFyIGtleXMgPSBPYmplY3Qua2V5cyhvYmopO1xuICAgICAgICBvYmpLZXlzID0gc29ydCA/IGtleXMuc29ydChzb3J0KSA6IGtleXM7XG4gICAgfVxuXG4gICAgZm9yICh2YXIgaSA9IDA7IGkgPCBvYmpLZXlzLmxlbmd0aDsgKytpKSB7XG4gICAgICAgIHZhciBrZXkgPSBvYmpLZXlzW2ldO1xuXG4gICAgICAgIGlmIChza2lwTnVsbHMgJiYgb2JqW2tleV0gPT09IG51bGwpIHtcbiAgICAgICAgICAgIGNvbnRpbnVlO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKGlzQXJyYXkob2JqKSkge1xuICAgICAgICAgICAgcHVzaFRvQXJyYXkodmFsdWVzLCBzdHJpbmdpZnkoXG4gICAgICAgICAgICAgICAgb2JqW2tleV0sXG4gICAgICAgICAgICAgICAgZ2VuZXJhdGVBcnJheVByZWZpeChwcmVmaXgsIGtleSksXG4gICAgICAgICAgICAgICAgZ2VuZXJhdGVBcnJheVByZWZpeCxcbiAgICAgICAgICAgICAgICBzdHJpY3ROdWxsSGFuZGxpbmcsXG4gICAgICAgICAgICAgICAgc2tpcE51bGxzLFxuICAgICAgICAgICAgICAgIGVuY29kZXIsXG4gICAgICAgICAgICAgICAgZmlsdGVyLFxuICAgICAgICAgICAgICAgIHNvcnQsXG4gICAgICAgICAgICAgICAgYWxsb3dEb3RzLFxuICAgICAgICAgICAgICAgIHNlcmlhbGl6ZURhdGUsXG4gICAgICAgICAgICAgICAgZm9ybWF0dGVyLFxuICAgICAgICAgICAgICAgIGVuY29kZVZhbHVlc09ubHlcbiAgICAgICAgICAgICkpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgcHVzaFRvQXJyYXkodmFsdWVzLCBzdHJpbmdpZnkoXG4gICAgICAgICAgICAgICAgb2JqW2tleV0sXG4gICAgICAgICAgICAgICAgcHJlZml4ICsgKGFsbG93RG90cyA/ICcuJyArIGtleSA6ICdbJyArIGtleSArICddJyksXG4gICAgICAgICAgICAgICAgZ2VuZXJhdGVBcnJheVByZWZpeCxcbiAgICAgICAgICAgICAgICBzdHJpY3ROdWxsSGFuZGxpbmcsXG4gICAgICAgICAgICAgICAgc2tpcE51bGxzLFxuICAgICAgICAgICAgICAgIGVuY29kZXIsXG4gICAgICAgICAgICAgICAgZmlsdGVyLFxuICAgICAgICAgICAgICAgIHNvcnQsXG4gICAgICAgICAgICAgICAgYWxsb3dEb3RzLFxuICAgICAgICAgICAgICAgIHNlcmlhbGl6ZURhdGUsXG4gICAgICAgICAgICAgICAgZm9ybWF0dGVyLFxuICAgICAgICAgICAgICAgIGVuY29kZVZhbHVlc09ubHlcbiAgICAgICAgICAgICkpO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgcmV0dXJuIHZhbHVlcztcbn07XG5cbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24gKG9iamVjdCwgb3B0cykge1xuICAgIHZhciBvYmogPSBvYmplY3Q7XG4gICAgdmFyIG9wdGlvbnMgPSBvcHRzID8gdXRpbHMuYXNzaWduKHt9LCBvcHRzKSA6IHt9O1xuXG4gICAgaWYgKG9wdGlvbnMuZW5jb2RlciAhPT0gbnVsbCAmJiB0eXBlb2Ygb3B0aW9ucy5lbmNvZGVyICE9PSAndW5kZWZpbmVkJyAmJiB0eXBlb2Ygb3B0aW9ucy5lbmNvZGVyICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgIHRocm93IG5ldyBUeXBlRXJyb3IoJ0VuY29kZXIgaGFzIHRvIGJlIGEgZnVuY3Rpb24uJyk7XG4gICAgfVxuXG4gICAgdmFyIGRlbGltaXRlciA9IHR5cGVvZiBvcHRpb25zLmRlbGltaXRlciA9PT0gJ3VuZGVmaW5lZCcgPyBkZWZhdWx0cy5kZWxpbWl0ZXIgOiBvcHRpb25zLmRlbGltaXRlcjtcbiAgICB2YXIgc3RyaWN0TnVsbEhhbmRsaW5nID0gdHlwZW9mIG9wdGlvbnMuc3RyaWN0TnVsbEhhbmRsaW5nID09PSAnYm9vbGVhbicgPyBvcHRpb25zLnN0cmljdE51bGxIYW5kbGluZyA6IGRlZmF1bHRzLnN0cmljdE51bGxIYW5kbGluZztcbiAgICB2YXIgc2tpcE51bGxzID0gdHlwZW9mIG9wdGlvbnMuc2tpcE51bGxzID09PSAnYm9vbGVhbicgPyBvcHRpb25zLnNraXBOdWxscyA6IGRlZmF1bHRzLnNraXBOdWxscztcbiAgICB2YXIgZW5jb2RlID0gdHlwZW9mIG9wdGlvbnMuZW5jb2RlID09PSAnYm9vbGVhbicgPyBvcHRpb25zLmVuY29kZSA6IGRlZmF1bHRzLmVuY29kZTtcbiAgICB2YXIgZW5jb2RlciA9IHR5cGVvZiBvcHRpb25zLmVuY29kZXIgPT09ICdmdW5jdGlvbicgPyBvcHRpb25zLmVuY29kZXIgOiBkZWZhdWx0cy5lbmNvZGVyO1xuICAgIHZhciBzb3J0ID0gdHlwZW9mIG9wdGlvbnMuc29ydCA9PT0gJ2Z1bmN0aW9uJyA/IG9wdGlvbnMuc29ydCA6IG51bGw7XG4gICAgdmFyIGFsbG93RG90cyA9IHR5cGVvZiBvcHRpb25zLmFsbG93RG90cyA9PT0gJ3VuZGVmaW5lZCcgPyBmYWxzZSA6IG9wdGlvbnMuYWxsb3dEb3RzO1xuICAgIHZhciBzZXJpYWxpemVEYXRlID0gdHlwZW9mIG9wdGlvbnMuc2VyaWFsaXplRGF0ZSA9PT0gJ2Z1bmN0aW9uJyA/IG9wdGlvbnMuc2VyaWFsaXplRGF0ZSA6IGRlZmF1bHRzLnNlcmlhbGl6ZURhdGU7XG4gICAgdmFyIGVuY29kZVZhbHVlc09ubHkgPSB0eXBlb2Ygb3B0aW9ucy5lbmNvZGVWYWx1ZXNPbmx5ID09PSAnYm9vbGVhbicgPyBvcHRpb25zLmVuY29kZVZhbHVlc09ubHkgOiBkZWZhdWx0cy5lbmNvZGVWYWx1ZXNPbmx5O1xuICAgIGlmICh0eXBlb2Ygb3B0aW9ucy5mb3JtYXQgPT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgIG9wdGlvbnMuZm9ybWF0ID0gZm9ybWF0c1snZGVmYXVsdCddO1xuICAgIH0gZWxzZSBpZiAoIU9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChmb3JtYXRzLmZvcm1hdHRlcnMsIG9wdGlvbnMuZm9ybWF0KSkge1xuICAgICAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdVbmtub3duIGZvcm1hdCBvcHRpb24gcHJvdmlkZWQuJyk7XG4gICAgfVxuICAgIHZhciBmb3JtYXR0ZXIgPSBmb3JtYXRzLmZvcm1hdHRlcnNbb3B0aW9ucy5mb3JtYXRdO1xuICAgIHZhciBvYmpLZXlzO1xuICAgIHZhciBmaWx0ZXI7XG5cbiAgICBpZiAodHlwZW9mIG9wdGlvbnMuZmlsdGVyID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgIGZpbHRlciA9IG9wdGlvbnMuZmlsdGVyO1xuICAgICAgICBvYmogPSBmaWx0ZXIoJycsIG9iaik7XG4gICAgfSBlbHNlIGlmIChpc0FycmF5KG9wdGlvbnMuZmlsdGVyKSkge1xuICAgICAgICBmaWx0ZXIgPSBvcHRpb25zLmZpbHRlcjtcbiAgICAgICAgb2JqS2V5cyA9IGZpbHRlcjtcbiAgICB9XG5cbiAgICB2YXIga2V5cyA9IFtdO1xuXG4gICAgaWYgKHR5cGVvZiBvYmogIT09ICdvYmplY3QnIHx8IG9iaiA9PT0gbnVsbCkge1xuICAgICAgICByZXR1cm4gJyc7XG4gICAgfVxuXG4gICAgdmFyIGFycmF5Rm9ybWF0O1xuICAgIGlmIChvcHRpb25zLmFycmF5Rm9ybWF0IGluIGFycmF5UHJlZml4R2VuZXJhdG9ycykge1xuICAgICAgICBhcnJheUZvcm1hdCA9IG9wdGlvbnMuYXJyYXlGb3JtYXQ7XG4gICAgfSBlbHNlIGlmICgnaW5kaWNlcycgaW4gb3B0aW9ucykge1xuICAgICAgICBhcnJheUZvcm1hdCA9IG9wdGlvbnMuaW5kaWNlcyA/ICdpbmRpY2VzJyA6ICdyZXBlYXQnO1xuICAgIH0gZWxzZSB7XG4gICAgICAgIGFycmF5Rm9ybWF0ID0gJ2luZGljZXMnO1xuICAgIH1cblxuICAgIHZhciBnZW5lcmF0ZUFycmF5UHJlZml4ID0gYXJyYXlQcmVmaXhHZW5lcmF0b3JzW2FycmF5Rm9ybWF0XTtcblxuICAgIGlmICghb2JqS2V5cykge1xuICAgICAgICBvYmpLZXlzID0gT2JqZWN0LmtleXMob2JqKTtcbiAgICB9XG5cbiAgICBpZiAoc29ydCkge1xuICAgICAgICBvYmpLZXlzLnNvcnQoc29ydCk7XG4gICAgfVxuXG4gICAgZm9yICh2YXIgaSA9IDA7IGkgPCBvYmpLZXlzLmxlbmd0aDsgKytpKSB7XG4gICAgICAgIHZhciBrZXkgPSBvYmpLZXlzW2ldO1xuXG4gICAgICAgIGlmIChza2lwTnVsbHMgJiYgb2JqW2tleV0gPT09IG51bGwpIHtcbiAgICAgICAgICAgIGNvbnRpbnVlO1xuICAgICAgICB9XG4gICAgICAgIHB1c2hUb0FycmF5KGtleXMsIHN0cmluZ2lmeShcbiAgICAgICAgICAgIG9ialtrZXldLFxuICAgICAgICAgICAga2V5LFxuICAgICAgICAgICAgZ2VuZXJhdGVBcnJheVByZWZpeCxcbiAgICAgICAgICAgIHN0cmljdE51bGxIYW5kbGluZyxcbiAgICAgICAgICAgIHNraXBOdWxscyxcbiAgICAgICAgICAgIGVuY29kZSA/IGVuY29kZXIgOiBudWxsLFxuICAgICAgICAgICAgZmlsdGVyLFxuICAgICAgICAgICAgc29ydCxcbiAgICAgICAgICAgIGFsbG93RG90cyxcbiAgICAgICAgICAgIHNlcmlhbGl6ZURhdGUsXG4gICAgICAgICAgICBmb3JtYXR0ZXIsXG4gICAgICAgICAgICBlbmNvZGVWYWx1ZXNPbmx5XG4gICAgICAgICkpO1xuICAgIH1cblxuICAgIHZhciBqb2luZWQgPSBrZXlzLmpvaW4oZGVsaW1pdGVyKTtcbiAgICB2YXIgcHJlZml4ID0gb3B0aW9ucy5hZGRRdWVyeVByZWZpeCA9PT0gdHJ1ZSA/ICc/JyA6ICcnO1xuXG4gICAgcmV0dXJuIGpvaW5lZC5sZW5ndGggPiAwID8gcHJlZml4ICsgam9pbmVkIDogJyc7XG59O1xuIiwiJ3VzZSBzdHJpY3QnO1xuXG52YXIgaGFzID0gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eTtcblxudmFyIGhleFRhYmxlID0gKGZ1bmN0aW9uICgpIHtcbiAgICB2YXIgYXJyYXkgPSBbXTtcbiAgICBmb3IgKHZhciBpID0gMDsgaSA8IDI1NjsgKytpKSB7XG4gICAgICAgIGFycmF5LnB1c2goJyUnICsgKChpIDwgMTYgPyAnMCcgOiAnJykgKyBpLnRvU3RyaW5nKDE2KSkudG9VcHBlckNhc2UoKSk7XG4gICAgfVxuXG4gICAgcmV0dXJuIGFycmF5O1xufSgpKTtcblxudmFyIGNvbXBhY3RRdWV1ZSA9IGZ1bmN0aW9uIGNvbXBhY3RRdWV1ZShxdWV1ZSkge1xuICAgIHZhciBvYmo7XG5cbiAgICB3aGlsZSAocXVldWUubGVuZ3RoKSB7XG4gICAgICAgIHZhciBpdGVtID0gcXVldWUucG9wKCk7XG4gICAgICAgIG9iaiA9IGl0ZW0ub2JqW2l0ZW0ucHJvcF07XG5cbiAgICAgICAgaWYgKEFycmF5LmlzQXJyYXkob2JqKSkge1xuICAgICAgICAgICAgdmFyIGNvbXBhY3RlZCA9IFtdO1xuXG4gICAgICAgICAgICBmb3IgKHZhciBqID0gMDsgaiA8IG9iai5sZW5ndGg7ICsraikge1xuICAgICAgICAgICAgICAgIGlmICh0eXBlb2Ygb2JqW2pdICE9PSAndW5kZWZpbmVkJykge1xuICAgICAgICAgICAgICAgICAgICBjb21wYWN0ZWQucHVzaChvYmpbal0pO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaXRlbS5vYmpbaXRlbS5wcm9wXSA9IGNvbXBhY3RlZDtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIHJldHVybiBvYmo7XG59O1xuXG52YXIgYXJyYXlUb09iamVjdCA9IGZ1bmN0aW9uIGFycmF5VG9PYmplY3Qoc291cmNlLCBvcHRpb25zKSB7XG4gICAgdmFyIG9iaiA9IG9wdGlvbnMgJiYgb3B0aW9ucy5wbGFpbk9iamVjdHMgPyBPYmplY3QuY3JlYXRlKG51bGwpIDoge307XG4gICAgZm9yICh2YXIgaSA9IDA7IGkgPCBzb3VyY2UubGVuZ3RoOyArK2kpIHtcbiAgICAgICAgaWYgKHR5cGVvZiBzb3VyY2VbaV0gIT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgICAgICBvYmpbaV0gPSBzb3VyY2VbaV07XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICByZXR1cm4gb2JqO1xufTtcblxudmFyIG1lcmdlID0gZnVuY3Rpb24gbWVyZ2UodGFyZ2V0LCBzb3VyY2UsIG9wdGlvbnMpIHtcbiAgICBpZiAoIXNvdXJjZSkge1xuICAgICAgICByZXR1cm4gdGFyZ2V0O1xuICAgIH1cblxuICAgIGlmICh0eXBlb2Ygc291cmNlICE9PSAnb2JqZWN0Jykge1xuICAgICAgICBpZiAoQXJyYXkuaXNBcnJheSh0YXJnZXQpKSB7XG4gICAgICAgICAgICB0YXJnZXQucHVzaChzb3VyY2UpO1xuICAgICAgICB9IGVsc2UgaWYgKHRhcmdldCAmJiB0eXBlb2YgdGFyZ2V0ID09PSAnb2JqZWN0Jykge1xuICAgICAgICAgICAgaWYgKChvcHRpb25zICYmIChvcHRpb25zLnBsYWluT2JqZWN0cyB8fCBvcHRpb25zLmFsbG93UHJvdG90eXBlcykpIHx8ICFoYXMuY2FsbChPYmplY3QucHJvdG90eXBlLCBzb3VyY2UpKSB7XG4gICAgICAgICAgICAgICAgdGFyZ2V0W3NvdXJjZV0gPSB0cnVlO1xuICAgICAgICAgICAgfVxuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgcmV0dXJuIFt0YXJnZXQsIHNvdXJjZV07XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdGFyZ2V0O1xuICAgIH1cblxuICAgIGlmICghdGFyZ2V0IHx8IHR5cGVvZiB0YXJnZXQgIT09ICdvYmplY3QnKSB7XG4gICAgICAgIHJldHVybiBbdGFyZ2V0XS5jb25jYXQoc291cmNlKTtcbiAgICB9XG5cbiAgICB2YXIgbWVyZ2VUYXJnZXQgPSB0YXJnZXQ7XG4gICAgaWYgKEFycmF5LmlzQXJyYXkodGFyZ2V0KSAmJiAhQXJyYXkuaXNBcnJheShzb3VyY2UpKSB7XG4gICAgICAgIG1lcmdlVGFyZ2V0ID0gYXJyYXlUb09iamVjdCh0YXJnZXQsIG9wdGlvbnMpO1xuICAgIH1cblxuICAgIGlmIChBcnJheS5pc0FycmF5KHRhcmdldCkgJiYgQXJyYXkuaXNBcnJheShzb3VyY2UpKSB7XG4gICAgICAgIHNvdXJjZS5mb3JFYWNoKGZ1bmN0aW9uIChpdGVtLCBpKSB7XG4gICAgICAgICAgICBpZiAoaGFzLmNhbGwodGFyZ2V0LCBpKSkge1xuICAgICAgICAgICAgICAgIHZhciB0YXJnZXRJdGVtID0gdGFyZ2V0W2ldO1xuICAgICAgICAgICAgICAgIGlmICh0YXJnZXRJdGVtICYmIHR5cGVvZiB0YXJnZXRJdGVtID09PSAnb2JqZWN0JyAmJiBpdGVtICYmIHR5cGVvZiBpdGVtID09PSAnb2JqZWN0Jykge1xuICAgICAgICAgICAgICAgICAgICB0YXJnZXRbaV0gPSBtZXJnZSh0YXJnZXRJdGVtLCBpdGVtLCBvcHRpb25zKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICB0YXJnZXQucHVzaChpdGVtKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIHRhcmdldFtpXSA9IGl0ZW07XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgICAgICByZXR1cm4gdGFyZ2V0O1xuICAgIH1cblxuICAgIHJldHVybiBPYmplY3Qua2V5cyhzb3VyY2UpLnJlZHVjZShmdW5jdGlvbiAoYWNjLCBrZXkpIHtcbiAgICAgICAgdmFyIHZhbHVlID0gc291cmNlW2tleV07XG5cbiAgICAgICAgaWYgKGhhcy5jYWxsKGFjYywga2V5KSkge1xuICAgICAgICAgICAgYWNjW2tleV0gPSBtZXJnZShhY2Nba2V5XSwgdmFsdWUsIG9wdGlvbnMpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgYWNjW2tleV0gPSB2YWx1ZTtcbiAgICAgICAgfVxuICAgICAgICByZXR1cm4gYWNjO1xuICAgIH0sIG1lcmdlVGFyZ2V0KTtcbn07XG5cbnZhciBhc3NpZ24gPSBmdW5jdGlvbiBhc3NpZ25TaW5nbGVTb3VyY2UodGFyZ2V0LCBzb3VyY2UpIHtcbiAgICByZXR1cm4gT2JqZWN0LmtleXMoc291cmNlKS5yZWR1Y2UoZnVuY3Rpb24gKGFjYywga2V5KSB7XG4gICAgICAgIGFjY1trZXldID0gc291cmNlW2tleV07XG4gICAgICAgIHJldHVybiBhY2M7XG4gICAgfSwgdGFyZ2V0KTtcbn07XG5cbnZhciBkZWNvZGUgPSBmdW5jdGlvbiAoc3RyKSB7XG4gICAgdHJ5IHtcbiAgICAgICAgcmV0dXJuIGRlY29kZVVSSUNvbXBvbmVudChzdHIucmVwbGFjZSgvXFwrL2csICcgJykpO1xuICAgIH0gY2F0Y2ggKGUpIHtcbiAgICAgICAgcmV0dXJuIHN0cjtcbiAgICB9XG59O1xuXG52YXIgZW5jb2RlID0gZnVuY3Rpb24gZW5jb2RlKHN0cikge1xuICAgIC8vIFRoaXMgY29kZSB3YXMgb3JpZ2luYWxseSB3cml0dGVuIGJ5IEJyaWFuIFdoaXRlIChtc2NkZXgpIGZvciB0aGUgaW8uanMgY29yZSBxdWVyeXN0cmluZyBsaWJyYXJ5LlxuICAgIC8vIEl0IGhhcyBiZWVuIGFkYXB0ZWQgaGVyZSBmb3Igc3RyaWN0ZXIgYWRoZXJlbmNlIHRvIFJGQyAzOTg2XG4gICAgaWYgKHN0ci5sZW5ndGggPT09IDApIHtcbiAgICAgICAgcmV0dXJuIHN0cjtcbiAgICB9XG5cbiAgICB2YXIgc3RyaW5nID0gdHlwZW9mIHN0ciA9PT0gJ3N0cmluZycgPyBzdHIgOiBTdHJpbmcoc3RyKTtcblxuICAgIHZhciBvdXQgPSAnJztcbiAgICBmb3IgKHZhciBpID0gMDsgaSA8IHN0cmluZy5sZW5ndGg7ICsraSkge1xuICAgICAgICB2YXIgYyA9IHN0cmluZy5jaGFyQ29kZUF0KGkpO1xuXG4gICAgICAgIGlmIChcbiAgICAgICAgICAgIGMgPT09IDB4MkQgLy8gLVxuICAgICAgICAgICAgfHwgYyA9PT0gMHgyRSAvLyAuXG4gICAgICAgICAgICB8fCBjID09PSAweDVGIC8vIF9cbiAgICAgICAgICAgIHx8IGMgPT09IDB4N0UgLy8gflxuICAgICAgICAgICAgfHwgKGMgPj0gMHgzMCAmJiBjIDw9IDB4MzkpIC8vIDAtOVxuICAgICAgICAgICAgfHwgKGMgPj0gMHg0MSAmJiBjIDw9IDB4NUEpIC8vIGEtelxuICAgICAgICAgICAgfHwgKGMgPj0gMHg2MSAmJiBjIDw9IDB4N0EpIC8vIEEtWlxuICAgICAgICApIHtcbiAgICAgICAgICAgIG91dCArPSBzdHJpbmcuY2hhckF0KGkpO1xuICAgICAgICAgICAgY29udGludWU7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoYyA8IDB4ODApIHtcbiAgICAgICAgICAgIG91dCA9IG91dCArIGhleFRhYmxlW2NdO1xuICAgICAgICAgICAgY29udGludWU7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoYyA8IDB4ODAwKSB7XG4gICAgICAgICAgICBvdXQgPSBvdXQgKyAoaGV4VGFibGVbMHhDMCB8IChjID4+IDYpXSArIGhleFRhYmxlWzB4ODAgfCAoYyAmIDB4M0YpXSk7XG4gICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChjIDwgMHhEODAwIHx8IGMgPj0gMHhFMDAwKSB7XG4gICAgICAgICAgICBvdXQgPSBvdXQgKyAoaGV4VGFibGVbMHhFMCB8IChjID4+IDEyKV0gKyBoZXhUYWJsZVsweDgwIHwgKChjID4+IDYpICYgMHgzRildICsgaGV4VGFibGVbMHg4MCB8IChjICYgMHgzRildKTtcbiAgICAgICAgICAgIGNvbnRpbnVlO1xuICAgICAgICB9XG5cbiAgICAgICAgaSArPSAxO1xuICAgICAgICBjID0gMHgxMDAwMCArICgoKGMgJiAweDNGRikgPDwgMTApIHwgKHN0cmluZy5jaGFyQ29kZUF0KGkpICYgMHgzRkYpKTtcbiAgICAgICAgLyogZXNsaW50IG9wZXJhdG9yLWxpbmVicmVhazogWzIsIFwiYmVmb3JlXCJdICovXG4gICAgICAgIG91dCArPSBoZXhUYWJsZVsweEYwIHwgKGMgPj4gMTgpXVxuICAgICAgICAgICAgKyBoZXhUYWJsZVsweDgwIHwgKChjID4+IDEyKSAmIDB4M0YpXVxuICAgICAgICAgICAgKyBoZXhUYWJsZVsweDgwIHwgKChjID4+IDYpICYgMHgzRildXG4gICAgICAgICAgICArIGhleFRhYmxlWzB4ODAgfCAoYyAmIDB4M0YpXTtcbiAgICB9XG5cbiAgICByZXR1cm4gb3V0O1xufTtcblxudmFyIGNvbXBhY3QgPSBmdW5jdGlvbiBjb21wYWN0KHZhbHVlKSB7XG4gICAgdmFyIHF1ZXVlID0gW3sgb2JqOiB7IG86IHZhbHVlIH0sIHByb3A6ICdvJyB9XTtcbiAgICB2YXIgcmVmcyA9IFtdO1xuXG4gICAgZm9yICh2YXIgaSA9IDA7IGkgPCBxdWV1ZS5sZW5ndGg7ICsraSkge1xuICAgICAgICB2YXIgaXRlbSA9IHF1ZXVlW2ldO1xuICAgICAgICB2YXIgb2JqID0gaXRlbS5vYmpbaXRlbS5wcm9wXTtcblxuICAgICAgICB2YXIga2V5cyA9IE9iamVjdC5rZXlzKG9iaik7XG4gICAgICAgIGZvciAodmFyIGogPSAwOyBqIDwga2V5cy5sZW5ndGg7ICsraikge1xuICAgICAgICAgICAgdmFyIGtleSA9IGtleXNbal07XG4gICAgICAgICAgICB2YXIgdmFsID0gb2JqW2tleV07XG4gICAgICAgICAgICBpZiAodHlwZW9mIHZhbCA9PT0gJ29iamVjdCcgJiYgdmFsICE9PSBudWxsICYmIHJlZnMuaW5kZXhPZih2YWwpID09PSAtMSkge1xuICAgICAgICAgICAgICAgIHF1ZXVlLnB1c2goeyBvYmo6IG9iaiwgcHJvcDoga2V5IH0pO1xuICAgICAgICAgICAgICAgIHJlZnMucHVzaCh2YWwpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfVxuXG4gICAgcmV0dXJuIGNvbXBhY3RRdWV1ZShxdWV1ZSk7XG59O1xuXG52YXIgaXNSZWdFeHAgPSBmdW5jdGlvbiBpc1JlZ0V4cChvYmopIHtcbiAgICByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS50b1N0cmluZy5jYWxsKG9iaikgPT09ICdbb2JqZWN0IFJlZ0V4cF0nO1xufTtcblxudmFyIGlzQnVmZmVyID0gZnVuY3Rpb24gaXNCdWZmZXIob2JqKSB7XG4gICAgaWYgKG9iaiA9PT0gbnVsbCB8fCB0eXBlb2Ygb2JqID09PSAndW5kZWZpbmVkJykge1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuXG4gICAgcmV0dXJuICEhKG9iai5jb25zdHJ1Y3RvciAmJiBvYmouY29uc3RydWN0b3IuaXNCdWZmZXIgJiYgb2JqLmNvbnN0cnVjdG9yLmlzQnVmZmVyKG9iaikpO1xufTtcblxubW9kdWxlLmV4cG9ydHMgPSB7XG4gICAgYXJyYXlUb09iamVjdDogYXJyYXlUb09iamVjdCxcbiAgICBhc3NpZ246IGFzc2lnbixcbiAgICBjb21wYWN0OiBjb21wYWN0LFxuICAgIGRlY29kZTogZGVjb2RlLFxuICAgIGVuY29kZTogZW5jb2RlLFxuICAgIGlzQnVmZmVyOiBpc0J1ZmZlcixcbiAgICBpc1JlZ0V4cDogaXNSZWdFeHAsXG4gICAgbWVyZ2U6IG1lcmdlXG59O1xuIiwibW9kdWxlLmV4cG9ydHMgPSB3aW5kb3dbXCJsb2Rhc2hcIl07IiwibW9kdWxlLmV4cG9ydHMgPSB3aW5kb3dbXCJ3cFwiXVtcInVybFwiXTsiLCJleHBvcnQgZGVmYXVsdCBmdW5jdGlvbiBfYXJyYXlMaWtlVG9BcnJheShhcnIsIGxlbikge1xuICBpZiAobGVuID09IG51bGwgfHwgbGVuID4gYXJyLmxlbmd0aCkgbGVuID0gYXJyLmxlbmd0aDtcbiAgZm9yICh2YXIgaSA9IDAsIGFycjIgPSBuZXcgQXJyYXkobGVuKTsgaSA8IGxlbjsgaSsrKSBhcnIyW2ldID0gYXJyW2ldO1xuICByZXR1cm4gYXJyMjtcbn0iLCJpbXBvcnQgYXJyYXlMaWtlVG9BcnJheSBmcm9tIFwiLi9hcnJheUxpa2VUb0FycmF5LmpzXCI7XG5leHBvcnQgZGVmYXVsdCBmdW5jdGlvbiBfYXJyYXlXaXRob3V0SG9sZXMoYXJyKSB7XG4gIGlmIChBcnJheS5pc0FycmF5KGFycikpIHJldHVybiBhcnJheUxpa2VUb0FycmF5KGFycik7XG59IiwiaW1wb3J0IHRvUHJvcGVydHlLZXkgZnJvbSBcIi4vdG9Qcm9wZXJ0eUtleS5qc1wiO1xuZXhwb3J0IGRlZmF1bHQgZnVuY3Rpb24gX2RlZmluZVByb3BlcnR5KG9iaiwga2V5LCB2YWx1ZSkge1xuICBrZXkgPSB0b1Byb3BlcnR5S2V5KGtleSk7XG4gIGlmIChrZXkgaW4gb2JqKSB7XG4gICAgT2JqZWN0LmRlZmluZVByb3BlcnR5KG9iaiwga2V5LCB7XG4gICAgICB2YWx1ZTogdmFsdWUsXG4gICAgICBlbnVtZXJhYmxlOiB0cnVlLFxuICAgICAgY29uZmlndXJhYmxlOiB0cnVlLFxuICAgICAgd3JpdGFibGU6IHRydWVcbiAgICB9KTtcbiAgfSBlbHNlIHtcbiAgICBvYmpba2V5XSA9IHZhbHVlO1xuICB9XG4gIHJldHVybiBvYmo7XG59IiwiZXhwb3J0IGRlZmF1bHQgZnVuY3Rpb24gX2V4dGVuZHMoKSB7XG4gIF9leHRlbmRzID0gT2JqZWN0LmFzc2lnbiA/IE9iamVjdC5hc3NpZ24uYmluZCgpIDogZnVuY3Rpb24gKHRhcmdldCkge1xuICAgIGZvciAodmFyIGkgPSAxOyBpIDwgYXJndW1lbnRzLmxlbmd0aDsgaSsrKSB7XG4gICAgICB2YXIgc291cmNlID0gYXJndW1lbnRzW2ldO1xuICAgICAgZm9yICh2YXIga2V5IGluIHNvdXJjZSkge1xuICAgICAgICBpZiAoT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKHNvdXJjZSwga2V5KSkge1xuICAgICAgICAgIHRhcmdldFtrZXldID0gc291cmNlW2tleV07XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9XG4gICAgcmV0dXJuIHRhcmdldDtcbiAgfTtcbiAgcmV0dXJuIF9leHRlbmRzLmFwcGx5KHRoaXMsIGFyZ3VtZW50cyk7XG59IiwiZXhwb3J0IGRlZmF1bHQgZnVuY3Rpb24gX2l0ZXJhYmxlVG9BcnJheShpdGVyKSB7XG4gIGlmICh0eXBlb2YgU3ltYm9sICE9PSBcInVuZGVmaW5lZFwiICYmIGl0ZXJbU3ltYm9sLml0ZXJhdG9yXSAhPSBudWxsIHx8IGl0ZXJbXCJAQGl0ZXJhdG9yXCJdICE9IG51bGwpIHJldHVybiBBcnJheS5mcm9tKGl0ZXIpO1xufSIsImV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIF9ub25JdGVyYWJsZVNwcmVhZCgpIHtcbiAgdGhyb3cgbmV3IFR5cGVFcnJvcihcIkludmFsaWQgYXR0ZW1wdCB0byBzcHJlYWQgbm9uLWl0ZXJhYmxlIGluc3RhbmNlLlxcbkluIG9yZGVyIHRvIGJlIGl0ZXJhYmxlLCBub24tYXJyYXkgb2JqZWN0cyBtdXN0IGhhdmUgYSBbU3ltYm9sLml0ZXJhdG9yXSgpIG1ldGhvZC5cIik7XG59IiwiaW1wb3J0IGFycmF5V2l0aG91dEhvbGVzIGZyb20gXCIuL2FycmF5V2l0aG91dEhvbGVzLmpzXCI7XG5pbXBvcnQgaXRlcmFibGVUb0FycmF5IGZyb20gXCIuL2l0ZXJhYmxlVG9BcnJheS5qc1wiO1xuaW1wb3J0IHVuc3VwcG9ydGVkSXRlcmFibGVUb0FycmF5IGZyb20gXCIuL3Vuc3VwcG9ydGVkSXRlcmFibGVUb0FycmF5LmpzXCI7XG5pbXBvcnQgbm9uSXRlcmFibGVTcHJlYWQgZnJvbSBcIi4vbm9uSXRlcmFibGVTcHJlYWQuanNcIjtcbmV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIF90b0NvbnN1bWFibGVBcnJheShhcnIpIHtcbiAgcmV0dXJuIGFycmF5V2l0aG91dEhvbGVzKGFycikgfHwgaXRlcmFibGVUb0FycmF5KGFycikgfHwgdW5zdXBwb3J0ZWRJdGVyYWJsZVRvQXJyYXkoYXJyKSB8fCBub25JdGVyYWJsZVNwcmVhZCgpO1xufSIsImltcG9ydCBfdHlwZW9mIGZyb20gXCIuL3R5cGVvZi5qc1wiO1xuZXhwb3J0IGRlZmF1bHQgZnVuY3Rpb24gX3RvUHJpbWl0aXZlKGlucHV0LCBoaW50KSB7XG4gIGlmIChfdHlwZW9mKGlucHV0KSAhPT0gXCJvYmplY3RcIiB8fCBpbnB1dCA9PT0gbnVsbCkgcmV0dXJuIGlucHV0O1xuICB2YXIgcHJpbSA9IGlucHV0W1N5bWJvbC50b1ByaW1pdGl2ZV07XG4gIGlmIChwcmltICE9PSB1bmRlZmluZWQpIHtcbiAgICB2YXIgcmVzID0gcHJpbS5jYWxsKGlucHV0LCBoaW50IHx8IFwiZGVmYXVsdFwiKTtcbiAgICBpZiAoX3R5cGVvZihyZXMpICE9PSBcIm9iamVjdFwiKSByZXR1cm4gcmVzO1xuICAgIHRocm93IG5ldyBUeXBlRXJyb3IoXCJAQHRvUHJpbWl0aXZlIG11c3QgcmV0dXJuIGEgcHJpbWl0aXZlIHZhbHVlLlwiKTtcbiAgfVxuICByZXR1cm4gKGhpbnQgPT09IFwic3RyaW5nXCIgPyBTdHJpbmcgOiBOdW1iZXIpKGlucHV0KTtcbn0iLCJpbXBvcnQgX3R5cGVvZiBmcm9tIFwiLi90eXBlb2YuanNcIjtcbmltcG9ydCB0b1ByaW1pdGl2ZSBmcm9tIFwiLi90b1ByaW1pdGl2ZS5qc1wiO1xuZXhwb3J0IGRlZmF1bHQgZnVuY3Rpb24gX3RvUHJvcGVydHlLZXkoYXJnKSB7XG4gIHZhciBrZXkgPSB0b1ByaW1pdGl2ZShhcmcsIFwic3RyaW5nXCIpO1xuICByZXR1cm4gX3R5cGVvZihrZXkpID09PSBcInN5bWJvbFwiID8ga2V5IDogU3RyaW5nKGtleSk7XG59IiwiZXhwb3J0IGRlZmF1bHQgZnVuY3Rpb24gX3R5cGVvZihvYmopIHtcbiAgXCJAYmFiZWwvaGVscGVycyAtIHR5cGVvZlwiO1xuXG4gIHJldHVybiBfdHlwZW9mID0gXCJmdW5jdGlvblwiID09IHR5cGVvZiBTeW1ib2wgJiYgXCJzeW1ib2xcIiA9PSB0eXBlb2YgU3ltYm9sLml0ZXJhdG9yID8gZnVuY3Rpb24gKG9iaikge1xuICAgIHJldHVybiB0eXBlb2Ygb2JqO1xuICB9IDogZnVuY3Rpb24gKG9iaikge1xuICAgIHJldHVybiBvYmogJiYgXCJmdW5jdGlvblwiID09IHR5cGVvZiBTeW1ib2wgJiYgb2JqLmNvbnN0cnVjdG9yID09PSBTeW1ib2wgJiYgb2JqICE9PSBTeW1ib2wucHJvdG90eXBlID8gXCJzeW1ib2xcIiA6IHR5cGVvZiBvYmo7XG4gIH0sIF90eXBlb2Yob2JqKTtcbn0iLCJpbXBvcnQgYXJyYXlMaWtlVG9BcnJheSBmcm9tIFwiLi9hcnJheUxpa2VUb0FycmF5LmpzXCI7XG5leHBvcnQgZGVmYXVsdCBmdW5jdGlvbiBfdW5zdXBwb3J0ZWRJdGVyYWJsZVRvQXJyYXkobywgbWluTGVuKSB7XG4gIGlmICghbykgcmV0dXJuO1xuICBpZiAodHlwZW9mIG8gPT09IFwic3RyaW5nXCIpIHJldHVybiBhcnJheUxpa2VUb0FycmF5KG8sIG1pbkxlbik7XG4gIHZhciBuID0gT2JqZWN0LnByb3RvdHlwZS50b1N0cmluZy5jYWxsKG8pLnNsaWNlKDgsIC0xKTtcbiAgaWYgKG4gPT09IFwiT2JqZWN0XCIgJiYgby5jb25zdHJ1Y3RvcikgbiA9IG8uY29uc3RydWN0b3IubmFtZTtcbiAgaWYgKG4gPT09IFwiTWFwXCIgfHwgbiA9PT0gXCJTZXRcIikgcmV0dXJuIEFycmF5LmZyb20obyk7XG4gIGlmIChuID09PSBcIkFyZ3VtZW50c1wiIHx8IC9eKD86VWl8SSludCg/Ojh8MTZ8MzIpKD86Q2xhbXBlZCk/QXJyYXkkLy50ZXN0KG4pKSByZXR1cm4gYXJyYXlMaWtlVG9BcnJheShvLCBtaW5MZW4pO1xufSIsIi8vIFRoZSBtb2R1bGUgY2FjaGVcbnZhciBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX18gPSB7fTtcblxuLy8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbmZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG5cdHZhciBjYWNoZWRNb2R1bGUgPSBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX19bbW9kdWxlSWRdO1xuXHRpZiAoY2FjaGVkTW9kdWxlICE9PSB1bmRlZmluZWQpIHtcblx0XHRyZXR1cm4gY2FjaGVkTW9kdWxlLmV4cG9ydHM7XG5cdH1cblx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcblx0dmFyIG1vZHVsZSA9IF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF0gPSB7XG5cdFx0Ly8gbm8gbW9kdWxlLmlkIG5lZWRlZFxuXHRcdC8vIG5vIG1vZHVsZS5sb2FkZWQgbmVlZGVkXG5cdFx0ZXhwb3J0czoge31cblx0fTtcblxuXHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cblx0X193ZWJwYWNrX21vZHVsZXNfX1ttb2R1bGVJZF0obW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cblx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcblx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xufVxuXG4iLCIvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuX193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG5cdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuXHRcdGZ1bmN0aW9uKCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuXHRcdGZ1bmN0aW9uKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCB7IGE6IGdldHRlciB9KTtcblx0cmV0dXJuIGdldHRlcjtcbn07IiwiLy8gZGVmaW5lIGdldHRlciBmdW5jdGlvbnMgZm9yIGhhcm1vbnkgZXhwb3J0c1xuX193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgZGVmaW5pdGlvbikge1xuXHRmb3IodmFyIGtleSBpbiBkZWZpbml0aW9uKSB7XG5cdFx0aWYoX193ZWJwYWNrX3JlcXVpcmVfXy5vKGRlZmluaXRpb24sIGtleSkgJiYgIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBrZXkpKSB7XG5cdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywga2V5LCB7IGVudW1lcmFibGU6IHRydWUsIGdldDogZGVmaW5pdGlvbltrZXldIH0pO1xuXHRcdH1cblx0fVxufTsiLCJfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmosIHByb3ApIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmosIHByb3ApOyB9IiwiLy8gZGVmaW5lIF9fZXNNb2R1bGUgb24gZXhwb3J0c1xuX193ZWJwYWNrX3JlcXVpcmVfXy5yID0gZnVuY3Rpb24oZXhwb3J0cykge1xuXHRpZih0eXBlb2YgU3ltYm9sICE9PSAndW5kZWZpbmVkJyAmJiBTeW1ib2wudG9TdHJpbmdUYWcpIHtcblx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgU3ltYm9sLnRvU3RyaW5nVGFnLCB7IHZhbHVlOiAnTW9kdWxlJyB9KTtcblx0fVxuXHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xufTsiLCIiLCIvLyBzdGFydHVwXG4vLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbi8vIFRoaXMgZW50cnkgbW9kdWxlIGlzIHJlZmVyZW5jZWQgYnkgb3RoZXIgbW9kdWxlcyBzbyBpdCBjYW4ndCBiZSBpbmxpbmVkXG52YXIgX193ZWJwYWNrX2V4cG9ydHNfXyA9IF9fd2VicGFja19yZXF1aXJlX18oXCIuL2NsaWVudC9wYWNrYWdlcy9uYXZpZ2F0aW9uL2luZGV4LmpzXCIpO1xuIiwiIl0sIm5hbWVzIjpbImZpbmQiLCJnZXQiLCJvbWl0IiwidW5pcVdpdGgiLCJpc0VxdWFsIiwiZmxhdHRlbkZpbHRlcnMiLCJmaWx0ZXJzIiwiYWxsRmlsdGVycyIsImZvckVhY2giLCJmIiwic3ViRmlsdGVycyIsInB1c2giLCJhcHBseSIsIl90b0NvbnN1bWFibGVBcnJheSIsImdldEFjdGl2ZUZpbHRlcnNGcm9tUXVlcnkiLCJxdWVyeSIsIk9iamVjdCIsImtleXMiLCJyZWR1Y2UiLCJhY3RpdmVGaWx0ZXJzIiwiY29uZmlnS2V5IiwiZmlsdGVyIiwicnVsZXMiLCJtYXRjaGVzIiwicnVsZSIsImhhc093blByb3BlcnR5IiwiZ2V0VXJsS2V5IiwidmFsdWUiLCJsZW5ndGgiLCJhbGxvd011bHRpcGxlIiwibWF0Y2giLCJmaWx0ZXJWYWx1ZSIsImtleSIsImdldERlZmF1bHRPcHRpb25WYWx1ZSIsIm9wdGlvbnMiLCJkZWZhdWx0T3B0aW9uIiwiaW5wdXQiLCJvcHRpb24iLCJjb25zb2xlIiwid2FybiIsImNvbmNhdCIsImxhYmVscyIsImFkZCIsInVuZGVmaW5lZCIsImdldFF1ZXJ5RnJvbUFjdGl2ZUZpbHRlcnMiLCJhcmd1bWVudHMiLCJwcmV2aW91c0ZpbHRlcnMiLCJwcmV2aW91c0RhdGEiLCJkYXRhIiwibmV4dERhdGEiLCJBcnJheSIsImlzQXJyYXkiLCJzb21lIiwidXJsS2V5IiwiX29iamVjdFNwcmVhZCIsIl9yZXF1aXJlIiwicmVxdWlyZSIsImNyZWF0ZUJyb3dzZXJIaXN0b3J5IiwiX3JlcXVpcmUyIiwicGFyc2UiLCJfaGlzdG9yeSIsImdldEhpc3RvcnkiLCJicm93c2VySGlzdG9yeSIsImFjdGlvbiIsImxvY2F0aW9uIiwic2VhcmNoIiwic3Vic3RyaW5nIiwicGF0aG5hbWUiLCJwYXRoIiwiY3JlYXRlSHJlZiIsInJlcGxhY2UiLCJnbyIsImJhY2siLCJmb3J3YXJkIiwiYmxvY2siLCJsaXN0ZW4iLCJsaXN0ZW5lciIsIl90aGlzIiwiYWRkUXVlcnlBcmdzIiwiaWRlbnRpdHkiLCJwaWNrQnkiLCJ1bmlxIiwiaGFzIiwibmF2VXRpbHMiLCJnZXRQYXRoIiwiZ2V0UGFnZSIsInBhZ2UiLCJnZXRTY3JlZW5Gcm9tUGF0aCIsImdldElkc0Zyb21RdWVyeSIsInF1ZXJ5U3RyaW5nIiwic3BsaXQiLCJtYXAiLCJpZCIsInBhcnNlSW50IiwiQm9vbGVhbiIsImdldElkRnJvbVF1ZXJ5IiwiZ2V0UXVlcnkiLCJnZXRTZWFyY2hXb3JkcyIsIl90eXBlb2YiLCJFcnJvciIsInNlYXJjaFdvcmQiLCJnZW5lcmF0ZVBhdGgiLCJjdXJyZW50UXVlcnkiLCJhcmdzIiwiZ2V0VGFibGVRdWVyeSIsIndoaXRlbGlzdHMiLCJkZWZhdWx0cyIsIngiLCJhY2MiLCJ3aGl0ZWxpc3QiLCJfZGVmaW5lUHJvcGVydHkiLCJvcmRlcmJ5Iiwib3JkZXIiLCJwZXJfcGFnZSIsInBhZ2VkIiwicXVlcnlLZXkiLCJxdWVyeVZhbHVlIiwib25RdWVyeUNoYW5nZSIsInBhcmFtIiwic29ydCIsInVwZGF0ZVF1ZXJ5U3RyaW5nIiwibmV3UGF0aCIsInJlbW92ZVF1ZXJ5QXJncyIsImFkZEhpc3RvcnlMaXN0ZW5lciIsIndpbmRvdyIsIndjTmF2aWdhdGlvbiIsImhpc3RvcnlQYXRjaGVkIiwiaGlzdG9yeSIsInB1c2hTdGF0ZSIsInJlcGxhY2VTdGF0ZSIsInN0YXRlIiwicHVzaFN0YXRlRXZlbnQiLCJDdXN0b21FdmVudCIsImRpc3BhdGNoRXZlbnQiLCJyZXBsYWNlU3RhdGVFdmVudCIsImFkZEV2ZW50TGlzdGVuZXIiLCJyZW1vdmVFdmVudExpc3RlbmVyIl0sInNvdXJjZVJvb3QiOiIifQ==