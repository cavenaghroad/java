from . import views
from django.urls import path,include
from django.contrib.auth import views as auth_views

app_name='common'

urlpatterns=[
    path('login/', views.login, name='login'),
    path('checkuser/',views.checkuser,name="checkuser"),
    path('logout/',views.logout, name='logout'),
    path('signup/',views.signup, name='signup'),
    path('newbie/',views.newbie, name='newbie'),
    path('info/',views.info,name='info'),
    path('info_update/',views.info_update,name='info_update'),
    path('passcode/',views.passcode,name='passcode'),
]