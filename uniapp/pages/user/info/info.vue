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
						<text class="u-margin-right-10">{{userInfo.mobile}}</text>
						<u-icon name="lock" size="28" color="#999"></u-icon>
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
			<view class="popup-form-widget">
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
		</u-popup>
		
		<!-- 按钮部件 -->
		<view class="button button--logout" @click="onLogout">退出登录</view>
		
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
				// 表单数据
				form: {
					type: '',
					avatar: '',
					nickname: '',
					mobile: ''
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
				const param = {key: type, value: this.form[type]}
				this.$u.api.apiUserSet(param).then(result => {
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
			 * 绑定手机号
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
