import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

import databaseHandler.ActivityManager;
import databaseHandler.KeywordManager;
import databaseHandler.UserProfileManager;

/*
 * 
 *  Test any manager in databaseHandler.
 * 
 * */


public class testWithDBFunction {
   public static void main(String[] args) throws IOException  { 
		   
		   
	    	String[] segs = {"澳門", "科技", "天才"};
	        Double[] idfs = {0.2,2.4,1.2};
	        HashMap<String, Double> map = new HashMap<String, Double>();
	        for(int i=0; i<segs.length;i++){
	        	map.put(segs[i], idfs[i]);
	        }
	        
	        // KeywordManager Test
	        /*
	        KeywordManager keywordManager = new KeywordManager();
	        keywordManager.updateKeywordsAndIDFsToDatabase(map);
	        keywordManager.getIDFsFromDatabase(segs);
	        */
	        
	        // ActivityManager Test
	        /*
	        ActivityManager activityManager = new ActivityManager("S.H.E 2GETHER 4EVER世界巡迴演唱會2013 澳門站");
	        activityManager.findActivityIndexFromActivityName();
	        activityManager.updateKeywordsAndTFsToDatabase(map);
        	activityManager.getTFsFromDatabase();
	        */
	        
	        
	        // UserProfileManager Test
	        /*
	        List< List< Integer >> MaximalfrequentItemsets = new ArrayList < List< Integer >>(); 
	        List<Integer> temp  = new ArrayList<Integer>();
	        temp.add(1);
	        temp.add(4);
	        List<Integer> temp2  = new ArrayList<Integer>();        
	        temp2.add(2);
	        temp2.add(13);
	        MaximalfrequentItemsets.add(temp);
	        MaximalfrequentItemsets.add(temp2);
	        
	        List<Double> MaximalfrequentItemsetsSupport = new ArrayList<Double>();
	        MaximalfrequentItemsetsSupport.add(0.1);
	        MaximalfrequentItemsetsSupport.add(0.4);   
	        
	        UserProfileManager userProfileManager =  new UserProfileManager(2);
	        userProfileManager.getUserProfileToDatabase();
	        userProfileManager.uploadUserProfileToDatabase(MaximalfrequentItemsets, MaximalfrequentItemsetsSupport);
	        */
   }
}
