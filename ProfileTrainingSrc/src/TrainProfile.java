import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import databaseHandler.ActivityManager;
import databaseHandler.KeywordManager;
import databaseHandler.UserJoinedActivityManager;


public class TrainProfile {
	public static void main(String[] args){
		/**
		 * 	One part of the system
		 *  1. read acticle from database given user id
		 * **/
		// read all activity description of a user
		UserJoinedActivityManager userJoinedActivityManager = new UserJoinedActivityManager(Integer.parseInt(args[0]));// user id  //*****
		int activityID[] = userJoinedActivityManager.getAllActivityID();
		ActivityManager am;
		KeywordManager km;
		List<Map<Integer, Double>> allkeywords = new ArrayList<Map<Integer, Double>>();
		Map<Integer, Double> TFIDF;
		for (int i= 0;i<activityID.length;i++){
			// get tf;
			am = new ActivityManager(activityID[i]);
			HashMap<Integer, Double> TFS = am.getTFsFromDatabaseReturnKeywordID();
			int keywordIDs[] = new int[TFS.size()];
			double tfs[] = new double[TFS.size()];
			int j = 0;
			for (Map.Entry<Integer, Double> entry : TFS.entrySet()) {
				keywordIDs[j] = entry.getKey().intValue();
				tfs[j] = entry.getValue().doubleValue();
				j++;
			}
			
			// get idf;
			km = new KeywordManager();
			double[] idfs = km.getIDFsFromDatabase(keywordIDs);
			
			// merge tf-idf;
			TFIDF = new HashMap<Integer, Double>();
			j = 0;
			for (Map.Entry<Integer, Double> entry : TFS.entrySet()) {
				TFIDF.put(entry.getKey(), new Double(tfs[j] * idfs[j])); // I hope the order in TFS will not change.
				j++;
			}
			
			allkeywords.add(TFIDF);
			System.out.println("\nTrainProfile --- TF_IDFs");
			for (Map.Entry<Integer, Double> entry : TFIDF.entrySet()) {
				System.out.printf("				"+entry.getKey()+" "+ entry.getValue() );
				j++;
			}
		}
	} 
}
