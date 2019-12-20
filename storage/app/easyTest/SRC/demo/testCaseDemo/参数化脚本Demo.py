# coding=utf-8

from SRC.unittest.case import TestCase
class Test2(TestCase):
	def __init__(self,webDriver,paramPath):
		#请不要修改该方法
		super(Test2, self).__init__(webDriver,paramPath)

	def runTest(self):
		driver=self.getDriver()
		param=self.param
		'''
		该方法内进行测试用例的编写
		##################################################################
		浏览器驱动：driver
		例如：
		driver.get('http://www.demo.com')
		driver.find_element_by_id("kw","输入框").send_keys("Remote")
		driver.find_elements_by_id("su","查找")[0].click()

		参数化说明：
		需要进行参数化的数据，用param.id 替换,id为参数化配置文件中的id值
		##################################################################
		'''
		driver.get('http://www.baidu.com')
		driver.find_element_by_id("kw","输入框").send_keys(param.username)
		driver.find_element_by_id("kw","输入框").send_keys(param.password)
		driver.find_element_by_id("kw","输入框").send_keys(param.firstName)
		driver.find_element_by_id("kw","输入框").send_keys(param.url)
		driver.find_element_by_id("su","查找").click()
