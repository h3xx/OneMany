/**  
 * Copyright (c) 2011-2012, Stefan Graupner. All rights reserved.
 *  
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 
 * Redistributions of source code must retain the above copyright notice, this
 * list of conditions and the following disclaimer. Redistributions in binary
 * form must reproduce the above copyright notice, this list of conditions and
 * the following disclaimer in the documentation and/or other materials provided
 * with the distribution. THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND
 * CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A
 * PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR
 * BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER
 * IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 **/

/**
 * Because sometimes all you need is a dice.
 *
 * @author Stefan Graupner <stefan.graupner@gmail.com>
 **/
(function($) {

$.widget("ui.dice", {
	options: {
		'background': '#ffffff',
		'callback': null,
		'glyphSize': 40,
		'glyphSrc': 'dice.gif',
		'juggleTimeout': 300,
		'number': 1,
		'running': 0,
	},
	selectGlyph: function (number) {
		var self = this,
		options = self.options;
		var x, y;
		switch (number) {
default:
case 1: x =                   0; y =  0;                break;
case 2: x = 2*options.glyphSize; y =  0;                break;
case 3: x =   options.glyphSize; y =  0;                break;
case 4: x =   options.glyphSize; y = options.glyphSize; break;
case 5: x = 2*options.glyphSize; y = options.glyphSize; break;
case 6: x =                   0; y = options.glyphSize; break;
		}
		self.uiDice.css('backgroundPosition', x+'px '+y+'px');
	},
	landOnNumber: function (num) {
		this.options.number = num;
		this.uiDice.stop();
		if (options.callback && typeof(options.callback) === "function")
			options.callback(options.number);
	},
	runanim: function () {
		 var self = this,
		 options = self.options;

		 $.when($.Deferred(function(dfd) {
			 var z = self.uiDice.css('z-index');
			 self.uiDice
				 .css('z-index', 1)
				 .animate(
					 {
						 'z-index': options.juggleTimeout
					 },
					 {
						step: function(now, fx) {
							self.selectGlyph(Math.floor(Math.random() * 6) + 1);
						},
						duration: options.juggleTimeout,
						complete: dfd.resolve,
					}
				)
				.css('z-index', z);

			return dfd.promise();
		}))
		.done(function() {
			self.uiDice.stop();
			if (options.number < 0) {
				// re-run animation
				return self.runanim();
			}

			self.selectGlyph(options.number);

			if (options.callback && typeof(options.callback) === "function")
				options.callback(options.number);
		});
	},

	widget: function () {
		return this.uiDice;
	},

	_create: function () {
		 var self = this,
		 options = self.options,

		 uiDice = (self.uiDice = $('<div></div>')
			 .css({
				'background': options.background+' url('+options.glyphSrc+')',
				'height': options.glyphSize,
				'width': options.glyphSize,
			 })
			
		);
		this.element.append(uiDice);
		
	},

	_setOptions: function() {
		// _super and _superApply handle keeping the right this-context
		this._superApply( arguments );
		if (this.options.running) {
			this.runanim();
		} else {
			this.landOnNumber(this.options.number);
		}
	},

});

})(jQuery);
