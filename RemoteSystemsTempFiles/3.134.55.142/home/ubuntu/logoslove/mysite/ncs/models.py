# This is an auto-generated Django model module.
# You'll have to do the following manually to clean this up:
#   * Rearrange models' order
#   * Make sure each model has one field with primary_key=True
#   * Make sure each ForeignKey and OneToOneField has `on_delete` set to the desired behavior
#   * Remove `managed = False` lines if you wish to allow Django to create, modify, and delete the table
# Feel free to rename the models, but don't rename db_table values or field names.
from django.db import models


class NcsConfig(models.Model):
    rowid = models.AutoField(primary_key=True)
    classcode = models.CharField(max_length=12)
    title = models.CharField(max_length=64, blank=True, null=True)
    orgname = models.CharField(max_length=64, blank=True, null=True)
    period1 = models.CharField(max_length=10, blank=True, null=True)
    days = models.DecimalField(max_digits=3, decimal_places=0, blank=True, null=True)
    period2 = models.CharField(max_length=10, blank=True, null=True)
    endtime = models.CharField(max_length=256, blank=True, null=True)
    seat_cnt = models.IntegerField(blank=True, null=True)
    alive = models.CharField(max_length=1, blank=True, null=True)
    col_cnt = models.IntegerField(blank=True, null=True)

    def __str__(self):
        return self.title
    
    class Meta:
        managed = False
        db_table = 'ncs_config'


class NcsDrill(models.Model):
    rowid = models.AutoField(primary_key=True)
    classcode = models.CharField(max_length=12, blank=True, null=True)
    name = models.CharField(max_length=32, db_collation='utf8_general_ci')
    done = models.TextField(db_collation='utf8_general_ci', blank=True, null=True)
    submit = models.TextField(db_collation='utf8_general_ci', blank=True, null=True)
    created = models.CharField(max_length=14, db_collation='utf8_general_ci', blank=True, null=True)
    
    def __str__(self):
        return self.name
     
    class Meta:
        managed = False
        db_table = 'ncs_drill'


class NcsStudent(models.Model):
    rowid = models.AutoField(primary_key=True)
    name = models.CharField(max_length=32)
    classcode = models.CharField(max_length=12, blank=True, null=True)
    passcode = models.CharField(max_length=12, blank=True, null=True)
    birth = models.CharField(max_length=8, blank=True, null=True)
    seq = models.PositiveIntegerField(blank=True, null=True)
    mobile = models.CharField(max_length=20, blank=True, null=True)
    active = models.CharField(max_length=10, blank=True, null=True)
    presented = models.CharField(max_length=8, blank=True, null=True)
    tvid = models.CharField(max_length=12, blank=True, null=True)
    school = models.CharField(max_length=32, blank=True, null=True)
    absence = models.CharField(max_length=1, blank=True, null=True)
    ipaddr = models.CharField(max_length=16, blank=True, null=True)
    address = models.TextField(blank=True, null=True)
    msgtime = models.CharField(max_length=14, blank=True, null=True)
    msg2student = models.TextField(blank=True, null=True)
    alive = models.CharField(max_length=1, blank=True, null=True)
    
    def __str__(self):
        return self.name
     
    class Meta:
        managed = False
        db_table = 'ncs_student'

