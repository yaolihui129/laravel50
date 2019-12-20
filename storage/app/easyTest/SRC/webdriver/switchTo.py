# coding=utf-8
from selenium.webdriver.remote.switch_to import SwitchTo as SeleniumSwitchTo

from SRC.common.decorator import driverAction_dec
from .alert import Alert

class SwitchTo(SeleniumSwitchTo):
	def __init__(self,driver):
		super(SwitchTo, self).__init__(driver)
	@driverAction_dec("切换到表单")
	def frame(self, frame_reference):
		super(SwitchTo, self).frame(frame_reference)
	@driverAction_dec("切换到窗口")
	def window(self, window_name):
		super(SwitchTo, self).window(window_name)
	@property
	def alert(self):
		return self.__alert()
	@driverAction_dec("切换到警告框")
	def __alert(self):
		return Alert(self._driver)