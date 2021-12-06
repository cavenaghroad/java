from django.contrib import admin
from .models import NcsConfig,NcsDrill,NcsStudent

admin.site.register(NcsDrill)
admin.site.register(NcsConfig)
admin.site.register(NcsStudent)
