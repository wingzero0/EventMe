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
	public HashMap<String, Integer> getTFs(String[] duplicateSegs){ // include duplicate seg.
		HashMap<String, Integer> distinct = new HashMap<String, Integer>();
		for (String s:duplicateSegs){
			if ( !distinct.containsKey(s) ){ // new key
				distinct.put(s, new Integer(0));
			}
			int count = distinct.get(s).intValue();
			distinct.put(s, new Integer(count + 1));
		}
		return distinct;
		
	}

	//HashMap<String, Double> map, map.size() -th = standword
	public  HashMap<String, Double> getIDFs( HashMap<String, Double> map){
		double total = 287000000; // from ���
		List<String> segs = new ArrayList<String>(map.keySet());

		for(String s : segs){
			if(map.get(s).doubleValue() < 0.0){
				int theCount = getActicleCountContainTheWords(s);
				double idf = Math.log( total / (double)(theCount+1) );
				if (idf < 0){idf = 0.0;}
				map.remove(s);
				map.put(s, new Double(idf));
				//System.out.println(s + "---  get idf from website  ");
			}else{
				//System.out.println("idf from db");
			}
		}
		// DEBUG
		System.out.println("\nDocumentFrequencyWithDBManager --  getIDFs");
		for (Map.Entry<String, Double> entry : map.entrySet()) {
			System.out.printf("				"+entry.getKey()+" "+ entry.getValue());
		}	
		return map;
	}
	public HashMap<String, Double> getIDFs(String keywords[]){
		double total = 287000000; // from ���
		HashMap<String, Double> idfs = new HashMap<String, Double>();
		
		for(String s : keywords){
			int theCount = getActicleCountContainTheWords(s);
			if (theCount < 0){ // blocked by bing
				idfs.put(s, new Double(-10000.0));
			}else{
				double idf = Math.log( total / (double)(theCount+1) );
				if (idf < 0){idf = 0.0;}
				idfs.put(s, new Double(idf));
			}
		}
		return idfs;

	}

	public HashMap<String, Double> getTF_IDFs(HashMap<String, Double> TFsMap, HashMap<String, Double> IDFsMap){
		HashMap<String, Double> temp = new HashMap<String, Double>();
		List<String> segs = new ArrayList<String>(TFsMap.keySet());
		for(String s:segs){
			//System.out.println(TFsMap.get(s) +"  "+ IDFsMap.get(s) );
			temp.put(s, TFsMap.get(s)*IDFsMap.get(s));
		}
		// DEBUG
		System.out.println("\nDocumentFrequencyWithDBManager --  getTF_IDFs");
		for (Map.Entry entry : temp.entrySet()) {
			System.out.println("				"+entry.getKey()+" "+ entry.getValue());
		}	
		return temp;
	}

}
