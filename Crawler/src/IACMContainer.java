import java.net.MalformedURLException;
import java.net.URL;
import java.sql.Date;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.apache.commons.lang3.StringEscapeUtils;
import org.htmlcleaner.CleanerProperties;
import org.htmlcleaner.HtmlCleaner;
import org.htmlcleaner.PrettyXmlSerializer;
import org.htmlcleaner.TagNode;
import org.htmlcleaner.XmlSerializer;

public class IACMContainer extends EventContainer {
	private TagNode tagNode;
	private String urlPrefix;
	private String pureText;
	public IACMContainer(String originalContentXML){
		this.originalContent = originalContentXML;
		this.tagNode = null;
		this.urlPrefix = "http://www.iacm.gov.mo";
	}
	public IACMContainer(TagNode node){
		this.tagNode = node;
		this.urlPrefix = "http://www.iacm.gov.mo";
	}
	public boolean Parse(){
		CleanerProperties props = new CleanerProperties();
		
		// set some properties to non-default values
		props.setTranslateSpecialEntities(true);
		props.setTransResCharsToNCR(true);
		props.setOmitComments(true);

		if (this.tagNode == null){					
			HtmlCleaner hc = new HtmlCleaner(props); 
			this.tagNode = hc.clean(this.originalContent);	
		}
		
		if (this.originalContent.isEmpty()){
			this.originalContent = new PrettyXmlSerializer(props).getAsString(tagNode);
		}
		
		this.pureText = this.tagNode.getText().toString();
		this.ParesName();
		this.ParseDescription();
		this.ParseStartDate();
		this.ParseEndDate();
		this.ParsePoster();
		System.out.println(this.originalContent);
		
		return false;
	}
	private boolean ParsePoster(){
		//System.out.println(this.tagNode.toString());
		TagNode[] hrefs = tagNode.getElementsByName("img", true);
		//XmlSerializer serializer =  new PrettyXmlSerializer(props);
		if (hrefs.length > 0){
			String url = hrefs[0].getAttributeByName("src");
			url = StringEscapeUtils.unescapeXml(url);
			//System.out.println(serializer.getAsString(url));
			System.out.println(urlPrefix + url);
			try {
				this.poster = new URL(urlPrefix + url);
				return true;
			} catch (MalformedURLException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
				return false;
			}
		}
		return false;
	}
	private boolean ParesName(){
		TagNode[] headings = this.tagNode.getElementsByName("h1", true);
		if (headings.length > 0){
			this.name = headings[0].getText().toString();
			return true;
		}
		return false;
	}
	private boolean ParseDescription(){
		TagNode[] desNode = this.tagNode.getElementsByAttValue("id", "contentDetail", true, false);
		if (desNode.length > 0){
			this.des = desNode[0].getText().toString();
			return true;
		}
		return false;
	}
	private boolean ParseHostName(){
		return false;
	}
	private boolean ParseStartDate(){
		Pattern pattern = Pattern.compile("開始日期 :((\n\\s*)*)(.*)(\n\\s*)");
		Matcher matcher = pattern.matcher(this.pureText);
		if (matcher.find()) {
			System.out.println(matcher.group(3));
			this.startDate = matcher.group(3);
			return true;
		}
		return false;
	}
	private boolean ParseEndDate(){
		Pattern pattern = Pattern.compile("結束日期 :((\n\\s*)*)(.*)(\n\\s*)");
		Matcher matcher = pattern.matcher(this.pureText);
		if (matcher.find()) {
			System.out.println(matcher.group(3));
			this.endDate = matcher.group(3);
			return true;
		}
		return false;
	}
}
