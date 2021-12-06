from django.contrib import admin
from .models import Board,BoardImage,BoardType,BoardConfig

admin.site.register(Board)
admin.site.register(BoardImage)
admin.site.register(BoardType)
admin.site.register(BoardConfig) 