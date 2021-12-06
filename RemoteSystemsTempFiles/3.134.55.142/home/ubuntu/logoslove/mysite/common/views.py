# from django.contrib.auth import authenticate, login
from django.shortcuts import render, redirect
from django.http import HttpResponse, JsonResponse
from .models import Member
# from common.forms import UserForm

def login(request):
#     return HttpResponse("Hi world")
  return render(request,'common/login.html')

def signup(request):
    return render(request,'common/signup.html')

def checkuser(request):
    print(request.POST['username'],request.POST['password'])
    rec=Member.objects.get(mobile=request.POST['username'],passcode=request.POST['password'])
    if rec is not None:
        request.session['mobile']=request.POST['username']
        request.session['nick']=rec.name
        return redirect(request.session['curpage'])
    else:
        return redirect('/common/login/')


#     if n>0:
#         return 'redirect:bbs'
#     else:
#         return 'redirect:login'
    
def logout(request):
    del request.session['mobile']
    del request.session['nick']
    return redirect(request.session['curpage'])

def newbie(request):
#     q=Member(mobile=request.POST['mobile'],passcode=request.POST['password1'],name=request.POST['username'],email=request.POST['email'])
#     q.save()
    return redirect(request.session['curpage'])

def info(request):
    rec=Member.objects.get(mobile=request.session['mobile'])
    male=''
    female=''
    if rec.gender=='m':
        male='checked'
    else:
        female='checked'
    return render(request,'common/info.html',{
             'rec' :rec,
             'male':male,'female':female
    })

def info_update(request):
    rec=Member.objects.get(mobile=request.session['mobile'])
    if rec.passcode!=request.POST['passcode']:
        msg='비밀번호가 틀렸습니다. (Invalid Password)'
        return info(request,msg)
    rec.name=request.POST['name']
    rec.email=request.POST['email']
    rec.birthday=request.POST['birthday']
    
    if request.POST['gender']=='male':
        rec.gender='m'
    else :
        rec.gender='f'
    rec.save()
    return redirect('/bbs/list/')
    
def passcode(request):
    return render(request,'common/passcode.html',{'mobile':request.session['mobile']}) 
       