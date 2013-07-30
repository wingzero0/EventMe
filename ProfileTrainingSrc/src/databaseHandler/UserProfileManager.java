package databaseHandler;

import java.io.UnsupportedEncodingException;
import java.net.URLEncoder;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONTokener;

public class UserProfileManager {

	int userIndex;
	List< List< Integer >> MaximalfrequentItemsets;
	List<Double> MaximalfrequentItemsetsSupport;
	
	
	public UserProfileManager(int userIndex) {
		this.userIndex = userIndex;
	}

	public void uploadUserProfileToDatabase(List< List< Integer >> MaximalfrequentItemsets,  List<Double> MaximalfrequentItemsetsSupport){
		// input user id , keyword id , weight
		// output ret 1
		//http://140.112.29.228/ActivitySuggestion/profileHandler.php?op=insert&userID=2&num=2&keywordID0=3&keywordID1=2&weight=11.11
		  try {
				// DEBUG
				System.out.println("UserProfileManager --  uploadUserProfileToDatabase start");
				for(int j = 0; j<MaximalfrequentItemsets.size();j++){
				  List< Integer > MaximalfrequentItemset = MaximalfrequentItemsets.get(j);
				  String urlParameters = "op=insert&num="+MaximalfrequentItemset.size()+"&userID="+userIndex;
				  for(int i = 0; i < MaximalfrequentItemset.size();i++){
					  int key = MaximalfrequentItemset.get(i);
					  String keyword = "keywordID"+i+"="+key;
					  urlParameters += "&" + keyword;
				  }
				  String weight = "weight="+MaximalfrequentItemsetsSupport.get(j);
				  urlParameters += "&" + weight;
			   
				  String request =  "http://140.112.29.228/ActivitySuggestion/profileHandler.php";
				  String jsonString = Utility.getJsonFromDatabase( request,  urlParameters, false);
				
				  // parsing json
				  JSONTokener tokener = new JSONTokener(jsonString);
				  JSONObject jsonObject = new JSONObject(tokener);
				  if(jsonObject.get("ret").toString().equals("1")){
						// DEBUG
						System.out.println("UserProfileManager --  uploadUserProfileToDatabase end");
				  }
				}
			} catch (JSONException e) {
				e.printStackTrace();
			}
		  
		 this.MaximalfrequentItemsets = MaximalfrequentItemsets;
		 this.MaximalfrequentItemsetsSupport = MaximalfrequentItemsetsSupport;		 
	}
	
	public void getUserProfileToDatabase(){
		// input user id 
		// output keyword id, weight
		   try {
				// DEBUG
				System.out.println("\nUserProfileManager --  getUserProfileToDatabase start");
				
				// assess a php page
				String urlParameters = "op=get&userID="+userIndex;
				String request =  "http://140.112.29.228/ActivitySuggestion/profileHandler.php";
				String jsonString= Utility.getJsonFromDatabase( request,  urlParameters,true );
				// parsing json
				JSONTokener tokener = new JSONTokener(jsonString);
				JSONObject jsonObject = new JSONObject(tokener);
				JSONArray objs =  jsonObject.getJSONArray("objs");
				if(objs.length()!=0){
					MaximalfrequentItemsets = new ArrayList< List< Integer >>();
					MaximalfrequentItemsetsSupport = new ArrayList< Double>();
					int profileID = -1;
					for (int i =0; i <objs.length();i++) {
						
						JSONTokener newTokener = new JSONTokener(objs.get(i).toString());
						JSONObject newJsonObject = new JSONObject(newTokener);
						int temp = Integer.parseInt(newJsonObject.get("profileID").toString());
						int keywordID = Integer.parseInt(newJsonObject.get("keywordID").toString());
						double weight = Double.parseDouble(newJsonObject.get("weight").toString());
						
						if(profileID!=temp){
							List< Integer> tempList = new ArrayList< Integer>();
							MaximalfrequentItemsets.add(tempList);
							MaximalfrequentItemsetsSupport.add(weight);
						}
						List<Integer> operatingList =MaximalfrequentItemsets.get(MaximalfrequentItemsets.size()-1);
						operatingList.add(keywordID);
						profileID = temp;
					}
					// DEBUG
					System.out.println("\nUserProfileManager --  getUserProfileToDatabase end");
				    for(int i =0; i< MaximalfrequentItemsets.size(); i++) {
				    	System.out.println("		MaximalfrequentItemsets " + i);
				    	List< Integer > pattern = MaximalfrequentItemsets.get(i);
				    	System.out.println("      		" + pattern + "  support: "+ MaximalfrequentItemsetsSupport.get(i));
				    }
				}
			} catch (JSONException e) {
				e.printStackTrace();
			}
		}	
}
