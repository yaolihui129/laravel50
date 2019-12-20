# coding=utf-8
import logging
import os
from os.path import basename
from urllib.parse import urlencode
from urllib.request import urlopen
from SRC.common.config import EasyConfig
from SRC.common.const import RunResult, RunType


def createLog(logPath):
	logName = os.path.basename(logPath)
	logger = logging.getLogger(logName)
	logger.setLevel(logging.INFO)
	# 创建一个handler，用于写入日志文件
	fh = logging.FileHandler(logPath)

	# 再创建一个handler，用于输出到控制台
	# ch = logging.StreamHandler()

	# 定义handler的输出格式formatter
	fmt = '%(asctime)s %(message)s'
	datefmt = '%Y-%m-%d %H:%M:%S'
	formatter = logging.Formatter(fmt, datefmt)
	fh.setFormatter(formatter)
	# ch.setFormatter(formatter)

	# 定义一个filter
	# filter = logging.Filter('mylogger.child1.child2')
	# fh.addFilter(filter)

	# 给logger添加handler
	# logger.addFilter(filter)
	logger.addHandler(fh)
	# logger.addHandler(ch)

	return logger


def findElementSuccess(data):
	return (data['cmd'] == 'find_element' or data['cmd'] == 'find_elements') and data['result'] == RunResult.PASS


def putLog(data, driver):
	if EasyConfig().isWriteLog:
		if findElementSuccess(data) and EasyConfig().showFindElementLog:  # 如果成功发现元素，则不打印日志
			putLogForFile(data, driver)  # 将日志写入文件
		elif findElementSuccess(data)==False :
			putLogForFile(data, driver)  # 将日志写入文件

	if EasyConfig().isRequest:
		putLogForServer(data, driver)  # 将入职发送到服务器

def putSystemLog(data,logger):
	if EasyConfig().isWriteLog:
		logger.info(data)


def putLogForFile(data, driver):
	if (driver.appType == RunType.REMOTE):
		writeLog(data, driver)
	else:
		writeLog(data, driver)


def putLogForServer(data, driver):
	if (driver.appType == RunType.REMOTE):
		sendLogToHTTP('', 'get', data)


def writeLog(data, driver):
	if data['image'] == '' or data['image'] == None:
		image = ''
	else:
		image = '截图:' + basename(data['image'])

	if data['result'] == RunResult.ERROR:
		txt = '行号:%-4s %-5s  %s[%s]:%s' % (data['lineNo'],data['result'], data['description'], data['cmd'], data['errorMessage'])
	elif data['result'] == RunResult.TRUE or data['result'] == RunResult.FALSE:
		txt = '行号:%-4s %-5s  %s[%s] 用时:%s %s' % (data['lineNo'],
			data['result'], data['description'], data['cmd'], data['during'], data['errorMessage'])
	elif data['result'] == RunResult.FAIL:
		txt = '行号:%-4s %-5s  %s[%s] %s 用时:%s %s 参数:%s %s' % (data['lineNo'],
			data['result'], data['description'], data['cmd'], data['elementAlias'], data['during'], image,
			data['cmdParam'],
			data['errorMessage'])
	else:
		txt = '行号:%-4s %-5s  %s[%s] %s 用时:%s %s %s' % (data['lineNo'],
			data['result'], data['description'], data['cmd'], data['elementAlias'], data['during'], image,
			data['errorMessage'])

	if int(data['level']) <= int(EasyConfig().logLevel):
		driver.logger.info(txt)


def sendLogToHTTP(_url, method='get', data=None, response=False):
	url = EasyConfig().requestURL if _url == '' else _url
	res_data = ""
	try:
		if 'GET' == method.upper():
			if data != None:
				fullUrl = url + '?' + urlencode(data)
			else:
				fullUrl = url
			res_data = urlopen(fullUrl)
		elif 'POST' == method.upper():
			res_data = urlopen(url, urlencode(data).encode(encoding='UTF8'))
		else:
			print('error:请求类型错误！')

		if response:
			return res_data
		else:
			return True
	except Exception as  e:
		# print('ERROR:发送请求失败，url：'+url)
		print(e)


# 日志信息整理
def getJsonData(result, cmd, cmdParam, errorMessage, during, image, elementAlias, description,lineNo, level):
	return {'result': result,
			'cmd': cmd,
			'cmdParam': cmdParam,
			'errorMessage': errorMessage,
			'during': during,
			'image': image,
			'elementAlias': elementAlias,
			'description': description,
			'lineNo':lineNo,
			'level': level
			}
