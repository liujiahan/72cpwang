;( function(window) {
		var $ = window.jQuery;
		var Q = window.qh360cp;

		var config = {
			src : "pcw_caipiao" + (Q.cookie.get('cp_360_agent') ? '_tg_' + Q.cookie.get('cp_360_agent') : ''),
			domainList : ["360pay.cn", "360.cn"],
			charset : 'GBK',
			useMonitor : false,
			signIn : {
				types : ['normal'],
				defaultKeepAlive : true
			}
		}
		QHPass.init(config);

		QHPass.DEBUG = false;
		QHPass.events.on('beforeShow.signIn beforeShow.signUp', function(e, ele) {
			$(ele).parents('.quc-wrapper').addClass('quc-licai-wrapper');
		});
		var signInCon = {
			thirdPart : {
				providers : []
			}
		}
		QHPass.setConfig('signIn.thirdPart', signInCon.thirdPart);
		QHPass.events.on('afterShow.signUp', function(e, ele) {
			$(ele).find('.quc-type-email').remove();
		});

		function loginCb(param) {

			var logined_ele = $('#passport_logined_box').add($('#already_login')).add($("#logined-form-index"));
			var login_ele = $('#passport_login_box').add($('#need_login')).add($("#login-form-index"));
			logined_ele.show();
			login_ele.hide();
			logined_ele.find('.passport_user_name').text(param.username);
			logined_ele.find('.passport_user_imgurl').attr("src", param.imageUrl);
			$("#passport_logined_box").find(".passport_user_name").attr("title", param.username);
			//获取帐户其它信息
			Q.pages.user_info.up_info();

		}

		//
		function login(callback) {
			QHPass.signIn(function(param) {
				loginCb(param);
				callback && callback(param);
			});
		}

		//注册
		function reg(callback) {

			QHPass.signUp(function(param) {

				loginCb(param);
				callback && callback(param);

			});
		}


		Q.lightBox.reg = function(cb) {
			reg(cb);
		};
		Q.lightBox.login = function(cb) {
			login(cb);
		}
	}(window));
