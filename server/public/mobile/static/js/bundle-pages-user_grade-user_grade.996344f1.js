(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["bundle-pages-user_grade-user_grade"],{"0941":function(t,e,i){"use strict";i.r(e);var a=i("e90f"),n=i("72a0");for(var r in n)"default"!==r&&function(t){i.d(e,t,(function(){return n[t]}))}(r);i("a204");var o,s=i("f0c5"),u=Object(s["a"])(n["default"],a["b"],a["c"],!1,null,"2de981a9",null,!1,a["a"],o);e["default"]=u.exports},"0b4a":function(t,e,i){"use strict";var a=i("c21a"),n=i.n(a);n.a},"1edd":function(t,e,i){"use strict";i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return r})),i.d(e,"a",(function(){return a}));var a={uIcon:i("0366f").default},n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"u-image",style:[t.wrapStyle,t.backgroundStyle],on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.onClick.apply(void 0,arguments)}}},[t.isError?t._e():i("v-uni-image",{staticClass:"u-image__image",style:{borderRadius:"circle"==t.shape?"50%":t.$u.addUnit(t.borderRadius)},attrs:{src:t.src,mode:t.mode,"lazy-load":t.lazyLoad,"show-menu-by-longpress":t.showMenuByLongpress},on:{error:function(e){arguments[0]=e=t.$handleEvent(e),t.onErrorHandler.apply(void 0,arguments)},load:function(e){arguments[0]=e=t.$handleEvent(e),t.onLoadHandler.apply(void 0,arguments)}}}),t.showLoading&&t.loading?i("v-uni-view",{staticClass:"u-image__loading",style:{borderRadius:"circle"==t.shape?"50%":t.$u.addUnit(t.borderRadius),backgroundColor:this.bgColor}},[t.$slots.loading?t._t("loading"):i("u-icon",{attrs:{name:t.loadingIcon,width:t.width,height:t.height}})],2):t._e(),t.showError&&t.isError&&!t.loading?i("v-uni-view",{staticClass:"u-image__error",style:{borderRadius:"circle"==t.shape?"50%":t.$u.addUnit(t.borderRadius)}},[t.$slots.error?t._t("error"):i("u-icon",{attrs:{name:t.errorIcon,width:t.width,height:t.height}})],2):t._e()],1)},r=[]},2400:function(t,e,i){var a=i("877d");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("7c200cdd",a,!0,{sourceMap:!1,shadowMode:!1})},2814:function(t,e,i){"use strict";i("a9e3"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a={name:"u-image",props:{src:{type:String,default:""},mode:{type:String,default:"aspectFill"},width:{type:[String,Number],default:"100%"},height:{type:[String,Number],default:"auto"},shape:{type:String,default:"square"},borderRadius:{type:[String,Number],default:0},lazyLoad:{type:Boolean,default:!0},showMenuByLongpress:{type:Boolean,default:!0},loadingIcon:{type:String,default:"photo"},errorIcon:{type:String,default:"error-circle"},showLoading:{type:Boolean,default:!0},showError:{type:Boolean,default:!0},fade:{type:Boolean,default:!0},webp:{type:Boolean,default:!1},duration:{type:[String,Number],default:500},bgColor:{type:String,default:"#f3f4f6"}},data:function(){return{isError:!1,loading:!0,opacity:1,durationTime:this.duration,backgroundStyle:{}}},watch:{src:{immediate:!0,handler:function(t){t?this.isError=!1:(this.isError=!0,this.loading=!1)}}},computed:{wrapStyle:function(){var t={};return t.width=this.$u.addUnit(this.width),t.height=this.$u.addUnit(this.height),t.borderRadius="circle"==this.shape?"50%":this.$u.addUnit(this.borderRadius),t.overflow=this.borderRadius>0?"hidden":"visible",this.fade&&(t.opacity=this.opacity,t.transition="opacity ".concat(Number(this.durationTime)/1e3,"s ease-in-out")),t}},methods:{onClick:function(){this.$emit("click")},onErrorHandler:function(t){this.loading=!1,this.isError=!0,this.$emit("error",t)},onLoadHandler:function(){var t=this;if(this.loading=!1,this.isError=!1,this.$emit("load"),!this.fade)return this.removeBgColor();this.opacity=0,this.durationTime=0,setTimeout((function(){t.durationTime=t.duration,t.opacity=1,setTimeout((function(){t.removeBgColor()}),t.durationTime)}),50)},removeBgColor:function(){this.backgroundStyle={backgroundColor:"transparent"}}}};e.default=a},4550:function(t,e,i){"use strict";i.r(e);var a=i("e87c"),n=i("5d49");for(var r in n)"default"!==r&&function(t){i.d(e,t,(function(){return n[t]}))}(r);i("5971");var o,s=i("f0c5"),u=Object(s["a"])(n["default"],a["b"],a["c"],!1,null,"fe5aece0",null,!1,a["a"],o);e["default"]=u.exports},5971:function(t,e,i){"use strict";var a=i("2400"),n=i.n(a);n.a},"5d49":function(t,e,i){"use strict";i.r(e);var a=i("d1e4"),n=i.n(a);for(var r in a)"default"!==r&&function(t){i.d(e,t,(function(){return a[t]}))}(r);e["default"]=n.a},"72a0":function(t,e,i){"use strict";i.r(e);var a=i("ce05"),n=i.n(a);for(var r in a)"default"!==r&&function(t){i.d(e,t,(function(){return a[t]}))}(r);e["default"]=n.a},"814f":function(t,e,i){var a=i("24fb");e=a(!1),e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/* 颜色 */\r\n/* 边框 */\r\n/* 引入Uview */.u-image[data-v-c22e95ae]{position:relative;transition:opacity .5s ease-in-out}.u-image__image[data-v-c22e95ae]{width:100%;height:100%}.u-image__loading[data-v-c22e95ae], .u-image__error[data-v-c22e95ae]{position:absolute;top:0;left:0;width:100%;height:100%;display:flex;flex-direction:row;align-items:center;justify-content:center;background-color:#f3f4f6;color:#909399;font-size:%?46?%}',""]),t.exports=e},"877d":function(t,e,i){var a=i("24fb");e=a(!1),e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/* 颜色 */\r\n/* 边框 */\r\n/* 引入Uview */.u-progress[data-v-fe5aece0]{overflow:hidden;height:15px;display:inline-flex;align-items:center;width:100%;border-radius:%?100?%}.u-active[data-v-fe5aece0]{width:0;height:100%;align-items:center;display:flex;flex-direction:row;justify-items:flex-end;justify-content:space-around;font-size:%?20?%;color:#fff;transition:all .4s ease}.u-striped[data-v-fe5aece0]{background-image:linear-gradient(45deg,hsla(0,0%,100%,.15) 25%,transparent 0,transparent 50%,hsla(0,0%,100%,.15) 0,hsla(0,0%,100%,.15) 75%,transparent 0,transparent);background-size:39px 39px}.u-striped-active[data-v-fe5aece0]{-webkit-animation:progress-stripes-data-v-fe5aece0 2s linear infinite;animation:progress-stripes-data-v-fe5aece0 2s linear infinite}@-webkit-keyframes progress-stripes-data-v-fe5aece0{0%{background-position:0 0}100%{background-position:39px 0}}@keyframes progress-stripes-data-v-fe5aece0{0%{background-position:0 0}100%{background-position:39px 0}}',""]),t.exports=e},a204:function(t,e,i){"use strict";var a=i("c5c2"),n=i.n(a);n.a},adf9:function(t,e,i){"use strict";i.r(e);var a=i("1edd"),n=i("c3d0");for(var r in n)"default"!==r&&function(t){i.d(e,t,(function(){return n[t]}))}(r);i("0b4a");var o,s=i("f0c5"),u=Object(s["a"])(n["default"],a["b"],a["c"],!1,null,"c22e95ae",null,!1,a["a"],o);e["default"]=u.exports},c21a:function(t,e,i){var a=i("814f");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("7153c700",a,!0,{sourceMap:!1,shadowMode:!1})},c3d0:function(t,e,i){"use strict";i.r(e);var a=i("2814"),n=i.n(a);for(var r in a)"default"!==r&&function(t){i.d(e,t,(function(){return a[t]}))}(r);e["default"]=n.a},c5c2:function(t,e,i){var a=i("e3f51");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("2a476f84",a,!0,{sourceMap:!1,shadowMode:!1})},ce05:function(t,e,i){"use strict";i("4160"),i("159b"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a=i("8837"),n={data:function(){return{isFirstLoading:!0,currentIndex:0,gradeList:[]}},onShow:function(){this.getGradeList()},methods:{bindChange:function(t){var e=t.detail.current;this.currentIndex=e},getGradeList:function(){var t=this;this.$u.api.apiGradeList().then((function(e){0===e.code?(t.gradeList=e.data,e.data.forEach((function(e,i){e.id===e.userInfo["grade_id"]&&(t.currentIndex=i)})),t.$nextTick((function(){t.isFirstLoading=!1}))):t.$showToast("loading data exception")}))},onJump:function(t){(0,a.toPage)(t)}}};e.default=n},d1e4:function(t,e,i){"use strict";i("a9e3"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a={name:"u-line-progress",props:{round:{type:Boolean,default:!0},type:{type:String,default:""},activeColor:{type:String,default:"#19be6b"},inactiveColor:{type:String,default:"#ececec"},percent:{type:Number,default:0},showPercent:{type:Boolean,default:!0},height:{type:[Number,String],default:28},striped:{type:Boolean,default:!1},stripedActive:{type:Boolean,default:!1}},data:function(){return{}},computed:{progressStyle:function(){var t={};return t.width=this.percent+"%",this.activeColor&&(t.backgroundColor=this.activeColor),t}},methods:{}};e.default=a},e3f51:function(t,e,i){var a=i("24fb");e=a(!1),e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/* 颜色 */\r\n/* 边框 */\r\n/* 引入Uview */.index-header-widget[data-v-2de981a9]{position:relative;width:100%;height:%?230?%;z-index:100;overflow:hidden}.index-header-widget[data-v-2de981a9]::after{width:140%;height:%?230?%;position:absolute;left:-20%;top:0;z-index:100;content:"";border-radius:0 0 50% 50%;background-color:#313131}.index-swiper-widget[data-v-2de981a9]{position:absolute;top:%?20?%;z-index:2000;width:100%}.index-swiper-widget .grade-card-item[data-v-2de981a9]{height:%?320?%;width:%?600?%;position:relative;background-size:100% 100%}.index-swiper-widget .grade-card-item .card-left[data-v-2de981a9]{padding:%?26?%;padding-right:0}.index-swiper-widget .grade-card-item .reach[data-v-2de981a9]{position:absolute;top:%?30?%;right:0;font-size:%?24?%;color:#fff;padding:%?8?% %?26?%;border-top-left-radius:%?30?%;border-bottom-left-radius:%?30?%;background-color:#4c4c4c}.index-privilege-widget[data-v-2de981a9]{width:100%;padding:%?160?% 0 %?20?% 0;background-color:#fff}.index-privilege-widget .main[data-v-2de981a9]{margin-top:%?40?%;margin-bottom:%?20?%}.index-privilege-widget .main .item[data-v-2de981a9]{width:25%}.index-task-widget[data-v-2de981a9]{color:#5c5c5c;padding:0 %?34?%;margin:%?20?% 0;background-color:#fff}.index-task-widget .task-item[data-v-2de981a9]{display:flex;align-items:center;justify-content:space-between;padding:%?34?% 0}.index-task-widget .task-item .icon[data-v-2de981a9]{display:flex;align-items:center;justify-content:center;width:%?80?%;height:%?80?%;background-color:#fff6f1}.index-task-widget .button[data-v-2de981a9]{font-size:%?28?%;color:#fff;text-align:center;width:%?160?%;height:%?60?%;line-height:%?60?%;border-radius:%?60?%;background-color:#c2926a}',""]),t.exports=e},e87c:function(t,e,i){"use strict";var a;i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return r})),i.d(e,"a",(function(){return a}));var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"u-progress",style:{borderRadius:t.round?"100rpx":0,height:t.height+"rpx",backgroundColor:t.inactiveColor}},[i("v-uni-view",{staticClass:"u-active",class:[t.type?"u-type-"+t.type+"-bg":"",t.striped?"u-striped":"",t.striped&&t.stripedActive?"u-striped-active":""],style:[t.progressStyle]},[t.$slots.default||t.$slots.$default?t._t("default"):t.showPercent?[t._v(t._s(t.percent+"%"))]:t._e()],2)],1)},r=[]},e90f:function(t,e,i){"use strict";i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return r})),i.d(e,"a",(function(){return a}));var a={waitLoading:i("4627").default,uIcon:i("0366f").default,uLineProgress:i("4550").default,waitTitle:i("e6f8").default,uImage:i("adf9").default},n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"container"},[t.isFirstLoading?i("wait-loading"):t._e(),i("v-uni-view",{staticClass:"index-header-widget"},[i("v-uni-view",{staticClass:"u-bg-white",staticStyle:{height:"300rpx"}})],1),i("v-uni-view",{staticClass:"index-swiper-widget"},[i("v-uni-swiper",{staticClass:"swiper",staticStyle:{height:"320rpx"},attrs:{"previous-margin":"60rpx","next-margin":"50rpx","display-multiple-items":"1",current:t.currentIndex},on:{change:function(e){arguments[0]=e=t.$handleEvent(e),t.bindChange.apply(void 0,arguments)}}},t._l(t.gradeList,(function(e,a){return i("v-uni-swiper-item",{key:a},[i("v-uni-view",{staticClass:"grade-card-item",style:"background-image: url("+e.background+");"},[i("v-uni-view",{staticClass:"card-left"},[i("v-uni-view",{staticClass:"u-flex u-margin-bottom-40"},[i("v-uni-view",{staticStyle:{width:"100rpx",height:"100rpx","border-radius":"50%"}},[i("v-uni-image",{staticClass:"u-equal-bfb u-radius-circular",attrs:{src:e.userInfo.avatar}})],1),i("v-uni-view",{staticClass:"u-margin-left-16"},[i("v-uni-view",{staticClass:"u-font-32 u-font-weight u-margin-bottom-10"},[t._v(t._s(e.userInfo.nickname))]),i("v-uni-view",{staticClass:"u-font-24",staticStyle:{color:"#696969"}},[t._v("商城购物可享"),i("v-uni-text",{staticClass:"u-margin-lr-10 u-font-30",staticStyle:{"font-style":"italic"}},[t._v(t._s(e.equity))]),t._v("折")],1)],1)],1),i("v-uni-view",{staticClass:"u-margin-bottom-6"},[i("u-icon",{attrs:{name:"lock-opened-fill",color:"#000",size:"32"}}),e.unlock?i("v-uni-text",{staticClass:"u-margin-left-14 u-font-30 u-font-weight u-color-black"},[t._v("当前等级已解锁")]):i("v-uni-text",{staticClass:"u-margin-left-14 u-font-30 u-font-weight u-color-black"},[t._v("暂未解锁该等级")])],1),i("v-uni-view",{staticStyle:{width:"330rpx"}},[i("u-line-progress",{attrs:{percent:e.progress,round:!0,height:10,"active-color":"#2f3334","inactive-color":"#a4a7ac"}},[i("v-uni-view",{attrs:{slot:"default"},slot:"default"})],1)],1),i("v-uni-view",{staticClass:"u-margin-top-16 u-font-26"},[t._v("当前"),i("v-uni-text",{staticClass:"u-font-34 u-margin-lr-10"},[t._v(t._s(e.userInfo.growth_value))]),t._v("点, \n\t\t\t\t\t\t\t需达到 "+t._s(e.upgrade.total_growth_value)+" 点解锁")],1)],1),e.unlock?i("v-uni-view",{staticClass:"reach"},[t._v("已解锁")]):i("v-uni-view",{staticClass:"reach"},[t._v("未解锁")])],1)],1)})),1)],1),i("v-uni-view",{staticClass:"index-privilege-widget"},[i("wait-title",{attrs:{skin:"speckle",text:"等级特权",textColor:"c2926a",topPadding:"0",bottomPadding:"0"}}),i("v-uni-view",{staticClass:"main u-flex u-row-between"},t._l(4,(function(e,a){return i("v-uni-view",{key:a,staticClass:"item u-flex u-flex-col"},[i("u-image",{attrs:{width:"90rpx",height:"90rpx",shape:"circle",src:"/static/share_preserveImg.png"}}),i("v-uni-text",{staticClass:"u-margin-top-14 u-font-26 u-color-lighter"},[t._v("专属徽章")])],1)})),1)],1),i("v-uni-view",{staticClass:"index-task-widget"},[i("v-uni-view",{staticClass:"task-item u-border-bottom"},[i("v-uni-view",{staticClass:"u-font-36 u-font-weight"},[t._v("我的任务")])],1),i("v-uni-view",{staticClass:"task-item u-border-bottom"},[i("v-uni-view",{staticClass:"u-flex"},[i("v-uni-view",{staticClass:"icon"},[i("u-icon",{attrs:{name:"calendar",color:"#cba485",size:"40"}})],1),i("v-uni-view",{staticClass:"u-margin-left-20"},[i("v-uni-view",{staticClass:"u-font-32 u-font-weight"},[t._v("完成签到")]),i("v-uni-view",{staticClass:"u-flex u-font-22 u-color-muted u-margin-top-10"},[t._v("每日签到可获得经验值")])],1)],1),i("v-uni-view",{staticClass:"button",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.onJump("/bundle/pages/user_sign/user_sign")}}},[t._v("去签到")])],1),i("v-uni-view",{staticClass:"task-item u-border-bottom"},[i("v-uni-view",{staticClass:"u-flex"},[i("v-uni-view",{staticClass:"icon"},[i("u-icon",{attrs:{name:"gift",color:"#cba485",size:"40"}})],1),i("v-uni-view",{staticClass:"u-margin-left-20"},[i("v-uni-view",{staticClass:"u-font-32 u-font-weight"},[t._v("购买商品")]),i("v-uni-view",{staticClass:"u-flex u-font-22 u-color-muted u-margin-top-10"},[t._v("购买商品可获得对应经验值")])],1)],1),i("v-uni-view",{staticClass:"button",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.onJump("/pages/goods/list/list")}}},[t._v("去购买")])],1),i("v-uni-view",{staticClass:"task-item u-border-bottom"},[i("v-uni-view",{staticClass:"u-flex"},[i("v-uni-view",{staticClass:"icon"},[i("u-icon",{attrs:{name:"zhuanfa",color:"#cba485",size:"40"}})],1),i("v-uni-view",{staticClass:"u-margin-left-20"},[i("v-uni-view",{staticClass:"u-font-32 u-font-weight"},[t._v("邀请好友")]),i("v-uni-view",{staticClass:"u-flex u-font-22 u-color-muted u-margin-top-10"},[t._v("邀请好友注册商城可获得经验值")])],1)],1),i("v-uni-view",{staticClass:"button",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.onJump("/bundle/pages/distribution_index/distribution_index?login=true")}}},[t._v("去邀请")])],1)],1)],1)},r=[]}}]);