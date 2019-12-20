# coding=utf-8
from SRC.common.decorator import codeException_dec
from SRC.unittest.case import TestCase


class AssertDemo(TestCase):
	def __init__(self, webDriver,paramsList):
		# 请不要修改该方法
		super(AssertDemo, self).__init__(webDriver,paramsList)

	@codeException_dec('3')
	def runTest(self):
		driver = self.getDriver()
		'''
		该方法内进行测试用例的编写
		##################################################################
		浏览器驱动：driver
		例如：
		driver.get('http://www.demo.com')
		driver.find_element_by_id("kw","输入框").send_keys("Remote")
		driver.find_elements_by_id("su","查找")[0].click()
		##################################################################
		'''
		self.assertEqual(1,2,"相等吗？")
		self.assertEqual(2,2,"相等吗？")
		self.assertNotEqual(1,2)
		self.assertNotEqual(2,2)
		self.assertTrue(1==1)
		self.assertTrue(1==2)
		self.assertFalse(1==1)
		self.assertFalse(1==2)
		self.assertIn('a','abc')
		self.assertNotIn('a','def')
		self.assertIsNone(None)
		self.assertIsNotNone(123)

