# coding=utf-8
from time import sleep

from selenium.webdriver.remote.webelement import WebElement as SWebElement

from SRC.common.decorator import elementAction_dec
# from SRC.common.verificationCode import vCode


class WebElementExt(SWebElement):
	def __init__(self,parent, id_, w3c=False):
		super(WebElementExt, self).__init__(parent, id_, w3c=False)
		self._driver= self.parent

	@elementAction_dec('显示元素')
	def show(self, wait=0):
		sleep(wait)
		self.parent.execute_script('$(arguments[0]).show()', self)

	@elementAction_dec('隐藏元素')
	def hidden(self, wait=0):
		sleep(wait)
		self.parent.execute_script('$(arguments[0]).hide()',self)

	# def readVCode(self, isNumber=False, wait=0):
	# 	'''
	# 	验证码识别
	# 	:return:
	# 	'''
	# 	sleep(wait)
	# 	png = self.parent.get_screenshot_as_png()
	# 	code = vCode(png, self.location, self.size, isNumber)
	# 	return code.strip()