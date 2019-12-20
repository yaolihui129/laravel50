# coding:utf-8
'''
测试方案模型
'''
import os

from SRC.common import xmlHelper
from SRC.common.config import EasyConfig
import xml.etree.ElementTree as ET
from ui.project.model.testCase import TestCase


class TestPlan():
	def __init__(self, name, path=''):
		self.path = path
		self.name = name
		self.hub = []
		self.testCaseList = []
		self.acceptBrowser = ['ff', 'chrome', 'ie']
		self.testCaseDir = EasyConfig().getDir('/script/testCase/')  # 测试用例脚本目录
		self.readTestPlanXML()  # 分析xml文件

	def setTestCaseList(self, list):
		self.testCaseList = list

	def getTestCaseList(self):
		return self.testCaseList

	def addTestCase(self, relativePath, enabled='True', paramPath=''):
		path = os.path.join(EasyConfig().rootPath, relativePath).replace('\\', '/')
		path = path + '.py' if path[-3:] != '.py' else path
		fileName = os.path.basename(path).split('.')[0]
		enabled = True if enabled is None or enabled.lower().strip() == 'true' else False
		model = {'name': fileName, 'obj': TestCase(path, paramPath), 'path': path, 'paramPath': paramPath,
				 'enabled': enabled}
		self.testCaseList.append(model)

	def removeTestCase(self):
		pass

	def addHub(self, browser, enabled, remoteUrl='http://0.0.0.0:5555/wd/hub'):
		if browser.strip().lower() in self.acceptBrowser:
			enabled = True if enabled is None or enabled.lower().strip() == 'true' else False
			model = {'browser': browser, 'enabled': enabled, 'remoteUrl': remoteUrl}
			self.hub.append(model)
			return model

	def removeHub(self, browser):
		for item in self.hub:
			if item['browser'] == browser:
				self.hub.remove(item)

	def isNoneOrEmpty(self, str):
		res = False
		if str == None or str.strip() == '':
			res = True
		return res

	def readTestPlanXML(self):
		'''
		加载测试方案配置文件
		:return:
		'''
		try:
			tree = ET.parse(self.path)  # 打开xml文档
			root = tree.getroot()  # 获得root节点
			for child in root:
				if child.tag == 'connection':
					for hub in child:
						self.addHub(hub.attrib['browser'], hub.get('enabled'), hub.text.strip())
				elif child.tag == 'scene':
					for tc in child:
						paramPath = tc.get('paramPath')
						if paramPath:
							if ':' not in paramPath and self.isNoneOrEmpty(paramPath) == False:
								paramPath = paramPath[1:] if paramPath[:1] == '/' or paramPath[
																					 :1] == '\\' else paramPath
								paramPath = '%s%s' % (EasyConfig().dataDir, paramPath)
						else:
							paramPath = ''
						testCasePath = tc.text.strip()
						testCasePath = testCasePath[1:] if testCasePath[:1] == '/' or testCasePath[
																					  :1] == '\\' else testCasePath

						self.addTestCase(testCasePath, tc.get('enabled'), paramPath)
		except Exception as e:
			print("[Error-TestPlan.readTestPlanXML]:读取测试方案错误 %s %s" % (self.path, str(e)))

	def writeTestPlanToXML(self):
		result=True
		try:
			tree =xmlHelper.read_xml(self.path)
			connectionNode=tree.find('connection')
			for model in self.hub:
				isUpdate=False
				for node in connectionNode:
					browserName=node.get('browser')
					if browserName and browserName.lower().strip()==model['browser'].lower().strip():
						node.set('enabled',str(model['enabled']))
						node.text=model['remoteUrl']
						isUpdate=True
						break
				if not isUpdate:
					newNode=xmlHelper.create_node('hub',{'browser':model['browser'],'enabled':str(model['enabled'])},model['remoteUrl'])
					xmlHelper.add_child_node([connectionNode],newNode)
			xmlHelper.indent(tree.getroot())
			xmlHelper.write_xml(tree,self.path)
		except Exception as e:
			print('[Error-TestPlan.writeTestPlanToXML]: %s'%(str(e)))
			result=False
		return result


