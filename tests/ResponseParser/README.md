The XML files in this folder and all sub-folders should not be modified, unless the SmartAPI interface has changed what it's sending back, making these samples no longer accurate.

These files represent just about every type of response we can expect to receive from the SmartAPI interface for the corresponding product. We test our library against these samples to ensure we will properly handle even the most uncommon of responses. The unit tests involved will often look for specific data points located in specific sample files, so modifying a sample will likely cause the respective unit tests to fail.

If additional samples need to be added, please follow the below naming convention:

##_Description_here.xml

Example:  
01_Description.xml  
02_Another_Description.xml  
03_Yet another description.xml

The naming convention is such that file naming starts at 01 and increments by one for each subsequent file. The "description" part can be whatever you choose and the file type must be .xml, in lower case. As we can see, this does limit the number of samples we can use for each product category to 99, which is more than sufficient.