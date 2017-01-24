var popupManager = popupManager || {};
(function(app){

	var Popup = function() {

		var self = this,
			interval = [],
			delay = 300,
			list = null,
			counter = 0,
			total = 0;

		this.init = function( data ) {

			counter = 0;
			list = data;
			total = data.length;
		};

		this.open = function() {

			var url = '',
				id = list[counter].id,
				winname = list[counter].popup_name,
				skin = list[counter].skin,
				left = list[counter].popup_left,
				top = list[counter].popup_top,
				width = list[counter].popup_width,
				height = list[counter].popup_height,
				is_usable = list[counter].is_usable,
				period = list[counter].period,
				nowtime = list[counter].nowtime;

			if (is_usable.toLowerCase() == 'y' && period > nowtime) {
				url  = jsux.rootPath + 'popup-event?id='  + id + '&winname=' + winname;		
				openPopup(url, winname, left , top , width , height);
			}

			counter++;
			if (counter == total) {
				self.stopTimer();
			}
		};

		this.startTimer = function() {
			interval.push(setInterval(self.open, delay));
		};

		this.stopTimer = function() {

			for (var i=0; i<interval.length; i++) {
				clearInterval(interval[i]);
			}
		};

		this.load = function( id ) {

			var params = { 'id': id },
				loaded = true,
				url = '';

			if (loaded === false) {
				return;
			}
			loaded = false;

			url = jsux.rootPath + 'opener-json';
			jsux.getJSON( url, params, function(e) {

				var result = $.trim(e.result.toLowerCase());
				if ( result == 'y') {

					self.init(e.data);
					self.startTimer();
				} else {
					trace(e.msg, 1);
				}
				loaded = true;
			});
		};
	};
	
	app.popup = new Popup();
	app.open = function( id ) {
		this.popup.load(id);
	};
	app.sux_path = function( path ) {
		this.popup.sux_path = path;
	};
})(popupManager);