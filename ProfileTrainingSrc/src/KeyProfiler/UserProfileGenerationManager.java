package KeyProfiler;

import java.io.BufferedReader;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import databaseHandler.Utility;

public class UserProfileGenerationManager {

	List< List< Integer >> MaximalfrequentItemsets;
	List<Double> MaximalfrequentItemsetsSupport;
   
	public List< List< Integer >> getMaximalfrequentItemsets(){
		return MaximalfrequentItemsets;
	}
	public List<Double> getMaximalfrequentItemsetsSupport(){
		return MaximalfrequentItemsetsSupport;
	}
    public void dataMining (String file,double percentage , HashMap<Integer,String> indexToWordHashMap) {
	    
    	Database db = null;
	    try {
	    	db = new Database(file); 
	    } catch(Exception e) {
	    	e.printStackTrace();
	    }
	    
	    System.out.println("\nStarting Apriori");
	    ExtendsApriori dm = new ExtendsApriori("test1", db, percentage);
	    ExtendsApriori.debugger = false;
	    dm.start();
	    try {
	    	dm.join();
	    	dm.caluateMaximalFrequentItemset();
	    	if(indexToWordHashMap==null)
	    		dm.printPatterns();
	    	else
	    		dm.printPatterns(indexToWordHashMap);	
	    	MaximalfrequentItemsets =  dm.getMaximalfrequentItemsets();
	    	MaximalfrequentItemsetsSupport = dm.getMaximalfrequentItemsetsSupport();
	    } catch(Exception e) {
	    e.printStackTrace();
	    }
  }
  
    public void outputTheKeywordsWithHighSupport(List<Map<String, Double>> allKeywords, String outputFile, int topmostSupportLength) throws IOException {
    	
    	// DEBUG
		System.out.println("UserProfileGenerationManager --  outputTheKeywordsWithHighSupport start");
		
		// figure out the minimum size. Just in case when some of the length of keywords map is smaller tahn topX
		int minSize = 10000;
		for(Map<String, Double> keywords : allKeywords){
			if(keywords.size()<minSize)minSize = keywords.size();
		}
		if(topmostSupportLength>minSize)topmostSupportLength = minSize;
		
		// output
        PrintWriter out = new PrintWriter(new FileWriter(outputFile), true);
    	try {
    		for(Map<String, Double> keywords : allKeywords){
            	List<String> row = new ArrayList<String>(keywords.keySet());
            	String newLine = "";
            	if(topmostSupportLength>row.size()) topmostSupportLength = row.size();
            	// invert
            	for(int i=row.size()-1 ; i>= row.size() - topmostSupportLength ;i--){
            		newLine += row.get(i) + " ";
            	}
            	out.println(newLine);
    		}
        } finally {
        	 out.close();
        }
		// DEBUG
		System.out.println("UserProfileGenerationManager --  outputTheKeywordsWithHighSupport end");
    } 
}
