# coding=utf-8

class ScreenShotException(Exception):
	def __init__(self,msg=None):
		super(ScreenShotException, self).__init__()
		self.error='截图发生异常'
		self.message=msg.msg.split('\n')[0][:150] if msg!=None else ''


class ParamNumberException(Exception):
	def __init__(self,msg=None):
		super(ParamNumberException, self).__init__()
		self.error='参数化文件中的参数个数不足'