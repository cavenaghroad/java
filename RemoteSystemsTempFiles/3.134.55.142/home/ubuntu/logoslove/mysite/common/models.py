from django.db import models

class Member(models.Model):
    mobile = models.CharField(primary_key=True, max_length=20)
    passcode = models.CharField(max_length=12)
    name = models.CharField(max_length=20)
    email = models.CharField(max_length=32, blank=True, null=True)
    gender = models.CharField(max_length=1, blank=True, null=True)
    birthday = models.CharField(max_length=8, blank=True, null=True)

    def __str__(self):
        return self.mobile+','+self.name
    
    class Meta:
        managed = False
        db_table = 'member'
