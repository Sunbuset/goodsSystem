App({
  globalData: {
    apiBase: 'http://10.58.187.133/backend/api',
    userInfo: null
  },

  onLaunch() {
    const cachedUser = wx.getStorageSync('userInfo');
    if (cachedUser) {
      this.globalData.userInfo = cachedUser;
    }
  }
});