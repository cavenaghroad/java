from django.db import models

class Board(models.Model):
    rowid = models.AutoField(primary_key=True)
    type = models.CharField(max_length=12)
    title = models.CharField(max_length=128, blank=True, null=True)
    content = models.TextField(blank=True, null=True)
    author = models.CharField(max_length=20, blank=True, null=True)
    created = models.CharField(max_length=20, blank=True, null=True)
    updated = models.CharField(max_length=20, blank=True, null=True)
    hit = models.DecimalField(max_digits=10, decimal_places=0, blank=True, null=True)
    like_user = models.TextField(blank=True, null=True)
    hate_user = models.TextField(blank=True, null=True)
    par_rowid = models.CharField(max_length=5, blank=True, null=True)

    def __str__(self):
        return self.type+" "+self.title
     
    class Meta:
        managed = False
        db_table = 'board'


class BoardImage(models.Model):
    par_rowid = models.DecimalField(max_digits=10, decimal_places=0, blank=True, null=True)
    rowid = models.DecimalField(primary_key=True, max_digits=10, decimal_places=0)
    filename = models.FileField()
    
    class Meta:
        managed = False
        db_table = 'board_image'

class BoardType(models.Model):
    code = models.CharField(primary_key=True, max_length=12)
    name = models.CharField(max_length=20)
    updated = models.CharField(max_length=19)

    def __str__(self):
        return self.code+','+self.name
    
    class Meta:
        managed = False
        db_table = 'board_type'
        


class BoardConfig(models.Model):
    board_id = models.AutoField(primary_key=True)
    row_cnt = models.PositiveIntegerField(blank=True, null=True)
    col_cnt = models.PositiveIntegerField(blank=True, null=True)
    list = models.CharField(max_length=32, blank=True, null=True)

    def __str__(self):
        return str(self.board_id)
    
    class Meta:
        managed = False
        db_table = 'board_config'
        