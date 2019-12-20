# coding:utf-8
import os
from collections import namedtuple

from SRC.common.config import EasyConfig
from  SRC.common.configFactory import ConfigFactory as CFactory
from ui.project.model.testPlan import TestPlan

'''
测试方案配置工厂
'''

class ConfigFactory(CFactory):
	def __init__(self,projectPath,configXmlPath=None):
		super(ConfigFactory, self).__init__(projectPath,configXmlPath)
		self.testPlanList=[] #测试方案列表
		self.loadConfigFile()
		self.xmlDir=EasyConfig().xmlDir #测试方案配置文件目录
		self.templateList=self.getTemplatePathList(projectPath)
		# dataDir=EasyConfig().dataDir #参数化文件目录

	def getTemplatePathList(self,projectPath):
		list=[]
		for fileName in ['xml模板.xml','testCase模板.py','data参数化模板.xml']:
			list.append(os.path.join(projectPath,'script/template/',fileName).replace('\\','/'))
		return list

	def loadConfigFile(self):
		super(ConfigFactory, self).loadProjectConfig() #加载全局配置文件

	def loadAllTestPlans(self):
		try:
			if os.path.isdir(self.xmlDir):
				self.diguiReadDir(self.xmlDir)
			else:
				print('loadAllTestPlans->目录错误:'+self.xmlDir)
		except Exception as e:
			print('loadAllTestPlans:'+str(e))

	def diguiReadDir(self, dir):
		files = os.listdir(dir)
		for file in files:
			path=os.path.join(dir,file).replace('\\','/')
			if os.path.isfile(path):
				self.addTestPlan(path,file)
			elif os.path.isdir(path):
				self.diguiReadDir(path)

	def addTestPlan(self,path,file):
		if file.split('.')[-1] == 'xml':
			fileName = file.split('.')[0]
			def func():
				return TestPlan(fileName, path)
			model = {'name': fileName,'path':path, 'objFunc': func}
			res=[x for x in self.testPlanList if x['path']==path]
			if res:
				self.removeTestPlan(res)
			self.testPlanList.append(model)

	def removeTestPlan(self,mode):
		if isinstance(mode,list):
			for m in mode:
				self.testPlanList.remove(m)
		else:
			self.testPlanList.remove(mode)

if __name__ =='__main__':
	cf=ConfigFactory('E:/Python/PythonWorkspace/easyTest/')
	cf.loadAllTestPlans()
	print(cf.testPlanList)
