import java.io.BufferedWriter;
import java.io.FileOutputStream;
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
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.htmlcleaner.CleanerProperties;
import org.htmlcleaner.HtmlCleaner;
import org.htmlcleaner.PrettyXmlSerializer;
import org.htmlcleaner.TagNode;


/**
 * @author wingzero
 * first version, crawling the first 5 master page form http://events.qoos.com/?city=1&period=8
 * It ignores category of event. All event will be held in upcoming 3 months 
 */
public class QoosCrawler extends SiteCrawler {
	public QoosCrawler(){
		this.baseURL = "http://events.qoos.com/";
		this.archiveMasterListPath = "qoosArchiveMasterList.txt";
		this.masterListPath = "qoosMasterList.txt";
		this.articlePath = "ArticleTmp/Qoos/";
	}
	public List<String> GetNewMasterList(){
		// get the list from EventListPage of IACM
		List<String> masterList = this.ParsingEventListPage();
		HashSet<String> masterHashSet = new HashSet<String>();
		masterHashSet.addAll(masterList);
		
		// read the list from Archive
		List<String> archiveList = SiteCrawler.ReadLines(archiveMasterListPath);
		masterHashSet.removeAll(archiveList);
		
		Writer writer = null;

		try {
		    writer = new BufferedWriter(new OutputStreamWriter(
		          new FileOutputStream(masterListPath), StandardCharsets.UTF_8));
			Iterator<String> it  = masterHashSet.iterator();
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
	 * This function will parse the master list from http://events.qoos.com/?city=1&period=8
	 * @return the list of link of event page
	 */
	private List<String> ParsingEventListPage(){
		String htmlTagAttName = "class";
		String htmlTagAttVal = "qoos_event_item";
		
		List<String> masterList = new ArrayList<String>();
		
		CleanerProperties props = GetCleanerProperties();
		for (int i = 1;i<= 5;i++){
			String masterListURL = "http://events.qoos.com/?city=1&period=8&page=" + Integer.toString(i);
			
			// do parsing
			TagNode tagNode = null;
			URL u = null;
			try {
				u = new URL(masterListURL);
				HtmlCleaner hc = new HtmlCleaner(props);
				tagNode = hc.clean(u);
			} catch (MalformedURLException e){
				e.printStackTrace();
				continue;
			}catch (IOException e1) {
				// TODO Auto-generated catch block
				e1.printStackTrace();
				continue;
			}
			
			TagNode[] myNodes = tagNode.getElementsByAttValue(htmlTagAttName, htmlTagAttVal, true, false);
			for (int j = 0; j < myNodes.length; j++){
				TagNode[] hrefs = myNodes[j].getElementsByName("a", true);
	            
				if (hrefs.length > 0){
		            String hrefString = hrefs[0].getAttributeByName("href");
		        	System.out.println(baseURL + hrefString);
					masterList.add(baseURL + hrefString);
				}
			}
		}
		return masterList; 
	}
	
	public TagNode ActivityPageParser(URL u, String filename){
		String htmlTagAttName = "class";
		String htmlTagAttVal = "qoos_event_left";
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
		
		TagNode[] myNodes = tagNode.getElementsByAttValue(htmlTagAttName, htmlTagAttVal, true, false);
		TagNode[] urlNodes = urlNode.getElementsByAttValue("id", "sourceLink", true, false);

		if (myNodes.length <= 0){
			return null;
		}
 
        try {
        		myNodes[0].insertChild(0, urlNodes[0]);
            new PrettyXmlSerializer(props).writeToFile(myNodes[0], 
                   filename + ".xml", "utf-8"); 
        } catch (IOException e) { 
            // TODO Auto-generated catch block 
            e.printStackTrace(); 
        }
		System.out.println("Done");
		
		return myNodes[0];
	}
	@Override
	public void CrawlMasterList() {
		// TODO Auto-generated method stub
		List<String> masterList = SiteCrawler.ReadLines(masterListPath);
		Iterator<String> it = masterList.iterator();
		while (it.hasNext()){
			String u = it.next(); // u for url
			URL url;
			Pattern pattern = Pattern.compile("([^/]+).html");
			try {
				url = new URL(u);
				Matcher matcher = pattern.matcher(url.toString());
				if (matcher.find()) {
					System.out.println(matcher.group(1));
					String filename = articlePath + "/" + matcher.group(1);
					this.ActivityPageParser(url, filename);
					Thread.sleep((long)(Math.random() * 2000));
				}
			} catch (MalformedURLException e) {
				// TODO Auto-generated catch block
				System.err.println(u + " is not a normal url");
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
	}
}
