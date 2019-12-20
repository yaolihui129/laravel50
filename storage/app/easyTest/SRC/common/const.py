# coding=utf-8

class Agent():
	'''
	浏览器类型
	'''
	REMOTE = "Remote"
	IE="Internet Explorer"
	CHROME="Google Chrome"
	FIREFOX="Mozilla Firefox"

class RunResult():
	'''
	运行结果
	'''
	PASS="PASS"
	ERROR="ERROR"
	FAIL="FAIL"
	TRUE="TRUE"
	FALSE="FALSE"

class RunType():
	'''
	运行类型
	'''
	REMOTE="REMOTE" #远程服务器启动
	BROWSER="BROWSER"#本地浏览器启动