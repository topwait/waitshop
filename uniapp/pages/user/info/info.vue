<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 用户信息部件 -->
		<view class="u-margin-tb-10 u-bg-white">
			<u-cell-group>
				<u-cell-item title="头像" @click="onUploadAvatar">
					<view style="position: absolute; right: 74rpx; bottom: 20rpx;">
						<u-image border-radius="50%" width="60rpx" height="60rpx" :src="userInfo.avatar"></u-image>
					</view>
				</u-cell-item>
				<u-cell-item title="昵称" :value="userInfo.nickname" @click="onShowPopup('nickname')"></u-cell-item>
				<u-cell-item title="ID号" :arrow="false">
					<text class="u-margin-right-10">{{userInfo.sn}}</text>
					<u-icon name="lock" size="28" color="#999"></u-icon>
				</u-cell-item>
				<u-cell-item title="手机" :arrow="false">
					<view v-if="userInfo.mobile">
						<text class="u-margin-right-20">{{userInfo.mobile}}</text>
						<!-- #ifdef MP-WEIXIN -->
							<u-button
								:plain="true" type="error" 
								open-type="getPhoneNumber" 
								@getphonenumber="onBindMobile" 
								hover-class="none" size="mini" 
								shape="circle">更换手机号
							</u-button>
						<!-- #endif -->
						<!-- #ifndef MP-WEIXIN -->
							<u-button
								:plain="true" type="error" 
								@click="onShowPopup('mobile')" 
								hover-class="none" size="mini" 
								shape="circle">更换手机号
							</u-button>
						<!-- #endif -->
					</view>
					<u-button v-else
						:plain="true" type="error" 
						open-type="getPhoneNumber" 
						@getphonenumber="onBindMobile"
						hover-class="none" size="mini" 
						shape="circle">绑定手机号
					</u-button>
				</u-cell-item>
			</u-cell-group>
		</view>
		
		<!-- 系统信息部件 -->
		<view class="u-margin-tb-10 u-bg-white">
			<u-cell-group>
				<u-cell-item title="服务协议" @click="$toPage('/bundle/pages/user_policy/user_policy?type=service')"></u-cell-item>
				<u-cell-item title="隐私政策" @click="$toPage('/bundle/pages/user_policy/user_policy?type=privacy')"></u-cell-item>
				<u-cell-item title="系统版本" :arrow="false">v2.3.0.2021</u-cell-item>
			</u-cell-group>
		</view>
		
		<!-- 弹窗部件 -->
		<u-popup v-model="showPopup" mode="center" border-radius="12" :closeable="true">
			<view class="popup-form-widget" v-if="form.type==='nickname'">
				<view class="title">修改用户名</view>
				<view class="form">
					<u-field v-model="form.nickname" label="新昵称" placeholder="请输入新的昵称"></u-field>
				</view>
				<view class="u-padding-tb-40">
					<u-button type="error" size="medium" 
						:custom-style="{width: '100%'}" 
						@click="onEdit(form.type)">确定
					</u-button>
				</view>
			</view>
			<view class="popup-form-widget" v-if="form.type==='mobile'">
				<view class="title">更换手机号</view>
				<view class="form">
					<u-field :value="userInfo.mobile" label="原手机号"></u-field>
					<u-field v-model="form.mobile" label="新手机号" placeholder="请输入新的手机号"></u-field>
					<u-field label="验证码" placeholder="请输入">
						<u-form-item slot="right" :border-bottom="false">
							<u-verification-code :seconds="seconds" ref="uCode" @change="onChangeSms"></u-verification-code>
							<u-button @click="onSendSms('changeMobile')"
								:plain="true" type="error" 
								hover-class="none" size="mini" 
								shape="circle">{{smsTips}}
							</u-button>
						</u-form-item>
					</u-field>
				</view>
				<view class="u-padding-tb-40">
					<u-button type="error" size="medium" 
						:custom-style="{width: '100%'}" 
						@click="onEdit(form.type)">确定
					</u-button>
				</view>
			</view>
		</u-popup>
		
		<!-- 按钮部件 -->
		<!-- #ifndef MP -->
		<view class="button button--logout" @click="onLogout">退出登录</view>
		<!-- #endif -->
		
		<!-- 版权部件 -->
		<wait-copyright v-if="!isFirstLoading"></wait-copyright>

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
	import {uploadFile} from "@/utils/tools"
	import {mapState, mapMutations} from 'vuex'
	export default {
		data() {
			return {
				// 首次加载
				isFirstLoading: true,
				// 用户信息
				userInfo: {},
				// 是否弹窗
				showPopup: false,
				// 验证码提示
				smsTips: '',
				// 验证码倒计时
				seconds: 60,
				// 表单数据
				form: {
					type: '',
					avatar: '',
					nickname: '',
					mobile: '',
					code: ''
				}
			}
		},
		onLoad() {
			this.getUserInfo()
		},
		methods: {
			...mapMutations(['LOGOUT']),

			/**
			 * 获取用户信息
			 */
			getUserInfo() {
				this.$u.api.apiUserInfo().then(result => {
					if (result.code === 0) {
						this.userInfo = result.data
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading goodsDetail error')
					}
				})
			},
			
			/**
			 * 打开弹窗
			 */
			onShowPopup(type) {
				this.showPopup = true
				this.form.type = type
			},
			
			/**
			 * 编辑数据
			 */
			onEdit(type) {		
				this.$u.api.apiUserSet({
					key: type, 
					value: this.form[type], 
					code: this.form.code,
				}).then(result => {
					if (result.code === 0) {
						this.showPopup = false
						this.userInfo[type] = this.form[type]
						this.form[type] = ''
						this.$showSuccess(result.msg)
					} else {
						this.$showToast(result.msg)
					}
				})
			},
			
			/**
			 * 微信绑定手机号
			 */
			async onBindMobile(e) {
				if (e.detail.errMsg !== 'getPhoneNumber:ok') {
					this.$showToast(e.detail.errMsg)
					return
				}

				const code = await getWxCode()
				const phoneNumber = await this.$u.api.apiWxPhone({
					encryptedData: e.detail.encryptedData,
					iv: e.detail.iv,
					code: this.wxCode,
				}).then(result => {
					if (result.code === 0) {
						return result.data.phoneNumber
					}
					return ''
				})
		
				if (phoneNumber === '') {
					return this.$showToast('获取手机号异常')
				} else {
					this.$u.api.apiBindMobile({mobile: phoneNumber}).then(result => {
						if (result.code === 0) {
							this.$showSuccess(result.msg)
							this.getUserInfo()
						} else {
							this.$showToast(result.msg)
						}
					})
				}
			},
			
			/**
			 * 上传头像
			 */
			onUploadAvatar() {
				const that = this
				uni.chooseImage({
				    success: (chooseImageRes) => {
				        const tempFilePaths = chooseImageRes.tempFilePaths;
						uploadFile(tempFilePaths[0], 'avatar').then(result => {
							if (result.code === 0) {
								that.form.avatar = result.data.image
								that.onEdit('avatar')
							} else {
								that.$showToast(result.msg)
							}
						})
				    }
				})
			},
			
			/**
			 * 退出登录
			 */
			onLogout() {
				const that = this
				uni.showModal({
				    title: '提示',
				    content: '您确定要退出登录吗？',
					confirmColor: '#FF5058',
				    success: function (res) {
						that.LOGOUT()
						that.$showSuccess('退出成功')
						setTimeout(function(){
							uni.switchTab({
								url: '/pages/index/index'
							})
						}, 800)
				    }
				})
			},

			/**
			 * 发送短信验证码
			 * @param {String} type 类型
			 */
			onSendSms(type) {
				let sceneeNum = null
				if (type === 'changeMobile') {
					// 更改手机号
					sceneeNum = 113
					if (this.form.mobile == '') return this.$u.toast('新手机号不能为空')
					if (this.form.mobile.length != 11) return this.$u.toast('新手机号输入有误')
					if (this.userInfo.mobile === this.form.mobile) return this.$u.toast('不能与原手机号一样')
				} else if (type === 'bindMobile') {
					// 绑定手机号
					sceneeNum = 112
					if (this.form.mobile == '') return this.$u.toast('手机号不能为空')
					if (this.form.mobile.length != 11) return this.$u.toast('手机号不能为空')
				}
				if(this.$refs.uCode.canGetCode) {
					uni.showLoading({title: '正在获取验证码'})
					this.$u.api.apiSendSms({
						mobile: this.form.mobile, 
						scene: sceneeNum,
					}).then(result => {
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
			}
		}
	}
</script>

<style lang="scss">
	.popup-form-widget {
		width: 560rpx;
		padding: 0 20rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.title {
			text-align: center;
			font-size: 28rpx;
			padding: 22rpx;
		}
	}

	.button {
		display: flex;
		align-items: center;
		justify-content: center;
		margin: 30rpx 60rpx;
		height: 80rpx;
		text-align: center;
		border-radius: 37px;
		color: #999;
		background-color: #eee;
	}
</style>
