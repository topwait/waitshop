(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-login-login"],{1362:function(e,t,n){"use strict";var i;n.d(t,"b",(function(){return a})),n.d(t,"c",(function(){return o})),n.d(t,"a",(function(){return i}));var a=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("v-uni-view",{staticClass:"u-code-wrap"})},o=[]},7121:function(e,t,n){"use strict";n("a9e3"),n("ac1f"),n("5319"),Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var i={name:"u-verification-code",props:{seconds:{type:[String,Number],default:60},startText:{type:String,default:"获取验证码"},changeText:{type:String,default:"X秒重新获取"},endText:{type:String,default:"重新获取"},keepRunning:{type:Boolean,default:!1},uniqueKey:{type:String,default:""}},data:function(){return{secNum:this.seconds,timer:null,canGetCode:!0}},mounted:function(){this.checkKeepRunning()},watch:{seconds:{immediate:!0,handler:function(e){this.secNum=e}}},methods:{checkKeepRunning:function(){var e=Number(uni.getStorageSync(this.uniqueKey+"_$uCountDownTimestamp"));if(!e)return this.changeEvent(this.startText);var t=Math.floor(+new Date/1e3);this.keepRunning&&e&&e>t?(this.secNum=e-t,uni.removeStorageSync(this.uniqueKey+"_$uCountDownTimestamp"),this.start()):this.changeEvent(this.startText)},start:function(){var e=this;this.timer&&(clearInterval(this.timer),this.timer=null),this.$emit("start"),this.canGetCode=!1,this.changeEvent(this.changeText.replace(/x|X/,this.secNum)),this.setTimeToStorage(),this.timer=setInterval((function(){--e.secNum?e.changeEvent(e.changeText.replace(/x|X/,e.secNum)):(clearInterval(e.timer),e.timer=null,e.changeEvent(e.endText),e.secNum=e.seconds,e.$emit("end"),e.canGetCode=!0)}),1e3)},reset:function(){this.canGetCode=!0,clearInterval(this.timer),this.secNum=this.seconds,this.changeEvent(this.endText)},changeEvent:function(e){this.$emit("change",e)},setTimeToStorage:function(){if(this.keepRunning&&this.timer&&this.secNum>0&&this.secNum<=this.seconds){var e=Math.floor(+new Date/1e3);uni.setStorage({key:this.uniqueKey+"_$uCountDownTimestamp",data:e+Number(this.secNum)})}}},beforeDestroy:function(){this.setTimeToStorage(),clearTimeout(this.timer),this.timer=null}};t.default=i},"8b44":function(e,t,n){var i=n("24fb");t=i(!1),t.push([e.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/* 颜色 */\r\n/* 边框 */\r\n/* 引入Uview */.u-code-wrap[data-v-23f13ed6]{width:0;height:0;position:fixed;z-index:-1}',""]),e.exports=t},"8d77":function(e,t,n){"use strict";n.d(t,"b",(function(){return a})),n.d(t,"c",(function(){return o})),n.d(t,"a",(function(){return i}));var i={uForm:n("8782").default,uFormItem:n("eb61").default,uInput:n("1fb0").default,uVerificationCode:n("9974").default,uButton:n("63fe").default,uCheckbox:n("74d2").default},a=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("v-uni-view",{staticClass:"container"},[e.isWeixin&&6===e.isClient?n("v-uni-view",{staticClass:"index-mnp-widget"},[n("v-uni-view",{staticClass:"avatar"},[n("v-uni-open-data",{attrs:{type:"userAvatarUrl"}})],1),n("v-uni-view",{staticClass:"u-font-28 u-margin-top-10"},[e._v("用户昵称")]),n("v-uni-view",{staticClass:"u-font-28 u-margin-top-30"},[e._v("申请获取以下权限")]),n("v-uni-view",{staticClass:"u-font-22 u-margin-top-20 u-color-muted"},[e._v("获得你的公开信息（昵称、头像等）")]),e.isBindMobile?[n("v-uni-view",{staticClass:"u-flex u-margin-top-50"},[n("v-uni-view",{staticClass:"button-item button-item--cancel",staticStyle:{width:"270rpx","margin-top":"0"},on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.onCancelLogin.apply(void 0,arguments)}}},[e._v("暂不登录")]),n("v-uni-button",{staticClass:"button-item button-item--mnp",staticStyle:{width:"270rpx","margin-top":"0","margin-left":"30rpx"},attrs:{"open-type":"getPhoneNumber"},on:{getphonenumber:function(t){arguments[0]=t=e.$handleEvent(t),e.getPhoneNumber.apply(void 0,arguments)}}},[n("v-uni-text",[e._v("微信登录")])],1)],1)]:[n("v-uni-view",{staticClass:"button-item button-item--mnp",on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.onWxLogin.apply(void 0,arguments)}}},[n("v-uni-image",{staticClass:"u-equal-50 u-margin-right-10",attrs:{src:"/static/ic_share_wechat.png"}}),n("v-uni-text",[e._v("微信一键授权")])],1),n("v-uni-view",{staticClass:"button-item button-item--cancel",on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.onCancelLogin.apply(void 0,arguments)}}},[e._v("暂不登录")])]],2):e._e(),e.isWeixin&&5!==e.isClient?e._e():n("v-uni-view",{staticClass:"index-mobile-widget"},[n("v-uni-view",{staticClass:"logo"},[n("v-uni-image",{staticStyle:{width:"560rpx",height:"156rpx"},attrs:{src:e.config.login_logo}})],1),n("u-form",{ref:"uForm",attrs:{model:e.form}},[n("u-form-item",[n("u-input",{attrs:{type:"number",placeholder:"请输入手机号"},model:{value:e.form.mobile,callback:function(t){e.$set(e.form,"mobile",t)},expression:"form.mobile"}})],1),2===e.loginType?n("u-form-item",[n("u-input",{attrs:{type:"number",placeholder:"请输入验证码"},model:{value:e.form.code,callback:function(t){e.$set(e.form,"code",t)},expression:"form.code"}}),n("u-form-item",{attrs:{slot:"right","border-bottom":!1},slot:"right"},[n("u-verification-code",{ref:"uCode",attrs:{seconds:e.seconds},on:{change:function(t){arguments[0]=t=e.$handleEvent(t),e.onChangeSms.apply(void 0,arguments)}}}),n("u-button",{attrs:{plain:!0,type:"error","hover-class":"none",size:"mini",shape:"circle"},on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.onSendSms.apply(void 0,arguments)}}},[e._v(e._s(e.smsTips))])],1)],1):e._e(),1===e.loginType?n("u-form-item",[n("u-input",{attrs:{type:"password",placeholder:"请输入密码"},model:{value:e.form.password,callback:function(t){e.$set(e.form,"password",t)},expression:"form.password"}})],1):e._e()],1),n("v-uni-view",{staticClass:"button-item button-item--mobile",on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.onMobileLogin.apply(void 0,arguments)}}},[e._v("登录")]),n("v-uni-view",{staticClass:"u-flex u-row-between u-margin-tb-40"},[this.config.login_way&&this.config.login_way.length>=2?n("v-uni-view",{staticClass:"u-font-28 u-color-lighter",on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.onSwitchLoginType.apply(void 0,arguments)}}},[e._v(e._s(0==e.loginType?"账号密码登录":"短信验证码登录"))]):n("v-uni-view"),n("v-uni-view",{staticClass:"u-font-28 u-color-lighter",on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.$toPage("/pages/register/register")}}},[e._v("注册账号")])],1),e.isWeixin&&5!==e.isClient?n("v-uni-view",{staticClass:"other"},[n("v-uni-view",{staticClass:"header"},[n("v-uni-view",{staticClass:"line"}),n("v-uni-view",{staticClass:"title"},[e._v("其他登录方式")]),n("v-uni-view",{staticClass:"line"})],1),n("v-uni-view",{staticClass:"u-margin-tb-40"},[n("v-uni-view",{staticClass:"wx",staticStyle:{"text-align":"center"},on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.onWxLogin.apply(void 0,arguments)}}},[n("v-uni-image",{staticClass:"u-equal-80 u-margin-right-10",attrs:{src:"/static/ic_share_wechat.png"}}),n("v-uni-view",{staticClass:"u-font-26 u-color-normal u-margin-top-10"},[e._v("微信登录")])],1)],1)],1):e._e()],1),n("v-uni-view",{staticClass:"index-agreement-widget"},[n("v-uni-view",{staticClass:"login-text"},[n("u-checkbox",{attrs:{"active-color":"#FF5058"},on:{change:function(t){arguments[0]=t=e.$handleEvent(t),e.onCheckboxChange.apply(void 0,arguments)}},model:{value:e.isAgree,callback:function(t){e.isAgree=t},expression:"isAgree"}}),n("v-uni-text",[e._v("已阅读并同意WaitShop")]),n("v-uni-text",{staticStyle:{color:"#FF2C3C"},on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.$toPage("/bundle/pages/user_policy/user_policy?type=service")}}},[e._v("《服务协议》")]),n("v-uni-text",[e._v("和")]),n("v-uni-text",{staticStyle:{color:"#FF2C3C"},on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.$toPage("/bundle/pages/user_policy/user_policy?type=privacy")}}},[e._v("《隐私协议》")])],1)],1)],1)},o=[]},"972d":function(e,t,n){"use strict";var i=n("ae65"),a=n.n(i);a.a},9974:function(e,t,n){"use strict";n.r(t);var i=n("1362"),a=n("aa29");for(var o in a)"default"!==o&&function(e){n.d(t,e,(function(){return a[e]}))}(o);n("972d");var r,s=n("f0c5"),u=Object(s["a"])(a["default"],i["b"],i["c"],!1,null,"23f13ed6",null,!1,i["a"],r);t["default"]=u.exports},aa29:function(e,t,n){"use strict";n.r(t);var i=n("7121"),a=n.n(i);for(var o in i)"default"!==o&&function(e){n.d(t,e,(function(){return i[e]}))}(o);t["default"]=a.a},ae65:function(e,t,n){var i=n("8b44");"string"===typeof i&&(i=[[e.i,i,""]]),i.locals&&(e.exports=i.locals);var a=n("4f06").default;a("3ed55779",i,!0,{sourceMap:!1,shadowMode:!1})},b543:function(e,t,n){var i=n("24fb");t=i(!1),t.push([e.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/* 颜色 */\r\n/* 边框 */\r\n/* 引入Uview */uni-page-body[data-v-7af40946]{background-color:#fff}.button-item[data-v-7af40946]{display:flex;justify-content:center;align-items:center;font-size:%?28?%;color:#fff;width:%?580?%;height:%?86?%;border-radius:%?50?%}.button-item--mnp[data-v-7af40946]{margin-top:%?60?%;border:1px solid #01d171;background-color:#01d171}.button-item--cancel[data-v-7af40946]{margin-top:%?30?%;color:#999;border:1px solid #999;background-color:#fff}.button-item--mobile[data-v-7af40946]{width:100%;margin-top:%?60?%;background-color:#ff5058}.index-mnp-widget[data-v-7af40946]{display:flex;flex-direction:column;align-items:center;margin-top:%?200?%}.index-mnp-widget .avatar[data-v-7af40946]{width:%?120?%;height:%?120?%;border-radius:50%;border:1px solid #eee;overflow:hidden;background-color:#eee}.index-mobile-widget[data-v-7af40946]{margin:0 %?40?%}.index-mobile-widget .logo[data-v-7af40946]{text-align:center;margin:%?60?% 0}.index-mobile-widget .other[data-v-7af40946]{margin-top:%?100?%;display:flex;flex-direction:column;align-items:center;justify-content:center}.index-mobile-widget .other .header[data-v-7af40946]{display:flex;align-items:center}.index-mobile-widget .other .header .line[data-v-7af40946]{width:%?180?%;border-bottom:1px solid #f2f2f2}.index-mobile-widget .other .header .title[data-v-7af40946]{padding:0 %?20?%;font-size:%?26?%;color:#999}.index-agreement-widget[data-v-7af40946]{position:absolute;bottom:%?40?%;width:100%;text-align:center;font-size:%?24?%;color:#666}.index-agreement-widget .u-checkbox__label[data-v-7af40946]{margin-right:0!important}body.?%PAGE?%[data-v-7af40946]{background-color:#fff}',""]),e.exports=t},c0d2:function(e,t,n){"use strict";n.r(t);var i=n("8d77"),a=n("f152");for(var o in a)"default"!==o&&function(e){n.d(t,e,(function(){return a[e]}))}(o);n("faaf");var r,s=n("f0c5"),u=Object(s["a"])(a["default"],i["b"],i["c"],!1,null,"7af40946",null,!1,i["a"],r);t["default"]=u.exports},d6bc:function(e,t,n){"use strict";var i=n("4ea4");n("26e9"),n("e25e"),Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0,n("96cf");var a=i(n("1da1")),o=i(n("5530")),r=n("ada8"),s=n("26cb"),u={data:function(){return{isBindMobile:!1,loginType:2,config:[],isWeixin:!0,isClient:0,isAgree:!1,smsTips:"",seconds:60,userInfo:null,wxCode:null,mobile:"",form:{mobile:"",code:"",password:""}}},onLoad:function(){this.isLogin&&uni.reLaunch({url:"/pages/index/index"}),this.getConfigFun(),this.isClient=(0,r.isClient)(),this.isWeixin=(0,r.isWeixin)()},computed:(0,o.default)({},(0,s.mapState)(["isLogin"])),methods:(0,o.default)((0,o.default)({},(0,s.mapMutations)(["LOGIN"])),{},{getConfigFun:function(){var e=this;return(0,a.default)(regeneratorRuntime.mark((function t(){return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:e.$u.api.apiLoginConfig().then((function(t){0===t.code?(e.config=t.data,e.config.login_way.length>0&&(e.config.login_way=e.config.login_way.reverse(),e.loginType=parseInt(e.config.login_way[0]))):e.$showToast("loading config error")}));case 1:case"end":return t.stop()}}),t)})))()},getPhoneNumber:function(e){var t=this;return(0,a.default)(regeneratorRuntime.mark((function n(){var i;return regeneratorRuntime.wrap((function(n){while(1)switch(n.prev=n.next){case 0:if("getPhoneNumber:ok"===e.detail.errMsg){n.next=3;break}return t.$showToast(e.detail.errMsg),n.abrupt("return");case 3:return uni.showLoading({title:"登录中...",mask:!0}),n.next=6,(0,r.getWxCode)();case 6:n.sent,i={encryptedData:e.detail.encryptedData,iv:e.detail.iv,code:t.wxCode,scene:"mnp",client:(0,r.isClient)(),avatarUrl:t.userInfo.avatarUrl,nickName:t.userInfo.nickName,gender:t.userInfo.gender},t.$u.api.apiLogin(i).then((function(e){0===e.code?(t.LOGIN(e.data),uni.hideLoading(),"pages/login/login"!==(0,r.currentPage)().route?uni.navigateBack({delta:1}):uni.reLaunch({url:"/pages/index/index"})):t.$showToast(e.msg)}));case 9:case"end":return n.stop()}}),n)})))()},onCancelLogin:function(){uni.navigateBack({})},onSwitchLoginType:function(){this.loginType=1===this.loginType?2:1},onWxLogin:function(){var e=this;return(0,a.default)(regeneratorRuntime.mark((function t(){var n,i,a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(e.isAgree){t.next=2;break}return t.abrupt("return",e.$showToast("请阅读服务协议并同意"));case 2:if(6===(0,r.isClient)()){t.next=4;break}return t.abrupt("return",e.$showToast("非微信环境禁止操作"));case 4:if(e.userInfo){t.next=10;break}return t.next=7,(0,r.getUserProfile)();case 7:n=t.sent,e.userInfo=n.userInfo,e.isBindMobile=0!=e.config.force_mobile;case 10:return t.next=12,(0,r.getWxCode)();case 12:i=t.sent,e.wxCode=i,0==e.config.force_mobile&&(uni.showLoading({title:"登录中...",mask:!0}),a={scene:"mnp",client:(0,r.isClient)(),code:e.wxCode,avatarUrl:e.userInfo.avatarUrl,nickName:e.userInfo.nickName,gender:e.userInfo.gender},e.$u.api.apiLogin(a).then((function(t){0===t.code?(e.LOGIN(t.data),uni.hideLoading(),"pages/login/login"!==(0,r.currentPage)().route?uni.navigateBack({delta:1}):uni.reLaunch({url:"/pages/index/index"})):e.$showToast(t.msg)})));case 15:case"end":return t.stop()}}),t)})))()},onMobileLogin:function(){var e=this;if(!this.isAgree)return this.$showToast("请阅读服务协议并同意");if(""==this.form.mobile)return this.$u.toast("手机号不能为空");if(11!=this.form.mobile.length)return this.$u.toast("手机号输入有误");if(2==this.loginType){if(""==this.form.code)return this.$u.toast("请输入验证码")}else{if(""==this.form.password)return this.$u.toast("登录密码不能为空");if(this.form.password.length<6)return this.$u.toast("登录密码错误");if(this.form.password.length>20)return this.$u.toast("登录密码错误")}var t={scene:2===this.loginType?"mobile":"account",client:(0,r.isClient)(),mobile:this.form.mobile,code:this.form.code,password:this.form.password};uni.showLoading({title:"登录中...",mask:!0}),this.$u.api.apiLogin(t).then((function(t){0===t.code?(e.LOGIN(t.data),uni.hideLoading(),"pages/login/login"!==(0,r.currentPage)().route?uni.navigateBack({delta:1}):uni.reLaunch({url:"/pages/index/index"})):e.$showToast(t.msg)}))},onSendSms:function(){var e=this;return""==this.form.mobile?this.$u.toast("手机号不能为空"):11!=this.form.mobile.length?this.$u.toast("手机号输入有误"):void(this.$refs.uCode.canGetCode?(uni.showLoading({title:"正在获取验证码"}),this.$u.api.apiSendSms({mobile:this.form.mobile,scene:110}).then((function(t){uni.hideLoading(),0===t.code?(e.$u.toast("验证码已发送"),e.$refs.uCode.start()):e.$u.toast(t.msg)}))):this.$u.toast("倒计时结束后再发送"))},onChangeSms:function(e){this.smsTips=e},onCheckboxChange:function(){this.isAgree=!this.isAgree}})};t.default=u},d884:function(e,t,n){var i=n("b543");"string"===typeof i&&(i=[[e.i,i,""]]),i.locals&&(e.exports=i.locals);var a=n("4f06").default;a("4b0c9053",i,!0,{sourceMap:!1,shadowMode:!1})},f152:function(e,t,n){"use strict";n.r(t);var i=n("d6bc"),a=n.n(i);for(var o in i)"default"!==o&&function(e){n.d(t,e,(function(){return i[e]}))}(o);t["default"]=a.a},faaf:function(e,t,n){"use strict";var i=n("d884"),a=n.n(i);a.a}}]);