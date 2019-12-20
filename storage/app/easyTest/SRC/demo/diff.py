import filecmp
from filecmp import dircmp


dir1=r'D:\demo\test1'
dir2=r'D:\demo\test2'

# result=filecmp.cmpfiles(dir1,dir2,['a.txt'])
# print(result)

dc=dircmp(dir1,dir2)
dc.report()



