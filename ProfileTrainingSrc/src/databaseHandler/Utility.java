package databaseHandler;

import java.io.BufferedReader;
import java.io.DataOutputStream;
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

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONTokener;

public class Utility {
	
	public static HashMap<String, Double> createHashMap(String[] keywords, Double[] values){
		HashMap<String, Double> map =  new HashMap<String, Double>();
		for(int i =0; i<keywords.length; i++){
			map.put(keywords[i], values[i]);
		}
		return map;
	}

	public static String getJsonFromDatabase(String request, String urlParameters, Boolean getMethod ){
		String line = "";
		System.out.println("\n");	
		System.out.println(request+"?"+urlParameters);		
		try {
			if(getMethod == true){
				// GET
				URL url = new URL(request+"?"+urlParameters);
				HttpURLConnection connection = (HttpURLConnection) url.openConnection();
				BufferedReader in = new BufferedReader(new InputStreamReader(connection.getInputStream()));
				line=in.readLine();
				in.close();
				connection.disconnect();		
			}
			else{
				// POST
				URL url = new URL(request); 
				HttpURLConnection connection = (HttpURLConnection) url.openConnection();           
				connection.setDoOutput(true);
				connection.setDoInput(true);
				connection.setInstanceFollowRedirects(false); 
				connection.setRequestMethod("POST"); 
				DataOutputStream wr = new DataOutputStream(connection.getOutputStream());
				wr.writeBytes(urlParameters);
				wr.flush();
				wr.close();
				//
				BufferedReader in = new BufferedReader(new InputStreamReader(connection.getInputStream()));
				line=in.readLine();
				//	System.out.println(line);
				//     while((line=in.readLine())!= null){
				//       System.out.println(line);
				//  }
				in.close();		
				// DEBUG
				connection.disconnect();
				System.out.println("\n");	
				System.out.println(request+"?"+urlParameters);		
			}
		} catch (IOException e) {
			e.printStackTrace();
		}
		 return line;
   }
	   

	public static String[] getAllkeywordsFromRecord(List<Map<String, Double>> allkeywords ){
		List<String> temp = new ArrayList<String>();
		for(Map<String, Double> keywords:allkeywords){
			String[] row = keywords.keySet().toArray(new String[0]);
			for(String keyword:row)
				temp.add(keyword);
		  	}
		   	return temp.toArray(new String[0]);
	   }

	public static HashMap<Integer,String> getMapWithIndexToKeyword(String[] keywordsIDList){
		
		HashMap<Integer,String> map =  new HashMap<Integer,String>();			
		   try {
			   //DEBUG
			   System.out.println("\nUtility -- getMapWithIndexToKeyword start");
			   // assess a php page
			   String urlParameters = "op=getTermByID&num="+keywordsIDList.length;
			   for(int i =0; i<keywordsIDList.length; i++){
				   String keyword = "id"+i+"="+keywordsIDList[i];
				   urlParameters += ("&"+keyword);
			   }
			   String request =  "http://140.112.29.228/ActivitySuggestion/keywordHandler.php";
			   String jsonString= Utility.getJsonFromDatabase( request,  urlParameters, false );
			   System.out.println(jsonString);
			   // parsing json
			   JSONTokener tokener = new JSONTokener(jsonString);
			   JSONObject jsonObject = new JSONObject(tokener);
			   JSONArray objs =  jsonObject.getJSONArray("val");
			   for (int i =0; i <objs.length();i++) {
				   String keyword = objs.getString(i).toString();
				   int id = Integer.parseInt(keywordsIDList[i]);
				   map.put(id, keyword);
				   }
			   // DEBUG
			   if(map.size()!=0){
				   System.out.println("\nUtility -- getMapWithIndexToKeyword end");
				   for (Map.Entry entry : map.entrySet()) {
					   System.out.printf("				"+entry.getKey()+" "+ entry.getValue());
				   }
	   			}
		} catch (JSONException e) {
			e.printStackTrace();	   
		} 
		return map;
	   }
	   
	public static String[] getDistinctKeyword(String[] keywords){
		HashMap<String, Integer> distinct = new HashMap<String, Integer>();
		for (String s:keywords){
			distinct.put(s, new Integer(1)); // remove duplicate
		}
		return distinct.keySet().toArray(new String[0]);
		
	}
	   
}
