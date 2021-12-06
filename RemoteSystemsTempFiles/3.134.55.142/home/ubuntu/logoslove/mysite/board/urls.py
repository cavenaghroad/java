from . import views 
from django.urls import path

app_name="board"

urlpatterns = [
    path('',views.bbs_home, name='bbs_home'),
    path('list/',views.bbs_list, name="bbs_list"),
    path('newview/',views.newview,name="newview"),
    path('newpost/',views.newpost, name='newpost'),
    path('view/<int:bid>/',views.view, name='view'),
    path("removepost/<int:bid>/",views.removepost, name="removepost"),
    path('modifyview/<int:bid>/',views.modifyview, name='modifyview'),
    path('modifypost/',views.modifypost, name='modifypost'),
    path('uploadfile/',views.uploadfile, name='uploadfile'),
]
