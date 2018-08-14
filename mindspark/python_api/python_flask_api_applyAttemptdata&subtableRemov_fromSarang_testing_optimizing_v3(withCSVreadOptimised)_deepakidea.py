# -*- coding: utf-8 -*-
"""
Created on Sat Feb 03 16:39:57 2018

@author: SB00478824
"""

# -*- coding: utf-8 -*-
"""
Created on Thu Jan 11 15:35:08 2018

@author: Sarang Bhat 
"""
from flask import Flask, render_template, request
from flask import jsonify
import pandas as pd
import ast
from memory_profiler import profile
import csv
#import time
app = Flask(__name__)

dict_sub_table_grp={}

def get_sub_table_4all(subset_list,ttcode): 
    total_subset_list = list(subset_list)
    # optimized reading csv
    with open(r'C:\Users\SB00478824\Desktop\December\AWS Lambda and DynamoDB\KST Docs\kst_KS_Distribution1.csv','rU') as csvFile:
        reader = csv.reader(csvFile)
        field_names_list = reader.next()
    del field_names_list[0]
    del field_names_list[-1]
    int8_cols = {item: 'int8' for item in field_names_list }     
    field_names_list.append("freq")         
    master_table=pd.read_csv(r'C:\Users\SB00478824\Desktop\December\AWS Lambda and DynamoDB\KST Docs\kst_KS_Distribution1.csv', dtype=int8_cols, usecols=field_names_list)

    ###create sub_table with Teacher cluster inputs
    sub_table_grp4all=pd.DataFrame()        #initialise sub_table each time Teacher create sub_table
    sub_table_grp4all[total_subset_list]=pd.read_csv(r'C:\Users\SB00478824\Desktop\December\AWS Lambda and DynamoDB\KST Docs\kst_KS_Distribution1.csv', dtype=int8_cols, usecols=field_names_list).loc[:,total_subset_list]  #create sub_table from mastertable respective to subset list
    sub_table_grp4all['freq']=master_table['freq']    #add freq column
    sub_table_grp4all=sub_table_grp4all.groupby(total_subset_list,as_index=False).sum().apply(pd.to_numeric,downcast='signed')  #normalise the table by removing repetitive rows and adding freq  for repetitive rows
    #sub_table_grp=sub_table_grp.apply(pd.to_numeric,downcast='signed') #optimized
    del master_table
    try:
        sub_table_grp4all.to_csv(ttcode+".csv")
        dict_sub_table_grp['orig_table']=sub_table_grp4all
        return 1
    except:
        return 0
#@profile
def adaptive_logic(qcode_list,sub_table_grp):
    temp_list=[]
    for each in qcode_list:        # loop to caclulate sum of freq based on where qcode values are 1. 
        temp_list.append(sub_table_grp['freq'][sub_table_grp[each]==1].sum())
    
    temp_list=[abs(x-0.5) for x in temp_list]         # find absolute difference 
    next_question=qcode_list[temp_list.index(min(temp_list))]       #find min value out of list of abs differences #and respective getting qcode 
    return next_question
#@profile
def get_first_question(total_subset_list,sub_table_grp):
    next_question=adaptive_logic(total_subset_list,sub_table_grp)
    prev_question= ''
    return next_question,prev_question
#@profile
def get_hist_next_question(dict_sub_table_grp,attemptData,key_name):
    sub_table_grp = dict_sub_table_grp[key_name]
    
    prev_questions_dict={}        
    prev_questions_dict=attemptData
    qcode_to_remv= prev_questions_dict.keys()[0:-1]             # get all qcodes except the latest one in a list.
    
    prev_question,resp=attemptData.keys()[-1],attemptData[attemptData.keys()[-1]] #get last question qcode and its response
    del prev_questions_dict[prev_question]
    for each in prev_questions_dict:
        response=prev_questions_dict[each]
        if response==1:
            sub_table_grp=sub_table_grp[sub_table_grp[each]==1]
        elif response==0 or 2:
            sub_table_grp=sub_table_grp[sub_table_grp[each]==0]
        
    print 'Last Question asked',prev_question
    for each in qcode_to_remv:
        try:
            del sub_table_grp[each] # remove attempted qcode from sub_table_grp
        except:
            print 'record not present'

    colmn_list = list(sub_table_grp.columns[:-1].values)   # get list of columns of updated sub_table
    sub_table_grp=sub_table_grp.groupby(colmn_list,as_index=False).sum()   #normalise the table by removing repetitive rows and adding freq  for repetitive rows
    dict_sub_table_grp[key_name]=sub_table_grp
    next_question,dict_sub_table_grp = get_next_question(dict_sub_table_grp,prev_question,resp,key_name)
    return next_question,dict_sub_table_grp,prev_question
#@profile
def get_next_question(dict_sub_table_grp,prev_question,res,key_name):
    global prev_question_dict
    prev_question_dict={}
    sub_table_grp=dict_sub_table_grp[key_name]
    if res==1:
        try:
            sub_table_grp=sub_table_grp[sub_table_grp[prev_question]==1]  #reduce the rows by keeping only those where next_question==1
        except :
            print 'Prev_question not present in sub_table_grp'
        sum_freq= sub_table_grp['freq'].sum()
        prev_question_dict[prev_question]=res
#    elif res==0 or 2:
    else:
        try:
            sub_table_grp=sub_table_grp[sub_table_grp[prev_question]==0] #reduce the rows by keeping only those where next_question==0
        except:
            print 'Prev_question not present in sub_table_grp'
        sum_freq= sub_table_grp['freq'].sum()
        prev_question_dict[prev_question]=res
    #check stopping criteria
    if len(sub_table_grp)==1:
        #stop_flag=1
        print 'no more questions'
        sub_table_grp = sub_table_grp.drop(labels='freq', axis=1)
        sub_table_grp = sub_table_grp.drop(labels= prev_question, axis=1)
        sub_table_grp.insert(loc=0,column='done',value=1)             #add done and value=1 to dataframe
        next_question=sub_table_grp.to_json(orient='records')
        dict_sub_table_grp[key_name]=sub_table_grp
        return next_question,dict_sub_table_grp
    else: 
        print 'More questions to ask'
        #stop_flag=0
        normalized_freq=sub_table_grp['freq']/sum_freq  #normalize freq here
        sub_table_grp.loc[:,'freq']=normalized_freq   #assign normalized freq to sub_table
        sub_table_grp=sub_table_grp.reset_index(drop=True)    #reindexing
        try:
            del sub_table_grp[prev_question] # remove attempted qcode from sub_table_grp
        except:
            print 'qcode not present'

        qcodes = sub_table_grp.columns[:-1].values
        next_question=adaptive_logic(qcodes,sub_table_grp)
        print 'Next Question',next_question
        dict_sub_table_grp[key_name]=sub_table_grp
        return next_question,dict_sub_table_grp

def get_question(user_id,prev_question,resp,flag,attemptData,ttcode):
    global dict_sub_table_grp
    key_name= user_id
    if flag == 0:
        try:
            sub_table_grp=dict_sub_table_grp['orig_table']
            #sub_table_grp=pd.read_csv(ttcode+".csv",index_col=0).apply(pd.to_numeric,downcast='signed')  # sub_table will be read from local csv file after created and saved by function sub_table_4all()
            #sub_table_grp=sub_table_grp.apply(pd.to_numeric,downcast='signed') #optimized
        except IOError:
            print 'No sub_table.csv available'
            return -2
        dict_sub_table_grp[key_name]=sub_table_grp
        
        total_subset_list=sub_table_grp.columns[:-1].values
        
        if attemptData == 0:
            print 'No hist. going to get 1st question'
            next_question,prev_question = get_first_question(total_subset_list,dict_sub_table_grp[key_name])
        else:
            print 'history available. going for next question based on history data'
            next_question,dict_sub_table_grp,prev_question = get_hist_next_question(dict_sub_table_grp,attemptData,key_name)

    else:    
        print 'going for next question'
        next_question,dict_sub_table_grp = get_next_question(dict_sub_table_grp,prev_question,resp,key_name)

    return next_question

@app.route('/', methods=['GET', 'POST'])
def index():
    if request.method == 'POST':
        userid = int(request.form.get('userID'))
        prev_question = str(request.form.get('prev_question'))
        answer_response = int(request.form.get('answer_response'))
        try:
            attemptData = int(request.form.get('attemptData'))
        except:
            attemptData = ast.literal_eval(request.form.get('attemptData'))

        ttcode = request.form.get('ttcode')
        flag = int(request.form.get('attempt_flag'))
        #start=time.time()
        next_question = get_question(userid,prev_question,answer_response,flag,attemptData,ttcode) 
        #print time.time()-start
        return jsonify(next_question)
    else:
        return 'Error! Parameters not passed properly'

@app.route('/subsetList/', methods=['GET', 'POST'])
def subTable_API():
    if request.method == 'POST':
        total_subset_list = request.form.get('subset_list')
        ttcode = request.form.get('ttcode')
        subset_list = ast.literal_eval(total_subset_list)        
        createCSV = get_sub_table_4all(subset_list,ttcode)
        return jsonify(createCSV)
        
if __name__ == '__main__':
  #app.run(host = '0.0.0.0', port=80, debug=True, threaded=True)
  app.run(debug=True,threaded=True)
