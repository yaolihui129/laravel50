# coding=utf-8

'''
文件及文件夹操作函数
'''
import datetime
import os

import shutil


def copyFile(sourceFile, targetFile,isBakUp=False):
	result = True
	try:
		if os.path.exists(targetFile):
			if isBakUp:
				currentTime = datetime.datetime.now().strftime('%Y%m%d%H%M%S%f')
				os.rename(targetFile, targetFile + '_bak' + currentTime)  # 备份本地文件
			else:
				os.remove(targetFile)

		if os.path.exists(sourceFile) and not os.path.exists(targetFile):
			print('cp %s %s' % (sourceFile, targetFile))
			shutil.copy(sourceFile, targetFile)
	except Exception as e:
		result = False
		print('[Error-fileHelp.copyFile]:'+str(e))

	return result

def delFile(filePath):
	result = True
	try:
		if os.path.exists(filePath):
			print('del %s' % (filePath))
			os.remove(filePath)
	except Exception as e:
		result = False
		print('[Error-fileHelp.delFile]:'+str(e))
	return result