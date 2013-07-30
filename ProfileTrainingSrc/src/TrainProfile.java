import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import DocumentFrequency.DocumentFrequencyWithDBManager;
import KeyProfiler.UserProfileGenerationManager;

import databaseHandler.ActivityManager;
import databaseHandler.KeywordManager;
import databaseHandler.UserJoinedActivityManager;
import databaseHandler.UserProfileManager;
import databaseHandler.Utility;


public class TrainProfile {
	public static void main(String[] args) throws IOException{
		
		int UID = 1;// Integer.parseInt(args[0]);
		
		// read all activity description of a user
		UserJoinedActivityManager userJoinedActivityManager = new UserJoinedActivityManager(UID);
		int[] activityIDs = userJoinedActivityManager.getAllActivityID(UID);
		
    	List<Map<String, Double>> allkeywords = new ArrayList<Map<String, Double>>();
		for(int i = 0; i<activityIDs.length ; i++){
			
			// Get TFs from database
			ActivityManager am = new ActivityManager("");
			am.SetActivityIndex(activityIDs[i]);
			HashMap<String, Double> TFs  = am.getTFsFromDatabase();
		
			// Get IDFs from database
			KeywordManager keywordManager = new KeywordManager();
			HashMap<String, Double> IDFs = keywordManager.getIDFsFromDatabaseByKeywordsID(TFs.keySet().toArray(new String[0])); 
			
			// Compute TD-IDF
			DocumentFrequencyWithDBManager documentFrequencyWithDBManager =  new DocumentFrequencyWithDBManager();
        	HashMap<String, Double> keywordsWithTF_IDFs = documentFrequencyWithDBManager.getTF_IDFs(TFs, IDFs);
        
        	// Sort keywords with TD-IDF to do data mining
        	Map OrderKeywords = documentFrequencyWithDBManager.sortByComparator(keywordsWithTF_IDFs);
        	//documentFrequencyWithDBManager.printMap(OrderKeywords);
        	allkeywords.add(OrderKeywords); // DEBUG
		}
        
        //  	Transfer and Save user profile to database.
	    UserProfileGenerationManager userProfileGenerationManager = new UserProfileGenerationManager();
    	userProfileGenerationManager.outputTheKeywordsWithHighSupport(allkeywords, "src/fileHighSupport.txt", 30); 
    	
    	// find out the map between keywords id and the keywords
    	String[] allKeywordsArray = Utility.getAllkeywordsFromRecord(allkeywords);
    	allKeywordsArray = Utility.getDistinctKeyword(allKeywordsArray);
    	HashMap<Integer,String> HashMap_IndexToWord = Utility.getMapWithIndexToKeyword(allKeywordsArray);
       
    	// find out user profile and display
        userProfileGenerationManager.dataMining("src/fileHighSupport.txt",40,HashMap_IndexToWord);
        
        // upload user profile
        UserProfileManager userProfileManager =  new UserProfileManager(UID);
        userProfileManager.uploadUserProfileToDatabase(userProfileGenerationManager.getMaximalfrequentItemsets(), userProfileGenerationManager.getMaximalfrequentItemsetsSupport());
	
	} 
}
