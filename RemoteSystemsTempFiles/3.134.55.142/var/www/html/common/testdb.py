#!/usr/bin/python3
from _init_ import conn
import json

print("content-type:text/html; charset=UTF-8\n")
print(9)
print(0)
curs=conn.cursor()
print(1)
sql='select * from a_member order by member_name'
print(2)
curs.execute(sql)
#print(3)
#rs=curs.fetchall()
print(4)
for row in curs:
  print(row[0]+','+row[1]+'<br>');