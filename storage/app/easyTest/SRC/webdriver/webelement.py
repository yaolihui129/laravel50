# coding=utf-8
from time import sleep
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.common.by import By
from selenium.webdriver.remote.webelement import WebElement as SeleniumWebElement
from SRC.common.decorator import elementAction_dec, findElement_dec
from SRC.webdriver.webelementext import WebElementExt


class WebElement(WebElementExt,SeleniumWebElement):
	def __init__(self, parent, id_, w3c=False):
		super(WebElement, self).__init__(parent, id_, w3c)
		self._driver = self.parent

	@elementAction_dec('左键单击')
	def click(self, wait=0):
		"""Clicks the element."""
		sleep(wait)
		super(WebElement, self).click()

	@elementAction_dec('清除文本')
	def clear(self, wait=0):
		"""Clears the text if it's a text entry element."""
		sleep(wait)
		super(WebElement, self).clear()

	@elementAction_dec('按键输入')
	def send_keys(self, *value, wait=0):
		sleep(wait)
		super(WebElement, self).send_keys(*value)

	@elementAction_dec('提交')
	def submit(self, wait=0):
		sleep(wait)
		super(WebElement, self).submit()

	@elementAction_dec('右键单击')
	def rightClick(self, wait=0):
		# 尽量不要使用，会造成意想不到的错误
		sleep(wait)
		ActionChains(self.parent).context_click(self).perform()

	@elementAction_dec('鼠标悬停')
	def hover(self, wait=0):
		sleep(wait)
		ActionChains(self.parent).move_to_element(self).perform()

	@elementAction_dec('鼠标悬停偏移')
	def hoverOffset(self, xOffset=1, yOffset=1, wait=0):
		sleep(wait)
		ActionChains(self.parent).move_to_element_with_offset(self, xOffset, yOffset).perform()

	@elementAction_dec('鼠标双击')
	def doubleClick(self, wait=0):
		sleep(wait)
		ActionChains(self.parent).double_click(self).perform()

	@elementAction_dec('鼠标拖放')
	def dragAndDrop(self, targetElement, wait=0):
		# 从本元素拖拽到目标元素targetElement
		sleep(wait)
		ActionChains(self.parent).drag_and_drop(self, targetElement).perform()

	@findElement_dec
	def find_element(self, by=By.ID, value=None, alias='Undefined'):
		return super(WebElement, self).find_element(by, value)

	@findElement_dec
	def find_elements(self, by=By.ID, value=None, alias='Undefined'):
		return super(WebElement, self).find_elements(by, value)

	def find_element_by_id(self, id_, alias='Undefined'):
		return self.find_element(By.ID, id_, alias)

	def find_elements_by_id(self, id_, alias='Undefined'):
		return self.find_elements(By.ID, id_, alias)

	def find_element_by_xpath(self, xpath, alias='Undefined'):
		return self.find_element(By.XPATH, xpath, alias)

	def find_elements_by_xpath(self, xpath, alias='Undefined'):
		return self.find_elements(By.XPATH, xpath, alias)

	def find_element_by_link_text(self, link_text, alias='Undefined'):
		return self.find_element(By.LINK_TEXT, link_text, alias)

	def find_elements_by_link_text(self, text, alias='Undefined'):
		return self.find_elements(By.LINK_TEXT, text, alias)

	def find_element_by_partial_link_text(self, link_text, alias='Undefined'):
		return self.find_element(By.PARTIAL_LINK_TEXT, link_text, alias)

	def find_elements_by_partial_link_text(self, link_text, alias='Undefined'):
		return self.find_elements(By.PARTIAL_LINK_TEXT, link_text, alias)

	def find_element_by_name(self, name, alias='Undefined'):
		return self.find_element(By.NAME, name, alias)

	def find_elements_by_name(self, name, alias='Undefined'):
		return self.find_elements(By.NAME, name, alias)

	def find_element_by_tag_name(self, name, alias='Undefined'):
		return self.find_element(By.TAG_NAME, name, alias)

	def find_elements_by_tag_name(self, name, alias='Undefined'):
		return self.find_elements(By.TAG_NAME, name, alias)

	def find_element_by_class_name(self, name, alias='Undefined'):
		return self.find_element(By.CLASS_NAME, name, alias)

	def find_elements_by_class_name(self, name, alias='Undefined'):
		return self.find_elements(By.CLASS_NAME, name, alias)

	def find_element_by_css_selector(self, css_selector, alias='Undefined'):
		return self.find_element(By.CSS_SELECTOR, css_selector, alias)

	def find_elements_by_css_selector(self, css_selector, alias='Undefined'):
		return self.find_elements(By.CSS_SELECTOR, css_selector, alias)
