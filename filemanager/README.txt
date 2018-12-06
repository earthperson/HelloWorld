File Manager as abstract task
    __________________________________________________________________________

Copyright (c) 2009 Ponomarev Dmitry
E-mail: ponomarev.base@gmail.com
URL: http://dmitry-ponomarev.ru
    
    Table of Contents
        1. Preface
        2. Tested on
        3. Steps by time

Preface
    
    This is step by step test realization of the simple file manager for TRINET http://www.trinet.ru

Tested on
	
	Apache/2.2.14 (Win32) PHP/5.2.11 MySQL/5.0.88-community-nt


Steps by time

    1.	Todo list.    2009-12-02 16:00:00 - 2009-12-02 18:00:00
    
        Understanding what I want to do. Also I saw interfaces and functionality another file managers.
        I try to understand what will be in the my realization. I understand that the task is very abstract.
        There are already exists very much file managers on the Web. I have not very much time. And I keep
        in the my mind that it is only test - not fully functionality release.
        
        And so what should be, what I understand:
        	1. Invent concept.
            2. Directory viewer as tree. I think that frontend may be to do on jsTree jquery tree plugin.
               But I don't familiar with this plugin. So I should be read documentation about.
            3. Creation directories and subdirectories.
            4. Deleting directories, subdirectories and files.
            5. Renaming files and directories.
            6. Uploading files (or file) - time dependence.
            7. Downloading files.
            8. Try to fix some security holes.
            9. Move libraries into the project.
        
        What about realization todo list? I do not know and have not much time to see how it another
        developers did. I have my idea about realization. I have development experience. So I am going
        on the step 2.
        
    2.	Engineering.    2009-12-02 18:20:00 - 2009-12-02 20:20:00
    	
    	1. Requrement libraries, frameworks
    		1. jsTree jquery tree plugin - http://jstree.com/
    		2. DbSimple - http://dklab.ru/lib/DbSimple/
    		
    	2. Concept
    		1  File system journaling. Directories and hierarchy of them are abstract and stored in data base.
    		2. Files are stored in the /var protected folder and don't have extension. All information about them are 
    		   stored in the data base. The name of files are md5(rand). It fix security hole.
    		3. Creating, deleting, renaming, and if I will have time moving directories and subdirectories
    		   are abstract and implements in the data base queries.
    		4. Renaming files are abstract and implements in the data base queries.
    		5. All manipulations (except upload and download) are rpc queries (AJAX queries).  
    		6. Frontend tree viewer data exchanger format is JSON.  
    		7. All is in UTF-8.
    	
    	3. Ather nuance under development section. So I am going on the step 3. 
    
    3.	Development.    2009-12-02 20:40:00 - 2009-12-03 06:00:00
    	
    	1. Development process. It is unspeakable...
    	
    	