<script>
	import {mapMutations} from 'vuex'
	import {setTabBar, tabBarList, silentLogin} from '@/utils/tools'
	
	export default {
		globalData: {
			navHeight: ""
		},
		onLaunch: function(options) {
			uni.hideTabBar({
				animation: false
			})
			this.getConfig(options) 
			this.getSystemInfo()
		},
		onShow: function(options) {
			//#ifdef MP
				silentLogin()
			// #endif
		},
		onHide: function() {

		},
		methods: {
			...mapMutations(['SET_CONFIG']),
			/**
			 * 系统信息
			 */
			getSystemInfo() {
				uni.getSystemInfo({
					success: res => {
						let navHeight
						let {statusBarHeight, platform} = res
						if (platform == 'ios' || platform == 'devtools') {
							navHeight = statusBarHeight + 44
						} else {
							navHeight = statusBarHeight + 48
						}
						this.globalData.navHeight = navHeight
					},
					fail(err) {
						console.log(err)
					}
				})
			},
			
			/**
			 * 系统配置
			 */
			getConfig(options) {
				try {
					this.$u.api.apiConfig().then(result => {
						if (result.code === 0) {
							this.SET_CONFIG(result.data)
							if (tabBarList.indexOf(options.path) !== -1) {
								setTabBar()
							}
						}		
					})
				} catch(e){
					uni.showTabBar()
				} finally {
					uni.showTabBar()
				}
			}
		}
	}
</script>

<style lang="scss">
	@import "styles/iconfont.css";
	@import "styles/base.scss";
	@import "styles/reset.scss";
	@import "components/uview-ui/index.scss";
</style>
