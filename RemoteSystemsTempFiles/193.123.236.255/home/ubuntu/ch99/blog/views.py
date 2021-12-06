# from django.shortcuts import render

# Create your views here.
from django.views.generic import ListView,DetailView, TemplateView, FormView
from django.views.generic.dates import ArchiveIndexView, YearArchiveView, MonthArchiveView
from django.views.generic.dates import DayArchiveView, TodayArchiveView
from django.conf import settings
from blog.models import Post
from blog.forms import PostSearchForm
from django.db.models import Q
from django.shortcuts import render

class PostLV(ListView):
    model=Post
    template_name='blog/post_all.html'
    context_object_name='posts'
    pagenate_by=2
    
class PostDV(DetailView):
    model=Post
    
class PostAV(ArchiveIndexView):
    model=Post
    date_field='modify_dt'    
    
class PostYAV(YearArchiveView):
    model=Post
    date_field='modify_dt'
    make_object_list=True
    
class PostMAV(MonthArchiveView):
    model=Post
    date_field='modify_dt'
    
class PostDAV(DayArchiveView):
    model=Post
    date_field='modify_dt'
    
class PostTAV(TodayArchiveView):
    model=Post
    date_field='modify_dt'
    
class TagCloudTV(TemplateView):
    template_name='taggit/taggit_cloud.html'
    
class TaggedObjectLV(ListView):
    template_name='taggit/taggit_post_list.html'
    model=Post
    
    def get_queryset(self):
        return Post.objects.filter(tags_name=self.kwargs.get('tag'))
    
    def get_context_data(selfself,**kwargs):
        context=super().get_context(**kwargs)
        context['tagname']=self.kwargs['tag']
        return context
    
class SearchFormView(FormView):
    form_class=PostSearchForm
    template_name='blog/post_search.html' 
    
    def form_valid(selfself,form):
        searchWord=form.cleaned_data['search_word']
        post_list=Post.objects.filter(Q(title_icontains=searchWord)|Q(description__icontains=searchWord)|Q(content__icontaints=searchWord)).distinct()
        context={}
        context['form']=form
        context['search_term']=searchWord
        context['object_list']=post_list
        return render(self.request,self.template_name,context)