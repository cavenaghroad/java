from django.shortcuts import render
from django.http import HttpResponse, JsonResponse
from django.core import serializers
from .models import NcsConfig, NcsDrill, NcsStudent
from django.forms.models import model_to_dict
from django.views.decorators.csrf import csrf_exempt
import simplejson as json
from importlib.resources import path
import time, datetime
from lib2to3.btm_utils import rec_test

def ncs_student(request,class_id,student_name):
    rec = NcsConfig.objects.get(classcode=class_id)
    drill=NcsDrill.objects.filter(classcode=class_id).order_by('-created')
    context = { 'name':student_name,'rec' :rec,'drill':drill}
    return render(request,'ncs/ncs.html',context)

def ncs_teacher(request,class_id):
    return render(request,'ncs/teacher.html',{
        'student':NcsStudent.objects.filter(classcode=class_id),
        'class':NcsConfig.objects.get(classcode=class_id)
   });


def ctl_course(request):
    print(NcsConfig.objects.all().order_by('-period1'))
    return render(request,'ncs/ctl_course.html',{
        'rec':NcsConfig.objects.all().order_by('-period1')
    })

@csrf_exempt
def ctl_student(request):
    result=None
    try:
        if request.POST['optype']=='selectall':
            rec=NcsStudent.objects.values('rowid','name','classcode','mobile','birth','active','school','alive','address','seq','tvid').filter(classcode=request.POST['classcode']).order_by('seq')
            result={'result':0,'rec':list(rec),'msg':''}
        elif request.POST['optype']=='add':
            rec=NcsStudent(name=request.POST['name'],classcode=request.POST['classcode'],birth=request.POST['birth'],
                           seq=request.POST['seq'],mobile=request.POST['mobile'],school=request.POST['school'],
                           alive=request.POST['alive'])
            rec.save()
            result={'result':0,'rec':None,'msg':''}
        elif request.POST['optype']=='delete':
            rec=NcsStudent.objects.get(rowid=request.POST['rowid'])
            rec.delete()
            result={'result':0,'rec':[],'msg':''}
        elif request.POST['optype']=='update':
            rec=NcsStudent(rowid=request.POST['rowid'])
            rec.name=request.POST['name']
            rec.birth=request.POST['birth']
            rec.seq=request.POST['seq']
            rec.classcode=request.POST['classcode']
            rec.mobile=request.POST['mobile']
            rec.school=request.POST['school']
            rec.alive=request.POST['alive']
            rec.save()
            result={'result':0,'rec':None,'msg':''}
    except Exception as e:
        result={'result':-1,'rec':None,'msg':''}
    finally:
        return JsonResponse(result,safe=False)

@csrf_exempt
def ctl_class(request):
    result=None
    try:
        if request.POST['optype']=='selectall':
            rec=NcsConfig.objects.all().order_by('-created')
            result={'result':0,'rec':list(rec),'msg':''}
        elif request.POST['optype']=='selectone':
            rec=NcsConfig.objects.values('rowid','classcode','title','orgname','period1','period2','days','endtime','seat_cnt','alive','col_cnt').filter(rowid=request.POST['rowid'])
            result={'result':0,'rec':list(rec),'msg':''}
        elif request.POST['optype']=='add':
            rec=NcsConfig(classcode=request.POST['classcode'],title=request.POST['title'],period1=request.POST['period1'],period2=request.POST['period2'],
                          seat_cnt=request.POST['seat_cnt'],col_cnt=request.POST['col_cnt'],alive=request.POST['alive'])
            rec.save()
            result={'result':0,'rec':None,'msg':''}
        elif request.POST['optype']=='delete':
            print('rowid ['+request.POST['rowid']+']');
            rec=NcsConfig.objects.get(rowid=request.POST['rowid'])
            rec.delete()
            result={'result':0,'rec':[],'msg':''}
        elif request.POST['optype']=='update':
            rec=NcsConfig(rowid=request.POST['rowid'])
            rec.classcode=request.POST['classcode']
            rec.title=request.POST['title']
            rec.period1=request.POST['period1']
            rec.period2=request.POST['period2']
            rec.seat_cnt=request.POST['seat_cnt']
            rec.col_cnt=request.POST['col_cnt']
            rec.alive=request.POST['alive']
            rec.save()
            result={'result':0,'rec':None,'msg':''}
    except Exception as e:
        result={'result':-1,'rec':None,'msg':e.message}
    finally:
        return JsonResponse(result,safe=False)

@csrf_exempt
def ncs_refresh(request):
    drill=NcsDrill.objects.values('name').order_by('-created')
    drillDone=NcsDrill.objects.values('name').filter(classcode=request.POST['classid'],done__contains=request.POST['student']).order_by('-created')
    drillSubmit=NcsDrill.objects.values('name').filter(classcode=request.POST['classid'],submit__contains=request.POST['student']).order_by('-created')
    # context={'result':request.POST['student_name']}
    context={'list':list(drill),'done':list(drillDone),'submit':list(drillSubmit)}
    # return HttpResponse(json.dumps(context),content_type=u"application/json; charset=utf-8")
    return JsonResponse(context,safe=False)

@csrf_exempt
def setmission(request):
    retval=0
    try:
        rec=NcsDrill.objects.get(classcode=request.POST['classid'],name=request.POST['name'])
        if rec.submit.find(request.POST['student'])>-1:
            lst=[name for name in rec.submit.split(',') if name!=request.POST['student']]
            rec.submit=','.join(lst)
            rec.save()
        elif rec.done.find(request.POST['student'])>-1:
            lst=[name for name in rec.done.split(',') if name!=request.POST['student']]
            rec.done=','.join(lst)
            rec.save()
        else:
            if rec.submit!='':
                rec.submit+=','
            rec.submit+=request.POST['student']
            rec.save()
    except Exception as e:
        retval=-1
    finally:
        return JsonResponse(list(str(retval)),safe=False)

@csrf_exempt
def studentList(request):
    retval=0
    rec=NcsStudent.objects.values('name','birth','seq','mobile','school').filter(classcode=request.POST['classid'],alive='1').order_by('seq')
    print(rec)
    return JsonResponse(list(rec),safe=False)

@csrf_exempt
def DrillControl(request):
    try:
        now=time.strftime('%Y%m%d%H%M%S', time.localtime(time.time()))
        if request.POST['optype']=='add':
            q=NcsDrill(classcode=request.POST['classid'],name=request.POST['name'],created=now)
            q.save()
        elif request.POST['optype']=='delete':
            q=NcsDrill.objects.filter(classcode=request.POST['classid'],name=request.POST['name'])
            q.delete()
    except:
        result = {'result':'-1','msg':''}
    else:
        result={'result':'0','msg':''}
    print(result)
    return JsonResponse(result,safe=False)
    
@csrf_exempt
def getDrill(request):
    rec=NcsDrill.objects.values('done','submit').filter(classcode=request.POST['classid'],name=request.POST['name'])
    print(rec)
    return JsonResponse(list(rec),safe=False)

@csrf_exempt
def DrillStatus(request):
    try:
        student=request.POST['student']
        print('student [{}'.format(student))
        q=NcsDrill.objects.get(classcode=request.POST['classid'],name=request.POST['name'])
        print('done [{}]'.format(q.done))
        print('submit [{}]'.format(q.submit))
        done=q.done
        submit=q.submit
        if request.POST['optype']=='done' and student not in done:
            submit=submit.replace(student,'')
            submit=submit.replace(',,',',')
            if student not in done:
                if done!='':
                    done=done+','
                done=done+student
            msg='done'
        elif request.POST['optype']=='reset':
            submit=submit.replace(student,'')
            submit=submit.replace(',,',',')
            done=done.replace(student,'')
            done=done.replace(',,',',')
            msg='reset'    
        q.done=done
        q.submit=submit
        q.save()
    except:
        result = {'result':'-1','msg':msg}
    else:
         result={'result':'0','msg':msg}
    print(result)
    return JsonResponse(result,safe=False)

@csrf_exempt
def drillList(request):
    rec=NcsDrill.objects.values('name').filter(classcode=request.POST['classid']).order_by('-created')
    return JsonResponse(list(rec),safe=False)