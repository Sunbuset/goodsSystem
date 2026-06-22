const { request } = require('../../utils/request');
const { setUserInfo } = require('../../utils/storage');

Page({
  data: {
    username: '',
    password: ''
  },

  onUsernameInput(e) {
    this.setData({ username: e.detail.value });
  },

  onPasswordInput(e) {
    this.setData({ password: e.detail.value });
  },

  login() {
    const { username, password } = this.data;
    if (!username || !password) {
      wx.showToast({ title: '请输入账号和密码', icon: 'none' });
      return;
    }

    request({
      url: 'login.php',
      method: 'POST',
      data: {
        username,
        password
      }
    }).then((res) => {
      setUserInfo(res.data.user);
      wx.showToast({ title: '登录成功' });
      wx.navigateBack({ delta: 1 });
    });
  }
});