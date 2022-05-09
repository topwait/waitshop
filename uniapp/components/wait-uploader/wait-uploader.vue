<template>
	<view class="u-flex u-flex-wrap">
		<u-upload @on-change="change" @on-remove="remove" 
			name="iFile"
			:action="action"
			:header="{'token': token}" 
			:deletable="deletable" 
			:max-count="maxUpload" 
			:showProgress="showProgress"
			:multiple="mutiple" 
			:custom-btn="true" 
			:width="previewSize" 
			:height="previewSize"
			:form-data{'dir': dir}
			ref="upload">
			
			<view slot="addBtn" class="uplader-upload" hover-class="slot-btn__hover" hover-stay-time="150">
				<u-icon name="camera" size="48" color="#dcdee0"></u-icon>
				<view class="u-font-22">
					{{fileList.length>=1?fileList.length+'/'+maxUpload:tips}}
				</view>
			</view>
		</u-upload>
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
	import store from '@/store'
	import {baseURL} from '@/common/config.js'
	export default {
		props: {
			// 默认不允许多选图片
			mutiple: {
				type: Boolean,
				default: false
			},
			// 限制上传文件数量
			maxUpload: {
				type: Number,
				default: 1
			},
			// upload大小
			previewSize: {
				type: String,
				default: "160"
			},
			// 是否可删除
			deletable: {
				type: Boolean,
				default: true,
			},
			// 上传目录
			dir: {
				type: String,
				default: ""
			},
			// 提示
			tips: {
				type: String,
				default: '上传图片'
			},
			// 是否显示进度条
			showProgress: {
				type: Boolean,
				default: false,
			}
		},
		data() {
			return {
				action: '',
				token: '',
				fileList: [],
			}
		},
		mounted() {
			this.action = baseURL + '/upload/image';
			this.token = store.state.token
		},
		 methods: {
			/**
			 * 上传图片
			 */
			change(event) {
				this.$showToast(JSON.parse(event.data).msg)
				if(JSON.parse(event.data).code == 0) {
					this.fileList.push(JSON.parse(event.data).data.image)
					this.$emit('input', this.fileList)
				}
			},
			
			/**
			 * 删除一个图片
			 */
			remove(event) {
				this.fileList.splice(event, 1);
				this.$emit('input', this.fileList)
			}
		}
	}
</script>

<style lang="scss">
	.uplader-upload {
		position: relative;
		width: 160rpx;
		height: 160rpx;
		padding-top: 30rpx;
		text-align: center;
		margin: 11rpx;
		border: 2px dashed #e5e5e5;
		background-color: #FFFFFF;
		>view {
			color: #BBB;
		}
	}
</style>
