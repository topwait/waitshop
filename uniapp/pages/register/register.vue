<template>
	<view class="container">
		
		<view class="index-register-widget">
			<u-form :model="form" ref="uForm">
				<u-form-item label="手机号码" label-width="140">
					<u-input type="number" v-model="form.mobile" placeholder="请输入手机号" />
				</u-form-item>
				<u-form-item label="设置密码" label-width="140">
					<u-input type="password" v-model="form.password" placeholder="6-20位数字+字母混合密码"  />
				</u-form-item>
				<u-form-item label="确认密码" label-width="140">
					<u-input type="password" v-model="form.okPassword" placeholder="6-20位数字+字母混合密码"  />
				</u-form-item>
				<u-form-item label="验证码" label-width="140">
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
			</u-form>
			<view class="index-agreement-widget u-margin-top-40">
				<view class="login-text">
					<u-checkbox 
						active-color="#FF5058"
						@change="onCheckboxChange" 
						v-model="isAgree" 
					></u-checkbox>
					<text>已阅读并同意</text>
					<text style="color: #FF2C3C;" @click="$toPage('/bundle/pages/user_policy/user_policy?type=service')">《服务协议》</text>
					<text>和</text>
					<text style="color: #FF2C3C;" @click="$toPage('/bundle/pages/user_policy/user_policy?type=privacy')">《隐私协议》</text>
				</view>
			</view>
			<button class="button-item" 
				:class="isHandler ? 'loading' : ''"
				:loading="isHandler"
				@click="onRegister">立即注册
			</button>
		</view>
		
	</view>
</template>

<script>
	// +----------------------------------------------------------------------
	// | WaitShop开源电商系统 [ 助力中小企业快速落地 ]
	// +----------------------------------------------------------------------
	// | 欢迎阅读学习程序代码
	// | gitee:   https://gitee.com/wafts/WaitShop
	// | github:  https://github.com/topwait/waitshop
	// | 官方网站: https://www.waitshop.cn
	// +----------------------------------------------------------------------
	// | 禁止对本系统程序代码以任何目的、任何形式再次发布或出售
	// | WaitShop并不是自由软件,未经许可不能去掉WaitShop相关版权
	// | WaitShop系统产品必须购买商业授权后,方可去除前后端官方标识
	// | WaitShop团队版权所有并拥有最终解释权
	// +----------------------------------------------------------------------
	// | Author: WaitShop Team <2474369941@qq.com>
	// +----------------------------------------------------------------------
	import {isClient} from '@/utils/tools'
	import {mapState, mapMutations} from 'vuex'
	export default {
		data() {
			return {
				isHandler: false,   //是否提交中
				// 是否同意协议
				isAgree: false,
				// 验证码提示
				smsTips: '',
				// 验证码倒计时
				seconds: 60,
				// 表单数据
				form: {
					code: '',
					mobile: '',
					password: '',
					okPassword: '',
					client: isClient()
				}
			}
		},
		onShow() {
			if (this.isLogin) {
				uni.reLaunch({
					url: '/pages/index/index'
				})
			}
		},
		computed: {
			...mapState(['isLogin'])
		},
		methods: {
			...mapMutations(['LOGIN']),

			/**
			 * 注册账号
			 */
			onRegister() {
				if (this.isHandler) return true
				if (!this.form.mobile) return this.$showToast('请输入手机号码')
				if (this.form.mobile.length != 11) return this.$u.toast('手机号格式有误')
				if (!this.form.password) return this.$showToast('请输入设置密码')
				if (this.form.password.length < 6 || this.form.password.length>20) {
					return this.$showToast('密码必须在6-20个字符内数字或字母')
				}
				if (!this.form.okPassword) return this.$showToast('请输入确认密码')
				if (this.form.okPassword !== this.form.password) return this.$showToast('两次密码不一致')
				if (!this.form.code) return this.$showToast('请输入验证码')
				if (!this.isAgree) return this.$showToast('请阅读服务协议并同意')
				
				this.isHandler = true
				this.$u.api.apiRegister(this.form).then(result => {
					if (result.code === 0) {	
						this.LOGIN(result.data)
						uni.navigateBack({
							delta: 1
						})
					} else {
						this.$showToast(result.msg)
					}

					this.isHandler = false
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
					this.$u.api.apiSendSms({mobile:this.form.mobile, scene:111}).then(result => {
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
	.index-register-widget {
		margin: 30rpx;
		.button-item {
			display: flex;
			justify-content: center;
			align-items: center;
			font-size: 28rpx;
			color: #FFFFFF;
			height: 86rpx;
			border-radius: 50rpx;
			width: 100%;
			margin-top: 60rpx;
			background-color: #FF5058;
			&.loading {
				color: #f7eaea !important;
				background-image: linear-gradient(to right, #fd9371, #f56772) !important;
			}
		}
	}
</style>
