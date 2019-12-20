# coding=utf-8
import os
import sys
from selenium.webdriver import DesiredCapabilities
import xml.etree.ElementTree as ET
from threading import Thread
from SRC.common.configFactory import ConfigFactory
from SRC.common.const import Agent, RunType
from SRC.common.config import EasyConfig
from SRC.unittest.scene import Scene
from SRC.webdriver.browser import Browser


class Main():
	'''
	该类为easyTest项目的管理类
	'''

	def __init__(self, xml):
		self.__xml = xml.replace('\\', '/').strip()  # 默认xml文件名称
		self.__hubDict = {}  # 远程主机的集合
		self.__testCaseList = []  # 测试用例的列表

	def run(self):
		configFactory = ConfigFactory(self.getRootDir('/SRC'))
		configFactory.loadProjectConfig()  # 加载项目配置文件
		self.selectModel()  # 选择启动模式
		self.loadTestCaseXML()  # 加载测试用例配置文件
		self.start()  # 启动

	def loadTestCaseXML(self):
		'''
		加载测试用例配置文件
		:return:
		'''
		try:
			tree = ET.parse(self.__xmlPath)  # 打开xml文档
			root = tree.getroot()  # 获得root节点
			for child in root:
				if child.tag == 'connection':
					for hub in child:
						enabled=hub.get('enabled').lower().strip() if hub.get('enabled') is not None else 'true'
						if enabled == 'true' or enabled== '1':
							self.__hubDict[hub.attrib['browser']] = hub.text.strip()
				elif child.tag == 'scene':
					for tc in child:
						enabled=tc.get('enabled').lower().strip() if tc.get('enabled') is not None else 'true'
						if enabled == 'true' or enabled== '1':
							if 'paramPath' in tc.attrib.keys():
								paramPath = tc.attrib['paramPath']
								if ':' not in paramPath and self.isNoneOrEmpty(paramPath) == False:
									paramPath = paramPath[1:] if paramPath[:1] == '/' or paramPath[
																						 :1] == '\\' else paramPath
									paramPath = '%s%s' % (EasyConfig().dataDir, paramPath)
							else:
								paramPath = None
							self.__testCaseList.append({'paramPath': paramPath, 'testCase': tc.text.strip()})
		except Exception:
			print("Error:测试用例配置文件加载失败:%s." % (self.__xmlPath))
			raise

	def selectModel(self):
		'''
		######################选择启动模式########################
		如果启动的时候，外部有传入参数，则使用外部传入的xml配置文件路径
		如果外部没有传入参数，则使用config.xml配置的xml路径
		self.__xmlPath：测试用例的xml配置文件路径
		######################选择启动模式########################
		'''
		if len(sys.argv) > 1:
			self.__xmlPath = sys.argv[1]
		else:
			if ':' in self.__xml:
				self.__xmlPath = self.__xml  # 绝对路径
			else:
				self.__xmlPath = '%s%s' % (EasyConfig().xmlDir, self.__xml)

	def start(self):
		# 配置场景
		runType = EasyConfig().runType.upper()
		if runType == RunType.REMOTE:
			self.__onLine()
		elif runType == RunType.BROWSER:
			self.__offLine()

	def __offLine(self):
		# 线下模式
		for browser, host in self.__hubDict.items():
			browser = self.__switchBrowser(browser)
			driver = Browser(browser, RunType.BROWSER).driver()
			Scene(driver, self.__testCaseList)

	def __onLine(self):
		# 线上模式
		threads = []
		files = range(len(self.__hubDict))
		# 创建线程
		for browser, host in self.__hubDict.items():
			browser = self.__switchBrowser(browser)
			t = Thread(target=self.__threadStart, args=(browser, host))
			threads.append(t)

		# 启动线程
		for i in files:
			threads[i].start()
		for i in files:
			threads[i].join()

	def __threadStart(self, browser, host):
		desiredCapabilities = self.__setDesiredCapabilities(browser)
		driver = Browser(Agent.REMOTE, RunType.REMOTE, command_executor=host,
						 desired_capabilities=desiredCapabilities).driver()
		# driver.appType = 'onLine'
		Scene(driver, self.__testCaseList)

	def __switchBrowser(self, key):
		browser = Agent.FIREFOX
		if key == 'FF':
			browser = Agent.FIREFOX
		elif key == 'Chrome':
			browser = Agent.CHROME
		elif key == 'IE':
			browser = Agent.IE
		return browser

	def __setDesiredCapabilities(self, browser):
		if browser == Agent.CHROME:
			desiredCapabilities = DesiredCapabilities.CHROME.copy()
			chrome_options = {}
			chrome_options['args'] = ['--disable-popup-blocking']
			chrome_options['excludeSwitches'] = ['ignore-certificate-errors']
			desiredCapabilities['chromeOptions'] = chrome_options
		elif browser == Agent.IE:
			desiredCapabilities = DesiredCapabilities.INTERNETEXPLORER.copy()
		else:
			desiredCapabilities = DesiredCapabilities.FIREFOX.copy()

		return desiredCapabilities

	def isNoneOrEmpty(self, str):
		res = False
		if str == None or str.strip() == '':
			res = True
		return res

	def getRootDir(self, relativePath):
		'''
		项目根目录
		:return:
		'''
		base_dir = os.path.dirname(os.path.abspath(__file__))
		base_dir = str(base_dir)
		base_dir = base_dir.replace('\\', '/')
		return base_dir.split(relativePath)[0]
