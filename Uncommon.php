<?php

// setup

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
        $this->default_file =
            "/home/jsae/2018/outputs/organizations/2020-orgfinder/cache/good/file-wsp-home.md";
    }

    // functions ----- ----- ----- -----

    function run()
    {
        $this->doUncommon();
    }

    public function doUncommon($file = null)
    {
        $this->commonwordsUncommon();
        // Bring file in to assess and create set of words to check
        if ($file == null) {
            $file = $this->default_file;
            if (isset($this->file)) {
                $file = $this->file;
            }
        }

        if (!file_exists($file)) {
            $this->response .= 'Did not find "' . $file . '". ';
            return true;
        }

        $data = file_get_contents($file, true);

        $data = strip_tags($data);

        $words = [];
        $arr = explode("\n", $data);
        foreach ($arr as $key => $line) {
            $tokens = explode(" ", $line);
            foreach ($tokens as $j => $token) {
                // Strip out numbers and spaces
                $word_name = trim(strtolower($token));
                $word_name = trim($word_name, ";");
                $word_name = trim($word_name, ':');
                $word_name = trim($word_name, ",");
                $word_name = trim($word_name, ".");
                $word_name = trim($word_name, " ");
                $word_name = trim($word_name, "'");
                $word_name = trim($word_name, '"');
                $word_name = trim($word_name, '?');

                $word_name = trim(preg_replace('/\t+/', '', $word_name));

                // Reject strange tokens
                if (substr_count($word_name, ".") >= 1) {
                    continue;
                }
                if (substr_count($word_name, '/') >= 2) {
                    continue;
                }
                if (substr_count($word_name, '{') >= 1) {
                    continue;
                }
                if (substr_count($word_name, '}') >= 1) {
                    continue;
                }
                if (substr_count($word_name, '(') >= 1) {
                    continue;
                }
                if (substr_count($word_name, ')') >= 1) {
                    continue;
                }
                if (substr_count($word_name, "='") >= 1) {
                    continue;
                }
                if (substr_count($word_name, ";") >= 1) {
                    continue;
                }

                if (substr_count($word_name, "_") >= 1) {
                    continue;
                }


                if (is_numeric($word_name)) {
                    continue;
                }

                if (strlen($word_name) <= $this->minimum_word_length) {
                    continue;
                }

                if (!isset($words[$word_name])) {
                    $words[$word_name] = 0;
                }
                $words[$word_name] += 1;
            }
        }

        // Tag types of word

        $this->brilltagger_agent = new Brilltagger($this->thing, "brilltagger");
        $allowed_tags = ['NN', 'VBN', 'JJ'];
        //echo "\n";
        foreach ($words as $word_name => $word) {
            $respond = $this->isUncommon($word_name);

            if ($respond === true) {
                $tags = $this->brilltagger_agent->tag($word_name);
                if (!isset($tags[0])) {
                    continue;
                }
                $tag = $tags[0]['tag'];
                if (in_array($tags[0]['tag'], $allowed_tags)) {
                    $this->response .= $word_name . " ";
                }
            }
        }

        //couple of lines of crude debugging code
        //echo "\n";
        // corncob_lowercase.txt

        // ---
        // Respond to caller

        if ($this->agent_input == null) {
            $array = ['miao', 'miaou', 'hiss', 'prrr', 'grrr'];
            $k = array_rand($array);
            $v = $array[$k];

            $response = "UNCOMMON | " . strtolower($v) . ".";
            // Save words
            //file_put_contents("/home/jsae/2018/outputs/organizations/2020-orgfinder/words/passive_survivability.txt",$v);

            $this->uncommon_message = $response; // mewsage?
        } else {
            $this->uncommon_message = $this->agent_input;
        }

        $this->resposne .= "Looked for uncommon words. ";
    }

    public function isUncommon($text = null)
    {
        // Identify uncommon words in the set
        // based on length of dictionary used
        if ($text == null) {
            return;
        }
        foreach ($this->common_words as $i => $common_word) {
            if (strtolower($text) == strtolower($common_word)) {
                return false;
            }
        }
        return true;
    }

    public function commonwordsUncommon()
    {
        //alternative dictionary lists for common words
        //$data = file_get_contents($this->resource_path . 'corncob/corncob_lowercase.txt');
        //$data = file_get_contents($this->resource_path . 'corncob/corncob_lowercase.txt');
        $dict_path = '/home/jsae/Outputs/independence/word-sources/dict/';

        $file = $dict_path . 'freq10000words-en-list.txt';

        if (!file_exists($file)) {
            $this->response .= 'Could not find dictionary, "' . $file . '". ';
            return true;
        }

        $data = file_get_contents($file);
        //$data = file_get_contents($dict_path . 'vocab-84669-en-list.txt');

        $words = [];
        $arr = explode("\n", $data);
        foreach ($arr as $key => $line) {
            $tokens = explode(" ", $line);
            foreach ($tokens as $j => $token) {
                $token = trim($token);

                //$word_name = strtolower($token);

                //if (!isset($words[$word_name])) {$words[$word_name] = 0;}
                $words[] = $token;
            }
        }

        $this->common_words = $words;
    }

    public function respondResponse()
    {
        $this->thing->flagGreen();

        $this->thing_report["info"] =
            "This is Uncommon. It sees uncommon things.";
        $this->thing_report["help"] =
            "This is about seeing what is uncommon. Try UNCOMMON <filename>";

        //$this->thing_report['sms'] = $this->sms_message;
        $this->thing_report['message'] = $this->sms_message;
        $this->thing_report['txt'] = $this->sms_message;

        $message_thing = new Message($this->thing, $this->thing_report);
        $thing_report['info'] = $message_thing->thing_report['info'];

        return $this->thing_report;
    }

    function makeSMS()
    {
        $this->node_list = ["uncommon" => ["cat", "dog"]];

        //$sms = "Looked for uncommon words. ";
        $sms = "UNCOMMON\n";

        $sms .= $this->response;

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
        // input gets either.
        // subject or
        // agent_input
        $input = $this->input;

        $filtered_input = $this->assert($input);

        $this->file = $filtered_input;
    }
}
