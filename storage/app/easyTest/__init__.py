#version='1.0.1'
'''
1.将模版放到单独的模版文件夹(/script/template)中
测试方案模版:xml模版.xml
参数化模版：data参数化模版.xml
测试用例模版:testCase模版.py

2.测试方案模版（xml模版.xml）中hub节点和testCase节点添加enabled属性
enabled='True'时 该条目启用；
enabled='False'时，该条目不启用；
如果hub节点和testCase节点不包含enabled属性，则该条目启用

3.加入tool界面
文件对比
目录对比
配置中心
'''