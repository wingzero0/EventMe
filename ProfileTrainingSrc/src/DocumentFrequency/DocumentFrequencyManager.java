package DocumentFrequency;
import java.io.BufferedReader;
import java.io.DataOutputStream;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.io.Reader;
import java.io.UnsupportedEncodingException;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLEncoder;
import java.text.DecimalFormat;
import java.text.ParseException;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.Iterator;
import java.util.LinkedHashMap;
import java.util.LinkedList;
import java.util.List;
import java.util.Map;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.htmlcleaner.CleanerProperties;
import org.htmlcleaner.HtmlCleaner;
import org.htmlcleaner.TagNode;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONTokener;


public class DocumentFrequencyManager {
       
	HashMap<String,Integer> hashMap_ActicleCountWithTheWord = null;
	
	public DocumentFrequencyManager() {	}
	
	   public  Double[] getTFs(String acticle, String[] segs){
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
	        return TFs;
	    }  
	 
	   public  void loadHashMapOfActicleCountWithTheWord(String filename) throws IOException{
		   
		   // there is at least one word 我 in this file.
	    	BufferedReader br = new BufferedReader(new FileReader(filename));
	    	hashMap_ActicleCountWithTheWord = new HashMap<String,Integer>();    
	    	
			String line;
			while ((line = br.readLine()) != null) {
				String key = line.split(" ")[0];
				int value = Integer.parseInt(line.split(" ")[1]);
				hashMap_ActicleCountWithTheWord.put(key, value);
			}
	   }
	   
	   public  Double[] getIDFs(String standardWord, String[] segs){
		   
	    	// find out the number of total acticle and the number of total acticle which contain the words
	        for(int i =0;i<segs.length;i++){
	        	String s = segs[i];
	        	if(hashMap_ActicleCountWithTheWord.get(s)==null){
	        		int theCount = getActicleCountContainTheWords(s);
	        		hashMap_ActicleCountWithTheWord.put(s, theCount);
	        		System.out.println(s + "  get result from website  " + theCount);
	        	}else{
	        		System.out.println(i +"-th");
	        	}
	        }
	        
	    	// compute IDF
	        Double[] IDFs=new Double[segs.length];
	        for(int i =0;i<segs.length;i++){
	        	IDFs[i] = Math.log((double)hashMap_ActicleCountWithTheWord.get(standardWord)/(hashMap_ActicleCountWithTheWord.get(segs[i])+1));
	        	System.out.println("IDFs  " + IDFs[i]);
	        }
	        return IDFs;
	    }
	   
	   public  void saveNewHashMapOfActicleCountWithTheWord(String filename) throws IOException{	
		   
		   List<String> keys = new ArrayList<String>(hashMap_ActicleCountWithTheWord.keySet());
		    // output
	        PrintWriter out = new PrintWriter(new FileWriter(filename), true);
	        for (int i = 0; i < keys.size(); i++) {
				String key = keys.get(i);
				out.printf(key+" "+ hashMap_ActicleCountWithTheWord.get(key));
				if(i< keys.size()-1) out.printf("\n");
			}
	        out.close();
	   }
	   
	   public int getActicleCountContainTheWords(String key){
	    	
			//===== Initial =====
			CleanerProperties props = new CleanerProperties();
			props.setTranslateSpecialEntities(true);
			props.setTransResCharsToNCR(true);
			props.setOmitComments(true);
			TagNode tagNode = null;
			try {
				tagNode = new HtmlCleaner(props).clean(
				    new URL("http://www.bing.com/search?q="+key)
				);
			} catch (MalformedURLException e1) {
				e1.printStackTrace();
			} catch (IOException e1) {
				e1.printStackTrace();
			}

			//===== get infor from the web =====
			TagNode[] myNodes = tagNode.getElementsByAttValue("class", "sb_count", true, false); 
			if (myNodes.length == 0){
				return -1;
			}
			String tempC =  myNodes[0].getText().toString().split(" 個結果")[0];

			// transfer string to double
			int  acticleCountContainTheWords = 0;
			DecimalFormat df = new DecimalFormat( "#,###,###,##" );
			try {
				acticleCountContainTheWords =  df.parse(tempC).intValue();
			} catch (ParseException e) {}
	   
			//System.out.println("in IDF s" + acticleCountContainTheWords);
			 
		   /*
		   String google = "http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=";
		    String search = key;
		    String charset = "UTF-8";

		    URL url;
		    Reader reader = null;
			try {
				url = new URL(google + URLEncoder.encode(search, charset));
				reader = new InputStreamReader(url.openStream(), charset);
			} catch (MalformedURLException e) {}
			catch (UnsupportedEncodingException e) {} 
			catch (IOException e) {}
		    GoogleResults results = new Gson().fromJson(reader, GoogleResults.class);
		    
		    double  acticleCountContainTheWords = results.getResponseData().getResults().size();
		    */
	    	return acticleCountContainTheWords;
	    }
	    
	    public Double[] getTF_IDFs(String[] segs, Double TFs[], Double IDFs[]){
	        // comput TF_IDF
	        Double[] TF_IDFs= new Double[segs.length];
	        for(int i =0;i<segs.length;i++){
	        	TF_IDFs[i] =TFs[i]*IDFs[i];
	        }
	        
	        return TF_IDFs;

	    }
	    
	   
	    public void uploadKeywordKetToDatabase (){
	    	 //- upload the typist ranking stuff to db. 
	    }
	   
	    public Map<String, Double> DebugDisplay(String[] segs, Double TF_IDFs[]){
	        // put in the map
	        Map<String,Double> mp=new HashMap<String, Double>();
	        for(int i =0;i<segs.length;i++){
	        	mp.put(segs[i], TF_IDFs[i]);
	        }
	    	// sort map
	        Map<String, Double> sortedMap = sortByComparator(mp);
			printMap(sortedMap);
			return sortedMap;
	    }
	    // ********************************** sort the key word ****************************************
	    public Map sortByComparator(Map unsortMap) {
			 
			List list = new LinkedList(unsortMap.entrySet());
	 
			// sort list based on comparator
			Collections.sort(list, new Comparator() {
				public int compare(Object o1, Object o2) {
					return ((Comparable) ((Map.Entry) (o1)).getValue())
	                                       .compareTo(((Map.Entry) (o2)).getValue());
				}
			});
	 
			// put sorted list into map again
	                //LinkedHashMap make sure order in which keys were inserted
			Map sortedMap = new LinkedHashMap();
			for (Iterator it = list.iterator(); it.hasNext();) {
				Map.Entry entry = (Map.Entry) it.next();
				sortedMap.put(entry.getKey(), entry.getValue());
			}
			return sortedMap;
		}
	 
	    public void printMap(Map<String, Double> map){
	    	/*
			for (Map.Entry entry : map.entrySet()) {
				System.out.println("Key : " + entry.getKey() 
	                                   + " Value : " + entry.getValue());
			}
			*/
	    	System.out.println("yoyo \n");
			for (Map.Entry entry : map.entrySet()) {
				System.out.printf(entry.getValue()+" ");
			}
			System.out.println("-------------------------");
			
	    	System.out.println("\n");
			for (Map.Entry entry : map.entrySet()) {
				System.out.printf(entry.getKey()+" ");
			}
			System.out.println("-------------------------");
		}


	    
}
