from django.shortcuts import render
from django.http import HttpResponse
from .models import Board, BoardImage, BoardType, BoardConfig
from django.core.paginator import Paginator
import time, datetime
from django.views.decorators.csrf import csrf_exempt

btype=None

def bbs_home(request):
    return render(request,'board/bbs_home.html',{
        'rec':BoardConfig.objects.get(board_id=1),
        'bbs0':Board.objects.filter(type='공지')[:10],
        'bbs1':Board.objects.filter(type='자유게시판')[:10],
        'bbs2':Board.objects.filter(type='질문과답변')[:10],
        'bbs3':Board.objects.filter(type='파이썬')[:10],
        'nick':request.session.get('nick',None)
    });
    
    
def bbs_list(request):
    print("bbs_list started")
    request.session['curpage']=request.path
    bbs=Board.objects.order_by('-created')
    page=request.GET.get('p',1)
    print('page [',page,']')
    paginator=Paginator(bbs,10)
    onepage=paginator.get_page(page)
    return render(request,'board/bbs_list.html',{
        "bbs":onepage,
        'nick':request.session.get('nick',None)
    })
    
def getBtype():
    return BoardType.objects.values('name')

def newview(request):
    return render(request, 'board/new_view.html',{
        'btype':getBtype(),
        'nick':request.session.get('nick',None)
    })
 
def newpost(request):
    now=time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time()))
    q=Board(type=request.POST['seltype'],title=request.POST['title'],content=request.POST['content'],author=request.session['mobile'],created=now,updated=now,hit=0)
    q.save()
    return bbs_list(request)
     

def view(request,bid):
    print('view [{}]'.format(bid))
    rec=Board.objects.get(rowid=bid)
    rec.hit=rec.hit+1
    rec.save()
    return render(request,"board/view.html",{
        'rec':rec, 
        'nick':request.session.get('nick',None)
    })
     
def modifyview(request,bid):
    print('modify_view just before return')
    return render(request,'board/modify_view.html',{
        'rec' :Board.objects.get(rowid=bid),
        'btype':getBtype(),
        'nick':request.session.get('nick',None)
   })
 
def modifypost(request):
    bid=request.POST['rowid']
    rec=Board.objects.get(rowid=bid)
    rec.type=request.POST['seltype']
    rec.title=request.POST['title']
    str=request.POST['content']
    
    if "\n" in str:
        str=str.replace("\n","<br>")
    rec.content=str
    rec.updated=time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time()))
    rec.save()
    return bbs_list(request)

def removepost(request,bid):
    print('removepost [{}]'.format(bid))
    if request.session.get('nick')!=None:
        rec=Board(rowid=bid)
        rec.delete()
        return bbs_list(request)
    
@csrf_exempt
def uploadfile(request):
    print('uploadfile started')
    print(request.FILES['userfile'])
    for userfiledata in request.FILES.getlist('userfile'):
        f = userfiledata
        print (f)
    return bbs_list(request)
    
    
    
    