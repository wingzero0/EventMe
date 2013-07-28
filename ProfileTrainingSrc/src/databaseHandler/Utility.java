package databaseHandler;

import java.io.BufferedReader;
import java.io.DataOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.UnsupportedEncodingException;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLEncoder;
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
	   
	   public static HashMap<Integer,String> getMapWithIndexToKeyword(List<String[]> keywordsList){
		   		HashMap<Integer,String> map =  new HashMap<Integer,String>();
		   		try {
		   			for(String[] keywords:keywordsList){
		   				//DEBUG
		   				System.out.println("\nUtility -- getMapWithIndexToKeyword start");
		   				// assess a php page
		   				String urlParameters = "op=getIDs&num="+keywords.length;
		   				for(int i =0; i<keywords.length; i++){
		   					String keyword = "keyword"+i+"="+URLEncoder.encode(keywords[i].toString(),"UTF-8");
		   					urlParameters += ("&"+keyword);
		   				}
		   				String request =  "http://140.112.29.228/ActivitySuggestion/keywordHandler.php";
		   				String jsonString= Utility.getJsonFromDatabase( request,  urlParameters, true );
		   				// parsing json
		   				JSONTokener tokener = new JSONTokener(jsonString);
		   				JSONObject jsonObject = new JSONObject(tokener);
		   				JSONArray objs =  jsonObject.getJSONArray("objs");
		   				for (int i =0; i <objs.length();i++) {
		   					JSONTokener newtokener = new JSONTokener(objs.get(i).toString());
		   					JSONObject newJsonObject = new JSONObject(newtokener);
		   					int temp = Integer.parseInt(newJsonObject.get("id").toString());
		   					if(temp==0){
		   						System.out.println("one of keywords don't exist in database.");
		   					}
		   					else{ 
		   						// if exist , don't put it to map
		   						Boolean exist = false; 
		   						for(Map.Entry entry : map.entrySet()){
		   							if(Integer.parseInt(entry.getKey().toString())==temp){
		   								exist = true;
		   								break;
		   								}
		   						}
		   						if(exist==false) map.put(temp, keywords[i].toString());
		   					}
		   				}
		   			}
	   				// DEBUG
	   				if(map.size()!=0){
	   					System.out.println("\nUtility -- getMapWithIndexToKeyword end");
	   					for (Map.Entry entry : map.entrySet()) {
	   						System.out.printf("				"+entry.getKey()+" "+ entry.getValue());
	   					}	
	   				}
		   		} catch (UnsupportedEncodingException e) {
					e.printStackTrace();
				} catch (JSONException e) {
					e.printStackTrace();
				}
			   return map;
	   }
}
