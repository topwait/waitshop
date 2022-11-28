<template>
	<view>

		<!-- 【1】分享按钮弹窗 -->
		<u-popup class="share-button" v-model="showPop" mode="bottom" :closeable="true" border-radius="14" @close="onClose" :safe-area-inset-bottom="true">
			<view class="header">分享至</view>
			<view class="main">
				<view class="main-item" @click="getPoster">
					<image class="icon" src="/static/ic_share_picture.png"></image>
					<view class="text">生成海报</view>
				</view>
				<!-- #ifdef MP-WEIXIN-->
				<button open-type="share" class="main-item" hover-class="none">
					<image class="icon" src="/static/ic_share_wechat.png"></image>
					<view class="text">微信好友</view>
				</button>
				<!-- #endif -->
				<!-- #ifdef H5 || APP-PLUS -->
				<view class="main-item" @tap="shareWx">
					<image class="icon" src="/static/ic_share_wechat.png"></image>
					<view class="text">微信好友</view>
				</view>
				<!-- #endif -->
			</view>
			<view class="footer" @click="onClose">取消</view>
		</u-popup>
		
		<!-- 【2】分享海报弹窗 -->
		<u-popup class="share-poster transparent" v-model="showPoster" @close="onPosterClose" mode="center" :closeable="true" :safe-area-inset-bottom="true">
			<image class="share-img" :src="shareImg"></image>
			<u-button hover-class="none" type="error" style="height: 84rpx" @click="savePoster">
				<!-- #ifndef H5 -->
					保存图片到相册
				<!-- #endif -->
				<!-- #ifdef H5 --> 
					长按保存图片到相册
				<!-- #endif -->
			</u-button>
		</u-popup>
		
		<!-- 【3】分享提示弹窗 -->
		<!-- #ifdef H5 -->
		<u-popup class="share-tips" v-model="showTips" mode="top">
			<view style="overflow: hidden;">
				<image src="/static/ic_share_arrow.png" class="share-arrow" />
				<view class="white" style="text-align: center;margin-top: 280rpx;">
					<view class="u-font-30 u-font-weight">立即分享给好友吧</view>
					<view class="u-font-22 u-margin-tb-20">点击屏幕右上角将本页面分享给好友</view>
				</view>
			</view>
		</u-popup>
		<!-- #endif -->

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
	export default {
		data() {
			return {
				// 分享按钮弹窗
				showPop: false,	
				// 海报弹窗
				showPoster: false,
				// 提示弹窗
				showTips: false,
				// 分享图片
				shareImg: ''
			};
		},
		props: {
			// 弹窗显示
			show: {
				type: Boolean,
				default: false
			},
			// 分享类型[user=用户,goods=商品,team=拼团]
			shareType: {
				type: String,
				default: 'goods'
			},
			// 分享标题
			shareTitle: {
				type: String,
				default: ''
			},
			// 分享简介
			intro: {
				type: String,
				default: ''
			},
			// 主键: 看分享类型[商品ID,秒杀ID,拼团ID..等]
			tid: null
		},
		watch: {
			show: {
				handler(newVal, oldVal) {
					if (this.shareType === 'user' && newVal === true) {
						this.getPoster()
					} else {
						this.showPop = newVal
					}
				}
			}
		},
		methods: {
			/**
			 * 关闭弹窗
			 */
			onClose() {
				this.$emit('close')
			},
			
			/**
			 * 关闭海报弹出
			 */
			onPosterClose() {
				this.$emit('close')
			},
			
			/**
			 * 获取分享海报
			 */
			getPoster() {
				uni.showLoading({title: '正在生成中...'});
				let paths = ''
				let param = null
				switch (this.shareType) {
					case 'user':
						paths = '/pages/index/index'
						param = {type: 'user', url: paths}
						break
					case 'goods':
						paths = '/pages/goods/detail/detail'
						param = {type: 'goods', id: this.tid, url: paths}
						break
					case 'team':
						paths = '/bundle/pages/team_detail/team_detail'
						param = {type: 'team', id: this.tid, url: paths}
						break
					case 'seckill':
						paths = '/bundle/pages/seckill_detail/seckill_detail'
						param = {type: 'seckill', id: this.tid, url: paths}
						break
				}
				
				// #ifdef H5 || APP-PLUS
					param.url = '/mobile/#' + paths
				// #endif
				this.$u.api.apiSharePoster(param).then(result => {
					if (result.code === 0) {
						this.shareImg = result.data.url.replace(/[\r\n]/g, "")
						this.showPop = false
						uni.hideLoading()
						this.showPoster = true
					} else {
						uni.hideLoading({
							success: () => {
								uni.showToast({title: result.msg, icon: 'none'})
							}
						})
					}		
				})
			},
			
			shareWx() {
				// #ifdef H5
					this.showTips = true
					this.showshare = false
				// #endif
				// #ifdef APP-PLUS
					uni.share({
						provider: "weixin",
						scene: "WXSceneSession",
						type: 0,
						href: this.getLink,
						title: this.config.name,
						summary: '',
						imageUrl: this.config.image,
						success: (res) => {
							console.log('分享成功');
						},
						fail: (err) => {
							this.$toast({
								title: err.errMsg
							})
						}
					});
				// #endif
			},
			
			/**
			 * 获取写入授权
			 */
			getWriteAuth() {
				const that = this
				return new Promise(resolve => {
					uni.getSetting({
						success(res) {
							if (!res.authSetting['scope.writePhotosAlbum']) {
								uni.authorize({
									scope: 'scope.writePhotosAlbum',
									success() { 
										resolve(true)
									},
									fail: () => {
										uni.showModal({
											title: '您已拒绝授权保存到相册',
											content: '是否进入权限管理，调整授权？',
											success: (res) => {
												if (res.confirm) {
													uni.openSetting({ success: function(res) {} })
												} else if (res.cancel) {
													return that.$showToast('已取消');
												}
											}

										});
									}
								});
							} else {
								resolve(true)
							}
						}
					})
				})
			},
			
			/**
			 * 保存海报
			 */
			async savePoster() {
				const {shareImg} = this
				//#ifdef MP-WEIXIN
					await this.getWriteAuth();
				//#endif
				// #ifdef APP-PLUS 
				let bitmap = new plus.nativeObj.Bitmap("goods_poster");
				bitmap.loadBase64Data(shareImg, (res) => {
					bitmap.save('share.png', {overwrite: true,quality: 100}, (e) => {
						uni.saveImageToPhotosAlbum({
							filePath: e.target,
							success: res => {
								this.showPoster = false
								this.$showSuccess('保存成功');
							},
							fail: (err) => {
								judgePermission('photoLibrary')
							}
						})
					})
				},(e) => {
						console.log("加载Base64图片数据失败：" + JSON.stringify(e));
						bitmap.clear();
					}
				);
				// #endif
				// #ifdef MP-WEIXIN
				let file = uni.getFileSystemManager();
				file.writeFile({
					filePath: wx.env.USER_DATA_PATH + "/share.png",
					data: shareImg.slice(22),
					encoding: 'base64',
					success: () => {
						uni.saveImageToPhotosAlbum({
							filePath: wx.env.USER_DATA_PATH + "/share.png",
							success: res => {
								this.showPoster = false
								this.$showSuccess('保存成功');
							},
							fail: (res) => {
								this.$showToast('保存失败');
							}
						})
					}
				})
				// #endif
			}
		}
	}
</script>

<style lang="scss">
	// 分享按钮弹窗样式
	.share-button {
		.header {
			font-weight: bold;
			font-size: 30rpx;
			text-align: center;
			color: #333;
			height: 100rpx;
			line-height: 100rpx;
			background: #fff;
		}
		.main {
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin: 50rpx 140rpx;
			.main-item {
				.icon {
					width: 100rpx;
					height: 100rpx;
					vertical-align: middle;
				}
				.text {
					font-size: 26rpx;
					color: #101010;
					text-align: center;
					margin-top: 20rpx;
				}
			}
		}
		.footer {
			font-weight: normal;
			font-size: 32rpx;
			text-align: center;
			color: #333;
			height: 98rpx;
			line-height: 98rpx;
			background: #f6f6f6;
		}
	}
	
	// 分享海报弹窗样式
	.share-poster {
		.share-img {
			width: 639rpx;
			height: 940rpx;
			border-radius: 12rpx;
			margin-bottom: 20rpx;
		}
	}
	
	// 分享提示弹窗样式
	.share-tips  {
		.share-arrow {
			width: 140rpx;
			height: 250rpx;
			float: right;
			margin: 15rpx 31rpx 0 0;
		}
	}
</style>
