import java.io.BufferedReader;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import DocumentFrequency.DocumentFrequencyManager;
import DocumentFrequency.DocumentFrequencyWithDBManager;
import KeyProfiler.UserProfileGenerationManager;
import KeywordSet.KeywordGenerationManager;
import KeywordSet.SegChinese;

/*
 * 
 *  Read 10 text file from computer ---> find out user profile
 * 
 * */

public class test {  
	
    public static void main(String[] args) throws IOException  { 
    	
    	
    	//  the data of keyword and support
    	List<Map<String, Double>> allkeywords = new ArrayList<Map<String, Double>>();
    	// readFile
    	for(int i =0;i<=9;i++){
    		// step 1 -  4
        	testKeyword("src/testActicle/text"+i+".txt", allkeywords);	
    	}
    	
    	
        // 5. Find Maximal Frequent Itemsets
        //  Transfer and Save user profile to database.
    	UserProfileGenerationManager userProfileGenerationManager = new UserProfileGenerationManager();
  //      userProfileManager.loadActivityKeywordsIndex();
        // DEBUG
    	userProfileGenerationManager.saveKeywordsToFile("src/file.txt", allkeywords);
    	userProfileGenerationManager.outputTheKeywordsWithHighSupport("src/file.txt", "src/fileHighSupport.txt", 30); 
        HashMap<Integer,String> HashMap_IndexToWord = userProfileGenerationManager.replaceKeywordToIndex("src/fileHighSupport.txt","src/fileHighSupportIndex.txt");
        
        userProfileGenerationManager.dataMining("src/fileHighSupportIndex.txt",40,HashMap_IndexToWord);
//**        userProfileGenerationManager.uploadUserProfileToDatabase(); // not test yet.............
       

    }
    
    public static void testKeyword(String fileName, List<Map<String, Double>> allkeywords) throws IOException{
       	
    	// 1. Read file 
    	String total = "";
    	BufferedReader br = new BufferedReader(new FileReader(fileName));   	
		String sCurrentLine;
		while ((sCurrentLine = br.readLine()) != null) {
			//	System.out.println(sCurrentLine);
			total +="\n"+sCurrentLine;
		}
    	
    	// 2. Keyword extraction --- Segment chinese
		KeywordGenerationManager keywordGenerationManager  = new KeywordGenerationManager();
		String segs[] = keywordGenerationManager.run( total);
		
		segs = keywordGenerationManager.kickoutStopWord("src/stopword.txt",segs);
		
        System.out.println("How many key word here?   " + segs.length);
        
        // 3. Compute TF-IDFs
        DocumentFrequencyManager documentFrequencyManager = new DocumentFrequencyManager();
        Double[] TFs =  documentFrequencyManager.getTFs(total, segs);
        
        //
        documentFrequencyManager.loadHashMapOfActicleCountWithTheWord("src/acticleCountWithTheWord.txt");
        Double[] IDFs =  documentFrequencyManager.getIDFs("我",segs);
        documentFrequencyManager.saveNewHashMapOfActicleCountWithTheWord("src/acticleCountWithTheWord.txt");
        
        Double[] TF_IDFs = documentFrequencyManager.getTF_IDFs(segs, TFs, IDFs);
        Map<String, Double> keywords = documentFrequencyManager.DebugDisplay(segs, TF_IDFs);
        allkeywords.add(keywords); // DEBUG
        documentFrequencyManager.uploadKeywordKetToDatabase();    //   OR upload keyword      
        // 4. Get data from database.
    }
  

}  
