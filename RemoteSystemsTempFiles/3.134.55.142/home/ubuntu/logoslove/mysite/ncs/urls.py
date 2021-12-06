from . import views 
from django.urls import path

app_name='ncs'
urlpatterns = [
	path('xaexal/<class_id>/',views.ncs_teacher,name='ncs_teacher'), 
	path('<class_id>/<student_name>/',views.ncs_student,name="ncs_student"),
	path('ctl_class',views.ctl_class,name='ctl_class'),
	path('ctl_student/',views.ctl_student,name='ctl_student'),
	path('ctl_course/',views.ctl_course,name='ctl_course'),
	path('refresh/',views.ncs_refresh,name="refresh"),
	path('setmission/',views.setmission,name='setmission'),
	path('studentList',views.studentList,name='studentList'),
	path('DrillControl',views.DrillControl,name="DrillControl"),
	path('DrillStatus',views.DrillStatus,name='DrillStatus'),
	path('getDrill',views.getDrill,name='getDrill'),
	path('drillList',views.drillList,name='drillList'),
]