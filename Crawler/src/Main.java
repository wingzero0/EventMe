import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.nio.charset.StandardCharsets;
import java.util.List;


public class Main {
	/**
	 * @param args
	 * The main function will provide two parts of utility for crawling.
	 * First one is preparing the updated document list. It will be used for the second part. 
	 * (The duplicated document "will" be omitted. The duplicating detection is not implement now.)   
	 * Second one is crawling the content of each document list in first part.
	 */
	public static void main(String[] args) {
		SiteCrawler crawler = null;
		if (args.length >= 2){
			if (args[1].equals("IACM")){
				crawler = new IACMCrawler();
			}else if (args[1].equals("Qoos")){
				crawler = new QoosCrawler();
			}else{
				return;
			}
			
			if (args[0].equals("updateMasterList")){
				// List<URL> masterList =; 
				crawler.GetNewMasterList();
			}else if (args[0].equals("crawlDoc")){
				crawler.CrawlMasterList();
			}else if (args[0].equals("archiveMasterList")){
				crawler.ArchiveMasterList();
			}
//			else if (args[0].equals("parseEvent")){
//				String tmp = SiteCrawler.ReadFile(args[1], StandardCharsets.UTF_8);
//				(new IACMContainer(tmp)).Parse();
//			}
		}
	}
}
