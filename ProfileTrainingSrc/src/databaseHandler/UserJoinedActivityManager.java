package databaseHandler;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONTokener;

public class UserJoinedActivityManager {

	int userIndex;
	public UserJoinedActivityManager(int userIndex) {
		this.userIndex = userIndex;
	}

	public ArrayList<String> getAllActivityWhichIsReadBefore() {
		ArrayList<String> list = new ArrayList<String>();
		// hope		// input activity id
		// ouput activity description, name or whatever.
		return list;
	}

	public int[] getAllActivityID(){
		try {
			String urlParameters = "op=getActivityIDByUID&uid="+this.userIndex;
			String request =  "http://localhost/ActivitySuggestion/userBrowsingRecordHandler.php";
			String jsonString= Utility.getJsonFromDatabase( request,  urlParameters, false );

			// parsing json
			JSONTokener tokener = new JSONTokener(jsonString);
			JSONObject jsonObject = new JSONObject(tokener);
			if (jsonObject.getInt("ret") != 1){
				System.err.println("userBrowsingRecordHandler.php error" + jsonObject.getString("error"));
			}else{
				JSONArray objs =  jsonObject.getJSONArray("activityID");
				int activity[] = new int[objs.length()]; 
				for (int i =0; i <objs.length();i++) {
					activity[i] = objs.getInt(i);
				}
				return activity;
			}
		}catch (JSONException e) {
			e.printStackTrace();
		} 
		return null;
	}

}
