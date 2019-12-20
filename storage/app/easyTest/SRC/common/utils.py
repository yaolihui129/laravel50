import os
import random
import time
from threading import current_thread
import datetime as dt


def getCurrentTime():
	'''
	获取当前时间
	:return:
	'''
	return dt.datetime.now().strftime('%Y%m%d%H%M%S%f')


def getLogName():
	'''
	获取一个当前线程的随机日志名称
	:return:
	'''
	now = getCurrentTime()
	threadId = current_thread().ident
	return "report_%s_%s.log" % (threadId, now)


# 获取图片保存路径
def getImageSavePath(dir):
	now = getCurrentTime()
	threadId = current_thread().ident
	# browserName=current_thread()._args[0]
	return os.path.join(dir, 'image_%s_%s.jpg' % (threadId, now))


def createDir(dir):
	date = time.strftime('%Y%m%d', time.localtime())
	dirPath = os.path.join(dir, date)
	time.sleep(random.uniform(0, 1))  # 延迟等待0-1秒
	try:
		if not os.path.exists(dirPath):
			os.makedirs(dirPath)
	except Exception as e:
		print('ERROR:创建目录失败:%s' % (e))
	return dirPath


def randomStr(length=6, lowerCaseLetter=False, capitalLetter=False, number=True, specialSign=False,
			  otherSignsList=None):
	'''
	返回一个随机字符串
	:param length: 字符串长度
	:param number: 是否包含数字
	:param lowerCaseLetter: 是否包含小写字母
	:param capitalLetter: 是否包含大写字母
	:param specialSign: 是否包含特殊符号
	:param otherSignsList: 其他字符
	:return:
	'''
	res = []
	if number == True:
		res.extend(map(lambda i: chr(i), [x for x in range(48, 58)]))
	if lowerCaseLetter == True:
		res.extend(map(lambda i: chr(i), [x for x in range(97, 123)]))
	if capitalLetter == True:
		res.extend(map(lambda i: chr(i), [x for x in range(65, 90)]))
	if specialSign == True:
		# res.extend(['_', '-'])
		if otherSignsList != None and isinstance(otherSignsList, list):
			res.extend(otherSignsList)

	str = ""
	if len(res) != 0:
		for x in range(length):
			index = random.randint(0, len(res) - 1)
			str = str + res[index]
	return str

