const { clearUserInfo, getUserInfo } = require('../../utils/storage');

Page({
  data: {
    userInfo: null
  },

  onShow() {
    this.setData({ userInfo: getUserInfo() });
  },

  toLogin() {
    wx.navigateTo({ url: '/pages/login/login' });
  },

  logout() {
    wx.showModal({
      title: '退出登录',
      content: '确定要退出当前账号吗？',
      success: (res) => {
        if (!res.confirm) {
          return;
        }

        clearUserInfo();
        this.setData({ userInfo: null });
        wx.showToast({ title: '已退出登录', icon: 'none' });
      }
    });
  }
});