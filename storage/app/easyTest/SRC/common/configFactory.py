# coding:utf-8
import os

from SRC.common.config import EasyConfig
import xml.etree.ElementTree as ET

class ConfigFactory():
	def __init__(self,projectPath,configXmlPath=None):
		self.projectPath=projectPath
		if not configXmlPath:
			self.configXmlPath=os.path.join(self.projectPath,'SRC/config.xml').replace('/','\\')

	def getEasyTestConfigSingletonObj(self):
		return EasyConfig()

	def loadProjectConfig(self):
		'''
		加载项目配置文件
		'''
		rootPath = self.projectPath
		configModel = self.getEasyTestConfigSingletonObj()
		configModel.rootPath = rootPath    #项目路径
		configXmlPath = self.configXmlPath #config.cml路径
		try:
			tree = ET.parse(configXmlPath)  # 打开xml文档

			configModel.runType = self.getValueFromXML(tree.find('testCase/runType'), ['Remote', 'Browser'], 'Browser')
			configModel.xmlDir = self.getValueFromXML(tree.find('testCase/xmlDir'))

			configModel.logLevel = self.getValueFromXML(tree.find('report/logLevel'))
			configModel.logDir = self.getValueFromXML(tree.find('report/logDir'))
			configModel.screenShotDir = self.getValueFromXML(tree.find('report/screenShotDir'))
			configModel.requestURL = self.getValueFromXML(tree.find('server/requestURL'))
			configModel.runTime = self.getValueFromXML(tree.find('parameterization/runTime'))
			configModel.dataDir = self.getValueFromXML(tree.find('parameterization/dataDir'))

			configModel.implicitlyWait = self.getValueFromXML(tree.find('driver/implicitlyWait'))
			configModel.afterActionWait = self.getValueFromXML(tree.find('driver/afterActionWait'))
			configModel.afterFindElementWait = self.getValueFromXML(tree.find('driver/afterFindElementWait'))

			configModel.repeatFindTime = self.getValueFromXML(tree.find('driver/repeatFindTime'))
			configModel.maxWindow = self.getValueFromXML(tree.find('driver/maximizeWindow'))

			configModel.isScreenShot = self.getValueFromXML(tree.find('report/isScreenShot'))
			configModel.isWriteLog = self.getValueFromXML(tree.find('report/isWriteLog'))
			configModel.showFindElementLog = self.getValueFromXML(tree.find('report/showFindElementLog'))
			configModel.isRequest = self.getValueFromXML(tree.find('server/isRequest'))

			configModel.ffLocation = self.getValueFromXML(tree.find("browser/fireFox/param[@name='binary_location']"))
			# configModel.ffProfileDirectory = self.getValueFromXML(tree.find("browser/fireFox/param[@name='profile_directory']"))

			configModel.chromeLocation = self.getValueFromXML(tree.find("browser/chrome/param[@name='binary_location']"))
			# configModel.chromeUserDataDir = self.getValueFromXML(tree.find("browser/chrome/param[@name='user-data-dir']"))

		except Exception:
			print("Error:config配置文件读取错误:%s." % (configXmlPath))
			raise

	def isNoneOrEmpty(self, str):
		res = False
		if str == None or str.strip() == '':
			res = True
		return res


	def getValueFromXML(self, element, limit=None, default=None):
		'''
		在xml文件中获取值,如果用户未直接提供，则返回默认值
		:param element: 元素节点
		:param limit: 限定列表
		:param default: 默认值
		:return: 值
		'''
		if self.isNoneOrEmpty(element.text):
			value = element.attrib['default']
		elif limit != None:
			if element.text.strip() in limit:
				value = element.text.strip()
			else:
				value = default
		else:
			value = element.text.strip()

		return value