import java.io.BufferedReader;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.nio.ByteBuffer;
import java.nio.charset.Charset;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.util.ArrayList;
import java.util.List;

import org.htmlcleaner.CleanerProperties;


public abstract class SiteCrawler {
	/**
	 * @param args
	 */
	private CleanerProperties props;
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
	public abstract void ArchiveMasterList();
}
