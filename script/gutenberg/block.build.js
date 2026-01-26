/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "chbsBookingFormBlockControl", function() { return chbsBookingFormBlockControl; });
var _lodash = lodash,
    assign = _lodash.assign;
var __ = wp.i18n.__;
var Fragment = wp.element.Fragment;
var addFilter = wp.hooks.addFilter;
var _wp$components = wp.components,
    PanelBody = _wp$components.PanelBody,
    SelectControl = _wp$components.SelectControl;
var createHigherOrderComponent = wp.compose.createHigherOrderComponent;
var registerBlockType = wp.blocks.registerBlockType;
var InspectorControls = wp.editor.InspectorControls;


function isValidBlockType(name) {
	var validBlockTypes = ['chbs-booking-form/block'];
	return validBlockTypes.includes(name);
}

var chbsBookingFormBlockControl = createHigherOrderComponent(function (BlockEdit)
{
    var bookingFormList=chbsData.booking_form_dictionary;    

	return function (props) {
		if (isValidBlockType(props.name) && props.isSelected) {
			return wp.element.createElement(
				Fragment,
				null,
				wp.element.createElement(BlockEdit, props),
				wp.element.createElement(
					InspectorControls,
					null,
					wp.element.createElement(
						PanelBody,
						{ title: __("Chauffeur Booking Form Settings", 'chauffeur-booking-form') },
						wp.element.createElement(SelectControl, {
							label: __("Chauffeur Booking Form shortcode ID", 'chauffeur-booking-form'),
							value: props.attributes.id,
							options: bookingFormList,
							onChange: function onChange(value) {
								props.setAttributes({
									id: value
								});
							}
						})
					)
				)
			);
		}
		return wp.element.createElement(BlockEdit, props);
	};
}, "chbsBookingFormBlockControl");
addFilter("editor.BlockEdit", "chbs-booking-form/control", chbsBookingFormBlockControl);

registerBlockType("chbs-booking-form/block", {
	title: __("Chauffeur Booking Form", 'chauffeur-booking-form'),
	description: __("Block for displaying Chauffeur Booking Form", 'chauffeur-booking-form'),
	icon: "welcome-widgets-menus",
	category: "chbs-booking-form",
	attributes: {
		id: {
			type: "string"
		}
	},
	edit: function edit(props) {
		var id = props.attributes.id;
		if (typeof id == "undefined" || id == "") id = __("Please select shortcode ID under settings panel", 'chauffeur-booking-form');
		return wp.element.createElement(
			Fragment,
			null,
			__("Chauffeur Booking Form: ", 'chauffeur-booking-form'),
			wp.element.createElement(
				'em',
				null,
				id
			)
		);
	},
	save: function save(props) {
		var attributes = props.attributes;
		return '[chbs_booking_form' + (typeof attributes.id != "undefined" && attributes.id != "" ? ' booking_form_id="' + attributes.id + '"' : '') + ']';
	}
});

/***/ })
/******/ ]);