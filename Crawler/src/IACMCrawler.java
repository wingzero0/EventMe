import java.io.BufferedWriter;
import java.io.FileOutputStream;
import java.io.FileWriter;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.io.Writer;
import java.net.MalformedURLException;
import java.net.URL;
import java.nio.charset.StandardCharsets;
import java.util.ArrayList;
import java.util.HashSet;
import java.util.Iterator;
import java.util.List;
import java.util.Set;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.htmlcleaner.CleanerProperties;
import org.htmlcleaner.HtmlCleaner;
import org.htmlcleaner.PrettyXmlSerializer;
import org.htmlcleaner.TagNode;

/**
 * @author macbookpro
 * This class provide the function to crawl the event content of IACM
 * You can get the recent links from IACM by calling GetNewMasterList.
 * 	It only download the recent links that we have never seen before by comparing the 
 * 	links in archive, which had been cached from the past. 
 * You can download the content page by calling ActivityPageParser
 * You can add the current master list to archive by call ArchiveMasterList
 * 
 */
public class IACMCrawler extends SiteCrawler {
	
	public static final String baseURL = "http://www.iacm.gov.mo";
	public String archiveMasterListPath = "IACMArchiveMasterList.txt";
	public String masterListPath = "IACMMasterList.txt";
	public String articlePath = "ArticleTmp/IACM/";
	public List<String> GetNewMasterList(){
		try {
			URL u = new URL("http://www.iacm.gov.mo/c/activity/list/");
			String targetID = "ctl00_ContentPlaceHolder1_GridView1";
			return GetNewMasterList(u, targetID);
		} catch (MalformedURLException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			return new ArrayList<String>(); // return empty object
		}
	}
	public List<String> GetNewMasterList(URL u, String targetID){
		// get the list from EventListPage of IACM
		List<String> masterList = this.ParsingEventListPage(u, targetID);
		Iterator<String> it  = masterList.iterator();
		HashSet<String> masterHashSet = new HashSet<String>();
		while (it.hasNext()){
			masterHashSet.add(it.next());
		}
		
		// read the list from Archive
		List<String> archiveList = SiteCrawler.ReadLines(archiveMasterListPath);
		it  = archiveList.iterator();
		HashSet<String> archiveHashSet = new HashSet<String>();
		while (it.hasNext()){
			archiveHashSet.add(it.next());
		}
		
		// remove the old links form masterHashSet. save the new links to file and return
		masterHashSet.removeAll(archiveHashSet);
		
		Writer writer = null;

		try {
		    writer = new BufferedWriter(new OutputStreamWriter(
		          new FileOutputStream(masterListPath), StandardCharsets.UTF_8));
		    it  = masterHashSet.iterator();
			while (it.hasNext()){
				writer.write(it.next() + "\n");
			}
		} catch (IOException ex) {
		  // report
			ex.printStackTrace();
		} finally {
		   try {writer.close();} catch (Exception ex) {}
		}
		
		return masterList;
	}
	/**
	 * @param u the URL of the Event List 
	 * @param targetID the html tag ID that only contain the block of event list
	 * @return list of URL that point to the Event page
	 */
	private List<String> ParsingEventListPage(URL u, String targetID){
		List<String> masterList = new ArrayList<String>();
		
		CleanerProperties props = GetCleanerProperties();

		// do parsing
		TagNode tagNode = null;
		
		try {
			HtmlCleaner hc = new HtmlCleaner(props); 
			tagNode = hc.clean(u);
		} catch (IOException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
			return masterList; // empty
		}
		
		if (targetID != ""){
			TagNode[] myNodes = tagNode.getElementsByAttValue("id", targetID, true, false);
			if (myNodes.length <= 0){
				return masterList; // empty
			}
		    // PrettyXmlSerializer serializer = new PrettyXmlSerializer(props);
            // serializer.writeToFile(myNodes[0], "masterList.xml", "utf-8");
            TagNode[] hrefs = myNodes[0].getElementsByName("a", true);
            
            String hrefString;
            String host = u.getHost();
            for (int i = 0;i< hrefs.length;i++){
        		//hrefString = serializer.getAsString(hrefs[i]);
        		
        		hrefString = hrefs[i].getAttributeByName("href");
        		System.out.println(IACMCrawler.baseURL + hrefString);
				masterList.add(IACMCrawler.baseURL + hrefString);
				
            }
		}

		return masterList; 
	}
	public void CrawlMasterList(){
		this.CrawlMasterList(articlePath);
	}
	public void CrawlMasterList(String folderPath){
		List<String> masterList = SiteCrawler.ReadLines(masterListPath);
		Iterator<String> it = masterList.iterator();
		while (it.hasNext()){
			String u = it.next(); // u for url
			URL url;
			Pattern pattern = Pattern.compile("/detail/(.+),(.+)");
			try {
				url = new URL(u);
				Matcher matcher = pattern.matcher(url.toString());
				if (matcher.find()) {
					System.out.println(matcher.group(1));
					String filename = folderPath + "/" + matcher.group(1);
					this.ActivityPageParser(url, filename);
					Thread.sleep((long)(Math.random() * 2000));
				}
			} catch (MalformedURLException e) {
				// TODO Auto-generated catch block
				// e.printStackTrace();
				System.err.println(u + " is not a normal url");
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
	}
	public TagNode ActivityPageParser(URL u, String filename){
		CleanerProperties props = GetCleanerProperties();

		// do parsing
		TagNode tagNode = null;
		TagNode urlNode = null; // the source link, which will be add to the file 
		try {
			HtmlCleaner hc = new HtmlCleaner(props); 
			tagNode = hc.clean(u);
			urlNode = hc.clean("<a id='sourceLink' href='" + u.toString() + "'>sourceLink</a>");
		} catch (IOException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		}
		
		TagNode[] myNodes = tagNode.getElementsByAttValue("id", "detail", true, false);
		TagNode[] urlNodes = urlNode.getElementsByAttValue("id", "sourceLink", true, false);
//		String info = new String();
		if (myNodes.length <= 0){
			return null;
		}
		
		// serialize to xml file 
        try {
        		myNodes[0].insertChild(0, urlNodes[0]);
            new PrettyXmlSerializer(props).writeToFile(myNodes[0], 
                   filename + ".xml", "utf-8"); 
        } catch (IOException e) { 
            // TODO Auto-generated catch block 
            e.printStackTrace(); 
        } 
        
//		for (int i = 0; i < myNodes.length; i++) {
//			TagNode tag = myNodes[i];
//			String tmp = tag.getText().toString();
//			info += tmp;
//		}

//		FileWriter fw;
//		try {
//			fw = new FileWriter(filename + ".txt");
//		} catch (IOException e) {
//			// TODO Auto-generated catch block
//			e.printStackTrace();
//			System.out.println("writing " + filename + " fail");
//			return myNodes[0];
//		}
//		
//		BufferedWriter bw = new BufferedWriter(fw);
//		
//		try {
//			//System.out.println(info);
//			bw.write(info);
//			bw.close();
//		} catch (IOException e) {
//			// TODO Auto-generated catch block
//			e.printStackTrace();
//			System.out.println("writing " + filename + " fail");
//		}
		

		System.out.println("Done");
		
		return myNodes[0];
	}
	/**
	 * It will do the archive operation.
	 * It add the master list into archive list.
	 */
	public void ArchiveMasterList(){
		List<String> masterList = SiteCrawler.ReadLines(this.masterListPath);
		List<String> archiveList = SiteCrawler.ReadLines(this.archiveMasterListPath);
		
		Iterator<String> it = masterList.iterator();
		Set<String> masterHashSet = new HashSet<String>();
		while (it.hasNext()){
			masterHashSet.add(it.next());
		}

		it  = archiveList.iterator();
		Set<String> archiveHashSet = new HashSet<String>();
		while (it.hasNext()){
			archiveHashSet.add(it.next());
		}
		
		archiveHashSet.addAll(masterHashSet);
		
		Writer writer = null;

		try {
		    writer = new BufferedWriter(new OutputStreamWriter(
		          new FileOutputStream(this.archiveMasterListPath), StandardCharsets.UTF_8));
		    it  = archiveHashSet.iterator();
			while (it.hasNext()){
				writer.write(it.next() + "\n");
			}
		} catch (IOException ex) {
		  // report
			ex.printStackTrace();
		} finally {
		   try {writer.close();} catch (Exception ex) {}
		}
	}
};