import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.Writer;
import java.nio.ByteBuffer;
import java.nio.charset.Charset;
import java.nio.charset.StandardCharsets;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.util.ArrayList;
import java.util.HashSet;
import java.util.Iterator;
import java.util.List;
import java.util.Set;

import org.htmlcleaner.CleanerProperties;


public abstract class SiteCrawler {
	private CleanerProperties props;
	public String baseURL = "";
	public String archiveMasterListPath = "";
	public String masterListPath = "";
	public String articlePath = "";
	static String ReadFile(String path, Charset encoding) throws IOException {
		byte[] encoded = Files.readAllBytes(Paths.get(path));
		return encoding.decode(ByteBuffer.wrap(encoded)).toString();
	}
	public static List<String> ReadLines(String path){
		List<String> lines = new ArrayList<String>();
		BufferedReader br;
		try {
			br = new BufferedReader(new InputStreamReader(new FileInputStream(path), "UTF-8") );
			String line;
			while ((line = br.readLine()) != null) {
			   // process the line.
				lines.add(line);
			}
			br.close();
		} catch (FileNotFoundException e) {
			// TODO Auto-generated catch block
			System.err.println("File :" + path + " does not exist\nSkipping from reading it\n");
		} catch (IOException e){
			e.printStackTrace();
		}
		
		return lines;
	} 
	/**
	 * Create one cleaner properties for html cleaner.
	 * @param args 
	 * @return CleanerProperties
	 */
	public CleanerProperties GetCleanerProperties() {
		if (this.props == null){
			props = new CleanerProperties();
			// set some properties to non-default values
			props.setTranslateSpecialEntities(true);
			props.setTransResCharsToNCR(true);
			props.setOmitComments(true);
		}
		return props;
	}
	public abstract List<String> GetNewMasterList();
	public abstract void CrawlMasterList();
	
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
}
