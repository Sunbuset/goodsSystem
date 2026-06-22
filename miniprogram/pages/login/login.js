const { request } = require('../../utils/request');
const { setUserInfo } = require('../../utils/storage');

Page({
  data: {
    mode: 'login',
    username: '',
    password: ''
  },

  onUsernameInput(e) {
    this.setData({ username: e.detail.value });
  },

  onPasswordInput(e) {
    this.setData({ password: e.detail.value });
  },

  switchMode() {
    this.setData({
      mode: this.data.mode === 'login' ? 'register' : 'login'
    });
  },

  submit() {
    const { username, password } = this.data;
    if (!username || !password) {
      wx.showToast({ title: '请输入账号和密码', icon: 'none' });
      return;
    }

    const isRegister = this.data.mode === 'register';
    const url = isRegister ? 'register.php' : 'login.php';
    const successTitle = isRegister ? '注册成功' : '登录成功';

    request({
      url,
      method: 'POST',
      data: {
        username,
        password
      }
    }).then((res) => {
      setUserInfo(res.data.user);
      wx.showToast({ title: successTitle });
      wx.navigateBack({ delta: 1 });
    });
  }
});