package KeywordSet;

import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
import java.util.ArrayList;
import java.util.Collections;
import java.util.List;

public class KeywordGenerationManager extends SegChinese {

		public String[] kickoutStopWord(String stopwordFileName, String[] segs) throws IOException {

			String[] segsTemp = segs.clone();
			int NullCount = 0;
			
			for(int i = 0 ; i< segsTemp.length;i++){
				
				// record the stop word index ( 1 character)
				if(segsTemp[i].length()==1){
					segsTemp[i] = "null";
					NullCount++;
				}
				
				
				
				// read stopword file
		    	BufferedReader br = new BufferedReader(new FileReader(stopwordFileName));
		    	List<String> stopwords = new ArrayList<String>();
		        String line = br.readLine();
		        while (line != null) {
		        	stopwords.add(line.trim());
		           	line = br.readLine();
		           }
		        br.close();
		        
				// record the stop word index 
				for(String stopword : stopwords){
					if(segsTemp[i].compareTo(stopword)==0){
						segsTemp[i] = "null";
						NullCount++;
						break;
					}
				}
				
				
				// record string with number 
				String s = segsTemp[i];
				if(s.replaceAll("\\d+","").length() == 0) {
					segsTemp[i] = "null";
					NullCount++;
				}	
			}
			
			// remove "null"
		    final List<String> list =  new ArrayList<String>();
		    Collections.addAll(list, segsTemp); 
		    for(int i = 0 ; i< NullCount; i++){
		    	list.remove("null");
		    }
		    segsTemp = list.toArray(new String[list.size()]);
		      
			// debug 
			for(int i=0;i<segsTemp.length;i++){
		    	System.out.println("after stop word " + segsTemp[i]);
			} 
			
			
			return segsTemp;
		}
	}
