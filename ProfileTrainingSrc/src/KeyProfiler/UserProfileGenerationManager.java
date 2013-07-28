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

	
   public void dataMining (String file,double percentage) {
	    Database db = null;
	    try {
	    db = new Database(file);  //  ?????? change 
	    } catch(Exception e) {
	    e.printStackTrace();
	    }
	    System.out.println("\nStarting Apriori");
	    ExtendsApriori test1 = new ExtendsApriori("test1", db, percentage);
	    ExtendsApriori.debugger = false;
	    test1.start();
	    try {
	    test1.join();
	    test1.caluateMaximalFrequentItemset();
	    test1.printPatterns();
	    
	    MaximalfrequentItemsets =  test1.getMaximalfrequentItemsets();
	    MaximalfrequentItemsetsSupport = test1.getMaximalfrequentItemsetsSupport();
	    
	    
	    } catch(Exception e) {
	    e.printStackTrace();
	    }
   }
   
   public List< List< Integer >> getMaximalfrequentItemsets(){
	   return MaximalfrequentItemsets;
   }
	public List<Double> getMaximalfrequentItemsetsSupport(){
		return MaximalfrequentItemsetsSupport;
	}
   
   /*
    public void uploadUserProfileToDatabase(){
    	for(int i =0;i<MaximalfrequentItemsets.size();i++){
    		
    		List< Integer > uploadingItemsets = MaximalfrequentItemsets.get(i);
    		//http://140.112.29.228/ActivitySuggestion/profileHandler.php?op=insert&userID=1&num=3&keywordID0=1&keywordID1=16&&keywordID2=10&weight0=5.5&weight1=55&weight2=10
    		String preAddress = "http://140.112.29.228/ActivitySuggestion/profileHandler.php?op=insert&userID="+userIndex;
    		String number = "&num="+MaximalfrequentItemsetsSupport.size();
    	
    		String keywordsIndex = "";
    		for(int j = 0; j<uploadingItemsets.size();j++){
    			Integer index = uploadingItemsets.get(j);
    			keywordsIndex+="&keywordID"+ j +"="+index;
    		}
		
    		Integer index = MaximalfrequentItemsetsSupport.get(i);
    		String weight ="&keywordID"+ i +"="+index;

    		try {
    			URL url = new URL(preAddress+number+keywordsIndex+weight);
    			HttpURLConnection connection = (HttpURLConnection) url.openConnection();
    			BufferedReader in = new BufferedReader(new InputStreamReader(connection.getInputStream()));
    			in.close();
    			System.out.println("insert successfully ");
    		} catch (IOException e) {
    			e.printStackTrace();
    		}	
    	}
		
    }
    */
    
    // ======================== DEBUG ========================
    
    
    public void dataMining (String file,double percentage , HashMap<Integer,String> indexToWordHashMap) {
	    Database db = null;
	    try {
	    db = new Database(file);  //  ?????? change 
	    } catch(Exception e) {
	    e.printStackTrace();
	    }
	    System.out.println("\nStarting Apriori");
	    ExtendsApriori test1 = new ExtendsApriori("test1", db, percentage);
	    ExtendsApriori.debugger = false;
	    test1.start();
	    try {
	    test1.join();
	    test1.caluateMaximalFrequentItemset();
	    test1.printPatterns(indexToWordHashMap);
	    
	    MaximalfrequentItemsets =  test1.getMaximalfrequentItemsets();
	    MaximalfrequentItemsetsSupport = test1.getMaximalfrequentItemsetsSupport();
	    
	    
	    } catch(Exception e) {
	    e.printStackTrace();
	    }
  }
    
    // ======================== Output high support word ========================
    public void saveKeywordsToFile(String filename, List<Map<String, Double>> allKeywords) throws IOException{
		// DEBUG
		System.out.println("UserProfileGenerationManager --  saveKeywordsToFile start");
    	 // output
        PrintWriter out = new PrintWriter(new FileWriter(filename), true);
        for(int i =0;i<allKeywords.size();i++){	
        	Map<String, Double> keywords = allKeywords.get(i);
        	for (int j  = 0;j<keywords.size();j++) {
        		List<String> keys = new ArrayList<String>(keywords.keySet());
        		out.printf(keys.get(j));
        		if(i<keywords.size()-1)out.printf(" ");	
        	}
        	if(i<allKeywords.size()-1)out.printf("\n");
        }
        out.close();
		// DEBUG
		System.out.println("UserProfileGenerationManager --  saveKeywordsToFile end");
	}
    // ======================== Output high support word ========================
    public void outputTheKeywordsWithHighSupport(String inputFile, String outputFile, int topX) throws IOException {
		// DEBUG
		System.out.println("UserProfileGenerationManager --  outputTheKeywordsWithHighSupport start");
        PrintWriter out = new PrintWriter(new FileWriter(outputFile), true);
    	BufferedReader in = new BufferedReader(new FileReader(inputFile));
    	try {
            String line1 = in.readLine();
            while (line1 != null) {
            	String[] row = line1.split(" ");
            	String newLine = "";
            	if(topX>row.length) topX = row.length;
            	// invert
            	for(int i=row.length-1 ; i>= row.length - topX ;i--){
            		newLine += row[i] + " ";
            	}
            	out.println(newLine);
            	line1 = in.readLine();
            }
        } finally {
        	in.close();
        	 out.close();
        }
		// DEBUG
		System.out.println("UserProfileGenerationManager --  outputTheKeywordsWithHighSupport end");
    } 
    
    // ======================== For replace keyword to index ========================
    public HashMap<Integer,String> replaceKeywordToIndex(String inputFile, String outputFile) throws IOException{
    	
    	// read file
    	BufferedReader br = new BufferedReader(new FileReader(inputFile));
    	List<String[]> keywords = new ArrayList<String[]>();
        try {
            StringBuilder sb = new StringBuilder();
            String line = br.readLine();

            while (line != null) {
            	String[] row = line.split(" ");
            	keywords.add(row);
            	line = br.readLine();
            }
        } finally {
            br.close();
        }
        
        // Create a hash map 
        HashMap<Integer,String> HashMap_IndexToWord = new HashMap<Integer, String>(); 
        
        // replace
        int indexNum = 0;
        for(int i =0;i< keywords.size();i++){
        	 for(int j =0;j<keywords.get(i).length;j++){
        		 //
        		 if(isNumeric(keywords.get(i)[j])==false){
        			 indexNum++;
        			 HashMap_IndexToWord.put(indexNum,keywords.get(i)[j]);  // add
        			 compared(indexNum, keywords.get(i)[j], keywords);
        		 }
        	 }
        }
        
        // output
        PrintWriter out = new PrintWriter(new FileWriter(outputFile), true);
		for (String[] keyword : keywords) {
			for (String k : keyword) {
				out.printf(k+" ");
			}
			out.printf("\n");
		}
        out.close();
        
        return  HashMap_IndexToWord;
        
    }
    
    public void compared(int indexNum, String temp, List<String[]> keywords)
    {
        for(int i =0;i< keywords.size();i++){
       	 for(int j =0;j<keywords.get(i).length;j++){
       		 if(keywords.get(i)[j].equals(temp)){
       			 keywords.get(i)[j] = Integer.toString(indexNum);
       		 }
    	 }
        }
    }
    
    public boolean isNumeric(String str)
    {
        return str.matches("-?\\d+(.\\d+)?");
    }

    // ========== For replace keyword to index by using db information ========================
    public HashMap<Integer,String> replaceKeywordToIndexWithDBInfromation(String inputFile, String outputFile) throws IOException{
    	
    	// read file
    	BufferedReader br = new BufferedReader(new FileReader(inputFile));
    	List<String[]> keywords = new ArrayList<String[]>();
        try {
            StringBuilder sb = new StringBuilder();
            String line = br.readLine();

            while (line != null) {
            	String[] row = line.split(" ");
            	keywords.add(row);
            	line = br.readLine();
            }
        } finally {
            br.close();
        }
        
        /**
         * The different between replaceKeywordToIndex and this is here
         **/
        // Create a hash map 
        HashMap<Integer,String> HashMap_IndexToWord = Utility.getMapWithIndexToKeyword(keywords);
        
        
        // output
        PrintWriter out = new PrintWriter(new FileWriter(outputFile), true);
		for (String[] keyword : keywords) {
			for (String k : keyword) {
				int number = -1;
				// find out the index from a keyword
				for (java.util.Map.Entry<Integer, String> entry : HashMap_IndexToWord.entrySet()) {
		            if (entry.getValue().equals(k)) {
		            	number = entry.getKey();
		            }
		        }
				//---
				out.printf(number+" ");
			}
			out.printf("\n");
		}
        out.close();
        
        return  HashMap_IndexToWord;
        
    }
    
    
    
}
