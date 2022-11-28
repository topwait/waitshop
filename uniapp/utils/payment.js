import {isWeixin} from '@/utils/tools.js'

export function wxpay(opt) {
	//#ifdef  H5
	return isWeixin()
		? wechath5.wxPay(opt.url)
		: location.href = opt.url
	// #endif
	
	//#ifdef MP-WEIXIN || APP-PLUS
	return new Promise((resolve, reject) => {
		// #ifdef MP-WEIXIN
		const params = {
			timeStamp: opt.timeStamp,
			// 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
			nonceStr: opt.nonceStr,
			// 支付签名随机串，不长于 32 位
			package: opt.package,
			// 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=***）
			signType: opt.signType,
			// 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
			paySign: opt.paySign,
		}
		// #endif
		
		// #ifdef APP-PLUS
		const params = {
			orderInfo: opt
		}
		// #endif
		
		uni.requestPayment({
			provider: 'wxpay',
			...params,
			success: res => {
				resolve(res)
			},
			cancel: (res) => {
				reject(res)
			},
			fail: (res) => {
				reject(res)
			}
		})
	})
	// #endif
}
