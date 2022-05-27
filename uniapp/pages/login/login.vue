<template>
	<view class="container">

		<!-- 微信登录 -->
		<view class="index-mnp-widget" v-if="isWeixin">
			<view class="avatar"><open-data type="userAvatarUrl"></open-data></view>
			<view class="u-font-28 u-margin-top-10">用户昵称</view>
			<view class="u-font-28 u-margin-top-30">申请获取以下权限</view>
			<view class="u-font-22 u-margin-top-20 u-color-muted">获得你的公开信息（昵称、头像等）</view>
			<block v-if="!isBindMobile">
				<view class="button-item button-item--mnp" @click="onWxLogin" >
					<image class="u-equal-50 u-margin-right-10" src="/static/ic_share_wechat.png"></image>
					<text>微信一键授权</text>
				</view>
				<view class="button-item button-item--cancel" @click="onCancelLogin">暂不登录</view>
			</block>
			<block v-else>
				<view class="u-flex u-margin-top-50">
					<view class="button-item button-item--cancel" @click="onCancelLogin" style="width:270rpx;margin-top:0;">暂不登录</view>
					<button class="button-item button-item--mnp" open-type="getPhoneNumber" @getphonenumber="getPhoneNumber" 
						style="width:270rpx;margin-top:0;margin-left:30rpx;">
						<text>微信登录</text>
					</button>
				</view>
			</block>
		</view>
		
		<!-- 手机登录 -->
		<view class="index-mobile-widget" v-if="!isWeixin">
			<view class="logo">
				<image style="width:560rpx; height:156rpx;" :src="config.login_logo"></image>
			</view>
			<u-form :model="form" ref="uForm">
				<u-form-item><u-input type="number" v-model="form.mobile" placeholder="请输入手机号" /></u-form-item>
				<u-form-item v-if="loginType === 2">
					<u-input type="number" v-model="form.code" placeholder="请输入验证码" />
					<u-form-item slot="right" :border-bottom="false">
						<u-verification-code :seconds="seconds" ref="uCode" @change="onChangeSms"></u-verification-code>
						<u-button @click="onSendSms"
							:plain="true" type="error" 
							hover-class="none" size="mini" 
							shape="circle">{{smsTips}}
						</u-button>
					</u-form-item>
				</u-form-item>
				<u-form-item v-if="loginType === 1">
					<u-input type="password" v-model="form.password" placeholder="请输入密码"  />
				</u-form-item>
			</u-form>
			<view class="button-item button-item--mobile" @click="onMobileLogin">登录</view>
			<view class="u-flex u-row-between u-margin-tb-40">
				<view class="u-font-28 u-color-lighter" @click="onSwitchLoginType" 
					v-if="this.config.login_way && this.config.login_way.length >= 2">
					{{loginType == 0 ? "账号密码登录" : "短信验证码登录"}}
				</view>
				<view v-else></view>
				<view class="u-font-28 u-color-lighter" @click="$toPage('/pages/register/register')">注册账号</view>
			</view>
			<!-- #ifdef H5 -->
			<!-- <view class="other" v-if="isWeixin && isClient === 5">
				<view class="header">
					<view class="line"></view>
					<view class="title">其他登录方式</view>
					<view class="line"></view>
				</view>
				<view class="u-margin-tb-40">
					<view class="wx" style="text-align:center;" @click="onWxLogin">
						<image class="u-equal-80 u-margin-right-10" src="/static/ic_share_wechat.png"></image>
						<view class="u-font-26 u-color-normal u-margin-top-10">微信登录</view>
					</view>
				</view>
			</view> -->
			<!-- #endif -->
		</view>

		<!-- 服务协议 -->
		<view class="index-agreement-widget">
			<view class="login-text">
				<u-checkbox 
					active-color="#FF5058"
					@change="onCheckboxChange" 
					v-model="isAgree" 
				></u-checkbox>
				<text>已阅读并同意WaitShop</text>
				<text style="color: #FF2C3C;" @click="$toPage('/bundle/pages/user_policy/user_policy?type=service')">《服务协议》</text>
				<text>和</text>
				<text style="color: #FF2C3C;" @click="$toPage('/bundle/pages/user_policy/user_policy?type=privacy')">《隐私协议》</text>
			</view>
		</view>

	</view>
</template>

<script>
	// +----------------------------------------------------------------------
	// | WaitShop开源电商系统 [ 助力中小企业快速落地 ]
	// +----------------------------------------------------------------------
	// | 欢迎阅读学习程序代码
	// | gitee:   https://gitee.com/wafts/WaitShop
	// | github:  https://github.com/miniWorlds/waitshop
	// | 官方网站: https://www.waitshop.cn
	// +----------------------------------------------------------------------
	// | 禁止对本系统程序代码以任何目的、任何形式再次发布或出售
	// | WaitShop并不是自由软件,未经许可不能去掉WaitShop相关版权
	// | WaitShop系统产品必须购买商业授权后,方可去除前后端官方标识
	// | WaitShop团队版权所有并拥有最终解释权
	// +----------------------------------------------------------------------
	// | Author: WaitShop Team <2474369941@qq.com>
	// +----------------------------------------------------------------------
	import {getWxCode, getUserProfile, currentPage, isClient, isWeixin} from '@/utils/tools'
	import {mapState, mapMutations} from 'vuex'
	export default {
		data() {
			return {
				// 是否需绑定手机
				isBindMobile: false,
				// 登录类型[1=账号, 2=短信]
				loginType: 2,
				// 登录配置参数
				config: [],
				// 是否是微信环境
				isWeixin: true,
				// 客服端
				isClient: 0,
				// 是否同意协议
				isAgree: false,
				// 验证码提示
				smsTips: '',
				// 验证码倒计时
				seconds: 60,
				// 微信授权信息
				userInfo: null,
				// 微信code码
				wxCode: null,
				// 微信授权手机
				mobile: '',
				// 表单数据
				form: {
					mobile: '',
					code: '',
					password: ''
				}
			}
		},
		onLoad() {
			if (this.isLogin) {
				uni.reLaunch({
					url: '/pages/index/index'
				})
			}
			this.getConfigFun()
			// #ifndef MP-WEIXIN
				this.isClient = isClient()
				this.isWeixin = isWeixin()
			//#endif
		},
		computed: {
			...mapState(['isLogin'])
		},
		methods: {
			...mapMutations(['LOGIN']),
			
			/**
			 * 获取配置
			 */
			async getConfigFun() {
				this.$u.api.apiLoginConfig().then(result => {
					if (result.code === 0) {
						this.config = result.data
						if (this.config.login_way.length > 0) {
							console.log(result.data)
							this.config.login_way = this.config.login_way.reverse()
							this.loginType = parseInt(this.config.login_way[0])
						}
					} else {
						this.$showToast('loading config error')
					}
				})
			},
			
			/**
			 * 微信授权手机号
			 */
			async getPhoneNumber(e) {
				if (e.detail.errMsg !== 'getPhoneNumber:ok') {
					this.$showToast(e.detail.errMsg)
					return
				}
				uni.showLoading({title: '登录中...', mask: true})
				const code = await getWxCode()
				const param = {
					encryptedData: e.detail.encryptedData,
					iv: e.detail.iv,
					code: this.wxCode,
					scene: 'mnp',
					client: isClient(),
					avatarUrl: this.userInfo.avatarUrl,
					nickName: this.userInfo.nickName,
					gender: this.userInfo.gender
				}
				this.$u.api.apiLogin(param).then(result => {
					if (result.code === 0) {	
						this.LOGIN(result.data)
						uni.hideLoading()
						if (currentPage().route !== 'pages/login/login') {
							uni.navigateBack({
								delta: 1
							})
						} else {
							uni.reLaunch({
								url: '/pages/index/index'
							})
						}
					} else {
						this.$showToast(result.msg)
					}
				})
			},
			
			/**
			 * 取消登录
			 */
			onCancelLogin() {
				uni.navigateBack({})
			},
			
			/**
			 * 切换登录方式[1=账号, 2=短信]
			 */
			onSwitchLoginType() {
				this.loginType = this.loginType===1 ? 2 : 1
			},
			
			/**
			 * 微信登录
			 */
			async onWxLogin() {
				if (!this.isAgree) return this.$showToast('请阅读服务协议并同意')
				if (isClient() !== 6) return this.$showToast('非微信环境禁止操作')
				if (!this.userInfo) {
					const res = await getUserProfile()
					this.userInfo = res.userInfo
					this.isBindMobile = this.config.force_mobile == 0 ? false : true
				}
				const code = await getWxCode()
				this.wxCode = code
				if (this.config.force_mobile == 0){
					uni.showLoading({title: '登录中...', mask: true})
					const param = {
						scene: 'mnp',
						client: isClient(),
						code: this.wxCode, 
						avatarUrl: this.userInfo.avatarUrl,
						nickName: this.userInfo.nickName,
						gender: this.userInfo.gender
					}
					this.$u.api.apiLogin(param).then(result => {
						if (result.code === 0) {	
							this.LOGIN(result.data)
							uni.hideLoading()
							if (currentPage().route !== 'pages/login/login') {
								uni.navigateBack({
									delta: 1
								})
							} else {
								uni.reLaunch({
									url: '/pages/index/index'
								})
							}
						} else {
							this.$showToast(result.msg)
						}
					})
				}
			},
			
			/**
			 * 手机号登录
			 */
			onMobileLogin() {
				if (!this.isAgree) return this.$showToast('请阅读服务协议并同意')
				if (this.form.mobile == '') return this.$u.toast('手机号不能为空')
				if (this.form.mobile.length != 11) return this.$u.toast('手机号输入有误')
				if (this.loginType == 2) {
					if (this.form.code == '') return this.$u.toast('请输入验证码')
				} else {
					if (this.form.password == '') return this.$u.toast('登录密码不能为空')
					if (this.form.password.length < 6) return this.$u.toast('登录密码错误')
					if (this.form.password.length > 20) return this.$u.toast('登录密码错误')
				}
				const param = {
					scene: this.loginType===2 ? 'mobile' : 'account',
					client: isClient(),
					mobile: this.form.mobile,
					code: this.form.code,
					password: this.form.password
				}
				uni.showLoading({title: '登录中...', mask: true})
				this.$u.api.apiLogin(param).then(result => {
					if (result.code === 0) {
						this.LOGIN(result.data)
						uni.hideLoading()
						if (currentPage().route !== 'pages/login/login') {
							uni.navigateBack({
								delta: 1
							})
						} else {
							uni.reLaunch({
								url: '/pages/index/index'
							})
						}
					} else {
						this.$showToast(result.msg)
					}
				})
			},

			/**
			 * 发送短信验证码
			 */
			onSendSms() {
				if (this.form.mobile == '') return this.$u.toast('手机号不能为空')
				if (this.form.mobile.length != 11) return this.$u.toast('手机号输入有误')
				if(this.$refs.uCode.canGetCode) {
					uni.showLoading({title: '正在获取验证码'})
					this.$u.api.apiSendSms({mobile:this.form.mobile, scene:110}).then(result => {
						uni.hideLoading();
						if (result.code === 0) {
							this.$u.toast('验证码已发送')
							this.$refs.uCode.start()
						} else {
							this.$u.toast(result.msg)
						}
					})
				} else {
					this.$u.toast('倒计时结束后再发送')
				}
			},
			
			/**
			 * 改变验证码提示
			 */
			onChangeSms(text) {
				this.smsTips = text
			},
			
			/**
			 * 勾选获取取消协议
			 */
			onCheckboxChange() {
				this.isAgree = !this.isAgree
			}
		}
	}
</script>

<style lang="scss">
	page {
		background-color: #FFFFFF;
	}
	
	// 公共按钮
	.button-item {
		display: flex;
		justify-content: center;
		align-items: center;
		font-size: 28rpx;
		color: #FFFFFF;
		width: 580rpx;
		height: 86rpx;
		border-radius: 50rpx;
		&--mnp {
			margin-top: 60rpx;
			border: 1px solid #01d171;
			background-color: #01d171;
		}
		&--cancel {
			margin-top: 30rpx;
			color: #999;
			border: 1px solid #999;
			background-color: #FFF;
		}
		&--mobile {
			width: 100%;
			margin-top: 60rpx;
			background-color: #FF5058;
		}
	}
	
	// 微信登录部件样式
	.index-mnp-widget {
		display: flex;
		flex-direction: column;
		align-items: center;
		margin-top: 200rpx;
		.avatar {
			width: 120rpx;
			height: 120rpx;
			border-radius: 50%;
			border: 1px solid #eee;
			overflow: hidden;
			background-color: #eee;
		}
	}
	
	// 手机号登录
	.index-mobile-widget {
		margin: 0 40rpx;
		.logo {
			text-align: center;
			margin: 60rpx 0;
		}
		.other {
			margin-top: 100rpx;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			.header {
				display: flex;
				align-items: center;
				.line {
					width: 180rpx;
					border-bottom: 1px solid #f2f2f2;
				}
				.title {
					padding: 0 20rpx;
					font-size: 26rpx;
					color: #999;
				}
			}
		}
	}
	
	// 服务协议
	.index-agreement-widget {
		position: absolute;
		bottom: 40rpx;
		width: 100%;
		text-align: center;
		font-size: 24rpx;
		color: #666666;
		.u-checkbox__label {
			margin-right: 0 !important;
		}
	}
</style>
