/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
/**
 * Framework is the visual component of the Editor and contains the tool bar, the iframe, the textarea and the status bar
 */
define('TYPO3/CMS/Rtehtmlarea/HTMLArea/Editor/Framework',
	['TYPO3/CMS/Rtehtmlarea/HTMLArea/Util/Util',
	'TYPO3/CMS/Rtehtmlarea/HTMLArea/Util/Resizable',
	'TYPO3/CMS/Rtehtmlarea/HTMLArea/DOM/DOM',
	'TYPO3/CMS/Rtehtmlarea/HTMLArea/Util/TYPO3',
	'TYPO3/CMS/Rtehtmlarea/HTMLArea/Event/Event',
	'TYPO3/CMS/Rtehtmlarea/HTMLArea/Editor/Toolbar',
	'TYPO3/CMS/Rtehtmlarea/HTMLArea/Editor/Iframe',
	'TYPO3/CMS/Rtehtmlarea/HTMLArea/Editor/StatusBar'],
	function (Util, Resizable, Dom, Typo3, Event, Toolbar, Iframe, Statusbar) {

	/**
	 * Framework constructor
	 */
	var Framework = function (config) {
		Util.apply(this, config);
		// Set some references
		for (var i = 0, n = this.items.length; i < n; i++) {
			var item = this.items[i];
			item.framework = this;
			this[item.itemId] = item;
		}
		// Monitor iframe becoming ready
		var self = this;
		Event.one(this.iframe, 'HTMLAreaEventIframeReady', function (event) { Event.stopEvent(event); self.onIframeReady(); return false; });
		// Let the framefork render itself, but it will fail to do so if inside a hidden tab or inline element
		if (!this.isNested || Typo3.allElementsAreDisplayed(this.nestedParentElements.sorted)) {
			this.render(this.textArea.parentNode, this.textArea.id);
		} else {
			// Clone the array of nested tabs and inline levels instead of using a reference as HTMLArea.util.TYPO3.accessParentElements will modify the array
			var parentElements = [].concat(this.nestedParentElements.sorted);
			// Walk through all nested tabs and inline levels to get correct sizes
			Typo3.accessParentElements(parentElements, 'args[0].render(args[0].textArea.parentNode, args[0].textArea.id)', [this]);
		}
	};

	Framework.prototype = {

		/**
		 * Render the framework
		 *
		 * @param object container: the container into which to insert the framework
		 * @param string position: the id of the child element of the container before which the framework should be inserted
		 * @return void
		 */
		render: function (container, position) {
			this.el = document.createElement('div');
			if (this.id) {
				this.el.setAttribute('id', this.id);
			}
			if (this.cls) {
				this.el.setAttribute('class', this.cls);
			}
			var position = document.getElementById(position);
			this.el = container.insertBefore(this.el, position);
			for (var i = 0, n = this.items.length; i < n; i++) {
				var item = this.items[i];
				item.render(this.el);
			}
			this.rendered = true;
		},

		/**
		 * Get the element to which the framework is rendered
		 */
		getEl: function () {
			return this.el;
		},

		/**
		 * Initiate events monitoring
		 */
		initEventListeners: function () {
			var self = this;
			// Make the framework resizable, if configured by the user
			this.makeResizable();
			// Monitor textArea container becoming shown or hidden as it may change the height of the status bar
			Event.on(this.textAreaContainer, 'HTMLAreaEventTextAreaContainerShow', function(event) { Event.stopEvent(event); self.resizable ? self.onTextAreaShow() : self.onWindowResize(); return false; });
			// Monitor iframe becoming shown or hidden as it may change the height of the status bar
			Event.on(this.iframe, 'HTMLAreaEventIframeShow', function(event) { Event.stopEvent(event); self.resizable ? self.onIframeShow() : self.onWindowResize(); return false; });
			// Monitor window resizing
			Event.on(window, 'resize', function (event) { self.onWindowResize(); });
			// If the textarea is inside a form, on reset, re-initialize the HTMLArea content and update the toolbar
			var form = this.textArea.form;
			if (form) {
				if (typeof form.onreset === 'function') {
					if (typeof form.htmlAreaPreviousOnReset === 'undefined') {
						form.htmlAreaPreviousOnReset = [];
					}
					form.htmlAreaPreviousOnReset.push(form.onreset);
				}
				Event.on(form, 'reset', function (event) { return self.onReset(event); });
			}
		},

		/**
		 * editorId should be set in config
		 */
		editorId: null,

		/**
		 * Get a reference to the editor
		 */
		getEditor: function() {
			return RTEarea[this.editorId].editor;
		},

		/**
		 * Flag indicating whether the framework is inside a tab or inline element that may be hidden
		 * Should be set in config
		 */
		isNested: false,

		/**
		 * All nested tabs and inline levels in the sorting order they were applied
		 * Should be set in config
		 */
		nestedParentElements: {},

		/**
		 * Flag set to true when the framework is ready
		 */
		ready: false,

		/**
		 * All nested tabs and inline levels in the sorting order they were applied
		 * Should be set in config
		 */
		nestedParentElements: {},

		/**
		 * Whether the framework should be made resizable
		 * May be set in config
		 */
		resizable: false,

		/**
		 * Maximum height to which the framework may resized (in pixels)
		 * May be set in config
		 */
		maxHeight: 2000,

		/**
		 * Initial textArea dimensions
		 * Should be set in config
		 */
		textAreaInitialSize: {
			width: 0,
			contextWidth: 0,
			height: 0
		},

		/**
		 * Get the toolbar
		 */
		getToolbar: function () {
			return this.toolbar;
		},

		/**
		 * Get the iframe
		 */
		getIframe: function () {
			return this.iframe;
		},

		/**
		 * Get the textarea container
		 */
		getTextAreaContainer: function () {
			return this.textAreaContainer;
		},

		/**
		 * Get the status bar
		 */
		getStatusBar: function () {
			return this.statusBar;
		},

		/**
		 * Make the framework resizable, if configured
		 */
		makeResizable: function () {
			if (this.resizable) {
				var self = this;
				this.resizer = Resizable.makeResizable(this.getEl(), {
					minHeight: 200,
					minWidth: 300,
					maxHeight: this.maxHeight,
					stop: function (event, ui) { Event.stopEvent(event); self.onHtmlAreaResize(ui.size); return false; }
				});
			}
		},

		/**
		 * Resize the framework when the resizer handles are used
		 */
		onHtmlAreaResize: function (size) {
			Dom.setSize(this.getEl(), size);
			this.onFrameworkResize();
		},

		/**
		 * Handle the window resize event
		 * Buffer the event for IE
		 */
		onWindowResize: function () {
			var self = this;
			if (this.windowResizeTimeoutId) {
				window.clearTimeout(this.windowResizeTimeoutId);
     			}
     			this.windowResizeTimeoutId = window.setTimeout(function () { self.doWindowResize(); }, 10);
		},

		/**
		 * Size the iframe according to initial textarea size as set by Page and User TSConfig
		 */
		doWindowResize: function () {
			if (!this.isNested || Typo3.allElementsAreDisplayed(this.nestedParentElements.sorted)) {
				this.resizeFramework();
			} else {
				// Clone the array of nested tabs and inline levels instead of using a reference as HTMLArea.util.TYPO3.accessParentElements will modify the array
				var parentElements = [].concat(this.nestedParentElements.sorted);
				// Walk through all nested tabs and inline levels to get correct sizes
				Typo3.accessParentElements(parentElements, 'args[0].resizeFramework()', [this]);
			}
		},

		/**
		 * Resize the framework to its initial size
		 */
		resizeFramework: function () {
			var frameworkHeight = this.fullScreen ? Typo3.getWindowSize().height - 20 : parseInt(this.textAreaInitialSize.height) + this.toolbar.getHeight() - this.statusBar.getHeight();
			if (this.textAreaInitialSize.width.indexOf('%') === -1) {
				// Width is specified in pixels
				// Initial framework sizing
				var frameworkWidth = parseInt(this.textAreaInitialSize.width);
			} else {
				// Width is specified in %
				// Framework sizing on actual window resize
				var frameworkWidth = parseInt(((Typo3.getWindowSize().width - this.textAreaInitialSize.wizardsWidth - (this.fullScreen ? 10 : Util.getScrollBarWidth()) - Dom.getPosition(this.getEl()).x - 15) * parseInt(this.textAreaInitialSize.width))/100);
			}
			Dom.setSize(this.getEl(), { width: frameworkWidth, height: frameworkHeight});
			this.onFrameworkResize();
		},

		/**
		 * Resize the framework components
		 */
		onFrameworkResize: function () {
			Dom.setSize(this.iframe.getEl().dom, { width: this.getInnerWidth(), height: this.getInnerHeight()});
			Dom.setSize(this.textArea, { width: this.getInnerWidth(), height: this.getInnerHeight()});
		},

		/**
		 * Adjust the height to the changing size of the statusbar when the textarea is shown
		 */
		onTextAreaShow: function () {
			Dom.setSize(this.iframe.getEl().dom, { height: this.getInnerHeight()});
			Dom.setSize(this.textArea, { width: this.getInnerWidth(), height: this.getInnerHeight()});
		},

		/**
		 * Adjust the height to the changing size of the statusbar when the iframe is shown
		 */
		onIframeShow: function () {
			if (this.getInnerHeight() <= 0) {
				this.onWindowResize();
			} else {
				//this.iframe.setHeight(this.getInnerHeight());
				Dom.setSize(this.iframe.getEl().dom, { height: this.getInnerHeight()});
				Dom.setSize(this.textArea, { height: this.getInnerHeight()});
			}
		},

		/**
		 * Calculate the height available for the editing iframe
		 */
		getInnerHeight: function () {
			return Dom.getSize(this.getEl()).height - this.toolbar.getHeight() - this.statusBar.getHeight() - 5;
		},

		/**
		 * Calculate the width available for the editing iframe
		 */
		getInnerWidth: function () {
			return Dom.getSize(this.getEl()).width;
		},

		/**
		 * Fire the editor when all components of the framework are rendered and ready
		 */
		onIframeReady: function () {
			this.ready = this.rendered && this.toolbar.rendered && this.statusBar.rendered && this.textAreaContainer.rendered;
			if (this.ready) {
				this.initEventListeners();
				this.textAreaContainer.show();
				if (!this.getEditor().config.showStatusBar) {
					this.statusBar.hide();
				}
				// Set the initial size of the framework
				this.onWindowResize();
				/**
				 * @event HTMLAreaEventFrameworkReady
				 * Fires when the iframe is ready and all components are rendered
				 */
				Event.trigger(this, 'HTMLAreaEventFrameworkReady');
			} else {
				var self = this;
				window.setTimeout(function () {
					self.onIframeReady();	
				}, 50);
			}
		},

		/**
		 * Handler invoked if we are inside a form and the form is reset
		 * On reset, re-initialize the HTMLArea content and update the toolbar
		 */
		onReset: function (event) {
			this.getEditor().setHTML(this.textArea.value);
			this.toolbar.update();
			// Invoke previous reset handlers, if any
			var htmlAreaPreviousOnReset = event.target.htmlAreaPreviousOnReset;
			if (typeof htmlAreaPreviousOnReset !== 'undefined') {
				for (var i = 0, n = htmlAreaPreviousOnReset.length; i < n; i++) {
					htmlAreaPreviousOnReset[i]();
				}
			}
			return true;
		},

		/**
		 * Cleanup on framework destruction
		 */
		destroy: function () {
			Event.off(window);
			Event.off(this.textAreaContainer);
			// Cleaning references to DOM in order to avoid IE memory leaks
			var form = this.textArea.form;
			if (form) {
				Event.off(form);
				form.htmlAreaPreviousOnReset = null;
			}
			// ExtJS is not releasing any resources when the iframe is unloaded
			this.toolbar.destroy();
			this.statusBar.destroy();
			while (this.el.firstChild) {
				this.el.removeChild(this.el.firstChild);
			}
			if (this.resizer) {
				Resizable.destroy(this.resizer);
			}
			this.el = null;
			return true;
		}
	};

	return Framework;

});
