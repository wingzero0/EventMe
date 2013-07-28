package DocumentFrequency;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.UnsupportedEncodingException;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLEncoder;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Set;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONTokener;

import databaseHandler.Utility;

public class DocumentFrequencyWithDBManager extends DocumentFrequencyManager{

	public DocumentFrequencyWithDBManager() {
		super();
	}

	   public  HashMap<String, Double> getTFs(String acticle, String[] segs, Boolean normalize ){
	        Double TFs[]=new Double[segs.length];
	        
	        double total = 0;
	        for(int i =0;i<segs.length;i++){
	        	// find word
	        	String s = segs[i];
	        	// compute the number of word in acticle
	        	double wordCount = 0;
	        	Pattern pattern = Pattern.compile(s); 
	        	Matcher matcher = pattern.matcher(acticle);
	        	while (matcher.find()) wordCount++;
	            TFs[i] =  wordCount;
	            total += wordCount;
	        }
	        for(int i =0;i<segs.length;i++){
	        	TFs[i] = TFs[i]/total;
	        }
	        HashMap<String, Double> keywordsWithTFs = Utility.createHashMap(segs, TFs);
	        return keywordsWithTFs;
	    }  
	   
	   //HashMap<String, Double> map, map.size() -th = standword
	   public  HashMap<String, Double> getIDFs( HashMap<String, Double> map){
		   
		   double total = 287000000; // from 我
		   List<String> segs = new ArrayList<String>(map.keySet());
		   
	        for(String s : segs){
	        	if(map.get(s) == -10000){
	        		int theCount = getActicleCountContainTheWords(s);
	        		double idf = Math.log((double)total/(theCount+1));
	        		map.remove(s);
	        		map.put(s, idf);
	        		//System.out.println(s + "---  get idf from website  ");
	        	}else{
	        		//System.out.println("idf from db");
	        	}
	        }
			// DEBUG
			System.out.println("\nDocumentFrequencyWithDBManager --  getIDFs");
			for (Map.Entry entry : map.entrySet()) {
				System.out.printf("				"+entry.getKey()+" "+ entry.getValue());
			}	
	        return map;
	    }
	   
	    public HashMap<String, Double> getTF_IDFs(HashMap<String, Double> TFsMap, HashMap<String, Double> IDFsMap){
	    	HashMap<String, Double> temp = new HashMap<String, Double>();
	    	List<String> segs = new ArrayList<String>(TFsMap.keySet());
	        for(String s:segs){
	        	temp.put(s, TFsMap.get(s)*IDFsMap.get(s));
	        }
	        // DEBUG
			System.out.println("\nDocumentFrequencyWithDBManager --  getTF_IDFs");
			for (Map.Entry entry : temp.entrySet()) {
				System.out.printf("				"+entry.getKey()+" "+ entry.getValue());
			}	
	        return temp;
	    }

}
