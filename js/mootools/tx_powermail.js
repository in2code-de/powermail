/***************************************************************
*  Copyright notice
*
*  (c) 2009 Stefan Aebischer <typo3@pixtron.ch>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * TxPowermailTabs! Simple tabs using Mootools
 * version 1.0 2009-02-19 
 * 
 * Credits to Andrew Teltaw for intial idea
 * http://tetlaw.id.au/view/blog/fabtabulous-simple-tabs-using-prototype/
 *
 */
var TxPowermailTabs = new Class({
	initialize: function (element) {
		this.element = $(element);
		if (this.element) {
			this.menu = $$('#' + element + ' a');
			this.show(this.getInitialTab());
			
			this.menu.each(function (element) {
				this.setupTab(element);
			}.bind(this));
		}
	},
	setupTab: function (element) {
		var idx = this.menu.indexOf(element);
		element.addEvent('click', this.activate.bind(this, idx));
			
	},
	activate :  function (idx) {
		this.menu.each(function (element, id) {
			this.hide(id);
		}.bind(this));
	
		this.show(idx);
	},
	hide : function (id) {
		this.menu[id].removeClass('active-tab');
		$(this.tabID(this.menu[id])).removeClass('active-tab-body');
	},
	show : function (id) {
		this.menu[id].addClass('active-tab');
		$(this.tabID(this.menu[id])).addClass('active-tab-body');

	},
	tabID : function (elm) {
		return elm.href.match(/#(\w.+)/)[1];
	},
	getInitialTab : function () {
		if (document.location.href.match(/#(\w.+)/)) {
			var returnId = 0;
			var loc = RegExp.$1;
			this.menu.each(function (element, id) {
				if (element.href.match(/#(\w.+)/)[1] === loc) {
					returnId = id;
				}
			});
			
			return returnId || 0;
		} else {
			return 0;
		}
	}
});

window.addEvent("domready", function () {
	var txPowermailTabs = new TxPowermailTabs('tabs');
});

var Validator = new Class({
	initialize: function (className, error, test, options) {
		if (typeof(test) === 'function') {
			this.options = new Hash(options);
			this._test = test;
		} else {
			this.options = new Hash(test);
			this._test = function () {
				return true;
			};
		}
		this.error = error || 'Validation failed.';
		this.className = className;
	},
	test : function (value, element) {
		return (this._test(value, element) &&  this.testOptions(value, element));
	},
	testOptions: function (value, element) {
		var returnValue = true;
		if (this.options) {
			this.options.each(function (options, key) {
				if (returnValue) {
					returnValue = this.methods[key] ? this.methods[key](value, element, options) : true;
				}
			}, this);
		}
		return returnValue;
	},
	methods: {
		pattern : function (v, element, opt) {
			return ValidationMethods.get('IsEmpty').test(v) || opt.test(v);
		},
		minLength : function (v, element, opt) {
			return v.length >= opt;
		},
		maxLength : function (v, element, opt) {
			return v.length <= opt;
		},
		min : function (v, element, opt) {
			return v >= parseFloat(opt);
		}, 
		max : function (v, element, opt) {
			return v <= parseFloat(opt);
		},
		notOneOf : function (v, element, opt) {
			return opt.every(function (value) {
				return v != value;
			});
		},
		oneOf : function (v, element, opt) {
			return $A(opt).any(function (value) {
				return v == value;
			});
		},
		is : function (v, element, opt) {
			return v == opt;
		},
		isNot : function (v, element, opt) {
			return v != opt;
		},
		equalToField : function (v, element, opt) {
			return v == opt.value;
		},
		notEqualToField : function (v, element, opt) {
			return v != opt.value;
		},
		include : function (v, element, opt) {
			return opt.every(function (value) {
				return ValidationMethods.get(value).test(v, element);
			});
		}
	}
});

var Validation = new Class({
	options: {
		onSubmit : true,
		stopOnFirst : false,
		immediate : false,
		focusOnError : true,
		useTitles : false,
		onFormValidate : function (result, form) {},
		onElementValidate : function (result, elm) {}
	},
	
	initialize: function (form, options) {
		$extend(this.options, options);
	
		this.form = $(form);
		
		if (this.options.onSubmit) {
			this.form.addEvent('submit', this.onSubmit.bind(this));
		}

		if (this.options.immediate) {
			this.form.getElements('input, textarea, select').each(function (input) {
				input.addEvent('blur', function (event) { 
					this.validateField(event.target); 
				}.bind(this));
			}.bind(this));
		}
	},
	
	onSubmit: function (event) {
		if (!this.validateForm()) {
			event.stop();
		}
	},
	
	validateForm: function () {
		var returnValue = true;
		if (this.options.stopOnFirst) {
			returnValue = this.form.getElements('input, textarea, select').every(function (element) { 
				return this.validateField(element);
			}.bind(this));
		} else {
			this.form.getElements('input, textarea, select').each(function (element) { 
				var tempReturnValue = this.validateField(element);	
				if (returnValue && !tempReturnValue) {
					returnValue = tempReturnValue;
				}
			}.bind(this));
		}
		if (!returnValue && this.options.focusOnError) {
			this.form.getElements('input, textarea, select').filter(function (element) {
				return element.hasClass('validation-failed');
			})[0].focus();
		}
		this.options.onFormValidate(returnValue, this.form);
		return returnValue;
	},
	
	validateField: function (element) {
		var cn = element.className.split(' ');
		return cn.every(function (value) {
			var test = this.test(value, element);
			this.options.onElementValidate(test, element);
			return test;
		}.bind(this));
	},
	
	test: function (name, element) {
		var v = this.get(name);
		var prop = '__advice' + name;
		var advice = '';
		if (this.isVisible(element) && !v.test(element.value, element)) {
			advice = this.getAdvice(name, element);
			if (!advice) {
				var errorMsg = this.options.useTitle ? ((element && element.title) ? element.title : v.error) : v.error;
				
				advice = new Element('div');
				advice.setStyles({
					'display': 'block',
					'opacity': '0'
				});
				advice.addClass('validation-advice');
				advice.set('text', errorMsg);
				
				switch (element.type.toLowerCase()) {
					case 'checkbox':
					case 'radio':
						var p = element.parentNode.parentNode;
						advice.setProperty('id', 'advice-' + name + '-' + this.getElementId(p));
				
						if (p) {
							advice.inject(p, 'bottom');
						} else {
							advice.inject(element, 'after');
						}
						break;
					default:
						advice.setProperty('id','advice-' + name + '-' + this.getElementId(element));
						advice.inject(element, 'after');
			    }
			    
			} else {
				advice.setStyles({
					'display': 'block',
					'opacity': '0'
				});
			}
			
			advice.fade(1);
			
			element.removeClass('validation-passed');
			element.addClass('validation-failed');
			return false;
		} else {
			advice = this.getAdvice(name, element);
			if (advice) {
				advice.setStyles({
					'display': 'none',
					'opacity': '0'
				});
			}
			element[prop] = false;
			element.removeClass('validation-failed');
			element.addClass('validation-passed');
			return true;
		}
	},
	
	isVisible : function (element) {
		while(element.tagName != 'BODY') {
			if (element.getStyle('display') == 'none' || element.getStyle('opacity') == '0') {
				return false;
			}
			element = element.getParent();
		}
		return true;
	},
	
	getAdvice : function (name, element) {
		switch (element.type.toLowerCase()) {
			case 'checkbox':
			case 'radio':
				var p = element.parentNode.parentNode;
				advice = $('advice-' + name + '-' + this.getElementId(p)) || $('advice-' + this.getElementId(p));
				break;
			default:
				advice = $('advice-' + name + '-' + this.getElementId(element)) || $('advice-' + this.getElementId(element));
		}
		return advice;
	},
	
	getElementId : function (element) {
		return element.id ? element.id : element.name;
	},
	
	reset: function () {
		this.form.getElements('input, textarea, select').each(function (element) {
			var cn = element.getClassNames();
			cn.each(function (value) {
				var advice = this.getAdvice(value, element);
				if (advice) {
					advice.setStyles({
						'display': 'none',
						'opacity': '0'
					});
				}
				
				element.removeClassName('validation-failed');
				element.removeClassName('validation-passed');
			});
		});
	},
	
	get : function (name) {
		return  ValidationMethods.methods[name] ? ValidationMethods.methods[name] : ValidationMethods.methods['_LikeNoIDIEverSaw_'];
	}
});

var ValidationMethods = {
	get : function (name) {
		return  ValidationMethods.methods[name] ? ValidationMethods.methods[name] : ValidationMethods.methods['_LikeNoIDIEverSaw_'];
	},
	
	add : function (className, error, test, options) {
		var nv = {};
		nv[className] = new Validator(className, error, test, options);
		$extend(ValidationMethods.methods, nv);
	},
	
	addAllThese : function (validators) {
		var nv = {};
		validators.each(function (value) {
				nv[value[0]] = new Validator(value[0], value[1], value[2], (value.length > 3 ? value[3] : {}));
			});
		$extend(ValidationMethods.methods, nv);
	},
	methods: {
		'_LikeNoIDIEverSaw_' : new Validator('_LikeNoIDIEverSaw_', '', function () {return true;}, {})
	}
};

ValidationMethods.addAllThese([
	['IsEmpty', '', function (v) {
			return  ((v === null) || (v.length === 0));
		}],
	['required', '<!-- ###REQUIRED### -->This is a required field.<!-- ###REQUIRED### -->', function (v) {
				return !ValidationMethods.get('IsEmpty').test(v);
			}],
	['validate-number', '<!-- ###VALIDATE_REQUIRED### -->Please enter a valid number in this field.<!-- ###VALIDATE_REQUIRED### -->', function (v) {
				return ValidationMethods.get('IsEmpty').test(v) || (!isNaN(v) && !/^\s+$/.test(v));
			}],
	['validate-digits', '<!-- ###VALIDATE_DIGITS### -->Please use numbers only in this field. please avoid spaces or other characters such as dots or commas.<!-- ###VALIDATE_DIGITS### -->', function (v) {
				return ValidationMethods.get('IsEmpty').test(v) ||  !/[^\d]/.test(v);
			}],
	['validate-alpha', '<!-- ###VALIDATE_ALPHA### -->Please use letters only (a-z) in this field.<!-- ###VALIDATE_ALPHA### -->', function (v) {
				return ValidationMethods.get('IsEmpty').test(v) || /^[\sa-z\u00C0-\u00FF\-]+$/i.test(v);
			}],
	['validate-alphanum', '<!-- ###VALIDATE_ALPHANUM### -->Please use only letters (a-z) or numbers (0-9) only in this field. No spaces or other characters are allowed.<!-- ###VALIDATE_ALPHANUM### -->', function (v) {
				return ValidationMethods.get('IsEmpty').test(v) || /^[\sa-z0-9\u00C0-\u00FF\-]+$/i.test(v);
			}],
	['validate-date', '<!-- ###VALIDATE_DATE### -->Please enter a valid date.<!-- ###VALIDATE_DATE### -->', function (v) {
				var test = new Date(v);
				return ValidationMethods.get('IsEmpty').test(v) || !isNaN(test);
			}],
	['validate-email', '<!-- ###VALIDATE_EMAIL### -->Please enter a valid email address. For example fred@domain.com .<!-- ###VALIDATE_EMAIL### -->', function (v) {
				return ValidationMethods.get('IsEmpty').test(v) || /\w{1,}[@][\w\-]{1,}([.]([\w\-]{1,})){1,3}$/.test(v);
			}],
	['validate-url', '<!-- ###VALIDATE_URL### -->Please enter a valid URL.<!-- ###VALIDATE_URL### -->', function (v) {
				return ValidationMethods.get('IsEmpty').test(v) || /^(http|https|ftp):\/\/(([A-Z0-9][A-Z0-9_\-]*)(\.[A-Z0-9][A-Z0-9_\-]*)+)(:(\d+))?\/?/i.test(v);
			}],
	['validate-date-au', '<!-- ###VALIDATE_DATE_AU### -->Please use this date format: dd/mm/yyyy. For example 17/03/2006 for the 17th of March, 2006.<!-- ###VALIDATE_DATE_AU### -->', function (v) {
				if (ValidationMethods.get('IsEmpty').test(v)) {
					return true;
				}
				var regex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
				if (!regex.test(v)) {
					return false;
				}
				var d = new Date(v.replace(regex, '$2/$1/$3'));
				return ( parseInt(RegExp.$2, 10) == (1+d.getMonth()) ) && 
							(parseInt(RegExp.$1, 10) == d.getDate()) && 
							(parseInt(RegExp.$3, 10) == d.getFullYear() );
			}],
	['validate-currency-dollar', '<!-- ###VALIDATE_CURRENCY_DOLLAR### -->Please enter a valid $ amount. For example $100.00 .<!-- ###VALIDATE_CURRENCY_DOLLAR### -->', function (v) {
				return ValidationMethods.get('IsEmpty').test(v) ||  /^\$?\-?([1-9]{1}[0-9]{0,2}(\,[0-9]{3})*(\.[0-9]{0,2})?|[1-9]{1}\d*(\.[0-9]{0,2})?|0(\.[0-9]{0,2})?|(\.[0-9]{1,2})?)$/.test(v);
			}],
	['validate-selection', '<!-- ###VALIDATE_SELECTION### -->Please make a selection<!-- ###VALIDATE_SELECTION### -->', function (v,elm) {
				return elm.options ? elm.selectedIndex > 0 : !ValidationMethods.get('IsEmpty').test(v);
			}],
	['validate-one-required', '<!-- ###VALIDATE_ONE_REQUIRED### -->Please select one of the above options.<!-- ###VALIDATE_ONE_REQUIRED### -->', function (v,element) {
				var p = element.parentNode;    
				p = p.parentNode; // enable parent DIV with parent DIV - Powermail Fix #2263

				var options = p.getElements('input');
				var returnValue = false;
				
				options.each(function (element) {
					if (!returnValue && (element.type.toLowerCase() == 'checkbox' || element.type.toLowerCase()=='radio')) {
						returnValue = element.checked;
					}
				});
				
				return returnValue;
			}]
]);