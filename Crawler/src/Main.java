
public class Main {
	/**
	 * @param args
	 * The main function will provide two parts of utility for crawling.
	 * First one is preparing the updated document list. It will be used for the second part.    
	 * Second one is crawling the content of each document list in first part.
	 * 
	 * sample usage:
	 * 	java Main IACM archiveMasterList //program will archive the previous IACM master list.
	 *  java Main IACM updateMasterList // program will download the new master list except in items already in archive
	 *  java Main IACM crawlDoc // program will crawl the doc by master list
	 * 
	 */
	public static void main(String[] args) {
		SiteCrawler crawler = null;
		if (args.length >= 2){
			if (args[1].equals("IACM")){
				System.out.println("IACM selected");
				crawler = new IACMCrawler();
			}else if (args[1].equals("Qoos")){
				System.out.println("Qoos selected");
				crawler = new QoosCrawler();
			}else{
				System.out.println("No source select");
				return;
			}
			
			if (args[0].equals("updateMasterList")){
				System.out.println("updating master list");
				crawler.GetNewMasterList();
			}else if (args[0].equals("crawlDoc")){
				System.out.println("crawling doc");
				crawler.CrawlMasterList();
			}else if (args[0].equals("archiveMasterList")){
				System.out.println("Archiving");
				crawler.ArchiveMasterList();
			}
		}
	}
}
