import functools
import random
import time
import sys
from selenium.common.exceptions import NoSuchElementException, StaleElementReferenceException, WebDriverException
from selenium.webdriver.remote.command import Command
from SRC.common.config import EasyConfig
from SRC.common.const import RunResult
from SRC.common.exceptions import ScreenShotException
from SRC.common.loga import putLog, getJsonData
from SRC.common.utils import getImageSavePath


# 带参数的装饰器
# def driver_dec(description):
# 	def decorator(func):
# 		@functools.wraps(func)
# 		def wrapper(*args,**kwargs):
# 			ret=None
# 			try:
# 				ret=func(*args,**kwargs)
# 			except Exception:
# 				print(description)
# 			return ret
# 		return wrapper
# 	return decorator

def findElement_dec(func):
	'''
	元素查找断言
	'''

	@functools.wraps(func)
	def wrapper(self, by, value, alias):
		def saveFunc():
			# 该函数保存查找元素的状态及方法
			ret = None
			try:
				ret = func(self, by, value, alias)
				addElementAttr(ret, self, by, value, alias, saveFunc)  # 为元素对象添加别名
			except Exception:
				pass
			return ret

		ret = None
		lineNo = getLineNo()  # 获取行号
		cmdParam = {
			'list': '',
			'dict': {by: value}
		}
		errorMessage = ''
		elementAlias = ''
		result = RunResult.FAIL
		sTime = time.time()
		number = int(EasyConfig().repeatFindTime)  # 重复查找次数
		n = number
		while n > 0:
			try:
				ret = func(self, by, value, alias)
				result = RunResult.PASS
				errorMessage = '在%d次重复查找后找到元素' % (number - n) if n != number else ''
				addElementAttr(ret, self, by, value, alias, saveFunc)  # 为元素对象添加别名
				break
			except NoSuchElementException:
				errorMessage = '页面中没有该元素.NoSuchElementException'
				break
			except StaleElementReferenceException:
				errorMessage = '由于一些原因，多次查找后仍然找不到元素.StaleElementReferenceException'
				time.sleep(1)
			except Exception as e:
				errorMessage = e
			n = n - 1

		eTime = time.time()
		image = screenShot(self._driver, ret)
		data = getJsonData(result, func.__name__, cmdParam, errorMessage, '%.3fs' % (eTime - sTime), image,
						   elementAlias, '查找元素', lineNo, 1)
		putLog(data, self._driver)

		time.sleep(EasyConfig().afterFindElementWait)
		return ret

	return wrapper


# webDriver行为的装饰器
def driverAction_dec(description):
	def decorator(func):
		@functools.wraps(func)
		def wrapper(self, *args, **kwargs):
			ret = None
			lineNo = getLineNo()  # 获取行号
			cmdParam = {
				'list': args,
				'dict': kwargs
			}
			errorMessage = ''
			result = RunResult.FAIL
			image = ''
			sTime = time.time()
			try:
				ret = func(self, *args, **kwargs)
				result = RunResult.PASS
			except Exception as e:
				errorMessage = e.args[0].split('\n')[0][:150]
			finally:
				eTime = time.time()
				if func.__name__ not in ['close', 'quit']:
					image = screenShot(self._driver, ret)
				data = getJsonData(result, func.__name__, cmdParam, errorMessage, '%.3fs' % (eTime - sTime), image, '',
								   description, lineNo, 1)
				putLog(data, self._driver)

				time.sleep(EasyConfig().afterActionWait)

			return ret

		return wrapper

	return decorator


# 元素行为截图
def elementAction_dec(description):
	def decorator(func):
		@functools.wraps(func)
		def wrapper(self, *args, **kwargs):
			ret = None
			element = self
			lineNo = getLineNo()  # 获取行号
			cmdParam = {
				'list': args,
				'dict': kwargs
			}
			result = RunResult.FAIL
			errorMessage = ''
			sTime = time.time()
			number = int(EasyConfig().repeatFindTime)  # 重复查找次数
			n = number
			while n > 0:
				try:
					ret = func(element, *args, **kwargs)
					result = RunResult.PASS
					errorMessage = '在%d次重复查找后找到元素' % (number - n) if n != number else ''
					break
				except WebDriverException:
					time.sleep(2)
					errorMessage = '由于页面改变，多次查找后未能找到元素'
					element = self.elementFunc()  # 重新查找元素
				except Exception as e:
					errorMessage = e
				n = n - 1

			eTime = time.time()
			image = screenShot(self.parent)
			data = getJsonData(result, func.__name__, cmdParam, errorMessage, '%.3fs' % (eTime - sTime), image,
							   self.elementAlias, description, lineNo, 1)
			putLog(data, self.parent)

			time.sleep(EasyConfig().afterActionWait)

			return ret

		return wrapper

	return decorator


def assert_dec(description):
	def decorator(func):
		@functools.wraps(func)
		def wrapper(self, *args, **kwargs):
			ret = None
			lineNo = getLineNo()  # 获取行号
			cmdParam = {
				'list': args,
				'dict': kwargs
			}
			errorMessage = ''
			result = RunResult.FALSE
			sTime = time.time()
			try:
				ret = func(self, *args, **kwargs)
				result = RunResult.TRUE
			except Exception as e:
				errorMessage = e.args[0].split('\n')[0][:150]
			finally:
				eTime = time.time()
				data = getJsonData(result, func.__name__, cmdParam, errorMessage, '%.3fs' % (eTime - sTime), '', '',
								   description, lineNo, 2)
				putLog(data, self._driver)

			return ret

		return wrapper

	return decorator


# 代码异常捕获
def codeException_dec(level):
	def decorator(func):
		@functools.wraps(func)
		def wrapper(driver, *args, **kwargs):
			ret = None
			lineNo = getLineNo()  # 获取行号
			try:
				ret = func(driver, *args, **kwargs)
			except ScreenShotException as e:
				data = getJsonData(RunResult.ERROR, func.__name__, '', e.message, '', '', '', e.error, lineNo, level)
				putLog(data, driver._driver)
			except Exception as e:
				data = getJsonData(RunResult.ERROR, func.__name__, '', e, '', '', '', '', lineNo, level)
				putLog(data, driver._driver)
			finally:
				return ret

		return wrapper

	return decorator


@codeException_dec('3')
def screenShot(driver, element=None):
	image = ""
	if EasyConfig().isScreenShot:
		if element and not isinstance(element, list):
			elementId = drawRect(driver, element)
			image = startScreenShot(driver)
			removeRectByJs(elementId, driver)
		elif element and isinstance(element, list):
			index = 0
			elementIdList = []
			for e in element:
				elementId = drawRect(driver, e, index)
				elementIdList.append(elementId)
				index = index + 1
			image = startScreenShot(driver)
			removeRect(elementIdList, driver)
		else:
			image = startScreenShot(driver)
	return image


def startScreenShot(driver):
	try:
		image = getImageSavePath(driver.screenShotDir)
		driver.get_screenshot_as_file(image)
		return image
	except Exception as e:
		raise ScreenShotException(e)


def drawRect(driver, e, index=0):
	elementLocation = e.location_once_scrolled_into_view
	elementSize = e.size
	num = random.uniform(100000, 999999)
	elementId = 'div_rect_%d_%d' % (num, index)
	addRectByJs(elementId, elementLocation, elementSize, driver, index)
	return elementId


def removeRect(elementIdList, driver):
	for elementId in elementIdList:
		removeRectByJs(elementId, driver)


def addElementAttr(ret, self, by, value, alias, func):
	'''
	为元素增加一些额外的属性
	:param ret:
	:param self:
	:param by: 查找元素使用的方法
	:param value:
	:param alias:
	:param func:
	:return:
	'''
	if isinstance(ret, list):
		index = 0
		for item in ret:
			elementAlias=getElementAlias(alias,item)
			item.elementAlias = '%s[%d]' % (elementAlias, index)
			item.elementParam = value
			item.elementFunc = func  # 将查找元素的方法状态都保存起来
			item.elementBy = by
			index = index + 1
	else:
		elementAlias=getElementAlias(alias,ret)
		ret.elementAlias = elementAlias  # 设置元素别名
		ret.elementParam = value  # 定位参数
		ret.elementFunc = func  # 将查找元素的方法状态都保存起来
		ret.elementBy = by


def addRectByJs(id, location, size, driver, index):
	js = '''
		var d = document.createElement('div');
    	document.body.appendChild(d);
    	var cssStr = 'z-index:9999;color:red;width:%spx;height:%spx;border:2px solid red;position:absolute;left:%spx;top:%spx;';
    	d.style.cssText = cssStr;
    	d.id = '%s';
    	d.innerHTML = '%d';
    	''' % (size['width'], size['height'], location['x'], location['y'], id, index)
	driver.execute(Command.EXECUTE_SCRIPT, {'script': js, 'args': []})


def removeRectByJs(id, driver):
	js = '''
		var d = document.getElementById('%s');
		if (d != null)
			d.parentNode.removeChild(d);
		''' % (id)
	driver.execute(Command.EXECUTE_SCRIPT, {'script': js, 'args': []})


def getElementAlias(alias, element):
	elementAlias = ''
	if alias != 'Undefined':
		elementAlias = alias
	elif alias == 'Undefined' and element != None:
		elementAlias = element.text
	return elementAlias


def getLineNo():
	'''
	获取行号
	:return:
	'''
	lineNo = 0
	try:
		frame = sys._getframe()
		for x in range(10):
			className = frame.f_code.co_name
			if className == 'runTest':
				lineNo = frame.f_lineno
				break
			else:
				frame = frame.f_back
	except Exception:
		pass
	return lineNo
