<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 地址列表部件 -->
		<view class="index-address-widget" v-if="addressList.length > 0">
			<view class="address-item" v-for="(item, index) in addressList" :key="item.id">
				<view class="u-padding-26 u-border-bottom" :data-id="item.id" @click="onSelectAddress">
					<view class="u-font-weight u-margin-bottom-10">
						<text class="u-margin-right-16">收货人：{{item.nickname}}</text>
						<text>{{item.mobile}}</text>
					</view>
					<view class="addr u-line-2">
						收货地址：{{item.region}}{{item.address}}
					</view>
				</view>
				<view class="operation">
					 <radio @click.stop="onDefaultAddress(item.id, index)" style="transform:scale(0.8)" color="#FF5058" :value="item.id + ''"
						:checked="item.is_default=='1' ? true : false">
						<text class="u-font-30">设为默认</text>
					</radio>
					<view class="u-flex">
						<view class="u-margin-right-25">
							<u-icon name="edit-pen" size="28" color="#999"></u-icon>
							<text class="u-margin-left-5 u-font-28" @click="onEditAddress(item.id)">编辑</text>
						</view>
						<view class="u-font-26">
							<u-icon name="trash" color="#999" size="28"></u-icon>
							<text class="u-margin-left-5 u-font-28" @click="onDelAddress(item.id)">删除</text>
						</view>
					</view>
				</view>
			</view>	
		</view>

		<wait-empty tips="暂无收货地址" v-else></wait-empty>
		
		<!-- 页脚按钮部件 -->
		<view class="index-footer-widget u-flex u-row-between">
			<!-- #ifdef H5 || MP-WEIXIN -->
			<view class="btn wx-btn u-margin-right-20" @click="onWxAddress()" v-if="isWeixin">
				 <image class="u-equal-34 u-margin-right-10" src="/static/ic_share_wechat.png"></image>
				 <text>微信导入</text>
			</view>
			<!-- #endif -->
			<view class="btn add-btn" @click="onAddAddress()">新增收货地址</view>
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
	import {isWeixin} from '@/utils/tools'
	export default {
		data() {
			return {
				// 首次加载
				isFirstLoading: true,
				// 是否可选择
				isSelect: false,
				// 是否微信端
				isWeixin: true,
				// 地址列表数据
				addressList: []
			}
		},
		onLoad(options) {
			this.isSelect = options.type
			this.getAddressList()
			// #ifdef H5
				this.isWeixin = isWeixin()
			// #endif
		},
		methods: {
			/**
			 * 获取收货地址
			 */
			getAddressList() {
				this.$u.api.apiAddressList().then((result) => {
					if (result.code === 0) {
						this.addressList = result.data
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading data error')
					}
				})
			},
			
			/**
			 * 选择收货地址
			 */
			onSelectAddress(e) {
				if (this.isSelect) {
					let { id } = e.currentTarget.dataset;
					uni.$emit('selectAddress', {
						id: id
					})
					uni.navigateBack()
				}
			},
			
			/**
			 * 设置默认地址
			 */
			onDefaultAddress(id, index) {
				if (!this.addressList[index].is_default) {
					this.addressList.forEach((item) => {
						item.is_default = 0
					})
					this.addressList[index].is_default = 1
					this.$u.api.apiAddressDefault({id: id})
				}
			},
			
			/**
			 * 新增收货地址
			 */
			onAddAddress() {
				uni.navigateTo({
					url: '/pages/address/edit/edit'
				})
			},
			
			/**
			 * 编辑收货地址
			 */
			onEditAddress(id) {
				uni.navigateTo({
					url: '/pages/address/edit/edit?id=' + id
				})
			},
			
			/**
			 * 删除收货地址
			 */
			onDelAddress(id) {
				const that = this
				uni.showModal({
				    title: '温馨提示',
				    content: '您确定要删除吗？',
					cancelText: '再想一想',
					confirmText: '狠心删除',
					confirmColor: '#FF5058',
				    success: function (res) {
				        if (res.confirm) {
							const param = {id: id}
							that.$u.api.apiAddressDel(param).then((result) => {
								if (result.code === 0) {
									that.$showSuccess(result.msg)
									that.getAddressList()
								} else {
									that.$showToast(result.msg)
								}
							})
				        }
				    }
				});
			},
			
			/**
			 * 授权微信地址
			 */
			onWxAddress() {
				// #ifdef H5
				wechath5.getWxAddress().then((res) => {
					uni.setStorageSync('wxAddress', JSON.stringify(res));
					setTimeout(() => {
						uni.navigateTo({
							url: '/pages/address/edit/edit'
						});
					}, 200);
				})
				// #endif
				// #ifdef MP-WEIXIN
				uni.authorize({
					scope: 'scope.address',
					success: function(res) {
						uni.chooseAddress({
							success: function(res) {
								uni.setStorageSync('wxAddress', JSON.stringify(res));
								setTimeout(() => {
									uni.navigateTo({
										url: '/pages/address/edit/edit'
									});
								}, 200);
							},
							fail: function(res) {
								if (res.errMsg == 'chooseAddress:cancel') return this.$toast({
									title: '取消选择'
								})
							}
						})
					},
					fail: function(res) {
						uni.showModal({
							title: '您已拒绝导入微信地址权限',
							content: '是否进入权限管理，调整授权？',
							success(res) {
								if (res.confirm) {
									uni.openSetting({
										success: function(res) {}
									});
								} else if (res.cancel) {
									return this.$toast({
										title: '已取消！'
									});
								}
							}
						})
					}
				})
				// #endif
			},	
		}
	}
</script>

<style lang="scss">
	// 地址列表部件样式
	.index-address-widget {
		.address-item {
			color: #0b0b0b;
			margin: 20rpx;
			border-radius: 12rpx;
			background-color: #FFFFFF;
			.addr {
				font-size: 26rpx;
				color: #7f7f7f;
				height: 60rpx;
				margin-top: 20rpx;
			}
			.operation {
				display: flex;
				align-items: center;
				justify-content: space-between;
				padding: 16rpx 20rpx;
				padding-left: 0;
				color: #7f7f7f;
			}
		}
	}
	
	// 页脚按钮部件样式
	.index-footer-widget {
		position: fixed;
		left: 0;
		right: 0;
		bottom: 0;
		height: 118rpx;
		padding: 0 30rpx;
		box-sizing: content-box;
		padding-bottom: env(safe-area-inset-bottom);
		background-color: #FFFFFF;
		.btn {
			display: flex;
			align-items: center;
			justify-content: center;
			width: 100%;
			height: 80rpx;
			text-align: center;
			border-radius: 37px;
		}
		.wx-btn {
			color: #222222;
			background: #f2f2f2;
		}
		.add-btn {
			color: #FFFFFF;
			background: #ff2c3c;
		}
	}
</style>
