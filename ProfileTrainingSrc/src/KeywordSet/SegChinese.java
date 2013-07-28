package KeywordSet;
import java.io.IOException;
import java.io.Reader;
import java.io.StringReader;
import java.util.List;

import com.chenlb.mmseg4j.ComplexSeg;
import com.chenlb.mmseg4j.Dictionary;
import com.chenlb.mmseg4j.MMSeg;
import com.chenlb.mmseg4j.Seg;
import com.chenlb.mmseg4j.Word;

public class SegChinese { 
	protected Dictionary dic;
	
	public SegChinese() {
		System.setProperty("mmseg.dic.path", "./exlib/mmseg4j-1.8.5/data/");	
		dic = Dictionary.getInstance();
	}

	protected Seg getSeg() {
		return new ComplexSeg(dic);
	}
	
	public String segWords(String txt, String wordSpilt) throws IOException {
		Reader input = new StringReader(txt);
		StringBuilder sb = new StringBuilder();
		Seg seg = getSeg();
		MMSeg mmSeg = new MMSeg(input, seg);
		Word word = null;
		boolean first = true;
		while((word=mmSeg.next())!=null) {
			if(!first) {
				sb.append(wordSpilt);
			}
			String w = word.getString();
			sb.append(w);
			first = false;
			
		}
		return sb.toString();
	}
	
	public  String[] run(String txt) throws IOException {
		String segmentArrary[];
    	String segment = segWords(txt, " | ");
		segmentArrary = segment.split(" \\| ");
		// Debug
		//for(int i=0;i<segmentArrary.length;i++){
	    //	System.out.println(segmentArrary[i]);
		//} 
		
		return segmentArrary;
	}
	
}