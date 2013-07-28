import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.TreeMap;
import java.util.Iterator;
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

public class PreProcessing {
	public static void main(String[] args){
		Map<Integer, String> desMap = ActivityManager.GetActivityDescription(Integer.parseInt(args[0]));
		//desMap.toString();
		Iterator<Map.Entry<Integer,String>> it = desMap.entrySet().iterator();
		while (it.hasNext()){
			Map.Entry<Integer, String> pairs = it.next();
			PreProcessing pp = new PreProcessing();
			System.out.println(pairs.getKey().toString() + pairs.getValue());
			pp.TFIDF(pairs.getKey().intValue(), pairs.getValue());
		}
	}
	public void TFIDF(int activityID, String description){
		try {
			//Keyword extraction
			KeywordGenerationManager keywordGenerationManager = new KeywordGenerationManager();
			String segs[];
			
			segs = keywordGenerationManager.run( description);
			segs = keywordGenerationManager.kickoutStopWord("src/stopword.txt",segs);
			HashMap<String, Integer> distinct = new HashMap<String, Integer>();
			for (String s:segs){
				distinct.put(s, new Integer(1)); // remove duplicate
			}
			
			KeywordManager keywordManager = new KeywordManager();
	        DocumentFrequencyWithDBManager documentFrequencyWithDBManager =  new DocumentFrequencyWithDBManager();
	        HashMap<String, Double> keywordsWithIDFs = keywordManager.getIDFsFromDatabase(distinct.keySet().toArray(new String[0])); // get idf from database
	        
	        ArrayList<String> unknownIDF = new ArrayList<String>();
			for(String s : new ArrayList<String>(keywordsWithIDFs.keySet())){
				if(keywordsWithIDFs.get(s).doubleValue() < 0.0){
					unknownIDF.add(s);
				}
			}
	        
	        HashMap<String, Double> newIDFs; 
	        newIDFs =  documentFrequencyWithDBManager.getIDFs(unknownIDF.toArray(new String[0]));		// add missing idf from search engine
	        keywordManager.updateKeywordsAndIDFsToDatabase(newIDFs);	// updata all keywords and idf to database
	        
	        keywordsWithIDFs.putAll(newIDFs);
	        

	        HashMap<String, Integer> tfs = documentFrequencyWithDBManager.getTFs(segs);
	        ActivityManager am = new ActivityManager(activityID);
	        am.updateKeywordsAndTFsToDatabase(tfs);
	        //System.out.println("IDFs:" + keywordsWithIDFs.toString());
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		 // Compute IDF
        
		return;
	}
	//Keyword extraction
	//	KeywordGenerationManager keywordGenerationManager  = new KeywordGenerationManager();
	//	String segs[] = keywordGenerationManager.run( description);
	//segs = keywordGenerationManager.kickoutStopWord("src/stopword.txt",segs);
	
}
