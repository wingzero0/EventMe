import java.net.URL;
import java.sql.Date;


public class EventContainer {
	public String name;
	public String des; // description
	public String hostname;
	public String location;
	public double[] GPS; // length = 2
	public URL poster;
	public String originalContent;
	//public Date startDate;
	public String startDate;
	public String endDate;
	public EventContainer(){
		this.name = new String();
		this.des = new String();
		this.hostname = new String();
		this.location = new String();
		this.GPS = new double[2];
		// URL is not initially
		this.originalContent = new String();
		this.startDate = null;
	}
}
