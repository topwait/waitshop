import config, { baseURL } from '../common/config.js'
import store from '@/store'
import Vue from 'vue'

/**
 * 当前客户端标识
 * [1=安卓端, 2=苹果端, 3=电脑端, 4=H5(非微信), 5=微信公众号, 6=微信小程序]
 */
export function isClient() {
	let client = 6;

	// #ifdef MP-WEIXIN
		client = 6;
	// #endif
	
	// #ifdef H5
		client = isWeixin() ? 5 : 4
	// #endif
	
	// #ifdef APP-PLUS
		client = 1;
		uni.getSystemInfo({
			success: res => {
				client = res.platform == 'ios' ? 2 : 1;
			},
			fail: res => {
				client = 1;
			}
		})
	// #endif
	
	return client;
}

/**
 * 是否为微信环境
 */
export function isWeixin() {
	var ua = navigator.userAgent.toLowerCase();
	if (ua.match(/MicroMessenger/i) == "micromessenger") {
		return true;
	} else {
		return false;
	}
}

/**
 * 是否为安卓环境
 */
export function isAndroid() {
	let u = navigator.userAgent;
	return u.indexOf('Android') > -1 || u.indexOf('Adr') > -1;
}

/**
 * 显示成功提示框
 */
export const showSuccess = (msg, callback) => {
  uni.showToast({
    title: msg,
    icon: 'success',
    mask: true,
    duration: 1500,
    success() {
      callback && callback()
    }
  })
}

/**
 * 显示错误提示框
 */
export const showError = (msg, callback) => {
  uni.showToast({
    title: msg,
    icon: 'error',
    mask: true,
    duration: 1500,
    success() {
      callback && callback()
    }
  })
}

/**
 * 显示纯文字提示框
 */
export const showToast = msg => {
  uni.showToast({
    title: msg,
    icon: 'none'
  })
}

/**
 * 文件上传
 * 
 * @author windy
 * @param {Object} path 上传的文件路径
 */
export const uploadFile = (path) => {
	return new Promise((resolve, reject) => {
		uni.uploadFile({
			url: baseURL + '/Upload/image',
			filePath: path,
			name: 'iFile',
			header: { token: store.state.isLogin },
			fileType: 'image',
			success: res => {
				let data = JSON.parse(res.data);
				if (data.code == 0) {
					resolve(data);
				} else {
					reject()
				}
			},
			fail: (err) => {
				reject(err)
			}
		});
	});
}

/**
 * 设置底部导航
 */
export function setTabBar() {
	// 设置导航文本颜色
	const config = store.state.config
	uni.setTabBarStyle({
		color: config.diyBottomStyle.unselectedColor,
		selectedColor: config.diyBottomStyle.selectedColor,
	})
	
	// APP端导航渲染
	// #ifdef APP-PLUS
	config.diyBottomNav.forEach((item, index) => {
		uni.downloadFile({
		  url: item.unselected_icon,
		  success: res => {
			uni.setTabBarItem({
				index,
				iconPath: res.tempFilePath,
			})
		  }
		})
		uni.downloadFile({
		  url: item.unselected_icon,
		  success: res => {
			uni.setTabBarItem({
				index,
				iconPath: res.tempFilePath,
			})
		  }
		})
		uni.downloadFile({
		  url: item.selected_icon,
		  success: res => {
			uni.setTabBarItem({
				index,
				selectedIconPath: res.tempFilePath,
			})
		  }
		})
	})
	// #endif
	
	// 非APP端导航渲染
	// #ifndef APP-PLUS
	config.diyBottomNav.forEach((item, index) => {
		uni.setTabBarItem({
			index,
			text: item.name,
			iconPath: item.unselected_icon,
			selectedIconPath: item.selected_icon,
			fail(res) { 
				console.log(res)
			}
		})
	})
	// #endif
	
	uni.showTabBar()
}

/**
 * 节流
 */
export const trottle = (func, time = 1000, context) => {
	let previous = new Date(0).getTime()
	return function(...args) {
		let now = new Date().getTime()
		if (now - previous > time) {
			func.apply(context, args)
			previous = now
		}
	}
}

/**
 * 当前页面
 */
export const currentPage = () => {
	let pages = getCurrentPages();
	let currentPage = pages[pages.length - 1];
	return currentPage || {};
}

/**
 * 获取URL参数
 */
export function strToParams(str) {
	let newparams = {}
	for (let item of str.split('&')) {
		newparams[item.split('=')[0]] = item.split('=')[1]
	}
	return newparams
}

/**
 * 获取微信登录凭证(code)
 */
export const getWxCode = () => {
	return new Promise((resolve, reject) => {
		uni.login({
			success(res) {
				resolve(res.code)
			},
			fail(res) {
				reject(res)
			}
		})
	})
}

/**
 * 获取小程序用户信息
 */
export const getUserProfile = () => {
	return new Promise((resolve, reject) => {
		uni.getUserProfile({
			desc: '仅用于登录系统生成基本用户信息',
			lang: 'zh_CN',
			success: function(infoRes) {
				resolve(infoRes)
			}
		})
	})
}

/**
 * 获取经纬度
 */
export const getLocation = () => {
	return new Promise((resolve, reject) => {
		uni.getLocation({
			type: 'wgs84',
			success: function (res) {
				resolve(res)
			}
		})
	})
}

/**
 * 将px转为prx并添加单位
 * 
 * @param {Object} value 值
 * @return String
 */
export function px2rpx(value) {
	if (!value) return 0
	const rpxValue = value / (uni.upx2px(100) / 100)
	return rpxValue;
}

/**
 * 跳转页面
 * 
 * @param {url}  跳转地址  
 * @param {type} 跳转类型
 */
export const toPage = trottle((url) => {
	let type = "to"
	let arrUrl = url.split('?')
	if (arrUrl[1]) {
		let arrParam = arrUrl[1].split("&")
		if (arrParam.includes("login=true") && !store.state.isLogin) {
			return toLogin()
		}
		if (arrParam.includes("store=true")) {
			getLocation().then(result => {
				url = url+"&longitude="+result.longitude+"&latitude="+result.latitude
				return uni.navigateTo({url: url})
			})
		} else {
			if(arrParam.includes("type=tab")) {
				type = "tab"
			} else if (arrParam.includes("type=back")) {
				type = "back"
			} else {
				type = "to"
			}	
			if(type == "tab") {
				return uni.switchTab({url: url})
			} else if (type == "back") {
				uni.navigateBack({})
			} else {
				return uni.navigateTo({url: url})
			}
		}
	} else {
		if(type == "tab") {
			return uni.switchTab({url: url})
		} else if (type == "back") {
			uni.navigateBack({})
		} else {
			return uni.navigateTo({url: url})
		}
	}
}, 1000)

/**
 * 静默登录
 */
export async function silentLogin() {
	const code = await getWxCode()
	Vue.prototype.$u.api.apiLogin({
		scene: 'silent',
		client: isClient(),
		code: code,
	}).then(result => {
		store.commit('LOGIN', result.data)
		const {onLoad, options} = currentPage()
		onLoad && onLoad(options)
	})
}

/**
 * 去登录
 */
export const toLogin = trottle(() => {
	//#ifdef MP
		silentLogin()
	// #endif
	//#ifndef MP
		uni.navigateTo({
			url: '/pages/login/login'
		})
	// #endif
}, 2000)

/**
 * tabBar页面路径
 */
export const tabBarList = [
	'pages/cart/cart',
	'pages/index/index',
	'pages/user/home/home',
	'pages/goods/category/category'
]