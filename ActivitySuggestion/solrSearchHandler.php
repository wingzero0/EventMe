<?php
/* provide the search interface for solr 
 * 
 * GET parameter:
 * 	"q": string value, query
 * 
 * Return value
 * 	json["ret"]: should be 1 if operation is successful
 * 	json["ids"]: the list of activity
 *  json["ids"][x]: the activity id
 *  json["error"]: if "ret" == -1, "error" will cantain the error message 
 * 
 * sample usage
 * http://140.112.29.228/ActivitySuggestion/solrSearchHandler.php?q=演唱會
 * */

require_once __DIR__ . '/utility.php';
require_once CLASSPATH . '/solr.php';

$s_var = array();
Utility::AddslashesToGETField("q", $s_var);

$solr = new solr();
$docIDsRet = $solr->KeywordSearch(preg_split("/ /", $s_var["q"]));

echo json_encode($docIDsRet);

?>