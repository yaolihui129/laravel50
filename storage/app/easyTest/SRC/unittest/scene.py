# coding=utf-8
import importlib
import os
import unittest
import xml.etree.ElementTree as ET
from time import time

from SRC.common.config import EasyConfig
from SRC.common.loga import createLog, putSystemLog
from SRC.common.utils import createDir, getLogName
from SRC.unittest.case import TestCase


class Scene():
	'''
	场景类
	'''

	def __init__(self, webDriverFunc, testCaseList):
		self._driverFunc = webDriverFunc  # 浏览器驱动调用方法
		self.__importTestCase(testCaseList)  # 用例类列表
		self.logger=None
		self.screenShotDir=None
		self.createLoggerObj()  # 创建该场景日志对象
		self.createScreenShotDir()  # 创建截图目录
		self.run()

	def run(self):
		logger=self.logger
		for x in range(int(EasyConfig().runTime)):
			try:
				putSystemLog('程序开始.',logger)
				putSystemLog('正在初始化....',logger)
				sTime=time()
				driver = self.getDriverObj()  # 获取一个浏览器驱动
				putSystemLog('%s浏览器启动成功.'%(driver.name),logger)
				putSystemLog('开始加载脚本...',logger)
				suite = self.suiteFactory(x, driver)  # 创建并配置测试单元套件
				putSystemLog('脚本加载完成...',logger)
				unittest.TextTestRunner().run(suite)
			except Exception as e:
				putSystemLog('程序运行过程中发生异常.',logger)
				putSystemLog(e,logger)
				raise
			finally:
				driver.quit()
				eTime=time()
				putSystemLog('程序结束,用时:%.3fs.'%(eTime-sTime),logger)


	def getDriverObj(self):
		driver = self._driverFunc()  # 创建浏览器驱动
		driver.logger = self.logger  # 为浏览器驱动配置日志对象
		driver.screenShotDir = self.screenShotDir  # 为浏览器驱动配置截图目录
		return driver

	def createLoggerObj(self):
		if EasyConfig().isWriteLog:
			# 创建日志对象
			logDir = createDir(EasyConfig().logDir)  # 创建日志目录
			logPath = os.path.join(logDir, getLogName())  # 日志绝对路径
			self.logger = createLog(logPath)  # 日志对象

	def createScreenShotDir(self):
		if EasyConfig().isScreenShot:
			screenShotDir = createDir(EasyConfig().screenShotDir)  # 创建截图目录
			self.screenShotDir = screenShotDir

	def suiteFactory(self, x, driver):
		suite = unittest.TestSuite()  # 创建测试单元套件
		number=1
		for testCaseClass in self.__testClassList:
			paramPath = testCaseClass['paramPath']  # 获取参数文件地址
			if paramPath != None and paramPath.strip() != '':
				paramsList = self.readParamXml(paramPath, testCaseClass['className'], x)
			else:
				paramsList = None
			suite.addTest(testCaseClass['testClass'](driver, paramsList)) #创建测试用例，并添加到套件中

			scriptName=testCaseClass['scriptPath'].split('/')[-1]
			putSystemLog('脚本<%d>：%s 加载成功.脚本路径：%s'%(number,scriptName,testCaseClass['scriptPath']),self.logger)
			if paramsList!=None:
				putSystemLog('脚本<%d>参数列表：'%(number),self.logger)
				for k,v in paramsList.items():
					putSystemLog("%s:%s"%(k,v),self.logger)
			number=number+1
		return suite

	def __importTestCase(self, testCaseList):
		'''
		动态添加测试用例的引用
		:param testCaseList:
		:return:
		'''
		classList = []
		for dict in testCaseList:
			try:
				p = dict['testCase'][:-3] if dict['testCase'][-3:] == '.py' else dict['testCase']
				path=p
				path = path.replace('/', '.')
				path = path[1:] if path[:1] == '.' else path
				model_module = importlib.import_module(path)  # 引入模块
			except:
				print('%s：引入模块错误！' % (dict['testCase']))  # 这块需要写到日志里 备忘
				continue
			for attr_name in dir(model_module):
				attr = getattr(model_module, attr_name)
				try:
					if issubclass(attr, TestCase) and attr.__name__ != TestCase.__name__:
						classList.append(
							{'paramPath': dict['paramPath'], 'testClass': attr, 'className': attr.__name__,'scriptPath':'%s'%(p)})
				except Exception:
					continue
		self.__testClassList = classList

	def readParamXml(self, paramPath, className, time):
		# 读取参数xml文件的数据
		params = {}
		try:
			tree = ET.parse(paramPath)  # 打开xml文档
			root = tree.getroot()  # 获得root节点
			num = time % len(root.findall('testCase'))  # 读取参数的次数
			testClassElement = root[num].find("testClass[@name='%s']" % (className))
			if testClassElement != None:
				for param in testClassElement:
					params[param.attrib['id']]=param.text
			else:
				paramsList = None
		except Exception:
			print("Error:参数化数据配置文件加载失败:%s." % (paramPath))
			raise
		return params
