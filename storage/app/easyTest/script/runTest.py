# coding=utf-8
import os
import sys
sys.path.append(os.path.dirname(os.path.abspath(__file__)).replace('\\','/').split('/script')[0])

from SRC.main import Main

'''
####################################################
启动方式：
1，不传参启动：
在Main()对象中填入xml文件名，该文件放在script/xml/目录下即可
命令行:python runTest
2，传参启动：
启动命令后跟参数（xml文件完整路径）
命令行:python runTest c:\demo.xml

运行位置：
1，本机运行：
SRC/config.xml文件中runType=Browser
2，联机运行：
SRC/config.xml文件中runType=Remote

启动代码如下：
Main("这里填入xml路径，外部参数启动忽略").run()
#####################################################
'''

Main('demo.xml').run()
