<?php
namespace Nrwtaylor\StackAgentThing;

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

ini_set("allow_url_fopen", 1);

class Uncommon extends Agent
{
    public $var = 'hello';

    function init()
    {

$this->minimum_word_length = 4;
    }

    function run()
    {
        $this->doUncommon();
    }

    public function doUncommon()
    {
    $this->commonwordsUncommon();
   
    $file = "/home/jsae/Outputs/independence/word-sources/articles/20200903-I5-2-buildinggreen-Passive Survivability A New Design Criterion for Buildings.txt";
    $data = file_get_contents($file,true);
    //var_dump("merp");
    //var_dump($data);
 $words = [];
 $arr = explode("\n", $data);
 foreach($arr as $key=>$line) {
 //var_dump($line);
 $tokens = explode(" ", $line);
 foreach($tokens as $j=>$token) {
 
 // Strip out numbers and spaces
 $word_name = trim(strtolower($token));
 $word_name = trim($word_name,";"); 
 $word_name = trim($word_name," ");
 $word_name = trim(preg_replace('/\t+/','', $word_name));

if (is_numeric($word_name)) {continue;}

if ( strlen($word_name) <= $this->minimum_word_length ) {continue;}
 
 if (!isset($words[$word_name])) {$words[$word_name] = 0;}
 $words[$word_name] += 1;
 
 }
 }

// Tag types of word

$this->brilltagger_agent = new Brilltagger($this->thing, "brilltagger");
$allowed_tags = ['NN','VBN','JJ'];
//echo "\n"; 
 foreach($words as $word_name=>$word) {
 
 //var_dump("---");
 //var_dump($word_name);
$respond = $this->isUncommon($word_name); 
 
 if ($respond === true) {
 


$tags = $this->brilltagger_agent->tag($word_name);


 $tag = $tags[0]['tag'];
 if (in_array($tags[0]['tag'], $allowed_tags)) {
 
$this->response .= $word_name . " ";
 //var_dump($tags[0]['tag']);
 
 }
 
 }
 
 }
 
 //echo "\n";
// corncob_lowercase.txt

    
        if ($this->agent_input == null) {
            $array = array('miao', 'miaou', 'hiss', 'prrr', 'grrr');
            $k = array_rand($array);
            $v = $array[$k];

            $response = "UNCOMMON | " . strtolower($v) . ".";

            $this->uncommon_message = $response; // mewsage?
        } else {
            $this->uncommon_message = $this->agent_input;
        }
    }

public function isUncommon($text = null) {

if ($text == null) {return;}
foreach($this->common_words as $i=>$common_word) {

if (strtolower($text) == strtolower($common_word)) {return false;}


}
return true;
}

public function commonwordsUncommon() {
//var_dump($this->resource_path);
$data = file_get_contents($this->resource_path . 'corncob/corncob_lowercase.txt');
//var_dump($data);

$words = [];
 $arr = explode("\n", $data);
 foreach($arr as $key=>$line) {
 //var_dump($line);
 $tokens = explode(" ", $line);
 foreach($tokens as $j=>$token) {
 
 $token = trim($token);
 
 //$word_name = strtolower($token);
 
 //if (!isset($words[$word_name])) {$words[$word_name] = 0;}
 $words[] = $token;
 
 }
 }

$this->common_words = $words;

}

    function getNegativetime()
    {
        $agent = new Negativetime($this->thing, "Uncommon");
        $this->negative_time = $agent->negative_time; //negative time is asking
    }

    // -----------------------

    public function respondResponse()
    {
        $this->thing->flagGreen();

        $this->thing_report["info"] =
            "This is a Uncommon keeping an eye on how late this Thing is.";
        $this->thing_report["help"] = "This is about being inscrutable.";

        //$this->thing_report['sms'] = $this->sms_message;
        $this->thing_report['message'] = $this->sms_message;
        $this->thing_report['txt'] = $this->sms_message;

        $message_thing = new Message($this->thing, $this->thing_report);
        $thing_report['info'] = $message_thing->thing_report['info'];

        return $this->thing_report;
    }

    function makeSMS()
    {
        $this->node_list = array("uncommon" => array("cat", "dog"));
 
$sms = "Found uncommon words: ". $this->response;
 
        $this->sms_message = $sms; 
        $this->thing_report['sms'] = $sms;
    }

    function makeChoices()
    {
        $this->thing->choice->Create('channel', $this->node_list, "uncommon");
        $choices = $this->thing->choice->makeLinks('uncommon');
        $this->thing_report['choices'] = $choices;
    }

    public function readSubject()
    {
        return false;
    }
}
