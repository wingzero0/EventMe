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
			   	String idf = "idf"+i+"="+map.get(key).doubleValue();
				urlParameters += "&" + keyword + "&" + idf;
			}
			String request =  "http://localhost/ActivitySuggestion/keywordHandler.php";
			String jsonString = Utility.getJsonFromDatabase( request,  urlParameters, false );
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
	public HashMap<String, Integer> getKeywordIDfromDatabase(String keywords[]){
		HashMap<String, Integer> ids = new HashMap<String, Integer>();
		try {
			// assess a php page
			String urlParameters = "op=getIDByTerm&num="+keywords.length;
			for(int i =0; i<keywords.length; i++){
			   	String keyword = "keyword"+i+"="+URLEncoder.encode(keywords[i],"UTF-8");
				urlParameters += ("&"+keyword);
			}
			String request =  "http://localhost/ActivitySuggestion/keywordHandler.php";
			String jsonString= Utility.getJsonFromDatabase( request,  urlParameters, false );
			// parsing json
			JSONTokener tokener = new JSONTokener(jsonString);
			JSONObject jsonObject = new JSONObject(tokener);
			if (jsonObject.getInt("ret") != 1){
				System.err.println("keywordHandler.php error" + jsonObject.getString("error"));
			}else{
				JSONArray objs =  jsonObject.getJSONArray("val");
				for (int i =0; i <objs.length();i++) {
					int id = objs.getInt(i);
					ids.put(keywords[i], new Integer(id));
				}
			}
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		} catch (JSONException e) {
			e.printStackTrace();
		}
		return ids; 
	}
	public HashMap<String, Double> getIDFsFromDatabase(String keywords[]){
		// input 	keywords word
		// sort the word to match keywords[]
		// id= 0 don't exist //  assign -10000.
		
		// get keyword and keyword id mapping
		HashMap<String, Integer> ids = this.getKeywordIDfromDatabase(keywords);
		HashMap<String, Double> keywordsWithIDFs = new HashMap<String, Double>(); // init;
		
		//Double[] IDFs = new Double[keywords.length];
		try {
			//DEBUG
			System.out.println("\nKeywordManager --  getIDFsFromDatabase start");
			// assess a php page
			String urlParameters = "op=getIDFByID&num="+keywords.length;
			for(int i =0; i<keywords.length; i++){
				int id = ids.get(keywords[i]);
				urlParameters += ("&id" + i + "=" + id);
			}
			String request =  "http://localhost/ActivitySuggestion/keywordHandler.php";
			String jsonString= Utility.getJsonFromDatabase( request,  urlParameters, false );
			
			// parsing json
			JSONTokener tokener = new JSONTokener(jsonString);
			JSONObject jsonObject = new JSONObject(tokener);
			if (jsonObject.getInt("ret") != 1){
				System.err.println("keywordHandler.php error" + jsonObject.getString("error"));
			}else{
				JSONArray objs =  jsonObject.getJSONArray("val");
				double idf;
				for (int i =0; i <objs.length();i++) {
					if (ids.get(keywords[i]).intValue() == 0){ //Kit: maybe this is a stupid action, Zac please help me to fix it 
						idf = -10000.0;
					}else{
						idf = objs.getDouble(i);
					}
					keywordsWithIDFs.put(keywords[i], new Double(idf));
				}
			}
		}catch (JSONException e) {
			e.printStackTrace();
		}
		
		if(keywordsWithIDFs.size()!=0){
			System.out.println("\nKeywordManager --  getIDFsFromDatabase end");
			for (Map.Entry<String, Double> entry : keywordsWithIDFs.entrySet()) {
				System.out.printf("				"+entry.getKey()+" "+ entry.getValue());
			}
		}
		return keywordsWithIDFs;
	}	
	
}
