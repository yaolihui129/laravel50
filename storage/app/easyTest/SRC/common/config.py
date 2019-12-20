# coding=utf-8
import os


def singleton(cls, *args, **kw):
	'''
	单例的装饰器
	'''
	instances = {}

	def _singleton():
		if cls not in instances:
			instances[cls] = cls(*args, **kw)
		return instances[cls]

	return _singleton


@singleton
class EasyConfig():
	'''
	本项目的通用配置文件获取类
	'''

	def __init__(self):
		self.__rootPath = ''
		self.__runType = ''
		self.__xmlDir = ''
		self.__logLevel = ''
		self.__logDir = ''
		self.__screenShotDir = ''
		self.__requestURL = ''

		self.__implicitlyWait = 0
		self.__afterActionWait =0
		self.__afterFindElementWait=0
		self.__repeatFindTime = ''
		self.__maxWindow = False

		self.__isScreenShot=False
		self.__isWriteLog=False
		self.__isRequest=False
		self.__showFindElementLog=False

		self.__dataDir = ''
		self.__runTime = ''

		self.__ffLocation = ''
		self.__ffProfileDirectory = ''

		self.__chromeLocation = ''
		self.__chromeUserDataDir = ''

	def getDir(self, relativePath):
		rp = relativePath[1:] if relativePath[:1] == '/' or relativePath[:1] == '\\' else relativePath
		return os.path.join(self.rootPath, rp).replace('\\', '/')

	@property
	def rootPath(self):
		return self.__rootPath

	@rootPath.setter
	def rootPath(self, value):
		self.__rootPath = value

	@property
	def runType(self):
		return self.__runType

	@runType.setter
	def runType(self, value):
		self.__runType = value

	@property
	def xmlDir(self):
		return self.__xmlDir

	@xmlDir.setter
	def xmlDir(self, value):
		if ':' in value:
			self.__xmlDir = value  # 绝对路径
		else:
			self.__xmlDir = self.getDir(value)

	@property
	def logLevel(self):
		return self.__logLevel

	@logLevel.setter
	def logLevel(self, value):
		self.__logLevel = value

	@property
	def logDir(self):
		return self.__logDir

	@logDir.setter
	def logDir(self, value):
		if ':' in value:
			self.__logDir = value  # 绝对路径
		else:
			self.__logDir = self.getDir(value)

	@property
	def screenShotDir(self):
		return self.__screenShotDir

	@screenShotDir.setter
	def screenShotDir(self, value):
		if ':' in value:
			self.__screenShotDir = value  # 绝对路径
		else:
			self.__screenShotDir = self.getDir(value)

	@property
	def dataDir(self):
		return self.__dataDir

	@dataDir.setter
	def dataDir(self, value):
		if ':' in value:
			self.__dataDir = value  # 绝对路径
		else:
			self.__dataDir = self.getDir(value)

	@property
	def requestURL(self):
		return self.__requestURL

	@requestURL.setter
	def requestURL(self, value):
		self.__requestURL = value

	@property
	def implicitlyWait(self):
		return self.__implicitlyWait

	@implicitlyWait.setter
	def implicitlyWait(self, value):
		self.__implicitlyWait = value

	@property
	def afterActionWait(self):
		return self.__afterActionWait

	@afterActionWait.setter
	def afterActionWait(self, value):
		wait=float(value)
		if wait<0 or wait>60*60:
			wait=0
		self.__afterActionWait =wait

	@property
	def afterFindElementWait(self):
		return self.__afterFindElementWait

	@afterFindElementWait.setter
	def afterFindElementWait(self, value):
		wait=float(value)
		if wait<0 or wait>60*60:
			wait=0
		self.__afterFindElementWait =wait

	@property
	def repeatFindTime(self):
		return self.__repeatFindTime

	@repeatFindTime.setter
	def repeatFindTime(self, value):
		self.__repeatFindTime = value

	@property
	def maxWindow(self):
		return self.__maxWindow

	@maxWindow.setter
	def maxWindow(self, value):
		if value.upper() == 'TRUE':
			self.__maxWindow = True
		else:
			self.__maxWindow = False

	@property
	def runTime(self):
		return self.__runTime

	@runTime.setter
	def runTime(self, value):
		self.__runTime = value

	@property
	def ffLocation(self):
		return self.__ffLocation

	@ffLocation.setter
	def ffLocation(self, value):
		self.__ffLocation = value

	@property
	def ffProfileDirectory(self):
		return self.__ffProfileDirectory

	@ffProfileDirectory.setter
	def ffProfileDirectory(self, value):
		self.__ffProfileDirectory = value

	@property
	def chromeLocation(self):
		return self.__chromeLocation

	@chromeLocation.setter
	def chromeLocation(self, value):
		self.__chromeLocation = value

	@property
	def chromeUserDataDir(self):
		return self.__chromeUserDataDir

	@chromeUserDataDir.setter
	def chromeUserDataDir(self, value):
		self.__chromeUserDataDir = value

	@property
	def isScreenShot(self):
		return self.__isScreenShot

	@isScreenShot.setter
	def isScreenShot(self, value):
		if value.upper() == 'TRUE':
			self.__isScreenShot = True
		else:
			self.__isScreenShot = False

	@property
	def isWriteLog(self):
		return self.__isWriteLog

	@isWriteLog.setter
	def isWriteLog(self, value):
		if value.upper() == 'TRUE':
			self.__isWriteLog = True
		else:
			self.__isWriteLog = False

	@property
	def isRequest(self):
		return self.__isRequest

	@isRequest.setter
	def isRequest(self, value):
		if value.upper() == 'TRUE':
			self.__isRequest = True
		else:
			self.__isRequest = False

	@property
	def showFindElementLog(self):
		return self.__showFindElementLog

	@showFindElementLog.setter
	def showFindElementLog(self, value):
		if value.upper() == 'TRUE':
			self.__showFindElementLog = True
		else:
			self.__showFindElementLog = False