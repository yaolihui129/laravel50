# -*- coding: utf-8 -*-

"""
Module implementing ConfigCenter.
"""
import datetime
import os
import shutil
from PyQt5 import QtCore, QtWidgets
from PyQt5.QtCore import pyqtSlot
from PyQt5.QtWidgets import QDialog, QFileDialog, QMessageBox, QListWidgetItem, QTableWidget, QWidget
from SRC.common import fileHelper
from SRC.common.config import EasyConfig
from SRC.common.xmlHelper import read_xml, find_nodes, create_node, add_child_node
from ui.common.configFactory import ConfigFactory
from ui.project.ui_designer.Ui_configCenter import Ui_Dialog


class ConfigCenter(QDialog, Ui_Dialog):
	"""
	Class documentation goes here.
	"""

	def __init__(self, parent=None):
		super(ConfigCenter, self).__init__(parent)
		self.setupUi(self)

		self._translate = QtCore.QCoreApplication.translate
		self.browsersDict = {'ff': [self.checkBox_ff, self.lineEdit_ffremote],
							 'chrome': [self.checkBox_ch, self.lineEdit_chremote],
							 'ie': [self.checkBox_ie, self.lineEdit_ieremote]}
		self.lineEdit_project.setText(self.getRootDir('/ui/project/ui_designer'))  # 设置项目目录

		self.initTableWidget()
		self.initTestPlan()

	def initTableWidget(self):
		self.tableWidget_testCase.resizeColumnsToContents()
		self.tableWidget_testCase.setColumnWidth(2, self.tableWidget_testCase.columnWidth(2) * 1.5)
		self.tableWidget_testCase.setColumnWidth(3, self.tableWidget_testCase.columnWidth(2) * 2.5)
		self.tableWidget_testCase.setColumnWidth(4, self.tableWidget_testCase.columnWidth(2) * 1.15)

	def initTestPlan(self):
		try:
			# 创建配置中心工厂对象
			self.configFactory = ConfigFactory(self.lineEdit_project.text())
			self.configFactory.loadAllTestPlans()  # 加载测试方案
			# 初始化测试方案列表
			self.loadListWidgetData()

			self.setEnableForBrowserRemote()  # 设置浏览器远程主机地址是否可以修改
		except Exception as e:
			QMessageBox.warning(self, '错误', '加载项目目录失败，请重新打开项目目录')
			self.listWidget_testplan.clear()

	def setEnableForBrowserRemote(self):
		for browserControl in self.browsersDict.values():
			browserControl[1].setEnabled(False)
			browserControl[0].setEnabled(False)

	def loadListWidgetData(self):
		'''
		向列表中加载测试方案数据
		:return:
		'''
		self.listWidget_testplan.clear()
		for index, testPlan in enumerate(self.configFactory.testPlanList):
			self.listWidget_testplan.addItem(QtWidgets.QListWidgetItem())
			item = self.listWidget_testplan.item(index)
			item.setText(self._translate("Dialog", '%s.%s' % (index + 1, testPlan['name'])))
			item.testPlan = testPlan  # 为每一项添加测试方案的对象

	@pyqtSlot()
	def on_pushButton_open1_clicked(self):
		fileDir = QFileDialog.getExistingDirectory(self, '打开文件夹', self.projectRootDir)
		if not fileDir:
			return
		if not os.path.isdir(fileDir):
			return

		self.lineEdit_project.setText(fileDir)
		self.initTestPlan()

	@pyqtSlot()
	def on_pushButton_createtestplan_clicked(self):
		xmlDir = self.configFactory.xmlDir
		path, _ = QFileDialog.getSaveFileName(self, '新建测试方案', xmlDir, '测试方案 (*.xml);;所有文件 (*)')
		if xmlDir not in path:
			QMessageBox.warning(self, '错误', '请保存在测试方案目录下：' + xmlDir)
			return

		templateFile = self.configFactory.templateList[0]  # 模版文件
		if not os.path.exists(templateFile):
			QMessageBox.warning(self, '错误', '找不到模版文件：' + templateFile)
			return

		if not fileHelper.copyFile(templateFile, path, True):
			QMessageBox.warning(self, '错误', '创建失败')
			return

		self.configFactory.addTestPlan(path, os.path.basename(path))
		self.loadListWidgetData()  # 重新加载测试方案列表

	@pyqtSlot()
	def on_pushButton_removetestplan_clicked(self):
		try:
			selectItems = self.listWidget_testplan.selectedItems()
			if not selectItems:
				QMessageBox.warning(self, '错误', '请先选择一个测试方案！')
				return

			button = QMessageBox.question(self, '确定删除', '确定要删除吗？（只删除该测试方案，不会删除用例脚本及参数化文件）',
										  QMessageBox.Yes | QMessageBox.No)
			if button == QMessageBox.No:
				return

			item = selectItems[0]

			self.configFactory.removeTestPlan(item.testPlan)  # 移除测试方案
			fileHelper.delFile(item.testPlan['path'])  # 物理上删除测试方案
			self.loadListWidgetData()  # 从新加载列表
		except Exception as e:
			print(e)

	@pyqtSlot(int, int)
	def on_tableWidget_testCase_cellDoubleClicked(self, row, column):
		pass

	@pyqtSlot()
	def on_pushButton_createtestcase_clicked(self):
		pass

	@pyqtSlot()
	def on_pushButton_up_clicked(self):
		pass

	@pyqtSlot()
	def on_pushButton_down_clicked(self):
		pass

	@pyqtSlot()
	def on_pushButton_removetestcase_clicked(self):
		pass

	@pyqtSlot()
	def on_pushButton_save_clicked(self):
		# 浏览器设置
		result = self.groupBox_testcase.testPlan.writeTestPlanToXML()
		if result:
			QMessageBox.information(self, '提示', '保存成功')
		else:
			QMessageBox.warning(self, '错误', '保存失败！')

	@pyqtSlot()
	def on_pushButton_start_clicked(self):
		pass

	@pyqtSlot()
	def on_pushButton_fresh_clicked(self):
		pass

	@pyqtSlot(QListWidgetItem)
	def on_listWidget_testplan_itemDoubleClicked(self, item):
		testPlan = item.testPlan['objFunc']()
		self.groupBox_testcase.setTitle(self._translate("Dialog", "2.测试用例套件>>>%s" % (item.testPlan['name'])))
		self.groupBox_testcase.testPlan = testPlan  # 测试方案对象

		self.setBrowserView(testPlan)
		self.setTestCaseView(testPlan)

	@pyqtSlot(bool)
	def on_checkBox_ff_clicked(self, checked):
		self.checkBox_ff.hub['enabled'] = 'True' if checked else 'False'

	@pyqtSlot(bool)
	def on_checkBox_ch_clicked(self, checked):
		self.checkBox_ch.hub['enabled'] = 'True' if checked else 'False'

	@pyqtSlot(bool)
	def on_checkBox_ie_clicked(self, checked):
		self.checkBox_ie.hub['enabled'] = 'True' if checked else 'False'

	@pyqtSlot()
	def on_lineEdit_ffremote_editingFinished(self):
		self.checkBox_ff.hub['remoteUrl'] = self.lineEdit_ffremote.text().strip()

	@pyqtSlot()
	def on_lineEdit_chremote_editingFinished(self):
		self.checkBox_ch.hub['remoteUrl'] = self.lineEdit_chremote.text().strip()

	@pyqtSlot()
	def on_lineEdit_ieremote_editingFinished(self):
		self.checkBox_ie.hub['remoteUrl'] = self.lineEdit_ieremote.text().strip()

	def setBrowserView(self, testPlan):
		for name, controls in self.browsersDict.items():
			result = False if EasyConfig().runType == 'Browser' else True
			controls[1].setEnabled(result)
			controls[0].setEnabled(True)
			self.setBrowserValue(controls)
			for hub in testPlan.hub:
				browserName = hub['browser'].strip().lower()
				if browserName == name:
					self.setBrowserValue(controls, hub['enabled'], hub['remoteUrl'], hub)
					break
			if not controls[0].hub:
				model = testPlan.addHub(name, 'False')
				self.setBrowserValue(controls, model['enabled'], model['remoteUrl'], model)

	def setTestCaseView(self, testPlan):
		try:
			self.tableWidget_testCase.clearContents()
			rowCount = len(testPlan.testCaseList)
			self.tableWidget_testCase.setRowCount(rowCount)
			for index, model in enumerate(testPlan.testCaseList):
				self.bingDataToTable(index, model)  # 绑定数据到表格中
		except Exception as e:
			print(e)
			QMessageBox.warning(self, '错误', '配置文件有错误！')

	def bingDataToTable(self, index, model):
		number = str(index + 1)  # 序号
		enabled = model['enabled']  # 启用

		newCheckBox = QtWidgets.QCheckBox()
		newCheckBox.setChecked(enabled)

		name = model['name']  # 用例名称
		# 用例路径
		relativePath = model['path'].split(self.lineEdit_project.text())[1]
		relativePath = relativePath[:-3] if relativePath[-3:] == '.py' else relativePath
		# 参数化路径
		paramRelativePath = model['paramPath'].split(EasyConfig().dataDir)[1] if model[
																					 'paramPath'].strip() != '' else ''

		dataList = [number, newCheckBox, name, relativePath, paramRelativePath]
		for i, content in enumerate(dataList):
			if isinstance(content, str):
				item = QtWidgets.QTableWidgetItem(content)
				self.tableWidget_testCase.setItem(index, i, item)
			elif isinstance(content, QWidget):
				self.tableWidget_testCase.setCellWidget(index, i, content)

		self.tableWidget_testCase.item(index, 0).testCase = model  # 绑定测试用例

	def setBrowserValue(self, controls, isChecked=False, text='', model=None):
		controls[0].setChecked(isChecked)
		controls[0].hub = model
		controls[1].setText(text)

	def getRootDir(self, relativePath):
		'''
		项目根目录
		:return:
		'''
		base_dir = os.path.dirname(os.path.abspath(__file__))
		base_dir = str(base_dir)
		base_dir = base_dir.replace('\\', '/')
		return base_dir.split(relativePath)[0]


if __name__ == "__main__":
	import sys
	from PyQt5.QtWidgets import QApplication

	app = QApplication(sys.argv)
	dlg = ConfigCenter()
	dlg.show()
	sys.exit(app.exec_())
