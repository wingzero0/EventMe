package databaseHandler;

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
import java.util.TreeMap;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONTokener;

public class ActivityManager {

	int activityIndex;
	String activityName;
	public ActivityManager(String activityName) {
		this.activityName = activityName;
	}
	public void SetActivityIndex(int index){
		this.activityIndex = index;
	}

	public void findActivityIndexFromActivityName(){
		// input.           activity word 
		// ouput 			activity index
		try {
			// DEBUG
			System.out.println("ActivityManager --  findActivityIndexFromActivityName start");
			// assess a php page
			String urlParameters = "op=getIDs&actName="+URLEncoder.encode(this.activityName,"UTF-8");;
			String request =  "http://140.112.29.228/ActivitySuggestion/getActivityIdByName.php";
			String jsonString= Utility.getJsonFromDatabase( request,  urlParameters,true );
			// parsing json
			JSONTokener tokener = new JSONTokener(jsonString);
			JSONObject jsonObject = new JSONObject(tokener);
			this.activityIndex = Integer.parseInt(jsonObject.get("id").toString());
			// DEBUG
			System.out.println("ActivityManager --  findActivityIndexFromActivityName end");
			System.out.printf("				activityIndex  "+ this.activityIndex);
		} catch (JSONException e) {
			e.printStackTrace();
		} catch (UnsupportedEncodingException e1) {
			e1.printStackTrace();
		}
	}

	public void updateKeywordsAndTFsToDatabase(HashMap<String, Integer> map){
		// input 	activity index,  keyword index, TF = 0
		// output {ref:1}
		// http://140.112.29.228/ActivitySuggestion/activityWordHandler.php?op=insert&num=2&activityID=1&keywordID0=1&keywordID1=5&tf0=10&tf1=222
		try {
			// DEBUG
			System.out.println("\nActivityManager --  updateKeywordsAndTFsToDatabase start");
			// assess a php page
			String urlParameters = "op=insertActivityWordByPlainTxt&num="+map.size()+"&activityID="+activityIndex;
			List<String> keys = new ArrayList<String>(map.keySet());
			for(int i = 0; i < keys.size();i++){
				String key = keys.get(i);
				String keyword = "keyword"+i+"="+URLEncoder.encode(key,"UTF-8");
				String tf = "tf"+i+"="+ map.get(key).intValue();
				urlParameters += "&" + keyword + "&" + tf; 
			}
			String request =  "http://localhost/ActivitySuggestion/activityWordHandler.php";
			String jsonString = Utility.getJsonFromDatabase( request,  urlParameters, false);
			// parsing json
			JSONTokener tokener = new JSONTokener(jsonString);
			JSONObject jsonObject = new JSONObject(tokener);
			// DEBUG
			if( jsonObject.getInt("ret") != 1){
				System.err.println("\nerror in activityWordHandler.php:" + jsonObject.getString("error"));
			}else{
				System.out.println("\nActivityManager --  updateKeywordsAndTFsToDatabase end");
			}
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}

	public HashMap<String, Double> getTFsFromDatabase(){

		// input 	activity index
		// sort the word to match keywords[]
		// don't exit return null

		// http://140.112.29.228/ActivitySuggestion/activityWordHandler.php?op=get&activityID=1
		Double[] TFs = null;
		String[] keywords = null;
		try {
			// DEBUG
			System.out.println("\nActivityManager --  getTFsFromDatabase start");
			// assess a php page
			String urlParameters = "op=get&activityID="+activityIndex;
			String request =  "http://140.112.29.228/ActivitySuggestion/activityWordHandlerPlaintext.php";
			String jsonString= Utility.getJsonFromDatabase( request,  urlParameters, true );
			// parsing json
			JSONTokener tokener = new JSONTokener(jsonString);
			JSONObject jsonObject = new JSONObject(tokener);
			JSONArray objs =  jsonObject.getJSONArray("objs");
			if(objs.length()==0) return null;
			// parsing json
			keywords =  new String[objs.length()];
			TFs = new Double[objs.length()];
			for (int i =0; i <objs.length();i++) {
				JSONTokener newtokener = new JSONTokener(objs.get(i).toString());
				JSONObject newJsonObject = new JSONObject(newtokener);
				keywords[i] = newJsonObject.get("keyword").toString();
				TFs[i] = Double.parseDouble(newJsonObject.get("tf").toString());
			}
		} catch (JSONException e) {
			e.printStackTrace();
		}
		// create hashmap
		HashMap<String, Double> keywordsWithIDFs = Utility.createHashMap(keywords, TFs);
		// DEBUG
		if(keywordsWithIDFs.size()!=0){
			System.out.println("\nActivityManager --  getTFsFromDatabase end");
			for (Map.Entry entry : keywordsWithIDFs.entrySet()) {
				System.out.printf("				"+entry.getKey()+" "+ entry.getValue());
			}	
		}
		else{
			System.out.println("\nActivityManager --  getTFsFromDatabase keywordsWithIDFs.size()==0");
		}
		return keywordsWithIDFs; 
	}	
	public static TreeMap<Integer, String> GetActivityDescription(int startID){
		TreeMap<Integer, String> desMap = new TreeMap<Integer, String>();
		try {
			// assess a php page
			String urlParameters = "op=getActivityDescription&id=" + startID;
			String request =  "http://localhost/ActivitySuggestion/activityHandler.php";
			String jsonString= Utility.getJsonFromDatabase( request,  urlParameters, false );
			// parsing json
			JSONTokener tokener = new JSONTokener(jsonString);
			JSONObject jsonObject = new JSONObject(tokener);
			JSONArray objs =  jsonObject.getJSONArray("objs");
			if(objs.length()==0) return null;
			// parsing json
			for (int i =0; i <objs.length();i++) {
				JSONTokener newtokener = new JSONTokener(objs.get(i).toString());
				JSONObject newJsonObject = new JSONObject(newtokener);
				desMap.put(new Integer( newJsonObject.get("id").toString() ), newJsonObject.get("description").toString());
			}
		} catch (JSONException e) {
			e.printStackTrace();
		}
		
		return desMap; 
	}
}
