#Project Specific Migration EM for the  ProjBiomarkerMigrator Repository
This EM will migrate and restructure the data
* Collapse various fields into single fields in multiple instance
* Rename fields
* Recode fields

# Supported Field Modifers

|modifer <br> (enter into custom)| Description | custom_1 <br> {target field}| custom_2 <br> {json format}|
|---|---|---|---|
|splitName|split a single field into two fields. ex: name -> first_name + last_name|first_name+last_name|
|textToCheckbox|convert freetext to coded values in checkbox. The + sign for the coded value with add to both checkboxes|ethnicity|{"Asian":"2","Asian/ Indian":"2","Asian/Caucasian":"0+2","Caucasian":"0","Caucasian/ Asian":"0+2","Caucasian/ Hispanic":"0+3","Caucasian/Asian":"0+2","Caucasian/Hispanic":"0+3","Caucasion":"0","Caucasion/ Hispanic":"0+3","Causcasian":"0","Hispanic":"3","Hispanic White":"0+3","Hispanic/ White":"0+3","Iranian":"0","Mixed Caucasian Polynesian":"0+5","Mixed Caucasian/African American":"0","mixed White & Asian":"0+2","Native American":"4","Native American/ European":"0+4","Non Hispanic White":"0","Non- Hispanic White":"0","Non- Hispanic White, Asian ":"0+2","Non-Hispanic White":"0","Non-Hispanic White/ Asian":"0+2","Not Hispanic":"0","Not Hispanic/ White":"0","Unknown":"99","white":"0","White & Hispanic":"0+3","White/Caucasian":"0","White/Hispanic":"0+3"}
|checkboxToCheckbox|recode the values in a checkbox|race|{"0":"0", "1":"2", "2":"3", "4":"4", "5":"1", "6":"99", "7":"98"}|
|recodeRadio|recode codes in radio|gender | {"0":"1","1":"2"} |
|checkboxToRadio|convert checkbox to radio codes| ethnicity | {"3": "1", "7":"0"} |
|radioToCheckbox|convert radio to checkbox|visit_sample|{"1":"2","2":"4","3":"3","4":"7"}|
|addToField|add contents of twwo fields into one|exercise_comments|exercise_comments+describe_intense_exercise|---|
|fixDate|||---|---|

If you have a field that needs to map to two fields:
for example, you need to recode your ethnicity field into race and ethnicity, you can use the second custom fields:

| custom | custom_1 | custom_2 | custom2 | custom2_1 | custom2_2 |
|---|---|---|---|---|---|
|checkboxToCheckbox| race | {"0":"0", "1":"4", "2":"2", "4":"3", "5":"1", "6":"0", "8":"99"} | checkboxToRadio | ethnicity | {"3": "1", "7":"0"} |
