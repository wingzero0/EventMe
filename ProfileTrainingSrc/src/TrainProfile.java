import databaseHandler.UserJoinedActivityManager;


public class TrainProfile {
	public static void main(String[] args){
		/**
		 * 	One part of the system
		 *  1. read acticle from database given user id
		 * **/
		// read all activity description of a user
		UserJoinedActivityManager userJoinedActivityManager = new UserJoinedActivityManager(1);// user id  //*****
		userJoinedActivityManager.getAllActivityTFIDF(1);
	} 
}
