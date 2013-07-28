package databaseHandler;

import java.io.UnsupportedEncodingException;
import java.net.URLEncoder;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONTokener;

public class KeywordManager {

	public void updateKeywordsAndIDFsToDatabase(HashMap<String, Double> map){
		// input keywords word
		// output ret 1
		   try {
				// DEBUG
				System.out.println("\nKeywordManager --  updateKeywordsAndIDFsToDatabase start");
				 // assess a php page		
				String urlParameters = "op=insert&num="+map.size();
				List<String> keys = new ArrayList<String>(map.keySet());
				for(int i = 0; i < keys.size();i++){
				   	String key = keys.get(i);
				   	String keyword = "keyword"+i+"="+URLEncoder.encode(key,"UTF-8");
				   	String idf = "idf"+i+"="+URLEncoder.encode(map.get(key).toString(),"UTF-8");
					urlParameters += "&" + keyword + "&" + idf;
				}
				String request =  "http://140.112.29.228/ActivitySuggestion/keywordHandler.php";
				String jsonString = Utility.getJsonFromDatabase( request,  urlParameters,true );
				// parsing json
				JSONTokener tokener = new JSONTokener(jsonString);
				JSONObject jsonObject = new JSONObject(tokener);
				// DEBUG
				if(jsonObject.get("ret").toString().equals("1")){
					System.out.println("\nKeywordManager --  updateKeywordsAndIDFsToDatabase end");
				}
			} catch (UnsupportedEncodingException e) {
				e.printStackTrace();
			} catch (JSONException e) {
				e.printStackTrace();
			}
	}	

	public HashMap<String, Double> getIDFsFromDatabase(String keywords[]){
		// input 	keywords word
		// sort the word to match keywords[]
		// id= 0 don't exist //  assign -10000.
		
			Double[] IDFs = new Double[keywords.length];
			try {
				//DEBUG
				System.out.println("\nKeywordManager --  getIDFsFromDatabase start");
				// assess a php page
				String urlParameters = "op=getIDs&num="+keywords.length;
				for(int i =0; i<keywords.length; i++){
				   	String keyword = "keyword"+i+"="+URLEncoder.encode(keywords[i],"UTF-8");
					urlParameters += ("&"+keyword);
				}
				String request =  "http://140.112.29.228/ActivitySuggestion/keywordHandler.php";
				String jsonString= Utility.getJsonFromDatabase( request,  urlParameters, true );
				// parsing json
				JSONTokener tokener = new JSONTokener(jsonString);
				JSONObject jsonObject = new JSONObject(tokener);
				JSONArray objs =  jsonObject.getJSONArray("objs");
				for (int i =0; i <objs.length();i++) {
					JSONTokener idftokener = new JSONTokener(objs.get(i).toString());
					JSONObject idfJsonObject = new JSONObject(idftokener);
					//System.out.println(idfJsonObject.get("idf").toString());
					Double temp = Double.parseDouble(idfJsonObject.get("idf").toString());
					if(idfJsonObject.get("id").toString().equals("0")){
						IDFs[i] = (double) -10000;
					}
					else{ IDFs[i] = temp; }
				}
			} catch (UnsupportedEncodingException e) {
				e.printStackTrace();
			} catch (JSONException e) {
				e.printStackTrace();
			}
		// create a hashmap
		HashMap<String, Double> keywordsWithIDFs = Utility.createHashMap(keywords, IDFs);
		// DEBUG
		if(keywordsWithIDFs.size()!=0){
			System.out.println("\nKeywordManager --  getIDFsFromDatabase end");
			for (Map.Entry entry : keywordsWithIDFs.entrySet()) {
				System.out.printf("				"+entry.getKey()+" "+ entry.getValue());
			}	
		}
		return keywordsWithIDFs;
	}	
	
}
