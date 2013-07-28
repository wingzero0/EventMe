import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import databaseHandler.ActivityManager;
import databaseHandler.KeywordManager;
import databaseHandler.UserJoinedActivityManager;
import databaseHandler.UserProfileManager;
import databaseHandler.Utility;

import DocumentFrequency.DocumentFrequencyManager;
import DocumentFrequency.DocumentFrequencyWithDBManager;
import KeyProfiler.UserProfileGenerationManager;
import KeywordSet.KeywordGenerationManager;
/*
 * 
 *  Read 10 text file from computer ---> find out user profile ( with all upload and get function from database)
 * 
 * */

public class testWithDB {
	   public static void main(String[] args) throws IOException  { 
	
		   // NEW START
		   /**
		    * 	One part of the system
		    *  1. read acticle from database given user id
		    * **/
		   // read all activity description of a user
		   //	UserJoinedActivityManager userJoinedActivityManager = new UserJoinedActivityManager(0);// user id  //*****
		   //	ArrayList<String> userActivityDescription =  userJoinedActivityManager.getAllActivityWhichIsReadBefore();
	    	
		   /**
		    *  Test 1
		    *  1. read acticle from computer
		    * **/
		   // read all activity description of a user 
		   /*
		   ArrayList<String> userActivityDescription = new ArrayList<String>();
		   for(int i =0;i<=0;i++){
	    		// 1. Read file 
		    	String total = "";
		    	BufferedReader br = new BufferedReader(new FileReader("src/testActicle/text"+i+".txt"));   	
				String sCurrentLine;
				while ((sCurrentLine = br.readLine()) != null) {
					//	System.out.println(sCurrentLine);
					total +="\n"+sCurrentLine;
				}
				userActivityDescription.add(total);
	    	}
	    	*/
        	List<Map<String, Double>> allkeywords = new ArrayList<Map<String, Double>>();
//	    	for(String description:userActivityDescription){
	    		
        		/**
        		 *  One part of the system
        		 *  2. after we get some key word from activity information
        		 * **/
		    	//  Keyword extraction
        		//	KeywordGenerationManager keywordGenerationManager  = new KeywordGenerationManager();
        		//	String segs[] = keywordGenerationManager.run( description);
        		//segs = keywordGenerationManager.kickoutStopWord("src/stopword.txt",segs);

        		/**
        		 *  Test 1
        		 *  2. read acticle from computer
        		 * **/
        		String[] segs = {"澳門test", "科技test", "天才test"};
        		
        		
		        // Compute IDF
		        KeywordManager keywordManager = new KeywordManager();
		        DocumentFrequencyWithDBManager documentFrequencyWithDBManager =  new DocumentFrequencyWithDBManager();
		        HashMap<String, Double> keywordsWithIDFs = keywordManager.getIDFsFromDatabase(segs); // get idf from database
		        keywordsWithIDFs =  documentFrequencyWithDBManager.getIDFs( keywordsWithIDFs);		// add missing idf from search engine
		        keywordManager.updateKeywordsAndIDFsToDatabase(keywordsWithIDFs);	// updata all keywords and idf to database
		        
				
			    // add TFs to database
		        ActivityManager activityManager = new ActivityManager("《海晨畫馬——孫海晨藝術作品展》");  //****
		        activityManager.findActivityIndexFromActivityName();
		        HashMap<String, Double> keywordsWithTFs  = activityManager.getTFsFromDatabase(); // get activity record with keyword from database
		        
		        
				// Compute TF
		        if(keywordsWithTFs==null){	// if there is not the activity record, compute tf
	        		/**
	        		 *  One part of the system
	        		 *  3. compute TFs
	        		 * **/
		        	//  	keywordsWithTFs =  documentFrequencyWithDBManager.getTFs(description, segs, true); // not yet tested
	        		/**
	        		 *  Test 1
	        		 *  3. compute TFs
	        		 * **/
		          	Double[] df = {0.2,2.4,1.2};
			        keywordsWithTFs = new HashMap<String, Double>();
			        for(int i=0; i<segs.length;i++){
			        	keywordsWithTFs.put(segs[i], df[i]);
			        }  
		        }    
		        
		        //activityManager.updateKeywordsAndTFsToDatabase(keywordsWithTFs);
		        activityManager.updateKeywordsAndTFsToDatabase(new HashMap<String, Integer>());
		        
		        // Compute TD-IDF
		        HashMap<String, Double> keywordsWithTF_IDFs = documentFrequencyWithDBManager.getTF_IDFs(keywordsWithTFs, keywordsWithIDFs);
		        
		        // Sort keywords with TD-IDF to do data mining
		        Map OrderKeywords = documentFrequencyWithDBManager.sortByComparator(keywordsWithTF_IDFs);
		        documentFrequencyWithDBManager.printMap(OrderKeywords);
		        allkeywords.add(OrderKeywords); // DEBUG
		        
	//    	}
	    	
	    	
	        //  	Transfer and Save user profile to database.
		    UserProfileGenerationManager userProfileGenerationManager = new UserProfileGenerationManager();
	    	userProfileGenerationManager.saveKeywordsToFile("src/file.txt", allkeywords);
	    	userProfileGenerationManager.outputTheKeywordsWithHighSupport("src/file.txt", "src/fileHighSupport.txt", 30); 
	    	
	    	// find out the map between keywords id and the keywords
	        HashMap<Integer,String> HashMap_IndexToWord = userProfileGenerationManager.replaceKeywordToIndexWithDBInfromation("src/fileHighSupport.txt","src/fileHighSupportIndex.txt");
	        // find out user profile.
	        userProfileGenerationManager.dataMining("src/fileHighSupportIndex.txt",40,HashMap_IndexToWord);
	        
	        // upload user profile
	        UserProfileManager userProfileManager =  new UserProfileManager(2);// user id //*******
	        userProfileManager.uploadUserProfileToDatabase(userProfileGenerationManager.getMaximalfrequentItemsets(), userProfileGenerationManager.getMaximalfrequentItemsetsSupport());
			
	   }
}
