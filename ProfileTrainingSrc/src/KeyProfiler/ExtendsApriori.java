package KeyProfiler;

import java.util.ArrayList;
import java.util.Collections;
import java.util.HashMap;
import java.util.List;


public class ExtendsApriori extends Apriori{

    protected List< List< Integer >> maximalfrequent;
  
	public ExtendsApriori(String thrName, Database db, double minsup) {
		super(thrName, db, minsup);
	}
	
	public void printPatterns() {
	    System.out.println("maximalfrequent Itemsets");
	    for(List< Integer > pattern : maximalfrequent) {
	    System.out.println(pattern + "  support: "+ db.scanDatabase(pattern));
	    }
	    System.out.println("maximalfrequentTotal " + maximalfrequent.size() + " itemsets");
	}
	
	public void caluateMaximalFrequentItemset() {
		maximalfrequent = cloneList(frequent);
	    for(List< Integer > sampleItems : frequent) {
		    for(List< Integer > freqItems : frequent) {	    	
		    	if( compareList(freqItems,sampleItems)==true){
		    		maximalfrequent.remove(sampleItems);
		    	}
		    }
	   }
	}  
	    
	
	public List< List< Integer >> getMaximalfrequentItemsets() {
		return maximalfrequent;
	}
	
	public List<Double> getMaximalfrequentItemsetsSupport() {
		 List<Double > temp = new ArrayList< Double >();
		 for(List< Integer > pattern : maximalfrequent) {
			 temp.add((double)db.scanDatabase(pattern)/maximalfrequent.size());
		 }
		return temp;
	}
	
	// *************************************************************************************
	// extra function 
	public static List< List< Integer >> cloneList(List< List< Integer >> tempList) {

		List< List< Integer >> clonedList = new ArrayList<List< Integer >>(tempList.size());
		for (List< Integer > tempElement : tempList) {
			clonedList.add(tempElement);
	    }
	    return clonedList;
	}

	public static boolean compareList(List< Integer > freqItems, List< Integer > sampleItems) {
		// check if freqitems include sampleitems or not in every element.
		boolean included = true;
		for (Integer sampleItem : sampleItems) {
			boolean sameItems = false;
			for (Integer freqItem : freqItems) {
				if(sampleItem.equals(freqItem)){
					sameItems = true;
					break;
				}
	    	}
			if(sameItems==false){
				included = false;
				break;
			}
		}	
		
		// if they are the same stuff, return false
		if(included == true && freqItems.size() == sampleItems.size()){
			return false;
		}
			
		return included;
	}
	
	 // ======================== DEBUG ========================
	
	 // ======================= show the keywords with maxiaml frequent itemsets ========================
		public void printPatterns(HashMap<Integer,String> indexToWordHashMap) {
		    System.out.println("maximalfrequent Itemsets");
		    for(List< Integer > pattern : maximalfrequent) {
			    for( Integer  integer : pattern) {  	
			    	System.out.print(indexToWordHashMap.get(integer) + " "); 
			    }
		    System.out.println("  support: "+ db.scanDatabase(pattern));
		    }
		    System.out.println("maximalfrequentTotal " + maximalfrequent.size() + " itemsets");
		}
}
